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

// Message on the Deals page (and the homepage "Weekly deals") when no product is on sale, with a way to the shop.
add_shortcode( 'dl_no_deals', function () {
	if ( wc_get_product_ids_on_sale() ) {
		return '';
	}

	return '<div class="dl-empty dl-shop-empty"><p>No deals right now. Check back soon, or have a look at everything else we have.</p>'
		. '<a class="dl-shop-empty-button" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">See the whole shop</a></div>';
} );

// The homepage "Weekly deals" section only shows while at least one product is on sale (home.css hides it on
// pages with the body class dl-no-deals). It appears by itself when the owner sets a sale price in wp-admin.
add_filter( 'body_class', function ( $classes ) {
	if ( is_front_page() && ! wc_get_product_ids_on_sale() ) {
		$classes[] = 'dl-no-deals';
	}

	return $classes;
} );

/**
 * The Deals page (English slug "deals"): products on sale. 0 when there is no such page.
 */
function delicja_deals_page_id(): int {
	$page = get_page_by_path( 'deals' );

	return $page ? delicja_english_id( (int) $page->ID ) : 0;
}

/**
 * True for the Deals page or one of its translations; $id defaults to the current page.
 */
function delicja_is_deals_page( int $id = 0 ): bool {
	$id = $id ?: ( is_page() ? get_queried_object_id() : 0 );

	return $id && delicja_deals_page_id() === delicja_english_id( $id );
}

// The Deals menu item shows only while at least one product is on sale.
add_filter( 'wp_nav_menu_objects', function ( $items ) {
	$deals = delicja_deals_page_id();
	if ( ! $deals || wc_get_product_ids_on_sale() ) {
		return $items;
	}

	return array_filter( $items, fn ( $item ) => ! ( 'page' === $item->object && delicja_is_deals_page( (int) $item->object_id ) ) );
} );

// Pages are cached (WP Fastest Cache): when sales start or end, or a product is saved, clear the cache so the
// Deals menu item and the homepage "Weekly deals" section appear or disappear right away.
foreach ( [ 'woocommerce_update_product', 'woocommerce_new_product', 'wc_after_products_starting_sales', 'wc_after_products_ending_sales' ] as $hook ) {
	add_action( $hook, function () {
		if ( function_exists( 'wpfc_clear_all_cache' ) ) {
			wpfc_clear_all_cache( true );
		}
	} );
}

// While the store is in WooCommerce's "Coming soon" mode, visitors can already browse: the shop, categories, tags,
// product search, product pages, the cart and the terms are open. Only the checkout stays closed until the mode is off
// (payment and delivery are not set up yet).
add_filter( 'woocommerce_coming_soon_exclude', function ( $excluded ) {
	return $excluded || is_shop() || is_product_taxonomy() || is_product() || is_cart()
		|| ( is_search() && 'product' === get_query_var( 'post_type' ) )
		|| ( wc_terms_and_conditions_page_id() && is_page( wc_terms_and_conditions_page_id() ) );
} );

// The screen WooCommerce shows instead of the shop, category, cart and checkout pages while the store is in
// "Coming soon" mode: the Elementor template "Coming soon (shop)" (Templates → Saved Templates, slug
// delicja-coming-soon), so the owner can edit it. Styles in site.css (=== Coming soon ===).
add_filter( 'render_block', function ( $html, $block ) {
	if ( 'core/pattern' !== $block['blockName'] || 'woocommerce/coming-soon' !== ( $block['attrs']['slug'] ?? '' ) ) {
		return $html;
	}

	$template = get_page_by_path( 'delicja-coming-soon', OBJECT, 'elementor_library' );
	if ( ! $template || ! class_exists( '\Elementor\Plugin' ) ) {
		return $html;
	}

	return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $template->ID, true );
}, 10, 2 );

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

	return '<div class="dl-shop-bar">' . ob_get_clean() . delicja_sort_menu() . '</div>';
} );

// WooCommerce sorts by price, popularity and rating with SQL clauses that it adds only to the main query. The
// Elementor Loop Grid of the shop runs its own copy of that query, which then fell back to newest first. Add the same
// clauses to that copy (once, only to that query).
add_action( 'pre_get_posts', function ( $query ) {
	if ( $query->is_main_query() || ! in_array( 'product', (array) $query->get( 'post_type' ), true ) ) {
		return;
	}

	$orderby   = $query->get( 'orderby' );
	$callbacks = [
		'price'      => 'DESC' === strtoupper( (string) $query->get( 'order' ) ) ? 'order_by_price_desc_post_clauses' : 'order_by_price_asc_post_clauses',
		'popularity' => 'order_by_popularity_post_clauses',
		'rating'     => 'order_by_rating_post_clauses',
	];
	if ( ! is_string( $orderby ) || ! isset( $callbacks[ $orderby ] ) || ! WC()->query ) {
		return;
	}

	$callback = $callbacks[ $orderby ];
	$apply    = function ( $clauses, $current ) use ( $query, $callback, &$apply ) {
		if ( $current !== $query ) {
			return $clauses;
		}
		remove_filter( 'posts_clauses', $apply, 20 );

		return WC()->query->$callback( $clauses );
	};
	add_filter( 'posts_clauses', $apply, 20, 2 );
} );

// Sorting as a small pill that opens a short list of links (site.css, Shop; site.js closes it), instead of
// WooCommerce's select box. Same options and rules as woocommerce_catalog_ordering(), with friendlier names; each
// option is a link to page 1 of the same list, the default order without ?orderby.
function delicja_sort_menu() {
	if ( ! wc_get_loop_prop( 'is_paginated' ) || ! woocommerce_products_will_display() ) {
		return '';
	}

	$names = [
		'relevance'  => __( 'Best match', 'delicja' ),
		'menu_order' => __( 'Our picks', 'delicja' ),
		'popularity' => __( 'Most popular', 'delicja' ),
		'rating'     => __( 'Best rated', 'delicja' ),
		'date'       => __( 'Newest', 'delicja' ),
		'price'      => __( 'Price: low to high', 'delicja' ),
		'price-desc' => __( 'Price: high to low', 'delicja' ),
	];

	$default = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
	$options = apply_filters( 'woocommerce_catalog_orderby', array_slice( $names, 1 ) );
	if ( wc_get_loop_prop( 'is_search' ) ) {
		$options = [ 'relevance' => $names['relevance'] ] + $options;
		unset( $options['menu_order'] );
		$default = 'relevance';
	} elseif ( 'menu_order' !== $default ) {
		unset( $options['menu_order'] );
	}
	if ( ! wc_review_ratings_enabled() ) {
		unset( $options['rating'] );
	}
	if ( ! $options ) {
		return '';
	}

	$current = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default;
	if ( ! isset( $options[ $current ] ) ) {
		$current = array_key_first( $options );
	}

	$base  = remove_query_arg( [ 'orderby', 'paged', 'product-page' ], get_pagenum_link( 1, false ) );
	$items = '';
	foreach ( $options as $key => $label ) {
		$url    = $key === $default ? $base : add_query_arg( 'orderby', $key, $base );
		$items .= sprintf(
			'<li><a class="dl-sort-option%s" href="%s" rel="nofollow"%s>%s</a></li>',
			$key === $current ? ' is-current' : '',
			esc_url( $url ),
			$key === $current ? ' aria-current="true"' : '',
			esc_html( $names[ $key ] ?? $label )
		);
	}

	return sprintf(
		'<details class="dl-sort"><summary class="dl-sort-current"><span class="dl-sort-label">%s</span> %s</summary><ul class="dl-sort-list">%s</ul></details>',
		esc_html__( 'Sort:', 'delicja' ),
		esc_html( $names[ $current ] ?? $options[ $current ] ),
		$items
	);
}

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

	// The search field in the header still holds the query; the category tiles follow under this (template "Product
	// search results", shown only when nothing is found).
	// With a matching category or page above ([dl_search_links]), the note points there.
	$text = delicja_search_link_matches( get_search_query( false ) )
		/* translators: %s: search query */
		? __( 'No product names match “%s”. The category or page above may have what you are looking for.', 'delicja' )
		/* translators: %s: search query */
		: __( 'No products match “%s”. Try another word, or browse the categories below.', 'delicja' );

	return '<div class="dl-empty dl-shop-empty dl-search-empty"><p>'
		. sprintf( esc_html( $text ), esc_html( get_search_query() ) )
		. '</p><a class="dl-shop-empty-button" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'See the whole shop', 'delicja' ) . '</a></div>';
} );

// Empty shop, category or tag page: a short note and a way back to all products.
add_shortcode( 'dl_shop_empty', function () {
	if ( ! empty( $GLOBALS['wp_query']->posts ) ) {
		return '';
	}

	return '<div class="dl-empty dl-shop-empty"><p>Nothing here yet. Have a look at everything else we have.</p>'
		. '<a class="dl-shop-empty-button" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">See the whole shop</a></div>';
} );
