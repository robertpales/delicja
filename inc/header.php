<?php
/**
 * Header pieces rendered from WordPress / WooCommerce blocks, so they look and work exactly like the
 * approved block theme: product search, account link, mini cart (with its drawer) and the checkout cart link.
 * The Theme Builder header places them with Shortcode widgets. Styles are in site.css (=== Header ===).
 */

defined( 'ABSPATH' ) || exit;

// Product search: the core search block in WooCommerce's product-search variation (adds post_type=product).
add_shortcode( 'dl_product_search', function () {
	return do_blocks( '<!-- wp:search {"label":"Search products","showLabel":false,"placeholder":"Search for sausages, sweets…","width":100,"widthUnit":"%","buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true,"query":{"post_type":"product"},"namespace":"woocommerce/product-search"} /-->' );
} );

// Account icon linking to My account.
add_shortcode( 'dl_account', function () {
	return do_blocks( '<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line"} /-->' );
} );

// Language switcher: three little greetings, Hej! (Danish), Hi! (English), Cześć! (Polish), each with its flag as a
// small round sticker (site.css). With WPML they link to the translations of the current page, the default language
// first. Until WPML is set up they only show the idea: nothing is a link, English (the site's language now) is marked.
add_shortcode( 'dl_lang_switch', function () {
	$greetings = [ 'da' => 'Hej!', 'en' => 'Hi!', 'pl' => 'Cześć!' ];
	$languages = apply_filters( 'wpml_active_languages', null, [ 'skip_missing' => 0 ] );
	$items     = [];

	if ( is_array( $languages ) && $languages ) {
		$default = apply_filters( 'wpml_default_language', null );
		uksort( $languages, fn( $a, $b ) => ( $b === $default ) <=> ( $a === $default ) );
		foreach ( $languages as $code => $language ) {
			if ( empty( $greetings[ $code ] ) ) {
				continue;
			}
			$items[] = sprintf(
				'<a class="dl-lang-item%s" href="%s" lang="%s"%s><span class="dl-lang-word">%s</span><span class="screen-reader-text"> (%s)</span></a>',
				$language['active'] ? ' is-current' : '',
				esc_url( $language['url'] ),
				esc_attr( $code ),
				$language['active'] ? ' aria-current="true"' : '',
				esc_html( $greetings[ $code ] ),
				esc_html( $language['native_name'] )
			);
		}

		return '<nav class="dl-lang" aria-label="' . esc_attr__( 'Language', 'delicja' ) . '">' . implode( '', $items ) . '</nav>';
	}

	foreach ( $greetings as $code => $greeting ) {
		$items[] = sprintf( '<span class="dl-lang-item%s" lang="%s"><span class="dl-lang-word">%s</span></span>', 'en' === $code ? ' is-current' : '', $code, esc_html( $greeting ) );
	}

	return '<div class="dl-lang" aria-hidden="true">' . implode( '', $items ) . '</div>';
} );

// Product categories without products stay out of the menus (Shop dropdown, footer) until they get products, like
// the category tiles.
add_filter( 'wp_nav_menu_objects', function ( $items ) {
	return array_filter( $items, function ( $item ) {
		if ( 'product_cat' !== $item->object ) {
			return true;
		}
		$term = get_term( (int) $item->object_id, 'product_cat' );

		return $term instanceof WP_Term && $term->count > 0;
	} );
} );

// Mini cart: bag icon with the item count and WooCommerce's cart drawer. WooCommerce hides the mini cart from
// visitors while the store is in "Coming soon" mode; then the cart icon is a plain link to the cart page.
add_shortcode( 'dl_mini_cart', function () {
	$html = do_blocks( '<!-- wp:woocommerce/mini-cart {"miniCartIcon":"bag"} /-->' );

	return '' !== trim( $html ) ? $html : do_blocks( '<!-- wp:woocommerce/cart-link {"content":""} /-->' );
} );

// Cart icon linking to the cart, for the checkout header (as in WooCommerce's checkout header).
add_shortcode( 'dl_cart_link', function () {
	return do_blocks( '<!-- wp:woocommerce/cart-link {"content":""} /-->' );
} );

// The blocks above render inside the page body, and Elementor dequeues block styles. Make their styles
// dependencies of site.css, so they always print in <head> and before our own rules.
add_action( 'wp_enqueue_scripts', function () {
	$blocks = plugins_url( 'woocommerce/assets/client/blocks/' );
	$styles = [
		'wp-block-search'                    => includes_url( 'blocks/search/style.min.css' ),
		'woocommerce-customer-account-style' => $blocks . 'woocommerce/customer-account-style.css',
		'woocommerce-mini-cart-style'        => $blocks . 'woocommerce/mini-cart-style.css',
		'wc-blocks-style'                    => $blocks . 'wc-blocks.css',
		'wc-blocks-packages-style'           => $blocks . 'packages-style.css',
		'wc-blocks-style-mini-cart-contents' => $blocks . 'mini-cart-contents.css',
		'wc-blocks-style-cart-link'          => $blocks . 'cart-link.css',
	];
	foreach ( $styles as $handle => $src ) {
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			wp_register_style( $handle, $src );
		}
	}

	// WooCommerce adds the drawer width while rendering the mini cart contents, which here happens after
	// <head> is printed. Same value as the block's default width (480px).
	wp_add_inline_style( 'wc-blocks-style', ':root { --drawer-width: 480px; --neg-drawer-width: calc(var(--drawer-width) * -1); }' );

	$site = wp_styles()->query( 'delicja', 'registered' );
	if ( $site ) {
		$site->deps = array_unique( array_merge( $site->deps, array_keys( $styles ) ) );
	}
}, 20 );

// Shop dropdown in the header: each category shows its picture (the WooCommerce category image) next to its name.
// The menu stays a WordPress menu; only links to product categories in a submenu get a picture.
add_filter( 'nav_menu_item_title', function ( $title, $item, $args, $depth ) {
	if ( $depth < 1 || 'product_cat' !== $item->object ) {
		return $title;
	}

	$thumbnail = (int) get_term_meta( (int) $item->object_id, 'thumbnail_id', true );
	if ( ! $thumbnail ) {
		return $title;
	}

	// Loaded with the page at low priority, so the pictures are there on the first hover.
	$image = wp_get_attachment_image( $thumbnail, 'woocommerce_gallery_thumbnail', false, [
		'class'         => 'dl-menu-thumb',
		'alt'           => '',
		'loading'       => 'eager',
		'fetchpriority' => 'low',
		'decoding'      => 'async',
	] );

	return $image . '<span class="dl-menu-label">' . $title . '</span>';
}, 10, 4 );
