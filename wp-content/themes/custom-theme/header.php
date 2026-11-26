<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="screen-reader-text" href="#main-content">
	<?php esc_html_e('Skip to content', 'custom-theme'); ?>
</a>

<header class="site-header">
	<nav
		class="navbar mobile-sidenav onepage-menu attr-border navbar-sticky navbar-default validnavs navbar-fixed dark no-background"
		aria-label="<?php esc_attr_e('Primary navigation', 'custom-theme'); ?>"
	>
		<div class="container d-flex justify-content-between align-items-center">
			<div class="navbar-header">
				<button
					class="navbar-toggle"
					type="button"
					data-toggle="collapse"
					data-target="#navbar-menu"
					aria-label="<?php esc_attr_e('Toggle menu', 'custom-theme'); ?>"
				>
					<i class="fa fa-bars"></i>
				</button>

				<a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
					<?php if (has_custom_logo()) : ?>
						<?php
						$logo_id = get_theme_mod('custom_logo');
						echo wp_get_attachment_image(
							$logo_id,
							'full',
							false,
							array(
								'class' => 'logo',
								'alt'   => get_bloginfo('name'),
							)
						);
						?>
					<?php else : ?>
						<span class="portfolio-site-title">ANANDHU R</span>
					<?php endif; ?>
				</a>
			</div>

			<div class="collapse navbar-collapse" id="navbar-menu">
				<button
					class="navbar-toggle"
					type="button"
					data-toggle="collapse"
					data-target="#navbar-menu"
					aria-label="<?php esc_attr_e('Close menu', 'custom-theme'); ?>"
				>
					<i class="fa fa-times"></i>
				</button>

				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'nav navbar-nav navbar-right',
						'fallback_cb'    => 'portfolio_nav_fallback',
						'depth'          => 1,
					)
				);
				?>
			</div>
		</div>

		<div class="overlay-screen"></div>
	</nav>
</header>
