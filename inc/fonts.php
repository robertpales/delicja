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
