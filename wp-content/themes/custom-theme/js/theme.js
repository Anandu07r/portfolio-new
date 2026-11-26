(function ($) {
	$(function () {
		var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var revealSelectors = [
			'.about-style-one-area .col-lg-5',
			'.about-style-one-info',
			'.left-heading',
			'.heading-left',
			'.service-style-one',
			'.portfolio-experience-card',
			'.portfolio-skill-card',
			'.portfolio-education-card',
			'.portfolio-project-card',
			'.contact-style-one-info',
			'.contact-form-style-one'
		];
		var revealElements = Array.prototype.slice.call(
			document.querySelectorAll(revealSelectors.join(','))
		);

		revealElements.forEach(function (element, index) {
			element.classList.add('portfolio-reveal');
			element.style.setProperty('--portfolio-reveal-delay', ((index % 4) * 80) + 'ms');

			if (element.matches('.about-style-one-area .col-lg-5, .contact-style-one-info')) {
				element.classList.add('portfolio-reveal--left');
			}

			if (element.matches('.about-style-one-info, .contact-form-style-one')) {
				element.classList.add('portfolio-reveal--right');
			}
		});

		document.documentElement.classList.add('portfolio-motion-ready');

		if (prefersReducedMotion || ! ('IntersectionObserver' in window)) {
			revealElements.forEach(function (element) {
				element.classList.add('is-visible');
			});
		} else {
			var revealObserver = new IntersectionObserver(function (entries, observer) {
				entries.forEach(function (entry) {
					if (! entry.isIntersecting) {
						return;
					}

					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				});
			}, {
				rootMargin: '0px 0px -8% 0px',
				threshold: 0.12
			});

			revealElements.forEach(function (element) {
				revealObserver.observe(element);
			});
		}

		$(document).on('submit', '.contact-form', function () {
			var $button = $(this).find('button[type="submit"]');
			$button.prop('disabled', true);
		});
	});
})(jQuery);
