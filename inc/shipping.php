<?php
/**
 * Shipping. The zones are set in WooCommerce → Settings → Shipping (database), by postcode and road distance from
 * the shop (Bethaniagade 5): Herning 7400; up to 15 km; 15–40 km; rest of Jutland (6000–9990). Ferry islands and
 * the rest of Denmark have only pickup in the shop. Delivery costs are entered without moms (49 kr. = 39.20).
 * Here:
 * - the paid delivery is hidden when the order has free delivery,
 * - the cart shows what getting the order costs (pickup free, delivery from the lowest price), before the total,
 * - the checkout starts with the choice "Pickup in the shop" or "Delivery" (field dl_fulfilment, kept in the
 *   session). Only the rates of that choice are offered, so pickup works without an address; the address fields are
 *   shown and required only for delivery (hidden by site.css). Without a choice, or for an address we do not deliver
 *   to, the order summary says what to do instead of WooCommerce's "no shipping options".
 * Styles in site.css (=== Cart and checkout widgets ===).
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

/**
 * Lowest delivery price (with moms) and lowest free delivery threshold of all zones, for the texts. The lowest
 * threshold is Herning's.
 */
function delicja_delivery_prices(): array {
	static $prices = null;
	if ( null !== $prices ) {
		return $prices;
	}

	$costs = [];
	$free  = [];
	foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
		foreach ( $zone['shipping_methods'] as $method ) {
			if ( ! $method->is_enabled() ) {
				continue;
			}
			if ( 'flat_rate' === $method->id ) {
				$costs[] = (float) $method->get_option( 'cost' );
			} elseif ( 'free_shipping' === $method->id ) {
				$free[] = (float) $method->get_option( 'min_amount' );
			}
		}
	}

	$from = $costs ? min( $costs ) : 0.0;
	$from += array_sum( WC_Tax::calc_shipping_tax( $from, WC_Tax::get_shipping_tax_rates() ) );

	$prices = [
		'from' => $from,
		'free' => $free ? min( $free ) : 0.0,
	];
	return $prices;
}

// Cart: what getting the order costs, before the total (the cart itself does not calculate shipping).
add_action( 'woocommerce_cart_totals_before_order_total', function () {
	if ( ! WC()->cart->needs_shipping() ) {
		return;
	}

	$prices = delicja_delivery_prices();
	printf(
		'<tr class="dl-cart-shipping"><th>%1$s</th><td data-title="%1$s"><span>%2$s</span><span>%3$s</span><small>%4$s</small></td></tr>',
		esc_html__( 'Delivery', 'delicja' ),
		wp_kses_post( __( 'Pickup in the shop: <strong>free</strong>', 'delicja' ) ),
		wp_kses_post( sprintf( __( 'Delivery on Wednesday or Friday: from %1$s, free in Herning from %2$s', 'delicja' ), wc_price( $prices['from'] ), wc_price( $prices['free'] ) ) ),
		esc_html__( 'You choose at checkout.', 'delicja' )
	);
} );

/**
 * Pickup or delivery as chosen at checkout: 'pickup', 'delivery' or '' (not chosen yet).
 */
function delicja_fulfilment(): string {
	$value = WC()->session ? WC()->session->get( 'dl_fulfilment', '' ) : '';
	return in_array( $value, [ 'pickup', 'delivery' ], true ) ? $value : '';
}

function delicja_set_fulfilment( $value ): void {
	if ( WC()->session ) {
		WC()->session->set( 'dl_fulfilment', in_array( $value, [ 'pickup', 'delivery' ], true ) ? $value : '' );
	}
}

// The choice comes with every update of the order summary and with the order itself.
add_action( 'woocommerce_checkout_update_order_review', function ( $post_data ) {
	parse_str( (string) $post_data, $data );
	delicja_set_fulfilment( $data['dl_fulfilment'] ?? '' );
} );

add_action( 'woocommerce_before_checkout_process', function () {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce has checked the nonce before this runs.
	delicja_set_fulfilment( sanitize_key( wp_unslash( $_POST['dl_fulfilment'] ?? '' ) ) );
} );

// The choice is part of the package, so WooCommerce recalculates the rates when it changes.
add_filter( 'woocommerce_cart_shipping_packages', function ( $packages ) {
	if ( is_checkout() ) {
		foreach ( $packages as $i => $package ) {
			$packages[ $i ]['dl_fulfilment'] = delicja_fulfilment();
		}
	}
	return $packages;
} );

// At checkout the rates are calculated without a postcode too (pickup needs no address).
add_filter( 'woocommerce_shipping_calculator_enable_postcode', fn( $enabled ) => is_checkout() ? false : $enabled );

// Only the rates of the choice; none before a choice is made.
add_filter( 'woocommerce_package_rates', function ( $rates, $package ) {
	if ( ! isset( $package['dl_fulfilment'] ) ) {
		return $rates;
	}

	if ( '' === $package['dl_fulfilment'] ) {
		return [];
	}

	$pickup = 'pickup' === $package['dl_fulfilment'];
	return array_filter( $rates, fn( $rate ) => $pickup === ( 'local_pickup' === $rate->get_method_id() ) );
}, 110, 2 );

/**
 * What to do when the order summary has no shipping rate: choose first, enter a postcode, or get in touch.
 */
function delicja_no_shipping_text(): string {
	$fulfilment = delicja_fulfilment();
	if ( '' === $fulfilment ) {
		return esc_html__( 'Choose pickup in the shop or delivery above.', 'delicja' );
	}
	if ( 'delivery' === $fulfilment && '' === WC()->customer->get_shipping_postcode() ) {
		return esc_html__( 'Enter your postcode to see the delivery price.', 'delicja' );
	}

	return sprintf(
		'%s <a href="mailto:info@delicja.dk">info@delicja.dk</a> · <a href="tel:+4551430321">+45 51 43 03 21</a>',
		esc_html__( 'We deliver in Jutland. Live even further away? Choose pickup, or get in touch and we will arrange it together:', 'delicja' )
	);
}

foreach ( [ 'woocommerce_no_shipping_available_html', 'woocommerce_shipping_may_be_available_html' ] as $hook ) {
	add_filter( $hook, fn( $html ) => is_checkout() ? '<p class="dl-shipping-note">' . delicja_no_shipping_text() . '</p>' : $html );
}

// Checkout: pickup or delivery first, above the billing details.
add_action( 'woocommerce_checkout_before_customer_details', function () {
	if ( ! WC()->cart->needs_shipping() ) {
		return;
	}

	$prices  = delicja_delivery_prices();
	$chosen  = delicja_fulfilment();
	$options = [
		'pickup'   => [
			__( 'Pickup in the shop', 'delicja' ),
			__( 'Free · Bethaniagade 5, Herning · ready in about 2 hours', 'delicja' ),
		],
		'delivery' => [
			__( 'Delivery to your door', 'delicja' ),
			sprintf( __( 'In Jutland, Wednesday or Friday · from %1$s, free in Herning from %2$s', 'delicja' ), wc_price( $prices['from'] ), wc_price( $prices['free'] ) ),
		],
	];

	echo '<fieldset class="dl-fulfilment update_totals_on_change"><legend class="dl-fulfilment-title">' . esc_html__( 'How would you like to get your order?', 'delicja' ) . '</legend><div class="dl-fulfilment-options">';
	foreach ( $options as $value => [ $name, $info ] ) {
		printf(
			'<label class="dl-fulfilment-option"><input type="radio" name="dl_fulfilment" value="%1$s"%2$s><span class="dl-fulfilment-name">%3$s</span><span class="dl-fulfilment-info">%4$s</span></label>',
			esc_attr( $value ),
			checked( $chosen, $value, false ),
			esc_html( $name ),
			wp_kses_post( $info )
		);
	}
	echo '</div></fieldset>';
} );

// Address fields required only for delivery.
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	if ( ! WC()->cart || ! WC()->cart->needs_shipping() || 'delivery' === delicja_fulfilment() ) {
		return $fields;
	}

	foreach ( [ 'billing_address_1', 'billing_postcode', 'billing_city' ] as $key ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			$fields['billing'][ $key ]['required'] = false;
		}
	}
	return $fields;
} );

// One clear message instead of WooCommerce's "No shipping method has been selected".
add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
	if ( ! WC()->cart->needs_shipping() || ! $errors->get_error_message( 'shipping' ) ) {
		return;
	}

	$fulfilment = delicja_fulfilment();
	if ( 'delivery' === $fulfilment && $errors->get_error_message( 'billing_postcode_required' ) ) {
		$errors->remove( 'shipping' ); // The postcode message says it already.
		return;
	}

	$errors->remove( 'shipping' );
	$errors->add( 'shipping', '' === $fulfilment ? esc_html__( 'Choose pickup in the shop or delivery.', 'delicja' ) : delicja_no_shipping_text() );
}, 10, 2 );
