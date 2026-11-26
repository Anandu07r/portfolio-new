<?php
/**
 * SMTP configuration and delivery testing for the portfolio contact form.
 */

if (! defined('ABSPATH')) {
	exit;
}

function portfolio_smtp_defaults() {
	return array(
		'enabled'    => 0,
		'host'       => '',
		'port'       => 587,
		'encryption' => 'tls',
		'auth'       => 1,
		'username'   => '',
		'password'   => '',
		'from_email' => get_option('admin_email'),
		'from_name'  => get_bloginfo('name'),
	);
}

function portfolio_smtp_get_settings() {
	return wp_parse_args(
		(array) get_option('portfolio_smtp_settings', array()),
		portfolio_smtp_defaults()
	);
}

function portfolio_smtp_sanitize_settings($input) {
	$input    = is_array($input) ? $input : array();
	$existing = portfolio_smtp_get_settings();
	$port     = absint($input['port'] ?? 587);
	$port     = max(1, min(65535, $port));
	$secure   = sanitize_key($input['encryption'] ?? 'tls');

	if (! in_array($secure, array('none', 'tls', 'ssl'), true)) {
		$secure = 'tls';
	}

	$password = isset($input['password'])
		? (string) wp_unslash($input['password'])
		: '';

	return array(
		'enabled'    => empty($input['enabled']) ? 0 : 1,
		'host'       => sanitize_text_field($input['host'] ?? ''),
		'port'       => $port,
		'encryption' => $secure,
		'auth'       => empty($input['auth']) ? 0 : 1,
		'username'   => sanitize_text_field($input['username'] ?? ''),
		'password'   => '' === $password ? $existing['password'] : $password,
		'from_email' => sanitize_email($input['from_email'] ?? ''),
		'from_name'  => sanitize_text_field($input['from_name'] ?? ''),
	);
}

function portfolio_smtp_register_settings() {
	register_setting(
		'portfolio_smtp',
		'portfolio_smtp_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'portfolio_smtp_sanitize_settings',
			'default'           => portfolio_smtp_defaults(),
		)
	);

	add_settings_section(
		'portfolio_smtp_server',
		__('SMTP server', 'custom-theme'),
		'portfolio_smtp_section_description',
		'portfolio-smtp'
	);

	$fields = array(
		'enabled'    => array(__('Enable SMTP delivery', 'custom-theme'), 'checkbox'),
		'host'       => array(__('SMTP host', 'custom-theme'), 'text'),
		'port'       => array(__('SMTP port', 'custom-theme'), 'number'),
		'encryption' => array(__('Encryption', 'custom-theme'), 'select'),
		'auth'       => array(__('Use authentication', 'custom-theme'), 'checkbox'),
		'username'   => array(__('SMTP username', 'custom-theme'), 'text'),
		'password'   => array(__('SMTP password', 'custom-theme'), 'password'),
		'from_email' => array(__('From email', 'custom-theme'), 'email'),
		'from_name'  => array(__('From name', 'custom-theme'), 'text'),
	);

	foreach ($fields as $name => $field) {
		add_settings_field(
			'portfolio_smtp_' . $name,
			$field[0],
			'portfolio_smtp_render_field',
			'portfolio-smtp',
			'portfolio_smtp_server',
			array(
				'name' => $name,
				'type' => $field[1],
			)
		);
	}
}
add_action('admin_init', 'portfolio_smtp_register_settings');

function portfolio_smtp_section_description() {
	echo '<p>' . esc_html__('Enter the credentials supplied by your hosting company or transactional email provider. The password is stored in the WordPress database and is never rendered back into this page.', 'custom-theme') . '</p>';
}

function portfolio_smtp_render_field($args) {
	$settings = portfolio_smtp_get_settings();
	$name     = $args['name'];
	$type     = $args['type'];
	$id       = 'portfolio_smtp_' . $name;
	$field    = 'portfolio_smtp_settings[' . $name . ']';
	$value    = $settings[$name] ?? '';

	if ('checkbox' === $type) {
		printf(
			'<label><input id="%1$s" name="%2$s" type="checkbox" value="1" %3$s> %4$s</label>',
			esc_attr($id),
			esc_attr($field),
			checked(1, (int) $value, false),
			esc_html('enabled' === $name ? __('Send contact-form email through this SMTP server.', 'custom-theme') : __('Authenticate with the SMTP username and password.', 'custom-theme'))
		);
		return;
	}

	if ('select' === $type) {
		?>
		<select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($field); ?>">
			<option value="none" <?php selected($value, 'none'); ?>><?php esc_html_e('None', 'custom-theme'); ?></option>
			<option value="tls" <?php selected($value, 'tls'); ?>><?php esc_html_e('TLS (recommended)', 'custom-theme'); ?></option>
			<option value="ssl" <?php selected($value, 'ssl'); ?>><?php esc_html_e('SSL', 'custom-theme'); ?></option>
		</select>
		<?php
		return;
	}

	$attributes = '';
	$input_value = $value;

	if ('password' === $type) {
		$input_value = '';
		$attributes  = ' autocomplete="new-password" placeholder="' . esc_attr__('Leave blank to keep the saved password', 'custom-theme') . '"';
	} elseif ('number' === $type) {
		$attributes = ' min="1" max="65535"';
	}

	printf(
		'<input class="regular-text" id="%1$s" name="%2$s" type="%3$s" value="%4$s"%5$s>',
		esc_attr($id),
		esc_attr($field),
		esc_attr($type),
		esc_attr($input_value),
		$attributes // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

function portfolio_smtp_add_settings_page() {
	add_options_page(
		__('Portfolio Email', 'custom-theme'),
		__('Portfolio Email', 'custom-theme'),
		'manage_options',
		'portfolio-smtp',
		'portfolio_smtp_settings_page'
	);
}
add_action('admin_menu', 'portfolio_smtp_add_settings_page');

function portfolio_smtp_settings_page() {
	if (! current_user_can('manage_options')) {
		return;
	}

	$settings = portfolio_smtp_get_settings();
	$result   = get_transient('portfolio_smtp_test_' . get_current_user_id());

	if ($result) {
		delete_transient('portfolio_smtp_test_' . get_current_user_id());
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e('Portfolio Email Delivery', 'custom-theme'); ?></h1>
		<p><?php esc_html_e('Configure authenticated SMTP for contact-form messages and verify delivery before submitting the website.', 'custom-theme'); ?></p>

		<?php settings_errors(); ?>
		<?php if ($result) : ?>
			<div class="notice <?php echo ! empty($result['success']) ? 'notice-success' : 'notice-error'; ?> is-dismissible">
				<p><?php echo esc_html($result['message']); ?></p>
			</div>
		<?php endif; ?>

		<?php if (! empty($settings['enabled']) && ! empty($settings['host'])) : ?>
			<div class="notice notice-success inline"><p><?php esc_html_e('SMTP delivery is enabled. Use the test below to verify the provider credentials.', 'custom-theme'); ?></p></div>
		<?php else : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e('SMTP delivery is not configured yet. Save the provider settings below before testing.', 'custom-theme'); ?></p></div>
		<?php endif; ?>

		<form action="options.php" method="post">
			<?php
			settings_fields('portfolio_smtp');
			do_settings_sections('portfolio-smtp');
			submit_button(__('Save SMTP Settings', 'custom-theme'));
			?>
		</form>

		<hr>
		<h2><?php esc_html_e('Send a Test Email', 'custom-theme'); ?></h2>
		<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
			<input type="hidden" name="action" value="portfolio_smtp_test">
			<?php wp_nonce_field('portfolio_smtp_test', 'portfolio_smtp_test_nonce'); ?>
			<p>
				<label for="portfolio_smtp_test_recipient"><strong><?php esc_html_e('Recipient', 'custom-theme'); ?></strong></label><br>
				<input class="regular-text" id="portfolio_smtp_test_recipient" name="recipient" type="email" value="<?php echo esc_attr(get_option('admin_email')); ?>" required>
			</p>
			<?php submit_button(__('Send Test Email', 'custom-theme'), 'secondary', 'submit', false); ?>
		</form>
	</div>
	<?php
}

function portfolio_smtp_configure_mailer($phpmailer) {
	$settings = portfolio_smtp_get_settings();

	if (empty($settings['enabled']) || empty($settings['host'])) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host       = $settings['host'];
	$phpmailer->Port       = (int) $settings['port'];
	$phpmailer->SMTPAuth   = ! empty($settings['auth']);
	$phpmailer->Username   = $settings['username'];
	$phpmailer->Password   = $settings['password'];
	$phpmailer->SMTPDebug  = 0;
	$phpmailer->CharSet    = 'UTF-8';

	if ('none' === $settings['encryption']) {
		$phpmailer->SMTPSecure  = '';
		$phpmailer->SMTPAutoTLS = false;
	} else {
		$phpmailer->SMTPSecure = $settings['encryption'];
	}

	$from_email = is_email($settings['from_email'])
		? $settings['from_email']
		: get_option('admin_email');
	$from_name  = $settings['from_name'] ?: get_bloginfo('name');

	$phpmailer->setFrom($from_email, $from_name, false);
}
add_action('phpmailer_init', 'portfolio_smtp_configure_mailer');

function portfolio_smtp_send_test() {
	if (! current_user_can('manage_options')) {
		wp_die(
			esc_html__('You are not allowed to test email delivery.', 'custom-theme'),
			'',
			array('response' => 403)
		);
	}

	check_admin_referer('portfolio_smtp_test', 'portfolio_smtp_test_nonce');

	$recipient = sanitize_email(wp_unslash($_POST['recipient'] ?? ''));
	$redirect  = admin_url('options-general.php?page=portfolio-smtp');
	$transient = 'portfolio_smtp_test_' . get_current_user_id();

	if (! is_email($recipient)) {
		set_transient(
			$transient,
			array('success' => false, 'message' => __('Enter a valid recipient email address.', 'custom-theme')),
			MINUTE_IN_SECONDS
		);
		wp_safe_redirect($redirect);
		exit;
	}

	$error_message = '';
	$failure        = static function ($error) use (&$error_message) {
		if (is_wp_error($error)) {
			$error_message = $error->get_error_message();
		}
	};

	add_action('wp_mail_failed', $failure);
	$sent = wp_mail(
		$recipient,
		__('Portfolio SMTP test', 'custom-theme'),
		__('This message confirms that SMTP delivery is working for your portfolio website.', 'custom-theme')
	);
	remove_action('wp_mail_failed', $failure);

	$message = $sent
		? sprintf(__('Test email sent successfully to %s.', 'custom-theme'), $recipient)
		: ($error_message ?: __('WordPress could not send the test email. Check the SMTP credentials and provider logs.', 'custom-theme'));

	set_transient(
		$transient,
		array('success' => (bool) $sent, 'message' => $message),
		MINUTE_IN_SECONDS
	);

	wp_safe_redirect($redirect);
	exit;
}
add_action('admin_post_portfolio_smtp_test', 'portfolio_smtp_send_test');
