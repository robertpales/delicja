<?php
/**
 * Homepage hero: a table with Polish food (Hotspot widget with the picture delicja-hero-scene.webp; a story and a
 * change per product on hover: home.css, Hero; site.js, Hero).
 */

defined( 'ABSPATH' ) || exit;

// The hero picture is on the first screen: load it right away (otherwise all but the first images in the page are
// lazy-loaded). Elementor gives the first image in the page the high fetch priority: keep the table picture first.
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment ) {
	if ( str_starts_with( basename( (string) get_attached_file( $attachment->ID ) ), 'delicja-hero-' ) ) {
		$attr['loading'] = 'eager';
	}

	return $attr;
}, 10, 2 );
