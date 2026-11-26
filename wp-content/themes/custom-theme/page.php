<?php get_header(); ?>

<main id="main-content" class="portfolio-inner">
	<div class="container">
		<div class="row">
			<div class="col-lg-9">
				<?php while (have_posts()) : ?>
					<?php the_post(); ?>
					<article <?php post_class(); ?>>
						<h1><?php the_title(); ?></h1>
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
