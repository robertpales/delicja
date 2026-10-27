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
	if ( ! class_exists( 'COMPLIANZ' ) || empty( COMPLIANZ::$banner_loader ) ) {
		return;
	}
	$loader = COMPLIANZ::$banner_loader;

	// Complianz loads gtag.js right away and only holds back the config. Hold back the library too, so nothing is
	// requested from Google before the visitor allows statistics (Complianz moves data-cmplz-src to src on consent).
	remove_action( 'cmplz_before_statistics_script', [ $loader, 'add_gtag_js' ], 10 );

	if ( ! delicja_is_live_site() && ! isset( $_GET['dl-analytics'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		remove_action( 'cmplz_statistics_script', [ $loader, 'get_statistics_script' ], 10 );

		return;
	}

	add_action( 'cmplz_before_statistics_script', function () use ( $loader ) {
		$code = cmplz_get_option( 'ua_code' );
		if ( 'google-analytics' !== cmplz_get_option( 'compile_statistics' ) || 'yes' !== cmplz_get_option( 'configuration_by_complianz' ) || ! $code ) {
			return;
		}
		$category = method_exists( $loader, 'get_statistics_category' ) ? $loader->get_statistics_category() : 'statistics';

		printf(
			'<script async type="text/plain" data-category="%s" data-cmplz-src="%s"></script>' . "\n",
			esc_attr( $category ),
			esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $code )
		);
	} );
} );
