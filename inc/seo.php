<?php
/**
 * SEO tweaks to Rank Math (meta titles and descriptions are set in Rank Math per page; schema in inc/schema.php).
 * - Deals (/deals/): noindex while nothing is on sale, so search engines do not index an empty page. The menu item
 *   is hidden then too (inc/woocommerce.php).
 * - robots.txt: the rules (including the AI crawlers that may read the shop) are in Rank Math → General → Edit
 *   robots.txt, used once the site is public. Its own text has no Sitemap line, so the sitemap of the current domain
 *   is added here (stays right when test.delicja.dk becomes delicja.dk).
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'rank_math/frontend/robots', function ( $robots ) {
	if ( delicja_is_deals_page() && ! wc_get_product_ids_on_sale() ) {
		$robots['index'] = 'noindex';
	}

	return $robots;
} );

add_filter( 'robots_txt', function ( $output, $is_public ) {
	$sitemap = home_url( '/sitemap_index.xml' );
	if ( $is_public && false === stripos( $output, 'Sitemap:' ) ) {
		$output = rtrim( $output ) . "\n\nSitemap: " . $sitemap . "\n";
	}

	return $output;
}, 100, 2 );
