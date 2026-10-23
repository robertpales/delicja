<?php
/**
 * Cart page (Elementor template "Cart" with the Cart widget, set to update the cart automatically):
 * - − and + buttons around each quantity field (site.js, === Cart ===, changes the number; the widget then updates),
 * - a handwritten thank-you under the checkout button,
 * - while the store is in WooCommerce's "Coming soon" mode, a short note that online ordering opens soon.
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
