<?php
/* Legacy education values are retained only as migration reference.
$portfolio_education = array(
	array(
		'qualification' => 'Master of Computer Applications (MCA)',
		'institution'   => 'Mar Thoma Institute of Information Technology',
		'period'        => '2023–2025',
	),
	array(
		'qualification' => 'Bachelor of Science in Computer Science',
		'institution'   => 'PMSA PTM Arts and Science College',
		'period'        => '2019–2022',
	),
	array(
		'qualification' => 'Higher Secondary Education',
		'institution'   => 'GHSS Thevannoor',
		'period'        => '2017–2019',
	),
);

*/
$portfolio_projects = portfolio_get_projects();

// All section collections are loaded from WordPress back-office entries.
$portfolio_services   = portfolio_get_managed_posts('portfolio_service');
$portfolio_experience = portfolio_get_managed_posts('portfolio_experience');
$portfolio_skills     = portfolio_get_managed_posts('portfolio_skill');
$portfolio_education  = portfolio_get_managed_posts('portfolio_education');

$home_page  = portfolio_get_section_page('home');
$about_page = portfolio_get_section_page('about');

$hero_name = $home_page ? get_post_meta($home_page->ID, '_portfolio_hero_name', true) : '';
$hero_name = $hero_name ?: portfolio_setting('hero_name', 'ANANDHU R');
$hero_role = portfolio_get_section_heading('home', portfolio_setting('hero_role', 'Software Engineer | Web Developer | Digital Creative'));
$hero_intro = portfolio_get_section_content('home', portfolio_setting('hero_intro', 'I build reliable web applications, scalable backend systems, AI-powered solutions, and engaging digital experiences.'));
$hero_image = $home_page ? get_the_post_thumbnail_url($home_page, 'full') : '';
$hero_image = $hero_image ?: (portfolio_setting('hero_image') ?: portfolio_asset('img/illustration/1.png'));

$about_title = portfolio_get_section_heading('about', portfolio_setting('about_title', 'Building Technology & Creating Digital Experiences'));
$about_text  = portfolio_get_section_content('about', portfolio_setting('about_text', 'I am a software engineer passionate about developing practical software solutions and modern digital experiences.'));
$about_image = $about_page ? get_the_post_thumbnail_url($about_page, 'full') : '';
$about_image = $about_image ?: (portfolio_setting('about_image') ?: portfolio_asset('img/illustration/2.jpg'));

$portfolio_email    = portfolio_setting('contact_email', 'ananduayoor144@gmail.com');
$portfolio_location = portfolio_setting('contact_location', 'Kollam, Kerala, India');

get_header();
?>

<main id="main-content" class="main-wrapper">
	<section id="home" class="banner-style-one-area default-padding portfolio-hero">
		<div class="container">
			<div class="banner-top">
				<div class="row align-center">
					<div class="col-lg-7 pr-50 pr-md-15 pr-xs-15">
						<div class="information">
							<p class="portfolio-eyebrow">Hello, I am</p>
							<h1 class="portfolio-hero-title">
								<?php echo esc_html($hero_name); ?>
							</h1>
							<p class="portfolio-hero-role">
								<?php echo esc_html($hero_role); ?>
							</p>
							<p class="portfolio-hero-intro">
								<?php echo esc_html(wp_strip_all_tags($hero_intro)); ?>
							</p>
							<div class="portfolio-hero-actions">
								<a class="btn-standard smooth-menu" href="#portfolio">
									View My Portfolio
								</a>
								<a class="btn-standard portfolio-button-secondary smooth-menu" href="#contact">
									Let's Connect
								</a>
							</div>
						</div>
					</div>

					<div class="col-lg-5">
						<div class="thumb">
							<img
								src="<?php echo esc_url($hero_image); ?>"
								alt="<?php echo esc_attr($hero_name); ?>"
							>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="about" class="about-style-one-area default-padding">
		<div class="container">
			<div class="row align-center">
				<div class="col-lg-5">
					<div class="about-style-one-thumb">
						<img
							src="<?php echo esc_url($about_image); ?>"
							alt="Anandhu R"
						>
					</div>
				</div>

				<div class="col-lg-6 offset-lg-1 mt-50">
					<div class="about-style-one-info">
						<h3 class="sub-title">About Me</h3>
						<h2 class="title">
							<?php echo esc_html($about_title); ?>
						</h2>
						<div class="portfolio-about-copy">
							<?php echo wp_kses_post(wpautop($about_text)); ?>
						</div>

						<div class="about-item">
							<h3><i class="fas fa-user"></i> Personal Details</h3>
							<ul class="biography-table">
								<li>
									<h5>Name</h5>
									<p>Anandhu R</p>
								</li>
								<li>
									<h5>Qualification</h5>
									<p><?php echo esc_html(portfolio_setting('qualification', 'MCA')); ?></p>
								</li>
								<li>
									<h5>Location</h5>
									<p><?php echo esc_html($portfolio_location); ?></p>
								</li>
								<li>
									<h5>Email</h5>
									<p>
										<a href="mailto:<?php echo esc_attr($portfolio_email); ?>">
											<?php echo esc_html($portfolio_email); ?>
										</a>
									</p>
								</li>
							</ul>
						</div>

						<?php if (portfolio_setting('cv_url')) : ?>
							<a
								class="btn-standard mt-10"
								href="<?php echo esc_url(portfolio_setting('cv_url')); ?>"
								target="_blank"
								rel="noopener noreferrer"
							>
								Download CV
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="services" class="services-style-one-area default-padding portfolio-muted-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="left-heading mb-50">
						<h4 class="sub-title">Services</h4>
						<h2 class="title"><?php echo esc_html(portfolio_get_section_heading('services', 'What I Can Help You Build')); ?></h2>
						<div class="devider"></div>
					</div>
				</div>
			</div>

			<div class="row">
				<?php foreach ($portfolio_services as $service) : ?>
					<div class="service-style-one col-lg-6 col-md-6">
						<div class="service-style-one-item portfolio-service-card">
							<div class="thumb">
								<i class="<?php echo esc_attr(get_post_meta($service->ID, '_portfolio_icon', true) ?: 'fas fa-code'); ?>" aria-hidden="true"></i>
							</div>
							<div class="info">
								<h4><?php echo esc_html(get_the_title($service)); ?></h4>
								<p><?php echo esc_html(has_excerpt($service) ? get_the_excerpt($service) : wp_trim_words(wp_strip_all_tags($service->post_content), 24)); ?></p>
								<div class="bottom">
									<span>Service</span>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section id="experience" class="default-padding portfolio-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="left-heading mb-50">
						<h4 class="sub-title">Experience</h4>
						<h2 class="title"><?php echo esc_html(portfolio_get_section_heading('experience', 'Professional Journey')); ?></h2>
						<div class="devider"></div>
					</div>
				</div>
			</div>

			<?php foreach ($portfolio_experience as $experience) : ?>
				<article class="portfolio-experience-card">
					<div class="portfolio-experience-heading">
						<div>
							<h3><?php echo esc_html(get_the_title($experience)); ?></h3>
							<p><?php echo esc_html(get_post_meta($experience->ID, '_portfolio_company', true)); ?></p>
						</div>
						<span><?php echo esc_html(get_post_meta($experience->ID, '_portfolio_period', true)); ?></span>
					</div>
					<div class="portfolio-experience-content">
						<?php echo wp_kses_post(apply_filters('the_content', $experience->post_content)); ?>
					</div>
				</article>
			<?php endforeach; ?>

			<?php if (false) : // Legacy fallback retained only for migration reference. ?>
			<article class="portfolio-experience-card">
				<div class="portfolio-experience-heading">
					<div>
						<h3>Python AI Engineer</h3>
						<p>Riverswave Technologies Pvt. Ltd.</p>
					</div>
					<span>August 2025 – Present</span>
				</div>
				<ul>
					<?php foreach ($portfolio_experience as $responsibility) : ?>
						<li><?php echo esc_html($responsibility); ?></li>
					<?php endforeach; ?>
				</ul>
			</article>
			<?php endif; ?>
		</div>
	</section>

	<section id="skills" class="default-padding portfolio-muted-section portfolio-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="left-heading mb-50">
						<h4 class="sub-title">Skills</h4>
						<h2 class="title"><?php echo esc_html(portfolio_get_section_heading('skills', 'Technical & Creative Toolkit')); ?></h2>
						<div class="devider"></div>
					</div>
				</div>
			</div>

			<div class="portfolio-skills-grid">
				<?php foreach ($portfolio_skills as $skill) : ?>
					<article class="portfolio-skill-card">
						<h3><?php echo esc_html(get_the_title($skill)); ?></h3>
						<p><?php echo esc_html(wp_strip_all_tags($skill->post_content)); ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section id="education" class="default-padding portfolio-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="left-heading mb-50">
						<h4 class="sub-title">Education</h4>
						<h2 class="title"><?php echo esc_html(portfolio_get_section_heading('education', 'Academic Background')); ?></h2>
						<div class="devider"></div>
					</div>
				</div>
			</div>

			<div class="portfolio-education-list">
				<?php foreach ($portfolio_education as $education) : ?>
					<article class="portfolio-education-card">
						<span><?php echo esc_html(get_post_meta($education->ID, '_portfolio_period', true)); ?></span>
						<div>
							<h3><?php echo esc_html(get_the_title($education)); ?></h3>
							<p><?php echo esc_html(get_post_meta($education->ID, '_portfolio_institution', true)); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section id="portfolio" class="project-style-one-area default-padding portfolio-muted-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="heading-left mb-50">
						<h4 class="sub-title">Portfolio</h4>
						<h2 class="title"><?php echo esc_html(portfolio_get_section_heading('portfolio', 'Selected Projects')); ?></h2>
						<div class="devider"></div>
					</div>
				</div>
			</div>

			<div class="row">
				<?php foreach ($portfolio_projects as $project) : ?>
					<div class="col-lg-6 item-center">
						<article class="portfolio-style-one portfolio-project-card">
							<a
								class="portfolio-project-link"
								href="<?php echo esc_url(portfolio_get_project_url($project)); ?>"
								aria-label="<?php echo esc_attr('View details for ' . $project['title']); ?>"
							>
								<div class="thumb-zoom">
									<img
										src="<?php echo esc_url($project['image_url']); ?>"
										alt="<?php echo esc_attr($project['title']); ?>"
									>
								</div>
								<div class="portfolio-project-copy">
									<?php if ($project['technologies']) : ?>
										<p class="portfolio-project-tech">
											<?php echo esc_html($project['technologies']); ?>
										</p>
									<?php endif; ?>
									<h3><?php echo esc_html($project['title']); ?></h3>
									<p><?php echo esc_html($project['description']); ?></p>
									<span class="portfolio-project-cta">View Project</span>
								</div>
							</a>
						</article>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section
		id="contact"
		class="contact-area contact-page overflow-hidden default-padding"
		style="background-image: url('<?php echo esc_url(portfolio_asset('img/shape/map-light.png')); ?>');"
	>
		<div class="container">
			<div class="row align-center">
				<div class="col-tact-stye-one col-lg-5 pr-50 pr-md-15 pr-xs-15">
					<div class="contact-style-one-info">
						<div class="top-info">
							<h2 class="gradient-text"><?php echo esc_html(portfolio_get_section_heading('contact', "Let's Build Something Great Together!")); ?></h2>
							<p><?php echo esc_html(wp_strip_all_tags(portfolio_get_section_content('contact', "Have a project or an idea? Let's connect and make it happen."))); ?></p>
						</div>

						<ul class="contact-address">
							<li>
								<div class="info">
									<h4>Email</h4>
									<a href="mailto:<?php echo esc_attr($portfolio_email); ?>">
										<?php echo esc_html($portfolio_email); ?>
									</a>
								</div>
							</li>
							<li>
								<div class="info">
									<h4>Location</h4>
									<p><?php echo esc_html($portfolio_location); ?></p>
								</div>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-tact-stye-one col-lg-7 pl-60 pl-md-15 pl-xs-15 mt-md-50 mt-xs-50">
					<div class="contact-form-style-one">
						<?php
						$result = isset($_GET['contact'])
							? sanitize_key(wp_unslash($_GET['contact']))
							: '';
						?>

						<?php if ('sent' === $result) : ?>
							<p class="portfolio-notice" role="status">
								Your message was sent successfully.
							</p>
						<?php elseif ('error' === $result) : ?>
							<p class="portfolio-notice error" role="alert">
								Unable to send your message. Please check the fields and try again.
							</p>
						<?php endif; ?>

						<form
							action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
							class="contact-form mt-30"
							method="post"
						>
							<input type="hidden" name="action" value="portfolio_contact">
							<?php wp_nonce_field('portfolio_contact', 'portfolio_nonce'); ?>

							<div class="portfolio-hp">
								<label for="website">Leave this empty</label>
								<input
									id="website"
									name="website"
									tabindex="-1"
									autocomplete="off"
								>
							</div>

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group">
										<input
											class="form-control"
											id="name"
											name="name"
											placeholder="Name *"
											type="text"
											required
											maxlength="120"
										>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-6">
									<div class="form-group">
										<input
											class="form-control"
											id="email"
											name="email"
											placeholder="Email *"
											type="email"
											required
											maxlength="180"
										>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<input
											class="form-control"
											id="phone"
											name="phone"
											placeholder="Phone"
											type="tel"
											maxlength="40"
										>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12">
									<div class="form-group comments">
										<textarea
											class="form-control"
											id="comments"
											name="comments"
											placeholder="Tell me about your project *"
											required
											maxlength="5000"
										></textarea>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12">
									<button name="submit" type="submit">
										<i class="fa fa-paper-plane"></i>
										Get in Touch
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
