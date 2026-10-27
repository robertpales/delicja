<?php
/**
 * Page cache and customers' personal data.
 *
 * WooCommerce's mini cart block prints the visitor's cart and their billing and shipping address (name, address,
 * e-mail, phone) into the page as Interactivity API state. WP Fastest Cache stored such a page and served it to
 * everyone (found 2026-10-27). Three layers prevent that:
 * - WP Fastest Cache → Exclude: visitors with the cookies wp_woocommerce_session_, woocommerce_items_in_cart or
 *   woocommerce_cart_hash get no cached pages and create none (database, written into .htaccess by the plugin);
 * - here: no page is cached while the visitor has a WooCommerce session (DONOTCACHEPAGE, also respected by other
 *   cache plugins);
 * - here: the addresses are removed from the printed state; the mini cart does not use them.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'template_redirect', function () {
	if ( function_exists( 'WC' ) && WC()->session && WC()->session->has_session() && ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
}, 0 );

add_filter( 'script_module_data_@wordpress/interactivity', function ( $data ) {
	if ( isset( $data['state']['woocommerce']['cart'] ) && is_array( $data['state']['woocommerce']['cart'] ) ) {
		unset( $data['state']['woocommerce']['cart']['billing_address'], $data['state']['woocommerce']['cart']['shipping_address'] );
	}

	return $data;
}, 20 );
