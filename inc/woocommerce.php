<?php
/**
 * WooCommerce tweaks: product card pieces (badges, add-to-cart button), sale badge text, "Sold out" button,
 * empty "Weekly deals" message, shop bar, search results, related products order, block-style notices and styles.
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

// Product count under the name on the category tile ("6 products"). Elementor's taxonomy Loop Grid keeps the
// current category in $wp_query->loop_term. Empty categories show nothing.
add_shortcode( 'dl_cat_count', function () {
	global $wp_query;

	$term  = $wp_query->loop_term ?? null;
	$count = $term instanceof WP_Term ? (int) $term->count : 0;
	if ( ! $count ) {
		return '';
	}

	/* translators: %s: number of products in the category */
	$text = sprintf( _n( '%s product', '%s products', $count, 'delicja' ), number_format_i18n( $count ) );

	return '<p class="dl-cat-count">' . esc_html( $text ) . '</p>';
} );

// "Weekly deals" message when no product is on sale (the Loop Carousel shows nothing then).
add_shortcode( 'dl_no_deals', function () {
	if ( wc_get_product_ids_on_sale() ) {
		return '';
	}

	return '<p class="dl-empty">No deals right now. Check back soon.</p>';
} );

// The homepage "Weekly deals" section only shows while at least one product is on sale (home.css hides it on
// pages with the body class dl-no-deals). It appears by itself when the owner sets a sale price in wp-admin.
add_filter( 'body_class', function ( $classes ) {
	if ( is_front_page() && ! wc_get_product_ids_on_sale() ) {
		$classes[] = 'dl-no-deals';
	}

	return $classes;
} );

// Product pages stay visible while the store is in WooCommerce's "Coming soon" mode, so a click on a product
// anywhere on the site shows the product. Shop, category, cart and checkout pages stay hidden until the mode is off.
add_filter( 'woocommerce_coming_soon_exclude', function ( $excluded ) {
	return $excluded || is_product();
} );

// WooCommerce's own sale flash reads "-20%" instead of "Sale!".
add_filter( 'woocommerce_sale_flash', function ( $html, $post, $product ) {
	$percent = $product instanceof WC_Product ? delicja_discount_percent( $product ) : 0;

	return $percent > 0 ? '<span class="onsale">-' . $percent . '%</span>' : $html;
}, 10, 3 );

// "Added to your cart" message as a small card: the product's picture, its name and a "View cart" button
// (site.css, === WooCommerce blocks ===). Several products at once keep WooCommerce's own message.
add_filter( 'wc_add_to_cart_message_html', function ( $message, $products ) {
	if ( 1 !== count( $products ) ) {
		return $message;
	}

	$product = wc_get_product( (int) array_key_first( $products ) );
	if ( ! $product ) {
		return $message;
	}

	$quantity = (float) reset( $products );
	$name     = ( $quantity > 1 ? wc_stock_amount( $quantity ) . ' × ' : '' ) . $product->get_name();

	return '<span class="dl-added">'
		. '<span class="dl-added-image">' . $product->get_image( 'woocommerce_gallery_thumbnail', [ 'alt' => '' ] ) . '</span>'
		. '<span class="dl-added-text"><strong class="dl-added-title">Added to your cart</strong>'
		. '<span class="dl-added-name">' . esc_html( $name ) . '</span></span>'
		. '<a href="' . esc_url( wc_get_cart_url() ) . '" class="dl-added-button">View cart</a>'
		. '</span>';
}, 10, 2 );

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

// Result count and sorting above the product grid on shop, category and tag pages (Products Archive template).
add_shortcode( 'dl_shop_bar', function () {
	wc_setup_loop();

	ob_start();
	woocommerce_result_count();
	woocommerce_catalog_ordering();

	return '<div class="dl-shop-bar">' . ob_get_clean() . '</div>';
} );

// Notices ("added to your cart", errors) use WooCommerce's block notice banners, as in a block theme.
// The Elementor Notices widget moves them into place.
add_filter( 'woocommerce_use_block_notices_in_classic_theme', '__return_true' );

// WooCommerce's store notices block for the cart and checkout pages, as in WooCommerce's page templates.
// The Elementor Notices widget would move the cart's and checkout's own messages out of them.
add_shortcode( 'dl_store_notices', function () {
	return do_blocks( '<!-- wp:woocommerce/store-notices /-->' );
} );

// WooCommerce's styles for block themes (forms, account pages, order details). The block theme version of the
// site had them; WooCommerce loads them only in block themes. Printed before WooCommerce's own stylesheets, as
// in a block theme (WooCommerce's rules win ties), or else before site.css.
add_action( 'wp_enqueue_scripts', function () {
	if ( ! wp_style_is( 'woocommerce-blocktheme', 'registered' ) ) {
		wp_register_style( 'woocommerce-blocktheme', WC()->plugin_url() . '/assets/css/woocommerce-blocktheme.css', [], WC_VERSION );
	}

	$before = wp_style_is( 'woocommerce-layout' ) ? 'woocommerce-layout' : 'delicja';
	$style  = wp_styles()->query( $before, 'registered' );
	if ( $style ) {
		$style->deps[] = 'woocommerce-blocktheme';
	}
}, 20 );

// Related products on the product page: the first in-stock products by name instead of a random pick,
// like the block theme's related products.
add_filter( 'woocommerce_product_related_posts_shuffle', '__return_false' );

add_filter( 'woocommerce_related_products', function ( $ids ) {
	if ( ! $ids ) {
		return $ids;
	}

	return get_posts( [
		'post_type'      => 'product',
		'post__in'       => $ids,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => [
			[
				'key'     => '_stock_status',
				'value'   => [ 'instock', 'onbackorder' ],
				'compare' => 'IN',
			],
		],
	] );
} );

// Product search results: the title and the empty result as in the block theme (WooCommerce's search template).
add_filter( 'elementor/utils/get_the_archive_title', function ( $title ) {
	if ( is_search() ) {
		return sprintf( 'Search results for: &#8220;%s&#8221;', get_search_query() );
	}

	return $title;
} );

add_shortcode( 'dl_search_empty', function () {
	if ( ! empty( $GLOBALS['wp_query']->posts ) ) {
		return '';
	}

	return '<div class="dl-search-empty"><p>No products were found matching your selection.</p>'
		. do_blocks( '<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search products…","buttonText":"Search","query":{"post_type":"product"}} /-->' )
		. '</div>';
} );

// Empty shop, category or tag page: a short note and a way back to all products.
add_shortcode( 'dl_shop_empty', function () {
	if ( ! empty( $GLOBALS['wp_query']->posts ) ) {
		return '';
	}

	return '<div class="dl-empty dl-shop-empty"><p>Nothing here yet. Have a look at everything else we have.</p>'
		. '<a class="dl-shop-empty-button" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">See the whole shop</a></div>';
} );
