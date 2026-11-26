<?php get_header(); ?>

<main id="main-content" class="portfolio-inner">
	<div class="container">
		<div class="row">
			<div class="col-lg-9">
				<?php while (have_posts()) : ?>
					<?php the_post(); ?>
					<article <?php post_class(); ?>>
						<h1><?php the_title(); ?></h1>

						<?php if ('portfolio_project' === get_post_type()) : ?>
							<p class="portfolio-project-meta">
								<?php echo wp_kses_post(get_the_term_list(get_the_ID(), 'project_category', '', ' · ')); ?>
							</p>
						<?php else : ?>
							<p><?php echo esc_html(get_the_date()); ?></p>
						<?php endif; ?>

						<?php if (has_post_thumbnail()) : ?>
							<div class="entry-featured-image">
								<?php the_post_thumbnail('large'); ?>
							</div>
						<?php endif; ?>

						<div class="entry-content">
							<?php
							the_content();
							wp_link_pages();
							?>
						</div>
					</article>

					<?php if (comments_open() || get_comments_number()) : ?>
						<?php comments_template(); ?>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>
