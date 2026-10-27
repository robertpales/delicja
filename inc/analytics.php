<?php
/**
 * Google Analytics 4 (G-X3VJVFR9B7) is set up in Complianz (Wizard → Consent → Statistics: "Yes, with Google
 * Analytics", added by Complianz): Complianz adds the script and runs it only after the visitor allows statistics in
 * the cookie banner. It collects only on the live site, delicja.dk. On the test site Complianz does not add it, so our
 * own testing stays out of the statistics; add ?dl-analytics=1 to a URL there to check that it works.
 * Microsoft Clarity is Robo's.
 */

defined( 'ABSPATH' ) || exit;

/**
 * True on the live site, delicja.dk.
 */
function delicja_is_live_site(): bool {
	return in_array( wp_parse_url( home_url(), PHP_URL_HOST ), [ 'delicja.dk', 'www.delicja.dk' ], true );
}

add_action( 'wp', function () {
	if ( delicja_is_live_site() || isset( $_GET['dl-analytics'] ) || ! class_exists( 'COMPLIANZ' ) || empty( COMPLIANZ::$banner_loader ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	remove_action( 'cmplz_statistics_script', [ COMPLIANZ::$banner_loader, 'get_statistics_script' ], 10 );
	remove_action( 'cmplz_before_statistics_script', [ COMPLIANZ::$banner_loader, 'add_gtag_js' ], 10 );
} );
