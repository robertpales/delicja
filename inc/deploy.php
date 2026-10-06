<?php
/**
 * Cache purge after a deploy.
 * The GitHub Action writes the commit SHA to .deployed in the theme folder. When it changes,
 * the page cache is cleared once, so cached HTML does not keep pointing at old CSS/JS versions.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	$file = get_stylesheet_directory() . '/.deployed';
	if ( ! is_readable( $file ) ) {
		return;
	}

	$sha = trim( (string) file_get_contents( $file ) );
	if ( '' === $sha || get_option( 'delicja_deployed_sha' ) === $sha ) {
		return;
	}

	update_option( 'delicja_deployed_sha', $sha, false );

	if ( function_exists( 'wpfc_clear_all_cache' ) ) {
		wpfc_clear_all_cache( true );
	}
} );
