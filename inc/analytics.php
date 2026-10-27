<?php
/**
 * Google Analytics 4 (G-X3VJVFR9B7) is set up in Complianz (Wizard → Consent → Statistics: "Yes, with Google
 * Analytics", added by Complianz): Complianz adds the script and runs it only after the visitor allows statistics in
 * the cookie banner. It collects only on the live site, delicja.dk. On the test site Complianz does not add it, so our
 * own testing stays out of the statistics; add ?dl-analytics=1 to a URL there to check that it works.
 *
 * Microsoft Clarity (project z4by01ybij) is added here, not in Complianz (its wizard takes one statistics tool):
 * nothing is loaded before the visitor allows statistics; then Clarity loads and gets Consent Mode v2 (analytics
 * granted, ads denied). When the consent is revoked, Clarity erases its cookies. Same live-site rule as above.
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
	// requested from Google before the visitor allows statistics. The tag is printed with a normal src: Complianz's
	// script blocker turns it into data-cmplz-src (a data-cmplz-src printed here would be renamed a second time, to
	// data-cmplz-data-cmplz-src, and never load) and moves it back to src on consent.
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
			'<script async type="text/plain" data-category="%s" src="%s"></script>' . "\n",
			esc_attr( $category ),
			esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . $code )
		);
	} );
} );

add_action( 'wp_head', function () {
	if ( ! delicja_is_live_site() && ! isset( $_GET['dl-analytics'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	?>
	<script>
	(function () {
		var id = 'z4by01ybij';
		var loaded = false;
		function clarity() {
			if (typeof window.clarity === 'function') {
				window.clarity.apply(null, arguments);
			}
		}
		function load() {
			if (loaded) {
				return;
			}
			loaded = true;
			window.clarity = window.clarity || function () { (window.clarity.q = window.clarity.q || []).push(arguments); };
			var script = document.createElement('script');
			script.async = true;
			script.src = 'https://www.clarity.ms/tag/' + id;
			document.head.appendChild(script);
		}
		function allowed(event) {
			var categories = event && event.detail && event.detail.categories;
			if (Array.isArray(categories)) {
				return categories.indexOf('statistics') !== -1;
			}
			return typeof window.cmplz_has_consent === 'function' && window.cmplz_has_consent('statistics');
		}
		document.addEventListener('cmplz_fire_categories', function (event) {
			if (allowed(event)) {
				load();
				clarity('consentv2', { ad_Storage: 'denied', analytics_Storage: 'granted' });
			}
		});
		document.addEventListener('cmplz_revoke', function (event) {
			if (loaded && !allowed(event)) {
				clarity('consentv2', { ad_Storage: 'denied', analytics_Storage: 'denied' });
				clarity('consent', false);
			}
		});
	})();
	</script>
	<?php
}, 5 );
