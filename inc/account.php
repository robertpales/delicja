<?php
/**
 * My account (page built in Elementor with WooCommerce's [woocommerce_my_account]): when visitors can create an
 * account, logging in and registering share one card. A switch on top ("Log in" / "Register") shows one form at a
 * time; it is two radio buttons, so it works without JavaScript. After a failed registration the register form stays
 * open. The social login buttons sit once under both forms (inc/social-login.php). Styles in my-account.css (=== Forms ===).
 *
 * The register form asks for two consents instead of WooCommerce's privacy text: "I have read the privacy policy"
 * (required) and e-mails about deals and new products (optional, unticked). Both are saved on the customer with the
 * date (user meta `delicja_privacy_accepted`, `delicja_marketing_consent`); the marketing consent can be changed in
 * My account → Account details. Using it (a newsletter tool) is Robo's.
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

/**
 * Checkbox row for a consent. $checked keeps the tick after a failed form.
 */
function delicja_consent_checkbox( string $name, string $label, bool $checked, bool $required = false ): string {
	return sprintf(
		'<p class="form-row dl-consent-row"><label class="dl-consent" for="%1$s"><input type="checkbox" name="%1$s" id="%1$s" value="1"%2$s%3$s><span>%4$s%5$s</span></label></p>',
		esc_attr( $name ),
		$checked ? ' checked' : '',
		$required ? ' required' : '',
		$label,
		$required ? '&nbsp;<span class="required" aria-hidden="true">*</span>' : ''
	);
}

/**
 * Label of the marketing consent (register form and Account details).
 */
function delicja_marketing_label(): string {
	return esc_html__( 'Send me e-mails about deals and new products. I can unsubscribe at any time.', 'delicja' );
}

// The consents replace WooCommerce's privacy text in the register form.
add_action( 'init', function () {
	remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
} );

add_action( 'woocommerce_register_form', function () {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- only keeps the ticks after a failed registration.
	$url     = get_privacy_policy_url();
	$privacy = $url
		? sprintf(
			/* translators: %s: link to the privacy policy */
			esc_html__( 'I have read the %s.', 'delicja' ),
			'<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html__( 'privacy policy', 'delicja' ) . '</a>'
		)
		: esc_html__( 'I have read the privacy policy.', 'delicja' );

	echo delicja_consent_checkbox( 'dl_privacy', $privacy, ! empty( $_POST['dl_privacy'] ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo delicja_consent_checkbox( 'dl_marketing', delicja_marketing_label(), ! empty( $_POST['dl_marketing'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	// phpcs:enable
}, 20 );

// WooCommerce has checked the nonce before these run. The filter also runs when the checkout creates an account; only
// the My account register form (its button is named "register") has the checkbox.
add_filter( 'woocommerce_registration_errors', function ( $errors ) {
	if ( isset( $_POST['register'] ) && empty( $_POST['dl_privacy'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$errors->add( 'dl_privacy', esc_html__( 'Please confirm that you have read the privacy policy.', 'delicja' ) );
	}

	return $errors;
} );

add_action( 'woocommerce_created_customer', function ( $customer_id ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( ! empty( $_POST['dl_privacy'] ) ) {
		update_user_meta( $customer_id, 'delicja_privacy_accepted', current_time( 'mysql' ) );
	}
	if ( ! empty( $_POST['dl_marketing'] ) ) {
		update_user_meta( $customer_id, 'delicja_marketing_consent', current_time( 'mysql' ) );
	}
	// phpcs:enable
} );

// Account details: the customer can give or withdraw the marketing consent.
add_action( 'woocommerce_edit_account_form', function () {
	$checked = (bool) get_user_meta( get_current_user_id(), 'delicja_marketing_consent', true );

	echo delicja_consent_checkbox( 'dl_marketing', delicja_marketing_label(), $checked ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} );

add_action( 'woocommerce_save_account_details', function ( $user_id ) {
	if ( empty( $_POST['dl_marketing'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		delete_user_meta( $user_id, 'delicja_marketing_consent' );
	} elseif ( ! get_user_meta( $user_id, 'delicja_marketing_consent', true ) ) {
		update_user_meta( $user_id, 'delicja_marketing_consent', current_time( 'mysql' ) );
	}
} );
