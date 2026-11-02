<?php
/**
 * Shipping. The zones are set in WooCommerce → Settings → Shipping (database), by postcode and road distance from
 * the shop (Bethaniagade 5): Herning 7400; up to 15 km; 15–40 km; rest of Jutland (6000–9990). Ferry islands and
 * the rest of Denmark have only pickup in the shop. Delivery costs are entered without moms (49 kr. = 39.20).
 * When the client adds or moves a town, change the postcodes of its zone.
 * Here:
 * - the paid delivery is hidden when the order has free delivery,
 * - the cart shows what getting the order costs (pickup free, delivery from the lowest price), before the total,
 * - the checkout starts with the choice "Pickup in the shop" or "Delivery" (field dl_fulfilment, kept in the
 *   session). Only the rates of that choice are offered, so pickup works without an address; the address fields are
 *   shown and required only for delivery (hidden by site.css). Without a choice, or for an address we do not deliver
 *   to, the order summary says what to do instead of WooCommerce's "no shipping options".
 * - a bar "Add 230 kr. more for free delivery" in the cart, the mini cart (site.js, === Free delivery ===) and, for
 *   delivery, in the checkout summary: the threshold of the customer's zone once the postcode is known, otherwise
 *   Herning's (the lowest).
 * - delivery needs an order of at least 499 kr. (pickup has no minimum), checked at checkout; the order summary
 *   the cart and the mini cart show a bar towards it above the free delivery bar, and Place order is disabled,
 * - delivery in the zones with the Wednesday/Friday round goes on the first delivery day at least 24 hours away
 *   (15:00, the start of the round); the order summary shows the day, the order keeps it (_delicja_delivery_date)
 *   and the e-mails, the thank-you page and the order in wp-admin show it.
 * Styles in site.css (=== Cart and checkout widgets ===).
 */

defined( 'ABSPATH' ) || exit;

// Minimum order for delivery, with moms (the client's terms, 2026-10-28).
const DELICJA_DELIVERY_MINIMUM = 499;

// Shipping zones with the Wednesday/Friday delivery round (WooCommerce zone IDs): Herning, up to 15 km, 15–40 km.
// Rest of Jutland has a time agreed with the customer.
const DELICJA_ROUND_ZONES = [ 2, 3, 4 ];

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
		'<tr class="dl-cart-shipping"><th>%1$s</th><td data-title="%1$s"><span>%2$s</span><span>%3$s</span><small>%4$s %5$s</small></td></tr>',
		esc_html__( 'Delivery', 'delicja' ),
		wp_kses_post( __( 'Pickup in the shop: <strong>free</strong>', 'delicja' ) ),
		wp_kses_post( sprintf( __( 'Delivery on Wednesday or Friday: from %1$s, free in Herning from %2$s', 'delicja' ), wc_price( $prices['from'] ), wc_price( $prices['free'] ) ) ),
		/* translators: %s: minimum order amount */
		wp_kses_post( sprintf( __( 'Minimum %s for delivery.', 'delicja' ), wc_price( DELICJA_DELIVERY_MINIMUM ) ) ),
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
		'%s <a href="mailto:shop@delicja.dk">shop@delicja.dk</a> · <a href="tel:+4551430321">+45 51 43 03 21</a>',
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
			sprintf( __( 'In Jutland, Wednesday or Friday · from %1$s, free in Herning from %2$s', 'delicja' ), wc_price( $prices['from'] ), wc_price( $prices['free'] ) )
				/* translators: %s: minimum order amount */
				. ' · ' . sprintf( __( 'minimum order %s', 'delicja' ), wc_price( DELICJA_DELIVERY_MINIMUM ) ),
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

/**
 * Free delivery method for the customer: of the zone of their postcode once they have entered one, otherwise the one
 * with the lowest threshold (Herning). Null when that zone has no free delivery from an amount. The second value is
 * true when the zone is the customer's own.
 */
function delicja_free_shipping_method(): array {
	$own     = WC()->customer && '' !== WC()->customer->get_shipping_postcode();
	$methods = [];
	if ( $own ) {
		$packages = WC()->cart ? WC()->cart->get_shipping_packages() : [];
		$zone     = $packages ? WC_Shipping_Zones::get_zone_matching_package( $packages[0] ) : null;
		$methods  = $zone ? $zone->get_shipping_methods( true ) : [];
	} else {
		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			$methods = array_merge( $methods, array_filter( $zone['shipping_methods'], fn( $method ) => $method->is_enabled() ) );
		}
	}

	$free = array_filter( $methods, fn( $method ) => 'free_shipping' === $method->id && in_array( $method->get_option( 'requires' ), [ 'min_amount', 'either' ], true ) );
	usort( $free, fn( $a, $b ) => (float) $a->get_option( 'min_amount' ) <=> (float) $b->get_option( 'min_amount' ) );

	return [ $free[0] ?? null, $own ];
}

/**
 * Amount the free delivery threshold is compared with, as WooCommerce's free shipping method counts it.
 */
function delicja_free_shipping_total( WC_Shipping_Method $method ): float {
	return delicja_cart_goods_total( 'yes' === $method->get_option( 'ignore_discounts' ) );
}

/**
 * Value of the goods in the cart as the customer sees it (with moms), after discounts unless ignored.
 */
function delicja_cart_goods_total( bool $ignore_discounts = false ): float {
	$cart  = WC()->cart;
	$total = $cart->get_displayed_subtotal();
	if ( ! $ignore_discounts ) {
		$total -= $cart->get_discount_total() + ( $cart->display_prices_including_tax() ? $cart->get_discount_tax() : 0 );
	}

	return round( $total, wc_get_price_decimals() );
}

/**
 * Bar "Add 230 kr. more for free delivery", or that free delivery is reached; empty without free delivery.
 */
function delicja_free_shipping_bar(): string {
	[ $method, $own ] = delicja_free_shipping_method();
	if ( ! $method || ! WC()->cart->needs_shipping() ) {
		return '';
	}

	$threshold = (float) $method->get_option( 'min_amount' );
	$total     = delicja_free_shipping_total( $method );
	$remaining = max( 0, $threshold - $total );
	$percent   = $threshold > 0 ? min( 100, round( $total / $threshold * 100 ) ) : 100;

	if ( $remaining > 0 ) {
		$text = $own
			/* translators: %s: amount still missing */
			? sprintf( __( 'Add %s more for free delivery.', 'delicja' ), wc_price( $remaining ) )
			/* translators: %s: amount still missing */
			: sprintf( __( 'Add %s more for free delivery in Herning.', 'delicja' ), wc_price( $remaining ) );
	} else {
		$text = $own ? __( 'Your order has free delivery.', 'delicja' ) : __( 'Your order has free delivery in Herning.', 'delicja' );
	}

	return delicja_progress_bar( $percent, $text, $remaining <= 0, __( 'Free delivery', 'delicja' ) );
}

/**
 * Progress bar with a text under it (free delivery, minimum order for delivery); $done makes the text bold.
 */
function delicja_progress_bar( int $percent, string $text, bool $done, string $label ): string {
	return sprintf(
		'<div class="dl-free-shipping%1$s"><div class="dl-free-shipping-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="%2$d" aria-label="%3$s"><div class="dl-free-shipping-fill" style="--dl-progress: %2$d%%"></div></div><p class="dl-free-shipping-text">%4$s</p></div>',
		$done ? ' is-free' : '',
		$percent,
		esc_attr( $label ),
		wp_kses_post( $text )
	);
}

// Cart page: right above the checkout button, the minimum order bar for delivery above the free delivery bar (the
// Cart widget refreshes them with the totals).
add_action( 'woocommerce_proceed_to_checkout', function () {
	if ( ! WC()->cart->needs_shipping() ) {
		return;
	}

	$bars = delicja_delivery_minimum_bar() . delicja_free_shipping_bar();
	if ( $bars ) {
		echo '<div class="dl-delivery-bars">' . $bars . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}, 6 );

// Mini cart: the same bars (minimum order for delivery, free delivery), drawn by site.js (=== Free delivery ===) from
// the cart totals of the Store API.
add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	[ $method, $own ] = delicja_free_shipping_method();

	$settings = [
		'minimum'         => DELICJA_DELIVERY_MINIMUM,
		'minimumLabel'    => __( 'Minimum order for delivery', 'delicja' ),
		/* translators: 1: minimum order amount, 2: amount still missing */
		'minimumText'     => __( 'For delivery the minimum order is %1$s: add %2$s more, or choose pickup in the shop.', 'delicja' ),
		'threshold'       => $method ? (float) $method->get_option( 'min_amount' ) : 0,
		'ignoreDiscounts' => $method && 'yes' === $method->get_option( 'ignore_discounts' ),
		'label'           => __( 'Free delivery', 'delicja' ),
		/* translators: %s: amount still missing */
		'missing'         => $own ? __( 'Add %s more for free delivery.', 'delicja' ) : __( 'Add %s more for free delivery in Herning.', 'delicja' ),
		'reached'         => $own ? __( 'Your order has free delivery.', 'delicja' ) : __( 'Your order has free delivery in Herning.', 'delicja' ),
	];

	wp_add_inline_script( 'delicja', 'window.dlFreeShipping = ' . wp_json_encode( $settings ) . ';', 'before' );
}, 30 );

/* === Minimum order and delivery day === */

/**
 * Amount still missing to the minimum order for delivery (0 when reached).
 */
function delicja_delivery_missing(): float {
	return max( 0, DELICJA_DELIVERY_MINIMUM - delicja_cart_goods_total() );
}

/**
 * First delivery day (Wednesday or Friday, 15:00) at least 24 hours from now.
 */
function delicja_next_delivery_day( ?DateTimeImmutable $now = null ): DateTimeImmutable {
	$now      = $now ?? new DateTimeImmutable( 'now', wp_timezone() );
	$earliest = $now->modify( '+24 hours' );
	for ( $days = 0; $days < 14; $days++ ) {
		$day = $now->setTime( 15, 0 )->modify( "+{$days} days" );
		if ( in_array( (int) $day->format( 'N' ), [ 3, 5 ], true ) && $day >= $earliest ) {
			return $day;
		}
	}
	return $earliest;
}

/**
 * Delivery day of the current checkout: only for delivery to a postcode in a zone with the delivery round.
 */
function delicja_checkout_delivery_day(): ?DateTimeImmutable {
	if ( 'delivery' !== delicja_fulfilment() || ! WC()->customer || '' === WC()->customer->get_shipping_postcode() ) {
		return null;
	}

	$packages = WC()->cart->get_shipping_packages();
	$zone     = $packages ? WC_Shipping_Zones::get_zone_matching_package( $packages[0] ) : null;
	return $zone && in_array( (int) $zone->get_id(), DELICJA_ROUND_ZONES, true ) ? delicja_next_delivery_day() : null;
}

/**
 * Delivery day for people: "Friday 6 November, 15:00–17:00" in the current language.
 */
function delicja_format_delivery_day( int $timestamp ): string {
	/* translators: PHP date format of the delivery day, e.g. "Friday 6 November" */
	return wp_date( _x( 'l j F', 'delivery day', 'delicja' ), $timestamp ) . ", 15:00\u{2013}17:00";
}

function delicja_delivery_minimum_text(): string {
	return sprintf(
		/* translators: 1: minimum order amount, 2: amount still missing */
		__( 'For delivery the minimum order is %1$s: add %2$s more, or choose pickup in the shop.', 'delicja' ),
		wc_price( DELICJA_DELIVERY_MINIMUM ),
		wc_price( delicja_delivery_missing() )
	);
}

/**
 * Bar towards the minimum order for delivery; empty when it is reached.
 */
function delicja_delivery_minimum_bar(): string {
	$missing = delicja_delivery_missing();
	if ( $missing <= 0 ) {
		return '';
	}

	$percent = (int) min( 100, round( ( DELICJA_DELIVERY_MINIMUM - $missing ) / DELICJA_DELIVERY_MINIMUM * 100 ) );
	return delicja_progress_bar( $percent, delicja_delivery_minimum_text(), false, __( 'Minimum order for delivery', 'delicja' ) );
}

// Order summary, for delivery (updates with the summary): the delivery day once the minimum is reached, then the bars
// for the minimum order and free delivery (free delivery only with a postcode, it depends on the zone), one above
// the other.
add_action( 'woocommerce_review_order_after_shipping', function () {
	if ( 'delivery' !== delicja_fulfilment() || ! WC()->cart->needs_shipping() ) {
		return;
	}

	$day = delicja_delivery_missing() > 0 ? null : delicja_checkout_delivery_day();
	if ( $day ) {
		printf(
			'<tr class="dl-delivery-day"><th>%1$s</th><td data-title="%1$s">%2$s</td></tr>',
			esc_html__( 'Delivery day', 'delicja' ),
			esc_html( delicja_format_delivery_day( $day->getTimestamp() ) )
		);
	}

	$bars = delicja_delivery_minimum_bar();
	if ( '' !== WC()->customer->get_shipping_postcode() ) {
		$bars .= delicja_free_shipping_bar();
	}
	if ( $bars ) {
		echo '<tr class="dl-free-shipping-row"><td colspan="2"><div class="dl-delivery-bars">' . $bars . '</div></td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
} );

// Delivery below the minimum order: the Place order button is disabled (the bar above says why). It is redrawn with
// every update of the order summary, so it comes back once the minimum is reached or pickup is chosen.
add_filter( 'woocommerce_order_button_html', function ( $html ) {
	if ( 'delivery' !== delicja_fulfilment() || ! WC()->cart || ! WC()->cart->needs_shipping() || delicja_delivery_missing() <= 0 ) {
		return $html;
	}

	return preg_replace( '/<button\b/', '<button disabled aria-disabled="true"', $html, 1 );
} );

// No delivery below the minimum order.
add_action( 'woocommerce_after_checkout_validation', function ( $data, $errors ) {
	if ( 'delivery' === delicja_fulfilment() && WC()->cart->needs_shipping() && delicja_delivery_missing() > 0 ) {
		$errors->add( 'dl_delivery_minimum', delicja_delivery_minimum_text() );
	}
}, 20, 2 );

// The order keeps its delivery day.
add_action( 'woocommerce_checkout_create_order', function ( $order ) {
	$day = delicja_checkout_delivery_day();
	if ( $day ) {
		$order->update_meta_data( '_delicja_delivery_date', $day->format( 'Y-m-d' ) );
	}
} );

/**
 * Timestamp of the order's delivery day at 15:00, or null.
 */
function delicja_order_delivery_day( $order ): ?int {
	$date = $order instanceof WC_Order ? (string) $order->get_meta( '_delicja_delivery_date' ) : '';
	$day  = $date ? DateTimeImmutable::createFromFormat( 'Y-m-d H:i', $date . ' 15:00', wp_timezone() ) : false;
	return $day ? $day->getTimestamp() : null;
}

// E-mails, thank-you page and My account: a row "Delivery day" under the shipping.
add_filter( 'woocommerce_get_order_item_totals', function ( $rows, $order ) {
	$day = delicja_order_delivery_day( $order );
	if ( ! $day ) {
		return $rows;
	}

	$row = [
		'dl_delivery_day' => [
			'label' => esc_html__( 'Delivery day', 'delicja' ) . ':',
			'value' => esc_html( delicja_format_delivery_day( $day ) ),
		],
	];
	$at  = array_search( 'shipping', array_keys( $rows ), true );
	return false === $at ? $rows + $row : array_slice( $rows, 0, $at + 1, true ) + $row + array_slice( $rows, $at + 1, null, true );
}, 10, 2 );

// wp-admin order: the delivery day under the shipping address.
add_action( 'woocommerce_admin_order_data_after_shipping_address', function ( $order ) {
	$day = delicja_order_delivery_day( $order );
	if ( $day ) {
		printf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Delivery day', 'delicja' ), esc_html( delicja_format_delivery_day( $day ) ) );
	}
} );
