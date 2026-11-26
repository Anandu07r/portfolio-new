<?php

if (! defined('ABSPATH')) {
	exit;
}

function portfolio_asset($path) {
	return get_template_directory_uri() . '/assets/' . ltrim($path, '/');
}

function portfolio_setting($name, $fallback = '') {
	return get_theme_mod($name, $fallback);
}

function portfolio_default_projects() {
	return array(
		array(
			'slug'         => 'vjv-ai-chatbot',
			'title'        => 'VJV AI Chatbot',
			'category'     => 'AI Application',
			'focus'        => 'Conversational experience',
			'technologies' => 'Python, FastAPI, Azure AI Foundry, Next.js',
			'description'  => 'An AI-powered chatbot project focused on delivering an interactive conversational experience.',
			'overview'     => 'VJV AI Chatbot combines a modern web interface with a Python and FastAPI backend to support an interactive AI conversation flow. Azure AI Foundry provides the AI integration, while Next.js powers the user-facing experience.',
			'highlights'   => array(
				'Interactive chatbot experience for natural user conversations.',
				'Python and FastAPI backend for application logic and API handling.',
				'Azure AI Foundry integration for AI-powered responses.',
				'Next.js interface for a responsive web experience.',
			),
			'image'        => 'img/portfolio/1.jpg',
		),
		array(
			'slug'         => 'rwave-billing-inventory-management',
			'title'        => 'Rwave Billing & Inventory Management',
			'category'     => 'Business Application',
			'focus'        => 'Billing and inventory workflows',
			'technologies' => 'Python, FastAPI, PostgreSQL',
			'description'  => 'A billing and inventory management application designed to support business operations and data management.',
			'overview'     => 'Rwave brings billing and inventory tasks into one application. The project uses FastAPI for backend services and PostgreSQL for structured, reliable business data management.',
			'highlights'   => array(
				'Billing workflows organized for day-to-day business operations.',
				'Inventory information managed from a centralized system.',
				'FastAPI services for maintainable backend functionality.',
				'PostgreSQL storage for structured operational data.',
			),
			'image'        => 'img/portfolio/2.jpg',
		),
		array(
			'slug'         => 'oximio-ai-competitor-analysis',
			'title'        => 'Oximio AI Competitor Analysis',
			'category'     => 'AI Research Tool',
			'focus'        => 'AI-assisted competitor research',
			'technologies' => 'Python, FastAPI, Google Gemini API',
			'description'  => 'An AI-powered competitor analysis project using LLM integration to assist with research and analysis.',
			'overview'     => 'Oximio applies large language model capabilities to competitor research and analysis. A Python and FastAPI backend coordinates the application workflow and integrates with the Google Gemini API.',
			'highlights'   => array(
				'AI-assisted workflow for competitor research and analysis.',
				'Google Gemini API integration for language-model capabilities.',
				'Python-based processing and application logic.',
				'FastAPI endpoints for a clean backend structure.',
			),
			'image'        => 'img/portfolio/3.jpg',
		),
		array(
			'slug'         => 'web-design-digital-experiences',
			'title'        => 'Web Design & Digital Experiences',
			'category'     => 'Web Design',
			'focus'        => 'Responsive digital experiences',
			'technologies' => 'HTML5, CSS3, JavaScript, Bootstrap',
			'description'  => 'Modern, responsive website designs and user-focused web experiences.',
			'overview'     => 'This work focuses on clear, responsive interfaces that adapt across devices. Each experience balances visual presentation with accessible structure and practical front-end implementation.',
			'highlights'   => array(
				'Responsive layouts for desktop, tablet, and mobile screens.',
				'User-focused page structure and visual hierarchy.',
				'Modern front-end implementation with reusable components.',
				'Accessible, readable interfaces for everyday use.',
			),
			'image'        => 'img/portfolio/4.jpg',
		),
		array(
			'slug'         => 'social-media-creative-content',
			'title'        => 'Social Media & Creative Content',
			'category'     => 'Digital Creative',
			'focus'        => 'Content and brand communication',
			'technologies' => 'Content Planning, Social Media, Digital Marketing',
			'description'  => 'Digital content creation and social media management.',
			'overview'     => 'This creative work brings together content planning, visual production, and social media management to communicate ideas clearly across digital platforms.',
			'highlights'   => array(
				'Content planning aligned with the intended audience.',
				'Platform-ready creative content for social channels.',
				'Consistent visual presentation across campaign assets.',
				'Digital marketing support for online communication.',
			),
			'image'        => 'img/portfolio/5.jpg',
		),
		array(
			'slug'         => 'photography-videography',
			'title'        => 'Photography & Videography',
			'category'     => 'Visual Media',
			'focus'        => 'Photography and visual storytelling',
			'technologies' => 'Photography, Videography, Visual Storytelling',
			'description'  => 'Creative photography, visual storytelling, and video content.',
			'overview'     => 'Photography and videography projects centered on visual storytelling. The work combines composition, image-making, and video content to create engaging media for digital use.',
			'highlights'   => array(
				'Photography composed for clear visual communication.',
				'Video content developed for digital platforms.',
				'Visual storytelling shaped around the subject and audience.',
				'Creative media prepared for web and social use.',
			),
			'image'        => 'img/portfolio/12.jpg',
		),
	);
}

require_once get_template_directory() . '/inc/content-management.php';
require_once get_template_directory() . '/inc/mail-settings.php';

function portfolio_setup() {
	load_theme_textdomain('custom-theme', get_template_directory() . '/languages');

	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 220,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support('responsive-embeds');
	add_theme_support('automatic-feed-links');
	add_post_type_support('page', 'excerpt');

	register_nav_menus(
		array(
			'primary' => __('Main menu', 'custom-theme'),
		)
	);

	add_image_size('portfolio-project', 800, 650, true);
}
add_action('after_setup_theme', 'portfolio_setup');

function portfolio_enqueue() {
	$base    = get_template_directory_uri() . '/assets/';
	$version = wp_get_theme()->get('Version');
	$css     = array(
		'bootstrap.min',
		'font-awesome.min',
		'flaticon-set',
		'swiper-bundle.min',
		'animate.min',
		'validnavs',
		'helper',
		'unit-test',
		'style',
	);

	foreach ($css as $name) {
		wp_enqueue_style(
			'portfolio-' . $name,
			$base . 'css/' . $name . '.css',
			array(),
			$version
		);
	}

	wp_enqueue_style(
		'portfolio-theme',
		get_stylesheet_uri(),
		array('portfolio-style'),
		$version
	);

	$js = array(
		'bootstrap.bundle.min',
		'jquery.appear',
		'jquery.easing.min',
		'swiper-bundle.min',
		'progress-bar.min',
		'circle-progress',
		'isotope.pkgd.min',
		'imagesloaded.pkgd.min',
		'count-to',
		'jquery.scrolla.min',
		'ScrollOnReveal',
		'YTPlayer.min',
		'validnavs',
		'main',
	);

	foreach ($js as $name) {
		wp_enqueue_script(
			'portfolio-' . $name,
			$base . 'js/' . $name . '.js',
			array('jquery'),
			$version,
			true
		);
	}

	wp_enqueue_script(
		'portfolio-theme-scripts',
		get_template_directory_uri() . '/js/theme.js',
		array('jquery'),
		$version,
		true
	);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'portfolio_enqueue');

function portfolio_customizer($wp_customize) {
	$wp_customize->add_panel(
		'portfolio_settings',
		array(
			'title'    => __('Portfolio Theme Settings', 'custom-theme'),
			'priority' => 30,
		)
	);

	$sections = array(
		'hero'    => array(
			'Hero',
			array(
				'hero_name'  => array('ANANDHU R', 'text'),
				'hero_role'  => array(
					'Software Engineer | Web Developer | Digital Creative',
					'text',
				),
				'hero_intro' => array(
					'I build reliable web applications, scalable backend systems, AI-powered solutions, and engaging digital experiences.',
					'textarea',
				),
				'hero_image' => array('', 'image'),
			),
		),
		'about'   => array(
			'About',
			array(
				'about_title' => array(
					'Building Technology & Creating Digital Experiences',
					'text',
				),
				'about_text'  => array(
					'I am a software engineer passionate about developing practical software solutions and modern digital experiences. My interests include full-stack development, backend engineering, AI-powered applications, and creative digital media.' . "\n\n" .
					'Alongside software development, I work with social media management, digital marketing, photography, and videography.',
					'textarea',
				),
				'about_image' => array('', 'image'),
				'qualification' => array('MCA', 'text'),
				'cv_url'      => array('', 'url'),
			),
		),
		'contact' => array(
			'Contact',
			array(
				'contact_email'    => array('ananduayoor144@gmail.com', 'email'),
				'contact_phone'    => array('', 'text'),
				'contact_location' => array('Kollam, Kerala, India', 'textarea'),
				'social_github'    => array('', 'url'),
				'social_linkedin'  => array('', 'url'),
				'social_instagram' => array('', 'url'),
				'contact_recipient' => array('ananduayoor144@gmail.com', 'email'),
			),
		),
	);

	foreach ($sections as $section => $value) {
		$wp_customize->add_section(
			'portfolio_' . $section,
			array(
				'title' => __($value[0], 'custom-theme'),
				'panel' => 'portfolio_settings',
			)
		);

		foreach ($value[1] as $name => $details) {
			$type = $details[1];

			if ('email' === $type) {
				$sanitize = 'sanitize_email';
			} elseif ('url' === $type || 'image' === $type) {
				$sanitize = 'esc_url_raw';
			} elseif ('textarea' === $type) {
				$sanitize = 'sanitize_textarea_field';
			} else {
				$sanitize = 'sanitize_text_field';
			}

			$wp_customize->add_setting(
				$name,
				array(
					'default'           => $details[0],
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);

			$wp_customize->add_control(
				$name,
				array(
					'label'   => ucwords(str_replace('_', ' ', $name)),
					'section' => 'portfolio_' . $section,
					'type'    => 'image' === $type ? 'url' : $type,
				)
			);
		}
	}
}
add_action('customize_register', 'portfolio_customizer');

function portfolio_nav_fallback() {
	$menu_items = array(
		'home'      => 'Home',
		'about'     => 'About',
		'services'  => 'Services',
		'experience' => 'Experience',
		'skills'    => 'Skills',
		'education' => 'Education',
		'portfolio' => 'Portfolio',
		'contact'   => 'Contact',
	);

	echo '<ul class="nav navbar-nav navbar-right" data-in="fadeInDown" data-out="fadeOutUp">';

	foreach ($menu_items as $id => $label) {
		echo '<li><a class="smooth-menu" href="' .
			esc_url(home_url('/#' . $id)) .
			'">' .
			esc_html($label) .
			'</a></li>';
	}

	echo '</ul>';
}

function portfolio_form_process() {
	if (
		! isset($_POST['portfolio_nonce']) ||
		! wp_verify_nonce(
			sanitize_text_field(wp_unslash($_POST['portfolio_nonce'])),
			'portfolio_contact'
		)
	) {
		wp_die(
			esc_html__('Security check failed.', 'custom-theme'),
			403
		);
	}

	$redirect = home_url('/#contact');
	$url      = add_query_arg('contact', 'error', $redirect);

	if (! empty($_POST['website'])) {
		wp_safe_redirect(add_query_arg('contact', 'sent', $redirect));
	} else {
		$name    = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
		$email   = sanitize_email(wp_unslash($_POST['email'] ?? ''));
		$phone   = sanitize_text_field(wp_unslash($_POST['phone'] ?? ''));
		$message = sanitize_textarea_field(wp_unslash($_POST['comments'] ?? ''));

		if ('' !== $name && is_email($email) && '' !== $message) {
			$recipient = sanitize_email(
				portfolio_setting('contact_recipient', get_option('admin_email'))
			);

			if (! is_email($recipient)) {
				$recipient = get_option('admin_email');
			}

			$body = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";
			$sent = wp_mail(
				$recipient,
				sprintf(__('Portfolio enquiry from %s', 'custom-theme'), $name),
				$body,
				array('Reply-To: ' . $email)
			);
			$url  = add_query_arg(
				'contact',
				$sent ? 'sent' : 'error',
				$redirect
			);
		}

		wp_safe_redirect($url);
	}

	exit;
}
add_action('admin_post_nopriv_portfolio_contact', 'portfolio_form_process');
add_action('admin_post_portfolio_contact', 'portfolio_form_process');
