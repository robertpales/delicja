<?php
/**
 * WooCommerce tweaks: product card badges, sale badge text, "Udsolgt" button.
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

// Badges on the product card: discount ("-20%") and product tags as pills. Used in the Loop Item template.
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
			$html .= '<span class="dl-badge dl-badge--tag">' . esc_html( $tag->name ) . '</span>';
		}
	}

	return $html ? '<div class="dl-badges">' . $html . '</div>' : '';
} );

// WooCommerce's own sale flash reads "-20%" instead of "Tilbud".
add_filter( 'woocommerce_sale_flash', function ( $html, $post, $product ) {
	$percent = $product instanceof WC_Product ? delicja_discount_percent( $product ) : 0;

	return $percent > 0 ? '<span class="onsale">-' . $percent . '%</span>' : $html;
}, 10, 3 );

// Out-of-stock products say "Udsolgt" on the button (styled as disabled in site.css).
add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) {
	if ( $product instanceof WC_Product && ! $product->is_in_stock() ) {
		return 'Udsolgt';
	}

	return $text;
}, 10, 2 );
