<?php
/**
 * Delicja Child – Hello Elementor child theme.
 * Only loads files here. Custom functionality belongs in inc/.
 */

defined( 'ABSPATH' ) || exit;

// Styles and scripts. The version is the file's modification time, so a deploy always busts the browser cache.
add_action( 'wp_enqueue_scripts', function () {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_style(
		'delicja-fonts',
		$uri . '/assets/css/fonts.css',
		[],
		filemtime( $dir . '/assets/css/fonts.css' )
	);

	wp_enqueue_style(
		'delicja',
		$uri . '/assets/css/site.css',
		[ 'delicja-fonts', 'elementor-frontend' ],
		filemtime( $dir . '/assets/css/site.css' )
	);

	// Page CSS: assets/css/pages/<slug>.css, loaded only on the page with that slug. The slug is the English page's,
	// so a translation (WPML) loads the same file as its English original.
	if ( is_singular() ) {
		$slug = get_post_field( 'post_name', delicja_english_id( get_queried_object_id() ) );
		$file = '/assets/css/pages/' . $slug . '.css';
		if ( $slug && file_exists( $dir . $file ) ) {
			wp_enqueue_style( 'delicja-page', $uri . $file, [ 'delicja' ], filemtime( $dir . $file ) );
		}
	}

	wp_enqueue_script(
		'delicja',
		$uri . '/assets/js/site.js',
		[],
		filemtime( $dir . '/assets/js/site.js' ),
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);
}, 20 );

/**
 * ID of the English version of a page or post (with WPML; without it the same ID). Code that looks for a page by
 * its slug uses the English slug.
 */
function delicja_english_id( int $id ): int {
	return (int) apply_filters( 'wpml_object_id', $id, get_post_type( $id ) ?: 'page', true, 'en' );
}

// Custom PHP from inc/ (one file per area).
foreach ( glob( get_stylesheet_directory() . '/inc/*.php' ) as $file ) {
	require_once $file;
}
