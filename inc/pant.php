<?php
/**
 * Pant: the Danish deposit on drinks in cans and bottles. The client picks the pant class on the product
 * (Product data → General, or several products at once with Bulk edit), from the pant mark on the pack.
 * The price stays without pant, as the price marking rules require ("salgspris … eksklusive eventuelt pant",
 * BEK 1696/2017 § 2): "+ pant 1,00 kr." is shown under the price, and the cart adds one "Pant" line for all items,
 * which also shows in the order, the e-mails and the invoice. Styles in site.css (=== Pant ===).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pant classes with the amount the customer pays (kr., incl. moms), as on the Danish pant mark.
 */
function delicja_pant_classes(): array {
	return [
		'A' => [ 'amount' => 1.00, 'label' => __( 'A – glass bottles and cans under 1 l', 'delicja' ) ],
		'B' => [ 'amount' => 1.50, 'label' => __( 'B – plastic bottles under 1 l', 'delicja' ) ],
		'C' => [ 'amount' => 3.00, 'label' => __( 'C – bottles and cans from 1 l', 'delicja' ) ],
	];
}

/**
 * Pant of one item of a product (kr., incl. moms); 0 without pant.
 */
function delicja_pant_amount( $product ): float {
	if ( ! $product instanceof WC_Product ) {
		return 0.0;
	}

	$class = $product->get_meta( '_delicja_pant' );
	if ( ! $class && $product->get_parent_id() ) {
		$class = get_post_meta( $product->get_parent_id(), '_delicja_pant', true );
	}
	$classes = delicja_pant_classes();

	return isset( $classes[ $class ] ) ? (float) $classes[ $class ]['amount'] : 0.0;
}

/**
 * Pant amount as plain text, e.g. "1,00 kr.".
 */
function delicja_pant_text( float $amount ): string {
	return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
}

/**
 * Options of the pant select: no pant and the classes with their amounts.
 */
function delicja_pant_options(): array {
	$options = [ '' => __( 'No pant', 'delicja' ) ];
	foreach ( delicja_pant_classes() as $key => $class ) {
		$options[ $key ] = sprintf( '%s (%s)', $class['label'], delicja_pant_text( $class['amount'] ) );
	}

	return $options;
}

// Product data → General: the pant select under the prices.
add_action( 'woocommerce_product_options_general_product_data', function () {
	echo '<div class="options_group">';
	woocommerce_wp_select(
		[
			'id'          => '_delicja_pant',
			'label'       => __( 'Pant', 'delicja' ),
			'options'     => delicja_pant_options(),
			'desc_tip'    => true,
			'description' => __( 'Drinks in cans and bottles: the class from the Danish pant mark on the pack. The shop shows "+ pant" under the price and adds it in the cart.', 'delicja' ),
		]
	);
	echo '</div>';
} );

// WooCommerce has checked the product nonce before this runs.
add_action( 'woocommerce_admin_process_product_object', function ( WC_Product $product ) {
	$class = isset( $_POST['_delicja_pant'] ) ? sanitize_text_field( wp_unslash( $_POST['_delicja_pant'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( isset( delicja_pant_classes()[ $class ] ) ) {
		$product->update_meta_data( '_delicja_pant', $class );
	} else {
		$product->delete_meta_data( '_delicja_pant' );
	}
} );

// Bulk edit in the product list: set the pant of several products at once.
add_action( 'woocommerce_product_bulk_edit_end', function () {
	echo '<div class="inline-edit-group"><label class="alignleft"><span class="title">' . esc_html__( 'Pant', 'delicja' ) . '</span>';
	echo '<span class="input-text-wrap"><select class="change_delicja_pant" name="_delicja_pant_bulk">';
	echo '<option value="">' . esc_html__( '— No change —', 'delicja' ) . '</option>';
	foreach ( delicja_pant_options() as $key => $label ) {
		printf( '<option value="%s">%s</option>', esc_attr( '' === $key ? 'none' : $key ), esc_html( $label ) );
	}
	echo '</select></span></label></div>';
} );

// WordPress has checked the bulk edit nonce before this runs.
add_action( 'woocommerce_product_bulk_edit_save', function ( WC_Product $product ) {
	$class = isset( $_REQUEST['_delicja_pant_bulk'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_delicja_pant_bulk'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'none' === $class ) {
		$product->delete_meta_data( '_delicja_pant' );
	} elseif ( isset( delicja_pant_classes()[ $class ] ) ) {
		$product->update_meta_data( '_delicja_pant', $class );
	} else {
		return;
	}
	$product->save();
} );

// "+ pant 1,00 kr." under the price, wherever WooCommerce shows a product price (cards, product page, admin list).
add_filter( 'woocommerce_get_price_html', function ( $html, $product ) {
	$pant = delicja_pant_amount( $product );
	if ( $pant <= 0 || '' === $html ) {
		return $html;
	}

	/* translators: %s: pant amount, e.g. "1,00 kr." */
	return $html . '<span class="dl-pant">' . sprintf( esc_html__( '+ pant %s', 'delicja' ), esc_html( delicja_pant_text( $pant ) ) ) . '</span>';
}, 20, 2 );

// Cart and mini cart: the pant of each item under its name.
add_filter( 'woocommerce_get_item_data', function ( $data, $item ) {
	$pant = delicja_pant_amount( $item['data'] ?? null );
	if ( $pant > 0 ) {
		$data[] = [
			'key'     => __( 'Pant', 'delicja' ),
			/* translators: %s: pant amount, e.g. "1,00 kr." */
			'display' => sprintf( __( '%s each', 'delicja' ), delicja_pant_text( $pant ) ),
		];
	}

	return $data;
}, 10, 2 );

// One "Pant" line in the cart totals. The pant amounts include moms, like the prices, so the fee is added without
// moms and WooCommerce adds the moms back: the customer pays exactly the pant. It is not part of the subtotal, so it
// does not count towards free delivery.
add_action( 'woocommerce_cart_calculate_fees', function ( WC_Cart $cart ) {
	$total = 0.0;
	foreach ( $cart->get_cart() as $item ) {
		$total += delicja_pant_amount( $item['data'] ) * $item['quantity'];
	}
	if ( $total <= 0 ) {
		return;
	}

	$rates   = wc_tax_enabled() ? WC_Tax::get_rates( '', WC()->customer ) : [];
	$taxable = ! empty( $rates );
	$amount  = $taxable && wc_prices_include_tax() ? $total - array_sum( WC_Tax::calc_inclusive_tax( $total, $rates ) ) : $total;

	$cart->add_fee( __( 'Pant', 'delicja' ), $amount, $taxable, '' );
} );

// Order lines keep the pant of each item, for the order page, e-mails and invoice.
add_action( 'woocommerce_checkout_create_order_line_item', function ( $item, $cart_item_key, $values ) {
	$pant = delicja_pant_amount( $values['data'] ?? null );
	if ( $pant > 0 ) {
		/* translators: %s: pant amount, e.g. "1,00 kr." */
		$item->add_meta_data( __( 'Pant', 'delicja' ), sprintf( __( '%s each', 'delicja' ), delicja_pant_text( $pant ) ), true );
	}
}, 10, 3 );
