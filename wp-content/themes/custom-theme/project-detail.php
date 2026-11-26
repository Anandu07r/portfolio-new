<?php
/**
 * Detail page for the featured portfolio projects.
 */

$project  = portfolio_project_to_array(get_queried_object());
$projects = portfolio_get_projects();

if (! $project) {
	status_header(404);
	get_template_part('404');
	return;
}

$project_slugs = wp_list_pluck($projects, 'slug');
$project_index = array_search($project['slug'], $project_slugs, true);
$previous      = $project_index > 0 ? $projects[$project_index - 1] : null;
$next          = $project_index < count($projects) - 1 ? $projects[$project_index + 1] : null;
$technologies  = array_filter(array_map('trim', explode(',', $project['technologies'])));

get_header();
?>

<main id="main-content" class="portfolio-project-detail">
	<section class="portfolio-detail-hero">
		<div class="container">
			<a class="portfolio-detail-back" href="<?php echo esc_url(home_url('/#portfolio')); ?>">
				Back to Selected Projects
			</a>

			<div class="portfolio-detail-heading">
				<p class="portfolio-eyebrow"><?php echo esc_html($project['category']); ?></p>
				<h1><?php echo esc_html($project['title']); ?></h1>
				<p class="portfolio-detail-intro"><?php echo esc_html($project['description']); ?></p>
			</div>

			<?php if ($technologies) : ?>
				<ul class="portfolio-detail-tech" aria-label="Project technologies">
					<?php foreach ($technologies as $technology) : ?>
						<li><?php echo esc_html($technology); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>

	<section class="portfolio-detail-showcase">
		<div class="container">
			<figure class="portfolio-detail-image">
				<img
					src="<?php echo esc_url($project['image_url']); ?>"
					alt="<?php echo esc_attr($project['title']); ?>"
				>
			</figure>
		</div>
	</section>

	<section class="portfolio-detail-content default-padding">
		<div class="container">
			<div class="row portfolio-detail-overview">
				<div class="col-lg-8">
					<article class="portfolio-detail-copy">
						<p class="portfolio-detail-label">Project Overview</p>
						<h2>About the Project</h2>
						<p><?php echo esc_html($project['overview']); ?></p>
					</article>
				</div>

				<div class="col-lg-4">
					<aside class="portfolio-detail-facts" aria-label="Project information">
						<h2>Project Details</h2>
						<dl>
							<div>
								<dt>Category</dt>
								<dd><?php echo esc_html($project['category']); ?></dd>
							</div>
							<div>
								<dt>Focus</dt>
								<dd><?php echo esc_html($project['focus']); ?></dd>
							</div>
							<div>
								<dt>Technology</dt>
								<dd><?php echo esc_html($project['technologies']); ?></dd>
							</div>
						</dl>
					</aside>
				</div>
			</div>

			<div class="portfolio-detail-highlights">
				<div class="portfolio-detail-section-heading">
					<p class="portfolio-detail-label">Project Highlights</p>
					<h2>What This Project Includes</h2>
				</div>

				<ul class="portfolio-detail-highlight-grid">
					<?php foreach ($project['highlights'] as $highlight) : ?>
						<li>
							<i class="fas fa-check" aria-hidden="true"></i>
							<span><?php echo esc_html($highlight); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<section class="portfolio-detail-next">
		<div class="container">
			<div class="portfolio-detail-navigation">
				<?php if ($previous) : ?>
					<a href="<?php echo esc_url(portfolio_get_project_url($previous)); ?>">
						<span>Previous Project</span>
						<strong><?php echo esc_html($previous['title']); ?></strong>
					</a>
				<?php else : ?>
					<span aria-hidden="true"></span>
				<?php endif; ?>

				<?php if ($next) : ?>
					<a class="portfolio-detail-navigation-next" href="<?php echo esc_url(portfolio_get_project_url($next)); ?>">
						<span>Next Project</span>
						<strong><?php echo esc_html($next['title']); ?></strong>
					</a>
				<?php endif; ?>
			</div>

			<div class="portfolio-detail-contact">
				<div>
					<p class="portfolio-detail-label">Have a Project in Mind?</p>
					<h2>Let's build something useful together.</h2>
				</div>
				<a class="btn-standard" href="<?php echo esc_url(home_url('/#contact')); ?>">Let's Connect</a>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
