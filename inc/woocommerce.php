<?php
/**
 * WooCommerce tweaks: product card pieces (badges, add-to-cart button), sale badge text, "Sold out" button,
 * empty "Weekly deals" message.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Largest discount of a product in whole percent (0 when it is not on sale).
 */
function delicja_discount_percent( WC_Product $product ): int {
	$pairs = [];
	if ( $product->is_type( 'variable' ) ) {
		$prices = $product->get_variation_prices( true );
		foreach ( $prices['regular_price'] as $id => $regular ) {
			$pairs[] = [ (float) $regular, (float) ( $prices['sale_price'][ $id ] ?? $regular ) ];
		}
	} else {
		$pairs[] = [ (float) $product->get_regular_price(), (float) $product->get_sale_price() ];
	}

	$best = 0;
	foreach ( $pairs as [ $regular, $sale ] ) {
		if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
			$best = max( $best, (int) round( ( 1 - $sale / $regular ) * 100 ) );
		}
	}

	return $best;
}

// Badges on the product card: discount ("-20%") and product tags as pills linking to the tag page.
// Used in the Loop Item template "Product card".
add_shortcode( 'dl_product_badges', function () {
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		return '';
	}

	$html    = '';
	$percent = delicja_discount_percent( $product );
	if ( $percent > 0 ) {
		$html .= '<span class="dl-badge dl-badge--sale">-' . $percent . '%</span>';
	}

	$tags = get_the_terms( $product->get_id(), 'product_tag' );
	if ( $tags && ! is_wp_error( $tags ) ) {
		foreach ( $tags as $tag ) {
			$html .= '<a class="dl-badge dl-badge--tag" href="' . esc_url( get_term_link( $tag ) ) . '">' . esc_html( $tag->name ) . '</a>';
		}
	}

	return $html ? '<div class="dl-badges">' . $html . '</div>' : '';
} );

// Add-to-cart button on the product card: WooCommerce's own loop button (AJAX, "View cart" link after adding).
add_shortcode( 'dl_add_to_cart', function () {
	global $product;

	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		return '';
	}

	ob_start();
	woocommerce_template_loop_add_to_cart();

	return ob_get_clean();
} );

// "Weekly deals" message when no product is on sale (the Loop Carousel shows nothing then).
add_shortcode( 'dl_no_deals', function () {
	if ( wc_get_product_ids_on_sale() ) {
		return '';
	}

	return '<p class="dl-empty">No deals right now. Check back soon.</p>';
} );

// WooCommerce's own sale flash reads "-20%" instead of "Sale!".
add_filter( 'woocommerce_sale_flash', function ( $html, $post, $product ) {
	$percent = $product instanceof WC_Product ? delicja_discount_percent( $product ) : 0;

	return $percent > 0 ? '<span class="onsale">-' . $percent . '%</span>' : $html;
}, 10, 3 );

// Out-of-stock products say "Sold out" on the button (styled as disabled in site.css).
add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) {
	if ( $product instanceof WC_Product && ! $product->is_in_stock() ) {
		return 'Sold out';
	}

	return $text;
}, 10, 2 );

// WooCommerce block buttons are styled in site.css (=== Buttons ===), as a block theme would, so WooCommerce
// leaves out its fallback button sizing (min-height: 3em).
add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'woocommerce-block-theme-has-button-styles';

	return $classes;
} );
