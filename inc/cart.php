<?php
/**
 * Cart page (Elementor template "Cart" with the Cart widget, set to update the cart automatically):
 * - − and + buttons around each quantity field (site.js, === Cart ===, changes the number; the widget then updates),
 * - a handwritten thank-you under the checkout button,
 * - while the store is in WooCommerce's "Coming soon" mode, a short note that online ordering opens soon,
 * - WooCommerce's empty cart message as plain text (hidden by site.css; the template has its own empty cart).
 * The heading and the empty cart (an empty crate that fills up on hover) are sections of the Elementor template, so the
 * owner can edit them; site.css shows one or the other (=== Cart page ===).
 */

defined( 'ABSPATH' ) || exit;

/**
 * True while visitors cannot order yet: the store is in "Coming soon" mode and the visitor is not a shop manager.
 */
function delicja_store_closed(): bool {
	return 'yes' === get_option( 'woocommerce_coming_soon' ) && ! current_user_can( 'manage_woocommerce' );
}

add_action( 'woocommerce_before_quantity_input_field', function () {
	if ( is_cart() ) {
		echo '<button type="button" class="dl-qty-button dl-qty-minus" aria-label="Remove one"></button>';
	}
} );

add_action( 'woocommerce_after_quantity_input_field', function () {
	if ( is_cart() ) {
		echo '<button type="button" class="dl-qty-button dl-qty-plus" aria-label="Add one more"></button>';
	}
} );

add_action( 'woocommerce_proceed_to_checkout', function () {
	if ( delicja_store_closed() ) {
		echo '<p class="dl-cart-soon">Online ordering opens very soon. Until then, come and shop with us at Bethaniagade 5 in Herning.</p>';
	}
}, 5 );

add_action( 'woocommerce_proceed_to_checkout', function () {
	echo '<p class="dl-cart-thanks">Dziękujemy – thank you!</p>';
}, 30 );

// The empty cart message without WooCommerce's notice box (the template shows its own empty cart). As a notice,
// WooCommerce's cart script would copy it to the top of the page when the last product is removed.
add_action( 'wp', function () {
	if ( ! is_cart() ) {
		return;
	}

	remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
	add_action( 'woocommerce_cart_is_empty', function () {
		echo '<div class="wc-empty-cart-message"><p class="cart-empty">' . esc_html__( 'Your cart is currently empty.', 'woocommerce' ) . '</p></div>';
	} );
} );
