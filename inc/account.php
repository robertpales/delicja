<?php
/**
 * My account (page built in Elementor with WooCommerce's [woocommerce_my_account]): when visitors can create an
 * account, logging in and registering share one card. A switch on top ("Log in" / "Register") shows one form at a
 * time; it is two radio buttons, so it works without JavaScript. After a failed registration the register form stays
 * open. The social login buttons sit once under both forms (inc/social-login.php). Styles in my-account.css (=== Forms ===).
 */

defined( 'ABSPATH' ) || exit;

/**
 * True when visitors can create an account on the My account page.
 */
function delicja_account_registration(): bool {
	return 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
}

// After the notices (priority 10), so an error shows above the switch.
add_action( 'woocommerce_before_customer_login_form', function () {
	if ( ! delicja_account_registration() ) {
		return;
	}

	// Only picks the open form; WooCommerce checks the nonce when it handles the registration.
	$register = isset( $_POST['register'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	printf(
		'<div class="dl-auth-switch" role="radiogroup" aria-label="%1$s">'
		. '<input class="dl-auth-radio" type="radio" name="dl-auth" id="dl-auth-login" value="login"%2$s>'
		. '<label class="dl-auth-option" for="dl-auth-login">%3$s</label>'
		. '<input class="dl-auth-radio" type="radio" name="dl-auth" id="dl-auth-register" value="register"%4$s>'
		. '<label class="dl-auth-option" for="dl-auth-register">%5$s</label>'
		. '</div>',
		esc_attr__( 'Log in or register', 'delicja' ),
		$register ? '' : ' checked',
		esc_html__( 'Log in', 'delicja' ),
		$register ? ' checked' : '',
		esc_html__( 'Register', 'delicja' )
	);
}, 20 );
