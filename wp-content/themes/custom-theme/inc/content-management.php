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
			// Content and summary are presented in the purpose-built Project Details
			// panel below. Keeping the generic editor/excerpt boxes as well makes the
			// back office look as though the same information must be entered twice.
			'supports'     => array('title', 'thumbnail', 'revisions'),
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
				'supports'            => array('title', 'revisions'),
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

/**
 * Portfolio pages which represent editable sections on the one-page site.
 */
function portfolio_section_page_slugs() {
	return array('home', 'about', 'services', 'experience', 'skills', 'education', 'portfolio', 'contact');
}

/**
 * Use the classic edit canvas for these small, structured records. The block
 * editor is useful for articles, but adds an unnecessary second content area
 * to forms whose fields are deliberately defined below.
 */
function portfolio_use_block_editor($use_block_editor, $post_type) {
	$structured_types = array(
		'portfolio_project',
		'portfolio_service',
		'portfolio_experience',
		'portfolio_skill',
		'portfolio_education',
	);

	return in_array($post_type, $structured_types, true) ? false : $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'portfolio_use_block_editor', 10, 2);

function portfolio_add_content_meta_boxes($post_type, $post) {
	$boxes = array(
		'portfolio_project'    => array('portfolio-project-details', __('Project Details', 'custom-theme'), 'portfolio_project_meta_box'),
		'portfolio_service'    => array('portfolio-service-details', __('Service Details', 'custom-theme'), 'portfolio_service_meta_box'),
		'portfolio_experience' => array('portfolio-experience-details', __('Experience Details', 'custom-theme'), 'portfolio_experience_meta_box'),
		'portfolio_skill'      => array('portfolio-skill-details', __('Skill Group Details', 'custom-theme'), 'portfolio_skill_meta_box'),
		'portfolio_education'  => array('portfolio-education-details', __('Education Details', 'custom-theme'), 'portfolio_education_meta_box'),
	);

	if (isset($boxes[$post_type])) {
		$box = $boxes[$post_type];
		add_meta_box($box[0], $box[1], $box[2], $post_type, 'normal', 'high');
	}

	if (
		'page' === $post_type &&
		$post instanceof WP_Post &&
		in_array($post->post_name, portfolio_section_page_slugs(), true)
	) {
		// On these eight pages the dedicated fields below are the editor. Remove
		// the generic content canvas for this request so there is only one obvious
		// place to update the section description.
		remove_post_type_support('page', 'editor');
		add_meta_box(
			'portfolio-page-details',
			__('Portfolio Section Content', 'custom-theme'),
			'portfolio_page_meta_box',
			'page',
			'normal',
			'high'
		);
	}
}
add_action('add_meta_boxes', 'portfolio_add_content_meta_boxes', 10, 2);

function portfolio_admin_panel_intro($text) {
	echo '<div class="portfolio-admin-intro"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span><p>' . esc_html($text) . '</p></div>';
}

function portfolio_admin_field($name, $label, $value, $type = 'text', $description = '', $options = array()) {
	$options = wp_parse_args(
		$options,
		array(
			'class'       => '',
			'placeholder' => '',
			'rows'        => 5,
			'min'         => '',
		)
	);
	$field_id = 'portfolio-field-' . sanitize_html_class($name);
	?>
	<div class="portfolio-admin-field <?php echo esc_attr($options['class']); ?>">
		<label for="<?php echo esc_attr($field_id); ?>"><?php echo esc_html($label); ?></label>
		<?php if ('textarea' === $type) : ?>
			<textarea
				class="widefat"
				rows="<?php echo esc_attr($options['rows']); ?>"
				id="<?php echo esc_attr($field_id); ?>"
				name="<?php echo esc_attr($name); ?>"
				placeholder="<?php echo esc_attr($options['placeholder']); ?>"
			><?php echo esc_textarea($value); ?></textarea>
		<?php else : ?>
			<input
				class="widefat"
				type="<?php echo esc_attr($type); ?>"
				id="<?php echo esc_attr($field_id); ?>"
				name="<?php echo esc_attr($name); ?>"
				value="<?php echo esc_attr($value); ?>"
				placeholder="<?php echo esc_attr($options['placeholder']); ?>"
				<?php echo '' !== $options['min'] ? 'min="' . esc_attr($options['min']) . '"' : ''; ?>
			>
		<?php endif; ?>
		<?php if ($description) : ?>
			<p class="description"><?php echo esc_html($description); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

function portfolio_meta_field($post, $name, $label, $type = 'text', $description = '', $options = array()) {
	portfolio_admin_field($name, $label, get_post_meta($post->ID, $name, true), $type, $description, $options);
}

function portfolio_display_order_field($post) {
	portfolio_admin_field(
		'portfolio_display_order',
		__('Display Order', 'custom-theme'),
		(int) $post->menu_order,
		'number',
		__('Lower numbers appear first on the website.', 'custom-theme'),
		array('min' => '0')
	);
}

function portfolio_experience_responsibilities($content) {
	if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', (string) $content, $matches)) {
		return implode("\n", array_map('wp_strip_all_tags', $matches[1]));
	}

	return trim(wp_strip_all_tags((string) $content));
}

function portfolio_project_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_admin_panel_intro(__('Complete each project field below. Category and cover image are managed in the panels on the right.', 'custom-theme'));
	echo '<div class="portfolio-admin-grid">';
	portfolio_meta_field($post, '_portfolio_technologies', __('Technologies', 'custom-theme'), 'text', __('Separate technologies with commas.', 'custom-theme'), array('placeholder' => 'Python, FastAPI, PostgreSQL'));
	portfolio_meta_field($post, '_portfolio_focus', __('Project Focus', 'custom-theme'), 'text', __('A short description of the project objective.', 'custom-theme'), array('placeholder' => 'Billing and inventory workflows'));
	portfolio_admin_field('portfolio_post_excerpt', __('Short Description', 'custom-theme'), $post->post_excerpt, 'textarea', __('Shown on the portfolio card and at the top of the project page.', 'custom-theme'), array('class' => 'portfolio-admin-field--full', 'rows' => 3));
	portfolio_admin_field('portfolio_post_content', __('Project Overview', 'custom-theme'), $post->post_content, 'textarea', __('Describe the project, the problem, and the solution.', 'custom-theme'), array('class' => 'portfolio-admin-field--full', 'rows' => 6));
	portfolio_meta_field($post, '_portfolio_highlights', __('Project Highlights', 'custom-theme'), 'textarea', __('Enter one highlight per line.', 'custom-theme'), array('class' => 'portfolio-admin-field--full', 'rows' => 6));
	portfolio_display_order_field($post);
	echo '</div>';
}

function portfolio_service_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_admin_panel_intro(__('Add the service name above, then complete its website card details here.', 'custom-theme'));
	echo '<div class="portfolio-admin-grid">';
	portfolio_admin_field('portfolio_post_excerpt', __('Service Description', 'custom-theme'), $post->post_excerpt, 'textarea', __('Keep this concise; it appears directly below the service name.', 'custom-theme'), array('class' => 'portfolio-admin-field--full', 'rows' => 4));
	portfolio_meta_field($post, '_portfolio_icon', __('Icon Class', 'custom-theme'), 'text', __('Use a Font Awesome class, for example: fas fa-code.', 'custom-theme'), array('placeholder' => 'fas fa-code'));
	portfolio_display_order_field($post);
	echo '</div>';
}

function portfolio_experience_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_admin_panel_intro(__('Use the job title field above for the role, then enter the employer, dates, and responsibilities.', 'custom-theme'));
	echo '<div class="portfolio-admin-grid">';
	portfolio_meta_field($post, '_portfolio_company', __('Company / Organisation', 'custom-theme'), 'text', '', array('placeholder' => 'Company name'));
	portfolio_meta_field($post, '_portfolio_period', __('Employment Period', 'custom-theme'), 'text', '', array('placeholder' => 'August 2025 - Present'));
	portfolio_admin_field('portfolio_experience_responsibilities', __('Key Responsibilities', 'custom-theme'), portfolio_experience_responsibilities($post->post_content), 'textarea', __('Enter one responsibility per line. The website formats them as a list.', 'custom-theme'), array('class' => 'portfolio-admin-field--full', 'rows' => 8));
	portfolio_display_order_field($post);
	echo '</div>';
}

function portfolio_skill_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_admin_panel_intro(__('Use the title field above for the skill group, such as “Backend” or “Databases”.', 'custom-theme'));
	echo '<div class="portfolio-admin-grid">';
	portfolio_admin_field('portfolio_post_content', __('Skills / Tools', 'custom-theme'), $post->post_content, 'textarea', __('Separate related skills with commas.', 'custom-theme'), array('class' => 'portfolio-admin-field--full', 'placeholder' => 'FastAPI, Django, REST APIs, SQLAlchemy', 'rows' => 4));
	portfolio_display_order_field($post);
	echo '</div>';
}

function portfolio_education_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	portfolio_admin_panel_intro(__('Use the title field above for the qualification or degree.', 'custom-theme'));
	echo '<div class="portfolio-admin-grid">';
	portfolio_meta_field($post, '_portfolio_institution', __('Institution', 'custom-theme'), 'text', '', array('placeholder' => 'College or university name'));
	portfolio_meta_field($post, '_portfolio_period', __('Study Period', 'custom-theme'), 'text', '', array('placeholder' => '2023 - 2025'));
	portfolio_display_order_field($post);
	echo '</div>';
}

function portfolio_page_meta_box($post) {
	wp_nonce_field('portfolio_save_content_meta', 'portfolio_content_meta_nonce');
	$intro = sprintf(__('These fields control the %s section of the homepage.', 'custom-theme'), get_the_title($post));
	if (in_array($post->post_name, array('home', 'about'), true)) {
		$intro .= ' ' . __('Use the Featured Image panel to change this section image.', 'custom-theme');
	}
	portfolio_admin_panel_intro($intro);
	echo '<div class="portfolio-admin-grid">';
	portfolio_admin_field('portfolio_post_excerpt', __('Section Heading', 'custom-theme'), $post->post_excerpt, 'text', __('The large heading displayed for this section.', 'custom-theme'));
	if ('home' === $post->post_name) {
		portfolio_meta_field($post, '_portfolio_hero_name', __('Hero Name', 'custom-theme'), 'text', __('The name displayed in the opening section.', 'custom-theme'));
	}
	portfolio_admin_field('portfolio_post_content', __('Section Description', 'custom-theme'), $post->post_content, 'textarea', __('The supporting text displayed in this section.', 'custom-theme'), array('class' => 'portfolio-admin-field--full', 'rows' => 6));
	echo '</div>';
}

function portfolio_save_content_meta($post_id) {
	static $saving = false;

	if (
		$saving ||
		! isset($_POST['portfolio_content_meta_nonce']) ||
		! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['portfolio_content_meta_nonce'])), 'portfolio_save_content_meta') ||
		(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
		wp_is_post_revision($post_id) ||
		! current_user_can('edit_post', $post_id)
	) {
		return;
	}

	$post = get_post($post_id);
	if (! $post) {
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

	$post_update = array('ID' => $post_id);

	if (array_key_exists('portfolio_post_excerpt', $_POST)) {
		$post_update['post_excerpt'] = sanitize_textarea_field(wp_unslash($_POST['portfolio_post_excerpt']));
	}

	if (array_key_exists('portfolio_post_content', $_POST)) {
		$post_update['post_content'] = wp_kses_post(wp_unslash($_POST['portfolio_post_content']));
	}

	if (array_key_exists('portfolio_experience_responsibilities', $_POST)) {
		$lines = preg_split('/\r\n|\r|\n/', sanitize_textarea_field(wp_unslash($_POST['portfolio_experience_responsibilities'])));
		$lines = array_values(array_filter(array_map('trim', $lines)));
		$post_update['post_content'] = $lines ? '<ul><li>' . implode('</li><li>', array_map('esc_html', $lines)) . '</li></ul>' : '';
	}

	if (array_key_exists('portfolio_display_order', $_POST)) {
		$post_update['menu_order'] = max(0, (int) $_POST['portfolio_display_order']);
	}

	if (count($post_update) > 1) {
		$saving = true;
		wp_update_post(wp_slash($post_update));
		$saving = false;
	}
}
add_action('save_post', 'portfolio_save_content_meta');

function portfolio_title_placeholder($placeholder, $post) {
	$labels = array(
		'portfolio_project'    => __('Enter project name', 'custom-theme'),
		'portfolio_service'    => __('Enter service name', 'custom-theme'),
		'portfolio_experience' => __('Enter job title / role', 'custom-theme'),
		'portfolio_skill'      => __('Enter skill group name', 'custom-theme'),
		'portfolio_education'  => __('Enter qualification / degree', 'custom-theme'),
	);

	return isset($labels[$post->post_type]) ? $labels[$post->post_type] : $placeholder;
}
add_filter('enter_title_here', 'portfolio_title_placeholder', 10, 2);

/**
 * Make each back-office list show the information an administrator needs to
 * identify and compare entries without opening them one by one.
 */
function portfolio_admin_columns($post_type, $columns) {
	$checkbox = isset($columns['cb']) ? $columns['cb'] : '<input type="checkbox">';
	$date     = isset($columns['date']) ? $columns['date'] : __('Date', 'custom-theme');

	$column_sets = array(
		'portfolio_project' => array(
			'cb'               => $checkbox,
			'portfolio_image'  => __('Cover', 'custom-theme'),
			'title'            => __('Project', 'custom-theme'),
			'project_category' => __('Category', 'custom-theme'),
			'technologies'     => __('Technologies', 'custom-theme'),
			'focus'            => __('Focus', 'custom-theme'),
			'portfolio_order'  => __('Order', 'custom-theme'),
			'date'             => $date,
		),
		'portfolio_service' => array(
			'cb'                    => $checkbox,
			'title'                 => __('Service', 'custom-theme'),
			'portfolio_description' => __('Description', 'custom-theme'),
			'icon'                  => __('Icon', 'custom-theme'),
			'portfolio_order'       => __('Order', 'custom-theme'),
			'date'                  => $date,
		),
		'portfolio_experience' => array(
			'cb'                    => $checkbox,
			'title'                 => __('Role', 'custom-theme'),
			'company'               => __('Company', 'custom-theme'),
			'period'                => __('Period', 'custom-theme'),
			'portfolio_description' => __('Responsibilities', 'custom-theme'),
			'portfolio_order'       => __('Order', 'custom-theme'),
			'date'                  => $date,
		),
		'portfolio_skill' => array(
			'cb'                    => $checkbox,
			'title'                 => __('Skill Group', 'custom-theme'),
			'portfolio_description' => __('Skills / Tools', 'custom-theme'),
			'portfolio_order'       => __('Order', 'custom-theme'),
			'date'                  => $date,
		),
		'portfolio_education' => array(
			'cb'                  => $checkbox,
			'title'               => __('Qualification', 'custom-theme'),
			'institution'         => __('Institution', 'custom-theme'),
			'period'              => __('Period', 'custom-theme'),
			'portfolio_order'     => __('Order', 'custom-theme'),
			'date'                => $date,
		),
	);

	return isset($column_sets[$post_type]) ? $column_sets[$post_type] : $columns;
}

function portfolio_project_admin_columns($columns) {
	return portfolio_admin_columns('portfolio_project', $columns);
}

function portfolio_service_admin_columns($columns) {
	return portfolio_admin_columns('portfolio_service', $columns);
}

function portfolio_experience_admin_columns($columns) {
	return portfolio_admin_columns('portfolio_experience', $columns);
}

function portfolio_skill_admin_columns($columns) {
	return portfolio_admin_columns('portfolio_skill', $columns);
}

function portfolio_education_admin_columns($columns) {
	return portfolio_admin_columns('portfolio_education', $columns);
}

add_filter('manage_portfolio_project_posts_columns', 'portfolio_project_admin_columns');
add_filter('manage_portfolio_service_posts_columns', 'portfolio_service_admin_columns');
add_filter('manage_portfolio_experience_posts_columns', 'portfolio_experience_admin_columns');
add_filter('manage_portfolio_skill_posts_columns', 'portfolio_skill_admin_columns');
add_filter('manage_portfolio_education_posts_columns', 'portfolio_education_admin_columns');

function portfolio_admin_column_content($column, $post_id) {
	$post = get_post($post_id);

	if (! $post) {
		return;
	}

	switch ($column) {
		case 'portfolio_image':
			if (has_post_thumbnail($post_id)) {
				echo get_the_post_thumbnail($post_id, array(64, 48), array('class' => 'portfolio-admin-thumbnail'));
			} else {
				echo '<span class="portfolio-admin-empty">' . esc_html__('No image', 'custom-theme') . '</span>';
			}
			break;
		case 'project_category':
			$terms = get_the_terms($post_id, 'project_category');
			echo $terms && ! is_wp_error($terms) ? esc_html(implode(', ', wp_list_pluck($terms, 'name'))) : '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'technologies':
			echo esc_html(get_post_meta($post_id, '_portfolio_technologies', true)) ?: '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'focus':
			echo esc_html(get_post_meta($post_id, '_portfolio_focus', true)) ?: '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'icon':
			$icon = get_post_meta($post_id, '_portfolio_icon', true);
			echo $icon ? '<code>' . esc_html($icon) . '</code>' : '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'company':
			echo esc_html(get_post_meta($post_id, '_portfolio_company', true)) ?: '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'institution':
			echo esc_html(get_post_meta($post_id, '_portfolio_institution', true)) ?: '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'period':
			echo esc_html(get_post_meta($post_id, '_portfolio_period', true)) ?: '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'portfolio_description':
			$description = 'portfolio_service' === $post->post_type ? $post->post_excerpt : $post->post_content;
			$description = wp_trim_words(wp_strip_all_tags($description), 16, '&hellip;');
			echo $description ? esc_html($description) : '<span class="portfolio-admin-empty">&mdash;</span>';
			break;
		case 'portfolio_order':
			echo '<span class="portfolio-admin-order">' . esc_html((string) (int) $post->menu_order) . '</span>';
			break;
	}
}
add_action('manage_posts_custom_column', 'portfolio_admin_column_content', 10, 2);

function portfolio_sortable_admin_columns($columns) {
	$columns['portfolio_order'] = 'menu_order';
	return $columns;
}

foreach (array('portfolio_project', 'portfolio_service', 'portfolio_experience', 'portfolio_skill', 'portfolio_education') as $portfolio_post_type) {
	add_filter('manage_edit-' . $portfolio_post_type . '_sortable_columns', 'portfolio_sortable_admin_columns');
}

function portfolio_order_admin_lists($query) {
	if (! is_admin() || ! $query->is_main_query()) {
		return;
	}

	$post_type = $query->get('post_type');
	$types     = array('portfolio_project', 'portfolio_service', 'portfolio_experience', 'portfolio_skill', 'portfolio_education');

	if (in_array($post_type, $types, true) && ! $query->get('orderby')) {
		$query->set('orderby', array('menu_order' => 'ASC', 'date' => 'ASC'));
		$query->set('order', 'ASC');
	}
}
add_action('pre_get_posts', 'portfolio_order_admin_lists');

function portfolio_admin_assets($hook_suffix) {
	if (! in_array($hook_suffix, array('post.php', 'post-new.php', 'edit.php'), true)) {
		return;
	}

	$screen = get_current_screen();
	$types  = array('portfolio_project', 'portfolio_service', 'portfolio_experience', 'portfolio_skill', 'portfolio_education', 'page');

	if (! $screen || ! in_array($screen->post_type, $types, true)) {
		return;
	}

	$stylesheet = get_template_directory() . '/assets/css/admin.css';
	wp_enqueue_style(
		'portfolio-admin',
		get_template_directory_uri() . '/assets/css/admin.css',
		array(),
		file_exists($stylesheet) ? (string) filemtime($stylesheet) : null
	);
}
add_action('admin_enqueue_scripts', 'portfolio_admin_assets');

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
