(function ($) {
	$(function () {
		$(document).on('submit', '.contact-form', function () {
			var $button = $(this).find('button[type="submit"]');
			$button.prop('disabled', true);
		});
	});
})(jQuery);
