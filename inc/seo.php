<?php
/**
 * SEO tweaks to Rank Math (meta titles and descriptions are set in Rank Math per page; schema in inc/schema.php).
 * - Deals (/deals/): noindex while nothing is on sale, so search engines do not index an empty page. The menu item
 *   is hidden then too (inc/woocommerce.php).
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'rank_math/frontend/robots', function ( $robots ) {
	if ( delicja_is_deals_page() && ! wc_get_product_ids_on_sale() ) {
		$robots['index'] = 'noindex';
	}

	return $robots;
} );
