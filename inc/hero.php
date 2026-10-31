<?php
/**
 * Homepage hero: real Polish products on the red panel (Hotspot widget without hotspots, picture
 * delicja-hero-products-3.webp; home.css, Hero).
 */

defined( 'ABSPATH' ) || exit;

// The hero picture is on the first screen: load it right away (otherwise all but the first images in the page are
// lazy-loaded). Elementor gives the first image in the page the high fetch priority: keep the table picture first.
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment ) {
	$file = basename( (string) get_attached_file( $attachment->ID ) );
	if ( str_starts_with( $file, 'delicja-hero-' ) && ! str_contains( $file, '-after' ) ) {
		$attr['loading'] = 'eager';
	}

	return $attr;
}, 10, 2 );
