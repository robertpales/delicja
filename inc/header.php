<?php
/**
 * Header pieces rendered from WordPress / WooCommerce blocks, so they look and work exactly like the
 * approved block theme: product search, account link and mini cart (with its drawer).
 * The Theme Builder header places them with Shortcode widgets. Styles are in site.css (=== Header ===).
 */

defined( 'ABSPATH' ) || exit;

// Product search: the core search block in WooCommerce's product-search variation (adds post_type=product).
add_shortcode( 'dl_product_search', function () {
	return do_blocks( '<!-- wp:search {"label":"Search products","showLabel":false,"placeholder":"Search products…","width":100,"widthUnit":"%","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"query":{"post_type":"product"},"namespace":"woocommerce/product-search"} /-->' );
} );

// Account icon linking to My account.
add_shortcode( 'dl_account', function () {
	return do_blocks( '<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line"} /-->' );
} );

// Mini cart: bag icon with the item count and WooCommerce's cart drawer.
add_shortcode( 'dl_mini_cart', function () {
	return do_blocks( '<!-- wp:woocommerce/mini-cart {"miniCartIcon":"bag"} /-->' );
} );

// The blocks above render inside the page body; load their styles in <head> so nothing flashes unstyled.
add_action( 'wp_enqueue_scripts', function () {
	foreach ( [ 'wp-block-search', 'woocommerce-customer-account-style', 'woocommerce-mini-cart-style', 'wc-blocks-style', 'wc-blocks-style-mini-cart-contents', 'wc-blocks-packages-style' ] as $handle ) {
		if ( wp_style_is( $handle, 'registered' ) ) {
			wp_enqueue_style( $handle );
		}
	}
}, 15 );
