<?php
/**
 * Shipping. The zones are set in WooCommerce → Settings → Shipping (database), by postcode and road distance from
 * the shop (Bethaniagade 5): Herning 7400; up to 15 km; 15–40 km; rest of Jutland (6000–9990). Ferry islands and
 * the rest of Denmark have only pickup in the shop. Delivery costs are entered without moms (49 kr. = 39.20).
 * Here: the paid delivery is hidden when the order has free delivery, and addresses outside the delivery area get
 * a note to get in touch. Styles in site.css (=== Cart and checkout widgets ===).
 */

defined( 'ABSPATH' ) || exit;

// Free delivery reached: only free delivery and pickup are offered, not the paid delivery of the same zone.
add_filter( 'woocommerce_package_rates', function ( $rates ) {
	$free = array_filter( $rates, fn( $rate ) => 'free_shipping' === $rate->get_method_id() );
	if ( ! $free ) {
		return $rates;
	}

	return array_filter( $rates, fn( $rate ) => 'flat_rate' !== $rate->get_method_id() );
}, 100 );

// Only pickup for the address: we do not deliver there by ourselves, but the shop arranges it on request.
add_action( 'woocommerce_after_shipping_rate', function ( $method, $index ) {
	$packages = WC()->shipping()->get_packages();
	$rates    = $packages[ $index ]['rates'] ?? [];
	if ( 'local_pickup' !== $method->get_method_id() || 1 !== count( $rates ) ) {
		return;
	}

	printf(
		'<p class="dl-shipping-note">%s <a href="mailto:info@delicja.dk">info@delicja.dk</a> · <a href="tel:+4551430321">+45 51 43 03 21</a></p>',
		esc_html__( 'We deliver in Jutland. Live even further away? Get in touch and we will arrange it together:', 'delicja' )
	);
}, 10, 2 );
