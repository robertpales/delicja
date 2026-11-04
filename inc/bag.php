<?php
/**
 * Paper bag at checkout (asked by the client, 2026-11-04): just above the payment methods the customer chooses
 * "Yes, please (+5,00 kr.)" or "No, thank you" (default no). Yes adds a "Paper bag" line to the order total; it shows
 * in the order, the e-mails and the invoice like any fee. The price includes moms, like the product prices, and does
 * not count towards the delivery minimum or free delivery (it is not part of the subtotal).
 * The choice is kept in the session, so it survives every update of the order summary.
 */

defined( 'ABSPATH' ) || exit;

// Price of one paper bag in kr., including moms.
const DELICJA_BAG_PRICE = 5;

function delicja_bag_chosen(): bool {
	return WC()->session && 'yes' === WC()->session->get( 'dl_bag' );
}

function delicja_set_bag( $value ): void {
	if ( WC()->session ) {
		WC()->session->set( 'dl_bag', 'yes' === $value ? 'yes' : 'no' );
	}
}

// The choice comes with every update of the order summary and with the order itself.
add_action( 'woocommerce_checkout_update_order_review', function ( $post_data ) {
	parse_str( (string) $post_data, $data );
	delicja_set_bag( $data['dl_bag'] ?? 'no' );
} );

add_action( 'woocommerce_before_checkout_process', function () {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce has checked the nonce before this runs.
	delicja_set_bag( sanitize_key( wp_unslash( $_POST['dl_bag'] ?? 'no' ) ) );
} );

// The question, above the payment methods (redrawn with the order summary).
add_action( 'woocommerce_review_order_before_payment', function () {
	$chosen  = delicja_bag_chosen() ? 'yes' : 'no';
	$options = [
		/* translators: %s: price of the bag, e.g. "5,00 kr." */
		'yes' => sprintf( __( 'Yes, please (+%s)', 'delicja' ), wp_strip_all_tags( wc_price( DELICJA_BAG_PRICE ) ) ),
		'no'  => __( 'No, thank you', 'delicja' ),
	];

	echo '<fieldset class="dl-fulfilment dl-bag update_totals_on_change"><legend class="dl-fulfilment-title">' . esc_html__( 'Would you like a paper bag?', 'delicja' ) . '</legend><div class="dl-fulfilment-options">';
	foreach ( $options as $value => $name ) {
		printf(
			'<label class="dl-fulfilment-option"><input type="radio" name="dl_bag" value="%1$s"%2$s><span class="dl-fulfilment-name">%3$s</span></label>',
			esc_attr( $value ),
			checked( $chosen, $value, false ),
			esc_html( $name )
		);
	}
	echo '</div></fieldset>';
} );

// The bag as a fee. Like the pant (inc/pant.php): the price includes moms, so the fee is added without it and
// WooCommerce adds the moms back.
add_action( 'woocommerce_cart_calculate_fees', function ( WC_Cart $cart ) {
	if ( ! is_checkout() && ! wp_doing_ajax() ) {
		return;
	}
	if ( ! delicja_bag_chosen() ) {
		return;
	}

	$rates   = wc_tax_enabled() ? WC_Tax::get_rates( '', WC()->customer ) : [];
	$taxable = ! empty( $rates );
	$price   = (float) DELICJA_BAG_PRICE;
	$amount  = $taxable && wc_prices_include_tax() ? $price - array_sum( WC_Tax::calc_inclusive_tax( $price, $rates ) ) : $price;

	$cart->add_fee( __( 'Paper bag', 'delicja' ), $amount, $taxable, '' );
} );

// A new order starts without a bag again.
add_action( 'woocommerce_thankyou', function () {
	delicja_set_bag( 'no' );
} );
