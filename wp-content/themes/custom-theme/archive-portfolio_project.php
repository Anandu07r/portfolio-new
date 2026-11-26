<?php
/**
 * Project archive template.
 */

get_header();
?>

<main id="main-content" class="portfolio-inner">
	<div class="container">
		<h1>Projects</h1>
		<div class="row">
			<?php while (have_posts()) : ?>
				<?php the_post(); ?>
				<article class="col-lg-6 item-center">
					<div class="portfolio-style-one">
						<div class="thumb-zoom">
							<a href="<?php the_permalink(); ?>">
								<?php if (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('portfolio-project'); ?>
								<?php endif; ?>
							</a>
						</div>
						<div class="pf-item-info">
							<div class="content-info">
								<h2>
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h2>
							</div>
						</div>
					</div>
				</article>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination(); ?>
	</div>
</main>

<?php get_footer(); ?>
