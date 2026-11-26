<?php
/**
 * WordPress back-office content model and one-time content migration.
 */

if (! defined('ABSPATH')) {
	exit;
}

function portfolio_admin_content_labels($plural, $singular, $menu_name = '') {
	$menu_name = $menu_name ?: $plural;

	return array(
		'name'                     => $plural,
		'singular_name'            => $singular,
		'menu_name'                => $menu_name,
		'name_admin_bar'           => $singular,
		'add_new'                  => __('Add New', 'custom-theme'),
		'add_new_item'             => sprintf(__('Add New %s', 'custom-theme'), $singular),
		'new_item'                 => sprintf(__('New %s', 'custom-theme'), $singular),
		'edit_item'                => sprintf(__('Edit %s', 'custom-theme'), $singular),
		'view_item'                => sprintf(__('View %s', 'custom-theme'), $singular),
		'view_items'               => sprintf(__('View %s', 'custom-theme'), $plural),
		'all_items'                => sprintf(__('All %s', 'custom-theme'), $plural),
		'search_items'             => sprintf(__('Search %s', 'custom-theme'), $plural),
		'parent_item_colon'        => sprintf(__('Parent %s:', 'custom-theme'), $singular),
		'not_found'                => sprintf(__('No %s found.', 'custom-theme'), strtolower($plural)),
		'not_found_in_trash'       => sprintf(__('No %s found in Trash.', 'custom-theme'), strtolower($plural)),
		'archives'                 => sprintf(__('%s Archives', 'custom-theme'), $singular),
		'attributes'               => sprintf(__('%s Attributes', 'custom-theme'), $singular),
		'insert_into_item'         => sprintf(__('Insert into %s', 'custom-theme'), strtolower($singular)),
		'uploaded_to_this_item'    => sprintf(__('Uploaded to this %s', 'custom-theme'), strtolower($singular)),
		'filter_items_list'        => sprintf(__('Filter %s list', 'custom-theme'), strtolower($plural)),
		'items_list_navigation'    => sprintf(__('%s list navigation', 'custom-theme'), $plural),
		'items_list'               => sprintf(__('%s list', 'custom-theme'), $plural),
		'item_published'           => sprintf(__('%s published.', 'custom-theme'), $singular),
		'item_published_privately' => sprintf(__('%s published privately.', 'custom-theme'), $singular),
		'item_reverted_to_draft'   => sprintf(__('%s reverted to draft.', 'custom-theme'), $singular),
		'item_scheduled'           => sprintf(__('%s scheduled.', 'custom-theme'), $singular),
		'item_updated'             => sprintf(__('%s updated.', 'custom-theme'), $singular),
		'item_link'                => sprintf(__('%s Link', 'custom-theme'), $singular),
		'item_link_description'    => sprintf(__('A link to a %s.', 'custom-theme'), strtolower($singular)),
	);
}

function portfolio_content_types() {
	$project_labels = portfolio_admin_content_labels(
		__('Portfolio Projects', 'custom-theme'),
		__('Portfolio Project', 'custom-theme')
	);
	$project_labels['featured_image']        = __('Project Cover Image', 'custom-theme');
	$project_labels['set_featured_image']    = __('Set project cover image', 'custom-theme');
	$project_labels['remove_featured_image'] = __('Remove project cover image', 'custom-theme');
	$project_labels['use_featured_image']    = __('Use as project cover image', 'custom-theme');

	register_post_type(
		'portfolio_project',
		array(
			'labels'       => $project_labels,
			'description'  => __('Manage portfolio projects and their public case-study pages.', 'custom-theme'),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-portfolio',
			'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes'),
			'has_archive'  => true,
			'rewrite'      => array('slug' => 'projects'),
		)
	);

	register_taxonomy(
		'project_category',
		'portfolio_project',
		array(
			'labels'       => array(
				'name'              => __('Project Categories', 'custom-theme'),
				'singular_name'     => __('Project Category', 'custom-theme'),
				'menu_name'         => __('Project Categories', 'custom-theme'),
				'search_items'      => __('Search Project Categories', 'custom-theme'),
				'all_items'         => __('All Project Categories', 'custom-theme'),
				'parent_item'       => __('Parent Project Category', 'custom-theme'),
				'parent_item_colon' => __('Parent Project Category:', 'custom-theme'),
				'edit_item'         => __('Edit Project Category', 'custom-theme'),
				'view_item'         => __('View Project Category', 'custom-theme'),
				'update_item'       => __('Update Project Category', 'custom-theme'),
				'add_new_item'      => __('Add New Project Category', 'custom-theme'),
				'new_item_name'     => __('New Project Category Name', 'custom-theme'),
				'not_found'         => __('No project categories found.', 'custom-theme'),
				'back_to_items'     => __('Back to Project Categories', 'custom-theme'),
			),
			'hierarchical' => true,
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array('slug' => 'project-category'),
		)
	);

	$managed_types = array(
		'portfolio_service'    => array('Services', 'Service', 'Services', 'dashicons-admin-tools'),
		'portfolio_experience' => array('Experience Entries', 'Experience Entry', 'Work Experience', 'dashicons-businessperson'),
		'portfolio_skill'      => array('Skill Groups', 'Skill Group', 'Skills', 'dashicons-awards'),
		'portfolio_education'  => array('Education Entries', 'Education Entry', 'Education', 'dashicons-welcome-learn-more'),
	);

	foreach ($managed_types as $post_type => $details) {
		register_post_type(
			$post_type,
			array(
				'labels'              => portfolio_admin_content_labels(
					__($details[0], 'custom-theme'),
					__($details[1], 'custom-theme'),
					__($details[2], 'custom-theme')
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'menu_icon'           => $details[3],
				'supports'            => array('title', 'editor', 'excerpt', 'revisions', 'page-attributes'),
			)
		);
	}
}
add_action('init', 'portfolio_content_types', 5);

function portfolio_get_section_page($slug) {
	$page = get_page_by_path(sanitize_title($slug), OBJECT, 'page');

	return $page instanceof WP_Post ? $page : null;
}

function portfolio_get_section_heading($slug, $fallback) {
	$page = portfolio_get_section_page($slug);

	if ($page && '' !== trim($page->post_excerpt)) {
		return $page->post_excerpt;
	}

	return $fallback;
}

function portfolio_get_section_content($slug, $fallback = '') {
	$page = portfolio_get_section_page($slug);

	if ($page && '' !== trim($page->post_content)) {
		return $page->post_content;
	}

	return $fallback;
}

function portfolio_get_managed_posts($post_type) {
	return get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array('menu_order' => 'ASC', 'date' => 'ASC'),
			'order'          => 'ASC',
		)
	);
}

function portfolio_project_to_array($post) {
	$post = get_post($post);

	if (! $post || 'portfolio_project' !== $post->post_type) {
		return null;
	}

	$terms      = get_the_terms($post, 'project_category');
	$highlights = preg_split('/\r\n|\r|\n/', (string) get_post_meta($post->ID, '_portfolio_highlights', true));
	$highlights = array_values(array_filter(array_map('trim', $highlights)));
	$image      = get_the_post_thumbnail_url($post, 'full');

	if (! $image) {
		$fallback_image = get_post_meta($post->ID, '_portfolio_theme_image', true);
		$image          = $fallback_image ? portfolio_asset($fallback_image) : '';
	}

	return array(
		'id'           => $post->ID,
		'slug'         => $post->post_name,
		'title'        => get_the_title($post),
		'category'     => $terms && ! is_wp_error($terms) ? $terms[0]->name : __('Project', 'custom-theme'),
		'focus'        => get_post_meta($post->ID, '_portfolio_focus', true),
		'technologies' => get_post_meta($post->ID, '_portfolio_technologies', true),
		'description'  => has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 28),
		'overview'     => wp_strip_all_tags($post->post_content),
		'highlights'   => $highlights,
		'image_url'    => $image,
		'url'          => get_permalink($post),
	);
}

function portfolio_get_projects() {
	$projects = array();

	foreach (portfolio_get_managed_posts('portfolio_project') as $post) {
		$project = portfolio_project_to_array($post);

		if ($project) {
			$projects[] = $project;
		}
	}

	return $projects;
}

function portfolio_get_project($slug) {
	$post = get_page_by_path(sanitize_title($slug), OBJECT, 'portfolio_project');

	return $post ? portfolio_project_to_array($post) : null;
}

function portfolio_get_project_url($project) {
	if (! empty($project['url'])) {
		return $project['url'];
	}

	return ! empty($project['id']) ? get_permalink($project['id']) : home_url('/#portfolio');
}

function portfolio_add_content_meta_boxes() {
	add_meta_box('portfolio-project-details', __('Project Details', 'custom-theme'), 'portfolio_project_meta_box', 'portfolio_project', 'normal', 'high');
	add_meta_box('portfolio-service-details', __('Service Details', 'custom-theme'), 'portfolio_service_meta_box', 'portfolio_service', 'side');
	add_meta_box('portfolio-experience-details', __('Experience Details', 'custom-theme'), 'portfolio_experience_meta_box', 'portfolio_experience', 'side');
	add_meta_box('portfolio-education-details', __('Education Details', 'custom-theme'), 'portfolio_education_meta_box', 'portfolio_education', 'side');
	add_meta_box('portfolio-page-details', __('Portfolio Section Settings', 'custom-theme'), 'portfolio_page_meta_box', 'page', 'side');
}
add_action('add_meta_boxes', 'portfolio_add_content_meta_boxes');

function portfolio_meta_field($post, $name, $label, $type = 'text', $description = '') {
	$value = get_post_meta($post->ID, $name, true);
	?>
	<p>
		<label for="<?php echo esc_attr($name); ?>"><strong><?php echo esc_html($label); ?></strong></label>
		<?php if ('textarea' === $type) : ?>
			<textarea class="widefat" rows="6" id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>"><?php echo esc_textarea($value); ?></textarea>
		<?php else : ?>
			<input class="widefat" type="text" id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
		<?php endif; ?>
		<?php if ($description) : ?>
			<small><?php echo esc_html($description); ?></small>
		<?php endif; ?>
	</p>
	<?php
}

function portfolio_project_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_meta_field($post, '_portfolio_technologies', __('Technologies', 'custom-theme'), 'text', __('Separate technologies with commas.', 'custom-theme'));
	portfolio_meta_field($post, '_portfolio_focus', __('Project Focus', 'custom-theme'));
	portfolio_meta_field($post, '_portfolio_highlights', __('Project Highlights', 'custom-theme'), 'textarea', __('Enter one highlight per line.', 'custom-theme'));
}

function portfolio_service_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_meta_field($post, '_portfolio_icon', __('Font Awesome icon class', 'custom-theme'), 'text', __('Example: fas fa-code', 'custom-theme'));
}

function portfolio_experience_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_meta_field($post, '_portfolio_company', __('Company', 'custom-theme'));
	portfolio_meta_field($post, '_portfolio_period', __('Period', 'custom-theme'));
}

function portfolio_education_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_meta_field($post, '_portfolio_institution', __('Institution', 'custom-theme'));
	portfolio_meta_field($post, '_portfolio_period', __('Period', 'custom-theme'));
}

function portfolio_page_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	echo '<p>' . esc_html__('Use the page excerpt as the section heading and the page content as its description. A featured image controls the Home or About image.', 'custom-theme') . '</p>';
	portfolio_meta_field($post, '_portfolio_hero_name', __('Hero Name (Home page only)', 'custom-theme'));
}

function portfolio_save_content_meta($post_id) {
	if (
		! isset($_POST['portfolio_content_meta_nonce']) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['portfolio_content_meta_nonce'])), 'portfolio_save_content_meta') ||
		(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
		! current_user_can('edit_post', $post_id)
	) {
		return;
	}

	$fields = array(
		'_portfolio_technologies',
		'_portfolio_focus',
		'_portfolio_highlights',
		'_portfolio_icon',
		'_portfolio_company',
		'_portfolio_period',
		'_portfolio_institution',
		'_portfolio_hero_name',
	);

	foreach ($fields as $field) {
		if (! array_key_exists($field, $_POST)) {
			continue;
		}

		$value = wp_unslash($_POST[$field]);
		$value = '_portfolio_highlights' === $field ? sanitize_textarea_field($value) : sanitize_text_field($value);
		update_post_meta($post_id, $field, $value);
	}
}
add_action('save_post', 'portfolio_save_content_meta');

function portfolio_import_theme_image($relative_path, $title, $parent_id = 0) {
	$source = get_template_directory() . '/assets/' . ltrim($relative_path, '/');

	if (! is_readable($source)) {
		return 0;
	}

	$filename = sanitize_file_name(sanitize_title($title) . '-' . wp_basename($source));
	$upload   = wp_upload_bits($filename, null, file_get_contents($source));

	if (! empty($upload['error'])) {
		return 0;
	}

	$filetype      = wp_check_filetype($upload['file']);
	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file'],
		$parent_id
	);

	if (is_wp_error($attachment_id)) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata($attachment_id, $upload['file']);
	wp_update_attachment_metadata($attachment_id, $metadata);

	return (int) $attachment_id;
}

function portfolio_seed_featured_image($post_id, $relative_path, $title) {
	if (has_post_thumbnail($post_id)) {
		return;
	}

	$attachment_id = portfolio_import_theme_image($relative_path, $title, $post_id);

	if ($attachment_id) {
		set_post_thumbnail($post_id, $attachment_id);
	}
}

function portfolio_seed_page($data) {
	$page = get_page_by_path($data['slug'], OBJECT, 'page');

	if ($page) {
		return $page->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $data['slug'],
			'post_title'   => $data['title'],
			'post_excerpt' => $data['excerpt'],
			'post_content' => $data['content'],
			'menu_order'   => $data['order'],
		)
	);

	if (! is_wp_error($page_id) && ! empty($data['image'])) {
		portfolio_seed_featured_image($page_id, $data['image'], $data['title']);
	}

	if (! is_wp_error($page_id) && ! empty($data['hero_name'])) {
		update_post_meta($page_id, '_portfolio_hero_name', $data['hero_name']);
	}

	return is_wp_error($page_id) ? 0 : $page_id;
}

function portfolio_seed_managed_post($post_type, $data, $meta = array()) {
	$existing = get_page_by_path($data['slug'], OBJECT, $post_type);

	if ($existing) {
		return $existing->ID;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => $post_type,
			'post_status'  => 'publish',
			'post_name'    => $data['slug'],
			'post_title'   => $data['title'],
			'post_excerpt' => $data['excerpt'] ?? '',
			'post_content' => $data['content'] ?? '',
			'menu_order'   => $data['order'] ?? 0,
		)
	);

	if (is_wp_error($post_id)) {
		return 0;
	}

	foreach ($meta as $key => $value) {
		update_post_meta($post_id, $key, $value);
	}

	if (! empty($data['image'])) {
		portfolio_seed_featured_image($post_id, $data['image'], $data['title']);
	}

	return $post_id;
}

function portfolio_seed_back_office_content() {
	$migration_version = '2.0.0';

	if (get_option('portfolio_content_migration_version') === $migration_version) {
		return;
	}

	$pages = array(
		array('slug' => 'home', 'title' => 'Home', 'excerpt' => 'Software Engineer | Web Developer | Digital Creative', 'content' => 'I build reliable web applications, scalable backend systems, AI-powered solutions, and engaging digital experiences.', 'image' => 'img/illustration/1.png', 'hero_name' => 'ANANDHU R', 'order' => 0),
		array('slug' => 'about', 'title' => 'About', 'excerpt' => 'Building Technology & Creating Digital Experiences', 'content' => "I am a software engineer passionate about developing practical software solutions and modern digital experiences. My interests include full-stack development, backend engineering, AI-powered applications, and creative digital media.\n\nAlongside software development, I work with social media management, digital marketing, photography, and videography.", 'image' => 'img/illustration/2.jpg', 'order' => 1),
		array('slug' => 'services', 'title' => 'Services', 'excerpt' => 'What I Can Help You Build', 'content' => 'Development and creative services focused on practical digital outcomes.', 'order' => 2),
		array('slug' => 'experience', 'title' => 'Experience', 'excerpt' => 'Professional Journey', 'content' => 'Professional roles, responsibilities, and applied technical experience.', 'order' => 3),
		array('slug' => 'skills', 'title' => 'Skills', 'excerpt' => 'Technical & Creative Toolkit', 'content' => 'A practical toolkit spanning software engineering, AI, web development, and digital media.', 'order' => 4),
		array('slug' => 'education', 'title' => 'Education', 'excerpt' => 'Academic Background', 'content' => 'Academic qualifications and learning journey.', 'order' => 5),
		array('slug' => 'portfolio', 'title' => 'Portfolio', 'excerpt' => 'Selected Projects', 'content' => 'A selection of software, AI, web, and creative projects.', 'order' => 6),
		array('slug' => 'contact', 'title' => 'Contact', 'excerpt' => "Let's Build Something Great Together!", 'content' => "Have a project or an idea? Let's connect and make it happen.", 'order' => 7),
	);

	$page_ids = array();
	foreach ($pages as $page) {
		$page_ids[$page['slug']] = portfolio_seed_page($page);
	}

	if (! empty($page_ids['home'])) {
		update_option('show_on_front', 'page');
		update_option('page_on_front', $page_ids['home']);
	}

	$services = array(
		array('Full-Stack Web Development', 'Responsive, end-to-end web applications built for real business needs.', 'fas fa-code'),
		array('Backend & API Development', 'Secure backend services and maintainable REST API integrations.', 'fas fa-server'),
		array('AI Application Development', 'AI-powered tools, assistants, and automation using modern LLM APIs.', 'fas fa-robot'),
		array('Database Management', 'Reliable PostgreSQL and MySQL data design, queries, and operations.', 'fas fa-database'),
		array('UI Design & Web Experiences', 'Accessible interfaces and responsive, user-focused web experiences.', 'fas fa-palette'),
		array('Social Media Management', 'Content planning, publishing, and audience-focused channel management.', 'fas fa-share-alt'),
		array('Digital Marketing', 'Campaign support, content strategy, and digital brand growth.', 'fas fa-bullhorn'),
		array('Photography & Videography', 'Photography, videography, and visual storytelling for digital platforms.', 'fas fa-camera'),
	);

	foreach ($services as $index => $service) {
		portfolio_seed_managed_post(
			'portfolio_service',
			array('slug' => sanitize_title($service[0]), 'title' => $service[0], 'excerpt' => $service[1], 'order' => $index),
			array('_portfolio_icon' => $service[2])
		);
	}

	$responsibilities = array(
		'Develop Python-based applications and backend services using FastAPI.',
		'Build and integrate REST APIs.',
		'Work with AI tools, LLMs, and external AI APIs.',
		'Manage database operations using PostgreSQL and MySQL.',
		'Implement application validation, error handling, and backend functionality.',
		'Contribute to chatbot, automation, and AI-powered application development.',
	);
	$experience_content = '<ul><li>' . implode('</li><li>', array_map('esc_html', $responsibilities)) . '</li></ul>';
	portfolio_seed_managed_post(
		'portfolio_experience',
		array('slug' => 'python-ai-engineer', 'title' => 'Python AI Engineer', 'content' => $experience_content, 'order' => 0),
		array('_portfolio_company' => 'Riverswave Technologies Pvt. Ltd.', '_portfolio_period' => 'August 2025 - Present')
	);

	$skills = array(
		'Programming' => 'Python, Java, C, C++, PHP',
		'Backend' => 'FastAPI, Django, REST APIs, SQLAlchemy',
		'Frontend' => 'HTML5, CSS3, JavaScript, Bootstrap, React Fundamentals',
		'Databases' => 'PostgreSQL, MySQL',
		'AI & Integration' => 'LLM Integration, Prompt Engineering, OpenAI API, Google Gemini API, Azure AI Foundry',
		'Tools' => 'Git, GitHub, VS Code, WordPress',
		'Creative Skills' => 'Social Media Management, Digital Marketing, Content Creation, Photography, Videography',
	);
	$skill_index = 0;
	foreach ($skills as $title => $content) {
		portfolio_seed_managed_post('portfolio_skill', array('slug' => sanitize_title($title), 'title' => $title, 'content' => $content, 'order' => $skill_index));
		$skill_index++;
	}

	$education = array(
		array('Master of Computer Applications (MCA)', 'Mar Thoma Institute of Information Technology', '2023 - 2025'),
		array('Bachelor of Science in Computer Science', 'PMSA PTM Arts and Science College', '2019 - 2022'),
		array('Higher Secondary Education', 'GHSS Thevannoor', '2017 - 2019'),
	);
	foreach ($education as $index => $entry) {
		portfolio_seed_managed_post(
			'portfolio_education',
			array('slug' => sanitize_title($entry[0]), 'title' => $entry[0], 'order' => $index),
			array('_portfolio_institution' => $entry[1], '_portfolio_period' => $entry[2])
		);
	}

	foreach (portfolio_default_projects() as $index => $project) {
		$post_id = portfolio_seed_managed_post(
			'portfolio_project',
			array(
				'slug'    => $project['slug'],
				'title'   => $project['title'],
				'excerpt' => $project['description'],
				'content' => $project['overview'],
				'image'   => $project['image'],
				'order'   => $index,
			),
			array(
				'_portfolio_technologies' => $project['technologies'],
				'_portfolio_focus'        => $project['focus'],
				'_portfolio_highlights'   => implode("\n", $project['highlights']),
				'_portfolio_theme_image'  => $project['image'],
			)
		);

		if ($post_id) {
			wp_set_object_terms($post_id, $project['category'], 'project_category');
		}
	}

	flush_rewrite_rules(false);
	update_option('portfolio_content_migration_version', $migration_version);
}
add_action('init', 'portfolio_seed_back_office_content', 30);

/**
 * Install the enhanced portrait once and expose it through the Media Library.
 * Home and About intentionally share one attachment so either page can be
 * managed from the normal Featured Image panel without duplicating media.
 */
function portfolio_install_enhanced_portrait() {
	$migration_version = '1.0.0';

	if (get_option('portfolio_portrait_migration_version') === $migration_version) {
		return;
	}

	$home_page  = get_page_by_path('home', OBJECT, 'page');
	$about_page = get_page_by_path('about', OBJECT, 'page');

	if (! $home_page || ! $about_page) {
		return;
	}

	$attachment_id = portfolio_import_theme_image(
		'img/anandhu-portrait-enhanced.png',
		'Anandhu R portrait',
		$home_page->ID
	);

	if (! $attachment_id) {
		return;
	}

	set_post_thumbnail($home_page->ID, $attachment_id);
	set_post_thumbnail($about_page->ID, $attachment_id);
	update_option('portfolio_portrait_migration_version', $migration_version);
}
add_action('init', 'portfolio_install_enhanced_portrait', 40);
