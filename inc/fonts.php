<?php
/**
 * Self-hosted fonts in Elementor.
 * Adds the theme fonts to Elementor's font list as "system" fonts, so Elementor never requests them
 * from Google. The @font-face rules are in assets/css/fonts.css.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'elementor/fonts/additional_fonts', function ( $fonts ) {
	$fonts['Plus Jakarta Sans'] = 'system';
	$fonts['Anton']             = 'system';
	$fonts['Caveat']            = 'system';

	return $fonts;
} );

// Fonts in the Elementor editor panel preview as well.
add_action( 'elementor/editor/after_enqueue_styles', function () {
	$dir = get_stylesheet_directory();
	wp_enqueue_style(
		'delicja-fonts',
		get_stylesheet_directory_uri() . '/assets/css/fonts.css',
		[],
		filemtime( $dir . '/assets/css/fonts.css' )
	);
} );

// The hero headline is the largest text on the homepage's first screen: load its fonts (Anton and the handwritten
// line in Caveat) early.
add_action( 'wp_head', function () {
	if ( ! is_front_page() ) {
		return;
	}
	foreach ( [ 'anton/anton-latin-400-normal.woff2', 'caveat/caveat-latin-wght-normal.woff2' ] as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_stylesheet_directory_uri() . '/assets/fonts/' . $font )
		);
	}
}, 2 );
