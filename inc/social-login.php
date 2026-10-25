<?php
/**
 * Social login (Nextend Social Login, free version): "Continue with Google" buttons in WooCommerce's forms.
 * The free plugin only adds them to wp-login.php; its WooCommerce placement is a Pro feature, so the buttons are
 * placed here with its shortcode. Providers (Google now, Facebook later) and their keys are set in
 * Settings → Nextend Social Login; every enabled provider shows up here by itself. Styles in site.css (=== Social login ===).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Buttons of all enabled providers with a divider label. Empty when the plugin is off or no provider is enabled.
 */
function delicja_social_login_buttons( string $context, string $label ): string {
	if ( is_user_logged_in() || ! shortcode_exists( 'nextend_social_login' ) ) {
		return '';
	}

	$buttons = do_shortcode( '[nextend_social_login]' );
	if ( '' === trim( $buttons ) ) {
		return '';
	}

	return '<div class="dl-social-login dl-social-login--' . esc_attr( $context ) . '">'
		. '<p class="dl-social-login-divider"><span>' . esc_html( $label ) . '</span></p>'
		. $buttons
		. '</div>';
}

// The Pro version places the buttons itself; then this file does nothing.
if ( ! class_exists( 'NextendSocialLoginPRO' ) ) {
	// My account: under the login form and under the registration form.
	add_action( 'woocommerce_login_form_end', function () {
		echo delicja_social_login_buttons( 'login', 'or' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} );

	add_action( 'woocommerce_register_form_end', function () {
		echo delicja_social_login_buttons( 'register', 'or' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} );

	// Checkout: guests can log in with one click before filling in the address.
	add_action( 'woocommerce_before_checkout_form', function () {
		echo delicja_social_login_buttons( 'checkout', 'Check out faster' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}, 5 );
}
