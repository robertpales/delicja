<?php
/**
 * Live search in the header (site.js, === Live search ===): the endpoint and the texts for the script. The results
 * come from WooCommerce's Store API (public, read-only), so they show the same products, prices and pant as the shop.
 * The full results page is the template "Product search results" (shop layout, styles in site.css, === Search ===).
 * Product searches (results page and live search) look in the name and the short description only: the long
 * description holds the ingredient lists, which made unrelated products show up first. Names where a word starts
 * with the search ("kie" → Kiełbasa) come before names that only contain it ("ruskie").
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
	];

	wp_add_inline_script( 'delicja', 'window.dlSearch = ' . wp_json_encode( $settings ) . ';', 'before' );
}, 30 );

add_action( 'pre_get_posts', function ( $query ) {
	if ( '' !== (string) $query->get( 's' ) && in_array( 'product', (array) $query->get( 'post_type' ), true ) ) {
		$query->set( 'search_columns', [ 'post_title', 'post_excerpt' ] );
	}
} );

add_filter( 'posts_search_orderby', function ( $orderby, $query ) {
	$search = trim( (string) $query->get( 's' ) );
	if ( '' === $search || ! in_array( 'product', (array) $query->get( 'post_type' ), true ) ) {
		return $orderby;
	}
	global $wpdb;
	$like  = $wpdb->esc_like( $search );
	$start = $wpdb->prepare( "WHEN {$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_title LIKE %s THEN 0", $like . '%', '% ' . $like . '%' );

	return "(CASE {$start} ELSE 1 END)" . ( $orderby ? ', ' . $orderby : '' );
}, 10, 2 );
