<?php
/**
 * Live search in the header (site.js, === Live search ===): the endpoint and the texts for the script. The results
 * come from WooCommerce's Store API (public, read-only), so they show the same products, prices and pant as the shop.
 * The full results page is the template "Product search results" (shop layout, styles in site.css, === Search ===).
 * Product searches (results page and live search) look in the name and the short description only: the long
 * description holds the ingredient lists, which made unrelated products show up first. Names where a word starts
 * with the search ("kie" → Kiełbasa) come before names that only contain it ("ruskie").
 * Categories and information pages that match the search are shown above the products, on the results page
 * ([dl_search_links] in the template) and in the live search: a category by its name, a page by its name or by the
 * words people type when they look for delivery, returns, contact and the like (delicja_search_links()). Styles in
 * site.css (=== Search ===).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
	$settings = [
		'endpoint'    => rest_url( 'wc/store/v1/products' ),
		'placeholder' => function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'thumbnail' ) : '',
		'label'       => __( 'Products', 'delicja' ),
		/* translators: %d: number of products found */
		'all'         => __( 'See all results (%d)', 'delicja' ),
		'empty'       => __( 'No products found. Try another word.', 'delicja' ),
		'linksLabel'  => __( 'Categories and pages', 'delicja' ),
		'links'       => array_map( fn( $link ) => array_intersect_key( $link, array_flip( [ 'type', 'name', 'url', 'terms' ] ) ), delicja_search_links() ),
	];

	wp_add_inline_script( 'delicja', 'window.dlSearch = ' . wp_json_encode( $settings ) . ';', 'before' );
}, 30 );

add_action( 'pre_get_posts', function ( $query ) {
	if ( '' !== (string) $query->get( 's' ) && in_array( 'product', (array) $query->get( 'post_type' ), true ) ) {
		$query->set( 'search_columns', [ 'post_title', 'post_excerpt' ] );
	}
} );

// On the ORDER BY itself, for both searches: the Store API (live search) uses its own query var "search" (name and
// SKU) and sorts by date, so WordPress's search order is not used there.
add_filter( 'posts_orderby', function ( $orderby, $query ) {
	$search = trim( (string) ( $query->get( 's' ) ?: $query->get( 'search' ) ) );
	// Only for the default order (best match, or the Store API's newest first), not when the customer sorts by price.
	$sort = $query->get( 'orderby' );
	if ( '' === $search || ! in_array( 'product', (array) $query->get( 'post_type' ), true ) || ! in_array( $sort, [ '', 'relevance', 'date', 'date ID' ], true ) ) {
		return $orderby;
	}
	global $wpdb;
	$like  = $wpdb->esc_like( $search );
	$start = $wpdb->prepare( "WHEN {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_title LIKE %s THEN 0", $like . '%', '% ' . $like . '%' );

	return "(CASE {$start} ELSE 1 END)" . ( $orderby ? ', ' . $orderby : '' );
}, 10, 2 );

/**
 * Lower case without accents, so "Kiełbasa" and "kielbasa", "Åbningstider" and "abningstider" match.
 */
function delicja_search_normalize( string $text ): string {
	return trim( mb_strtolower( remove_accents( $text ) ) );
}

/**
 * Shop categories and information pages the search points to: type label, name, URL, number of products (categories)
 * and the words they are found by, normalized. Page words in English, Danish and Polish.
 */
function delicja_search_links(): array {
	static $links = null;
	if ( null !== $links ) {
		return $links;
	}

	$links = [];
	$terms = get_terms( [
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'exclude'    => [ (int) get_option( 'default_product_cat' ) ],
	] );
	foreach ( is_array( $terms ) ? $terms : [] as $term ) {
		$links[] = [
			'type'  => __( 'Category', 'delicja' ),
			'name'  => $term->name,
			'url'   => get_term_link( $term ),
			'count' => (int) $term->count,
			'terms' => [ delicja_search_normalize( $term->name ) ],
		];
	}

	$pages = [
		'delivery-and-returns' => [ 'delivery', 'deliver', 'shipping', 'ship', 'free delivery', 'returns', 'return', 'refund', 'pickup', 'pick up', 'collect', 'levering', 'fragt', 'retur', 'returnering', 'afhentning', 'dostawa', 'wysyłka', 'zwrot', 'odbiór' ],
		'contact'              => [ 'contact', 'phone', 'call', 'email', 'e-mail', 'opening hours', 'hours', 'open', 'address', 'directions', 'kontakt', 'telefon', 'åbningstider', 'adresse', 'godziny otwarcia', 'adres' ],
		'about'                => [ 'about', 'story', 'who we are', 'om os', 'o nas' ],
		'terms-and-conditions' => [ 'terms', 'conditions', 'payment', 'pay', 'withdrawal', 'complaint', 'handelsbetingelser', 'betaling', 'fortrydelsesret', 'reklamation', 'regulamin', 'płatność', 'reklamacja' ],
		'privacy-policy'       => [ 'privacy', 'cookies', 'gdpr', 'personal data', 'privatlivspolitik', 'persondata', 'prywatność' ],
	];
	foreach ( $pages as $slug => $words ) {
		$page = get_page_by_path( $slug );
		if ( ! $page || 'publish' !== $page->post_status ) {
			continue;
		}
		$name    = get_the_title( $page );
		$links[] = [
			'type'  => __( 'Page', 'delicja' ),
			'name'  => $name,
			'url'   => get_permalink( $page ),
			'count' => 0,
			'terms' => array_map( 'delicja_search_normalize', array_merge( [ $name ], $words ) ),
		];
	}

	return $links;
}

/**
 * Links whose words match the search: a word of the link starts with the search (from 2 letters for a category name,
 * 3 for the rest), or the search contains one of the link's words.
 */
function delicja_search_link_matches( string $search ): array {
	$search = delicja_search_normalize( $search );
	if ( mb_strlen( $search ) < 2 ) {
		return [];
	}

	return array_values( array_filter( delicja_search_links(), function ( $link ) use ( $search ) {
		foreach ( $link['terms'] as $i => $term ) {
			$min = 0 === $i ? 2 : 3;
			if ( mb_strlen( $search ) >= $min && preg_match( '/(^|\s)' . preg_quote( $search, '/' ) . '/u', $term ) ) {
				return true;
			}
			if ( mb_strlen( $term ) >= 3 && preg_match( '/(^|\s)' . preg_quote( $term, '/' ) . '(\s|$)/u', $search ) ) {
				return true;
			}
		}
		return false;
	} ) );
}

// Results page: the matching categories and pages above the products.
add_shortcode( 'dl_search_links', function () {
	$links = is_search() ? delicja_search_link_matches( get_search_query( false ) ) : [];
	if ( ! $links ) {
		return '';
	}

	$html = '';
	foreach ( $links as $link ) {
		$info = $link['count']
			/* translators: %d: number of products in the category */
			? sprintf( _n( '%d product', '%d products', $link['count'], 'delicja' ), $link['count'] )
			: '';
		$html .= sprintf(
			'<li><a class="dl-search-link" href="%1$s"><span class="dl-search-link-type">%2$s</span><span class="dl-search-link-name">%3$s</span>%4$s</a></li>',
			esc_url( $link['url'] ),
			esc_html( $link['type'] ),
			esc_html( $link['name'] ),
			$info ? '<span class="dl-search-link-info">' . esc_html( $info ) . '</span>' : ''
		);
	}

	return '<nav class="dl-search-links" aria-label="' . esc_attr__( 'Categories and pages', 'delicja' ) . '"><ul>' . $html . '</ul></nav>';
} );

