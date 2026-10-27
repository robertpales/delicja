<?php
/**
 * Live search in the header (site.js, === Live search ===): the endpoint and the texts for the script. The results
 * come from WooCommerce's Store API (public, read-only), so they show the same products, prices and pant as the shop.
 * The full results page is the template "Product search results" (shop layout, styles in site.css, === Search ===).
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
