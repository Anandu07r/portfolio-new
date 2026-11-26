<?php get_header(); ?>

<main id="main-content" class="portfolio-inner">
	<div class="container">
		<h1>
			<?php echo esc_html(is_home() ? __('Blog', 'custom-theme') : get_the_archive_title()); ?>
		</h1>

		<div class="row">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : ?>
					<?php the_post(); ?>
					<article <?php post_class('col-lg-6 portfolio-content-card'); ?>>
						<a href="<?php the_permalink(); ?>">
							<?php if (has_post_thumbnail()) : ?>
								<?php the_post_thumbnail('medium_large'); ?>
							<?php endif; ?>
						</a>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p><?php echo esc_html(get_the_date()); ?></p>
						<p><?php echo esc_html(get_the_excerpt()); ?></p>
						<a href="<?php the_permalink(); ?>">Read More →</a>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e('No content has been published yet.', 'custom-theme'); ?></p>
			<?php endif; ?>
		</div>

		<div class="portfolio-pagination">
			<?php the_posts_pagination(); ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
