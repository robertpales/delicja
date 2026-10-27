<?php
/**
 * Schema.org structured data (JSON-LD).
 *
 * One @graph script per template, linked by @id:
 * - every page: Organization (GroceryStore + OnlineStore), WebSite with SearchAction
 * - pages: WebPage / AboutPage / ContactPage + BreadcrumbList
 * - product: Product + Offer + ItemPage + BreadcrumbList
 * - shop and product categories: CollectionPage + ItemList + BreadcrumbList
 * Rank Math's and WooCommerce's own copies of these types are switched off at the bottom.
 *
 * Prices, stock, categories, payment methods and dates are read live from WooCommerce.
 * Company data is in dl_schema_config() only.
 *
 * Left out on purpose (unknown, never invent – add here when the owner confirms):
 * - slogan: no official slogan; the site tagline is used as the description instead.
 * - founder / employees / Person: unknown.
 * - aggregateRating / review: product reviews are disabled and there are no shop ratings.
 * - priceRange: not set; makesOffer carries the live min/max price instead.
 * - OfferShippingDetails: delivery prices and zones are not decided yet (own delivery,
 *   possibly priced by distance). The free shipping set in WooCommerce is a test setting.
 * - MerchantReturnPolicy: the returns page (levering-og-returnering) is still [TODO];
 *   perishable food usually has no right of withdrawal, so wait for the owner's terms.
 * - Product brand / countryOfOrigin: only output when the product has a WooCommerce brand
 *   (product_brand) or a "Country of origin" attribute. Products are third-party Polish brands,
 *   so Delicja is never used as the brand or manufacturer.
 * - gtin: only output when the product's GTIN field is filled in (none are yet).
 * - Wikipedia links (additionalType, sameAs): no article about Delicja exists.
 * - availableLanguage / knowsLanguage: languages spoken in the shop and on the phone unknown.
 */

defined( 'ABSPATH' ) || exit;

/** Opening hours from delicja_opening_hours() (inc/pickup.php), one entry per group of days with the same hours. */
function dl_schema_opening_hours() {
	if ( ! function_exists( 'delicja_opening_hours' ) ) {
		return null;
	}
	$names = [ 1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];
	$specs = [];
	foreach ( delicja_opening_hours() as $n => $hours ) {
		if ( ! $hours ) {
			continue;
		}
		$key = implode( '-', $hours );
		if ( ! isset( $specs[ $key ] ) ) {
			$specs[ $key ] = [ '@type' => 'OpeningHoursSpecification', 'dayOfWeek' => [], 'opens' => $hours[0], 'closes' => $hours[1] ];
		}
		$specs[ $key ]['dayOfWeek'][] = 'https://schema.org/' . $names[ $n ];
	}
	return array_values( $specs );
}

/** Company data and @id map. The only place where @ids are defined. */
function dl_schema_config() {
	static $cfg = null;
	if ( null !== $cfg ) {
		return $cfg;
	}

	$base = trailingslashit( home_url( '/' ) );

	$cfg = [
		'base' => $base,
		'lang' => get_bloginfo( 'language' ),
		'id'   => [
			'org'     => $base . '#organization',
			'website' => $base . '#website',
			'logo'    => $base . '#logo',
			'address' => $base . '#address',
			'place'   => $base . '#place',
			'catalog' => $base . '#catalog',
		],

		// Official data: CVR register (datacvr.virk.dk) and EU VIES, checked 2026-10-23.
		'types'      => [ 'GroceryStore', 'OnlineStore' ],
		'name'       => 'Delicja',
		'legal_name' => 'Delicja ApS',
		'cvr'        => '45266575',
		'vat_id'     => 'DK45266575',
		'founding'   => '2024-12-10',
		'phone'      => '+4551430321',
		'email'      => 'info@delicja.dk',
		'logo_id'    => 45,
		'address'    => [
			'streetAddress'   => 'Bethaniagade 5',
			'addressLocality' => 'Herning',
			'addressRegion'   => 'Region Midtjylland',
			'postalCode'      => '7400',
			'addressCountry'  => 'DK',
		],
		// OpenStreetMap, house-level match for Bethaniagade 5, 7400 Herning.
		'geo'        => [ 56.137038, 8.973008 ],
		'same_as'    => [
			'https://www.instagram.com/delicja.dk/',
		],
		'additional_type' => [
			'https://en.wikipedia.org/wiki/Grocery_store',
			'https://en.wikipedia.org/wiki/Delicatessen',
			'https://en.wikipedia.org/wiki/Polish_cuisine',
		],
		// [TODO] Delivery area: own delivery from the shop, area not decided. Herning only for now.
		'area_served' => [
			[ '@type' => 'City', 'name' => 'Herning', 'sameAs' => 'https://en.wikipedia.org/wiki/Herning' ],
		],
		'knows_about' => [
			'Polish cuisine',
			'Polish delicatessen',
			'Polish groceries in Denmark',
			'Kiełbasa',
			'Kabanos',
			'Pierogi',
			'Żurek',
			'Barszcz',
			'Twaróg',
			'Kefir',
			'Sauerkraut',
			'Pickled cucumbers',
			'Herring',
			'Ptasie mleczko',
			'Krówki',
			'Halva',
			'Polish mustard and horseradish',
			'Polish mayonnaise',
			'Polish sweets and snacks',
		],
	];

	return $cfg;
}

/** Removes empty values (null, '', []) recursively; lists stay lists. */
function dl_schema_clean( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}
	$is_list = array_is_list( $data );
	foreach ( $data as $k => $v ) {
		$v = dl_schema_clean( $v );
		if ( null === $v || '' === $v || [] === $v ) {
			unset( $data[ $k ] );
		} else {
			$data[ $k ] = $v;
		}
	}
	return $is_list ? array_values( $data ) : $data;
}

function dl_schema_print( array $graph ) {
	$data = [
		'@context' => 'https://schema.org',
		'@graph'   => dl_schema_clean( $graph ),
	];
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

/** SEO title and meta description from Rank Math, with WordPress fallbacks. */
function dl_schema_seo_title() {
	return wp_strip_all_tags( html_entity_decode( wp_get_document_title(), ENT_QUOTES ) );
}

function dl_schema_seo_description( $fallback = '' ) {
	$desc = '';
	if ( class_exists( '\RankMath\Paper\Paper' ) ) {
		$desc = \RankMath\Paper\Paper::get()->get_description();
	}
	return wp_strip_all_tags( $desc ?: $fallback );
}

/** Breadcrumb: Home › (shop) › (parent categories) › current. $trail = [ [ name, url ], ... ]. */
function dl_schema_breadcrumb( $id, array $trail ) {
	$front = (int) get_option( 'page_on_front' );
	$items = [ [ $front ? get_the_title( $front ) : get_bloginfo( 'name' ), home_url( '/' ) ] ];
	$items = array_merge( $items, $trail );

	$list = [];
	foreach ( $items as $i => $item ) {
		$list[] = [
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => wp_strip_all_tags( html_entity_decode( $item[0], ENT_QUOTES ) ),
			// The last item is the current page and has no link.
			'item'     => $i < count( $items ) - 1 ? $item[1] : null,
		];
	}
	return [
		'@type'           => 'BreadcrumbList',
		'@id'             => $id,
		'itemListElement' => $list,
	];
}

/** Shop page and the category chain (deepest last) as breadcrumb items. */
function dl_schema_shop_trail( $term = null ) {
	$trail = [];
	$shop  = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'shop' ) : 0;
	if ( $shop > 0 ) {
		$trail[] = [ get_the_title( $shop ), get_permalink( $shop ) ];
	}
	if ( $term ) {
		foreach ( array_reverse( get_ancestors( $term->term_id, 'product_cat' ) ) as $anc_id ) {
			$anc     = get_term( $anc_id, 'product_cat' );
			$trail[] = [ $anc->name, get_term_link( $anc ) ];
		}
		$trail[] = [ $term->name, get_term_link( $term ) ];
	}
	return $trail;
}

/** Enabled WooCommerce payment methods as PaymentMethod entities. */
function dl_schema_payment_methods() {
	if ( ! function_exists( 'WC' ) || ! WC()->payment_gateways() ) {
		return [];
	}
	$methods = [];
	foreach ( WC()->payment_gateways()->payment_gateways() as $gateway ) {
		if ( 'yes' === $gateway->enabled ) {
			$methods[] = [ '@type' => 'PaymentMethod', 'name' => wp_strip_all_tags( $gateway->get_title() ) ];
		}
	}
	return $methods;
}

/* === Every page: Organization and WebSite === */

add_action( 'wp_head', function () {
	$c     = dl_schema_config();
	$logo  = wp_get_attachment_image_url( $c['logo_id'], 'full' );
	$meta  = wp_get_attachment_metadata( $c['logo_id'] );

	$org = [
		'@type'           => array_merge( $c['types'], [ 'Organization' ] ),
		'@id'             => $c['id']['org'],
		'name'            => $c['name'],
		'legalName'       => $c['legal_name'],
		'url'             => $c['base'],
		'description'     => get_bloginfo( 'description' ),
		'logo'            => [
			'@type'      => 'ImageObject',
			'@id'        => $c['id']['logo'],
			'url'        => $logo,
			'contentUrl' => $logo,
			'caption'    => $c['name'],
			'width'      => $meta['width'] ?? null,
			'height'     => $meta['height'] ?? null,
		],
		'image'           => [ '@id' => $c['id']['logo'] ],
		'telephone'       => $c['phone'],
		'email'           => $c['email'],
		'foundingDate'    => $c['founding'],
		'vatID'           => $c['vat_id'],
		'taxID'           => $c['cvr'],
		'identifier'      => [
			'@type'      => 'PropertyValue',
			'propertyID' => 'CVR',
			'value'      => $c['cvr'],
		],
		'address'         => [ '@type' => 'PostalAddress', '@id' => $c['id']['address'] ] + $c['address'],
		'location'        => [
			'@type'   => 'Place',
			'@id'     => $c['id']['place'],
			'address' => [ '@id' => $c['id']['address'] ],
			'geo'     => [ '@type' => 'GeoCoordinates', 'latitude' => $c['geo'][0], 'longitude' => $c['geo'][1] ],
			'hasMap'  => 'https://www.google.com/maps/search/?api=1&query=' . $c['geo'][0] . ',' . $c['geo'][1],
		],
		'geo'             => [ '@type' => 'GeoCoordinates', 'latitude' => $c['geo'][0], 'longitude' => $c['geo'][1] ],
		'hasMap'          => 'https://www.google.com/maps/search/?api=1&query=' . $c['geo'][0] . ',' . $c['geo'][1],
		'openingHoursSpecification' => dl_schema_opening_hours(),
		'contactPoint'    => [
			'@type'             => 'ContactPoint',
			'contactType'       => 'customer service',
			'telephone'         => $c['phone'],
			'email'             => $c['email'],
			'areaServed'        => 'DK',
			'url'               => get_permalink( get_page_by_path( 'kontakt' ) ) ?: null,
		],
		'areaServed'      => $c['area_served'],
		'knowsAbout'      => $c['knows_about'],
		'additionalType'  => $c['additional_type'],
		'sameAs'          => $c['same_as'],
		'currenciesAccepted' => function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : null,
		'paymentAccepted' => implode( ', ', wp_list_pluck( dl_schema_payment_methods(), 'name' ) ),
	];

	// Shop: catalogue from the top-level categories that have products, live price range.
	if ( class_exists( 'WooCommerce' ) ) {
		$cats = get_terms( [
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'exclude'    => [ (int) get_option( 'default_product_cat' ) ],
		] );
		if ( $cats && ! is_wp_error( $cats ) ) {
			$org['hasOfferCatalog'] = [
				'@type'           => 'OfferCatalog',
				'@id'             => $c['id']['catalog'],
				'name'            => 'Polish groceries',
				'url'             => get_permalink( wc_get_page_id( 'shop' ) ),
				'itemListElement' => array_map( fn( $t ) => [
					'@type'         => 'OfferCatalog',
					'name'          => html_entity_decode( $t->name, ENT_QUOTES ),
					'url'           => get_term_link( $t ),
					'numberOfItems' => (int) $t->count,
				], array_values( $cats ) ),
			];
		}

		global $wpdb;
		$range = $wpdb->get_row( "SELECT MIN(l.min_price) AS mn, MAX(l.max_price) AS mx FROM {$wpdb->prefix}wc_product_meta_lookup l JOIN {$wpdb->posts} p ON p.ID = l.product_id WHERE p.post_status = 'publish' AND l.min_price > 0" );
		if ( $range && $range->mn ) {
			$org['makesOffer'] = [
				'@type'              => 'Offer',
				'name'               => 'Polish groceries',
				'priceSpecification' => [
					'@type'         => 'PriceSpecification',
					'minPrice'      => (float) $range->mn,
					'maxPrice'      => (float) $range->mx,
					'priceCurrency' => get_woocommerce_currency(),
				],
			];
		}
	}

	$search = class_exists( 'WooCommerce' ) ? '?s={search_term_string}&post_type=product' : '?s={search_term_string}';

	$website = [
		'@type'           => 'WebSite',
		'@id'             => $c['id']['website'],
		'url'             => $c['base'],
		'name'            => $c['name'],
		'description'     => get_bloginfo( 'description' ),
		'inLanguage'      => $c['lang'],
		'publisher'       => [ '@id' => $c['id']['org'] ],
		'potentialAction' => [
			'@type'       => 'SearchAction',
			'target'      => [ '@type' => 'EntryPoint', 'urlTemplate' => $c['base'] . $search ],
			'query-input' => 'required name=search_term_string',
		],
	];

	dl_schema_print( [ $org, $website ] );
}, 5 );

/* === Pages: WebPage, AboutPage, ContactPage === */

add_action( 'wp_head', function () {
	if ( ! is_front_page() && ! is_page() ) {
		return;
	}
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_cart() || is_checkout() || is_account_page() ) ) {
		return; // The shop is a CollectionPage below; cart, checkout and account are not for search.
	}

	$c   = dl_schema_config();
	$pid = get_queried_object_id();
	$url = is_front_page() ? $c['base'] : get_permalink( $pid );

	$types = [
		'about'   => 'AboutPage',
		'om-os'   => 'AboutPage',
		'kontakt' => 'ContactPage',
	];
	$type = $types[ get_post_field( 'post_name', $pid ) ] ?? 'WebPage';

	$page = [
		'@type'         => $type,
		'@id'           => $url . '#webpage',
		'url'           => $url,
		'name'          => dl_schema_seo_title(),
		'description'   => dl_schema_seo_description( is_front_page() ? get_bloginfo( 'description' ) : get_the_excerpt( $pid ) ),
		'inLanguage'    => $c['lang'],
		'isPartOf'      => [ '@id' => $c['id']['website'] ],
		'about'         => [ '@id' => $c['id']['org'] ],
		'datePublished' => get_the_date( 'c', $pid ),
		'dateModified'  => get_the_modified_date( 'c', $pid ),
		'speakable'     => [ '@type' => 'SpeakableSpecification', 'cssSelector' => [ 'h1' ] ],
		'breadcrumb'    => is_front_page() ? null : [ '@id' => $url . '#breadcrumb' ],
	];
	if ( 'WebPage' !== $type ) {
		$page['mainEntity'] = [ '@id' => $c['id']['org'] ];
	}

	$thumb = get_post_thumbnail_id( $pid );
	$page['primaryImageOfPage'] = $thumb
		? [ '@type' => 'ImageObject', 'url' => wp_get_attachment_image_url( $thumb, 'full' ) ]
		: [ '@id' => $c['id']['logo'] ];

	$graph = [ $page ];
	if ( ! is_front_page() ) {
		$trail = [];
		foreach ( array_reverse( get_post_ancestors( $pid ) ) as $anc ) {
			$trail[] = [ get_the_title( $anc ), get_permalink( $anc ) ];
		}
		$trail[] = [ get_the_title( $pid ), $url ];
		$graph[] = dl_schema_breadcrumb( $url . '#breadcrumb', $trail );
	}

	dl_schema_print( $graph );
}, 20 );

/* === Product === */

add_action( 'wp_head', function () {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	$product = wc_get_product( get_queried_object_id() );
	if ( ! $product ) {
		return;
	}

	$c        = dl_schema_config();
	$id       = $product->get_id();
	$url      = get_permalink( $id );
	$currency = get_woocommerce_currency();

	$images = [];
	foreach ( array_merge( [ $product->get_image_id() ], array_slice( $product->get_gallery_image_ids(), 0, 4 ) ) as $img_id ) {
		if ( $img_id && ( $src = wp_get_attachment_image_url( $img_id, 'full' ) ) ) {
			$images[] = $src;
		}
	}

	$cats = wc_get_product_terms( $id, 'product_cat', [ 'orderby' => 'parent', 'order' => 'DESC' ] );
	$cat  = $cats ? $cats[0] : null; // Deepest category.

	$prod = [
		'@type'            => 'Product',
		'@id'              => $url . '#product',
		'name'             => wp_strip_all_tags( $product->get_name() ),
		'url'              => $url,
		'description'      => wp_trim_words( wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ), 80, '…' ),
		'image'            => array_values( array_unique( $images ) ),
		'category'         => $cat ? html_entity_decode( $cat->name, ENT_QUOTES ) : null,
		'sku'              => $product->get_sku(),
		'gtin'             => method_exists( $product, 'get_global_unique_id' ) ? $product->get_global_unique_id() : null,
		'mainEntityOfPage' => [ '@id' => $url . '#webpage' ],
	];

	// Brand only from the product's own data (WooCommerce Brands), never Delicja.
	if ( taxonomy_exists( 'product_brand' ) ) {
		$brands = wp_get_post_terms( $id, 'product_brand', [ 'fields' => 'names' ] );
		if ( $brands && ! is_wp_error( $brands ) ) {
			$prod['brand'] = [ '@type' => 'Brand', 'name' => $brands[0] ];
		}
	}

	$tags = wp_get_post_terms( $id, 'product_tag', [ 'fields' => 'names' ] );
	if ( $tags && ! is_wp_error( $tags ) ) {
		$prod['keywords'] = implode( ', ', $tags );
	}

	// Weight and dimensions in the shop's units.
	$dim_unit    = [ 'cm' => 'CMT', 'mm' => 'MMT', 'm' => 'MTR', 'in' => 'INH' ][ get_option( 'woocommerce_dimension_unit', 'cm' ) ] ?? 'CMT';
	$weight_unit = [ 'kg' => 'KGM', 'g' => 'GRM', 'lbs' => 'LBR', 'oz' => 'ONZ' ][ get_option( 'woocommerce_weight_unit', 'kg' ) ] ?? 'KGM';
	foreach ( [ 'width' => $product->get_width(), 'height' => $product->get_height(), 'depth' => $product->get_length() ] as $k => $v ) {
		if ( $v ) {
			$prod[ $k ] = [ '@type' => 'QuantitativeValue', 'value' => (float) $v, 'unitCode' => $dim_unit ];
		}
	}
	if ( $product->get_weight() ) {
		$prod['weight'] = [ '@type' => 'QuantitativeValue', 'value' => (float) $product->get_weight(), 'unitCode' => $weight_unit ];
	}

	// Attributes; "Country of origin" also fills countryOfOrigin.
	$props = [];
	foreach ( $product->get_attributes() as $attr ) {
		$label  = wc_attribute_label( $attr->get_name(), $product );
		$values = $product->get_attribute( $attr->get_name() );
		if ( ! $values ) {
			continue;
		}
		$props[] = [ '@type' => 'PropertyValue', 'name' => $label, 'value' => $values ];
		if ( preg_match( '/country of origin|oprindelsesland|kraj pochodzenia/i', $label ) ) {
			$prod['countryOfOrigin'] = [ '@type' => 'Country', 'name' => $values ];
		}
	}
	$prod['additionalProperty'] = $props;

	$availability = $product->is_in_stock()
		? ( $product->is_on_backorder() ? 'https://schema.org/BackOrder' : 'https://schema.org/InStock' )
		: 'https://schema.org/OutOfStock';

	$offer_common = [
		'url'                   => $url,
		'priceCurrency'         => $currency,
		'availability'          => $availability,
		'itemCondition'         => 'https://schema.org/NewCondition',
		'seller'                => [ '@id' => $c['id']['org'] ],
		'acceptedPaymentMethod' => dl_schema_payment_methods(),
		'areaServed'            => $c['area_served'],
	];

	if ( $product->is_type( 'variable' ) ) {
		$prices         = $product->get_variation_prices( true );
		$prod['offers'] = [
			'@type'      => 'AggregateOffer',
			'@id'        => $url . '#offer',
			'lowPrice'   => wc_format_decimal( min( $prices['price'] ?: [ 0 ] ), 2 ),
			'highPrice'  => wc_format_decimal( max( $prices['price'] ?: [ 0 ] ), 2 ),
			'offerCount' => count( $prices['price'] ),
		] + $offer_common;
	} else {
		$sale_end = $product->is_on_sale() ? $product->get_date_on_sale_to() : null;
		$offer    = [
			'@type'           => 'Offer',
			'@id'             => $url . '#offer',
			'price'           => wc_format_decimal( $product->get_price(), 2 ),
			'priceValidUntil' => $sale_end ? $sale_end->date( 'Y-m-d' ) : gmdate( 'Y-m-d', strtotime( '+1 year' ) ),
		] + $offer_common;

		if ( $product->is_on_sale() && $product->get_regular_price() ) {
			$offer['priceSpecification'] = [
				'@type'         => 'UnitPriceSpecification',
				'priceType'     => 'https://schema.org/StrikethroughPrice',
				'price'         => wc_format_decimal( $product->get_regular_price(), 2 ),
				'priceCurrency' => $currency,
			];
		}
		if ( $product->managing_stock() && null !== $product->get_stock_quantity() ) {
			$offer['inventoryLevel'] = [ '@type' => 'QuantitativeValue', 'value' => max( 0, (int) $product->get_stock_quantity() ) ];
		}
		$prod['offers'] = $offer;
	}

	// Only real, approved reviews.
	if ( $product->get_review_count() > 0 && $product->get_average_rating() > 0 ) {
		$prod['aggregateRating'] = [
			'@type'       => 'AggregateRating',
			'ratingValue' => (string) $product->get_average_rating(),
			'reviewCount' => (string) $product->get_review_count(),
			'bestRating'  => '5',
			'worstRating' => '1',
		];
	}

	$page = [
		'@type'        => 'ItemPage',
		'@id'          => $url . '#webpage',
		'url'          => $url,
		'name'         => dl_schema_seo_title(),
		'description'  => dl_schema_seo_description( $prod['description'] ),
		'inLanguage'   => $c['lang'],
		'isPartOf'     => [ '@id' => $c['id']['website'] ],
		'mainEntity'   => [ '@id' => $url . '#product' ],
		'breadcrumb'   => [ '@id' => $url . '#breadcrumb' ],
		'speakable'    => [ '@type' => 'SpeakableSpecification', 'cssSelector' => [ 'h1' ] ],
		'dateModified' => $product->get_date_modified() ? $product->get_date_modified()->date( 'c' ) : null,
	];

	$trail   = dl_schema_shop_trail( $cat );
	$trail[] = [ $product->get_name(), $url ];

	dl_schema_print( [ $prod, $page, dl_schema_breadcrumb( $url . '#breadcrumb', $trail ) ] );
}, 20 );

/* === Shop and product categories === */

add_action( 'wp_head', function () {
	if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_category() ) ) {
		return;
	}

	$c     = dl_schema_config();
	$term  = is_product_category() ? get_queried_object() : null;
	$shop  = wc_get_page_id( 'shop' );
	$url   = $term ? get_term_link( $term ) : get_permalink( $shop );
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	$url   = $paged > 1 ? trailingslashit( $url ) . 'page/' . $paged . '/' : $url;

	global $wp_query;
	$items = [];
	if ( ! $term && ! is_search() ) {
		// The shop page shows the categories with products (template "Shop – categories").
		$cats = get_terms( [
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'orderby'    => 'menu_order',
			'exclude'    => [ (int) get_option( 'default_product_cat' ) ],
		] );
		foreach ( is_wp_error( $cats ) ? [] : array_values( $cats ) as $i => $cat ) {
			$items[] = [
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'url'      => get_term_link( $cat ),
				'name'     => html_entity_decode( $cat->name, ENT_QUOTES ),
			];
		}
	} else {
		// Products in the listing on this page, positions continue on later pages.
		$offset = ( $paged - 1 ) * (int) $wp_query->get( 'posts_per_page' );
		foreach ( $wp_query->posts as $i => $p ) {
			$items[] = [
				'@type'    => 'ListItem',
				'position' => $offset + $i + 1,
				'url'      => get_permalink( $p->ID ),
				'name'     => wp_strip_all_tags( html_entity_decode( get_the_title( $p->ID ), ENT_QUOTES ) ),
			];
		}
	}

	$name     = $term ? html_entity_decode( $term->name, ENT_QUOTES ) : get_the_title( $shop );
	$thumb_id = $term ? get_term_meta( $term->term_id, 'thumbnail_id', true ) : 0;

	$page = [
		'@type'              => 'CollectionPage',
		'@id'                => $url . '#webpage',
		'url'                => $url,
		'name'               => dl_schema_seo_title(),
		'description'        => dl_schema_seo_description( $term ? $term->description : '' ),
		'inLanguage'         => $c['lang'],
		'isPartOf'           => [ '@id' => $c['id']['website'] ],
		'about'              => [ '@id' => $c['id']['catalog'] ],
		'provider'           => [ '@id' => $c['id']['org'] ],
		'primaryImageOfPage' => $thumb_id ? [ '@type' => 'ImageObject', 'url' => wp_get_attachment_image_url( $thumb_id, 'full' ) ] : null,
		'mainEntity'         => [ '@id' => $url . '#itemlist' ],
		'breadcrumb'         => [ '@id' => $url . '#breadcrumb' ],
	];

	$list = [
		'@type'           => 'ItemList',
		'@id'             => $url . '#itemlist',
		'name'            => $name,
		'numberOfItems'   => count( $items ), // Items in this list, not in the whole category.
		'itemListElement' => $items,
	];

	$trail = dl_schema_shop_trail( $term );

	dl_schema_print( [ $page, $list, dl_schema_breadcrumb( $url . '#breadcrumb', $trail ) ] );
}, 20 );

/* === Switch off duplicates === */

// Rank Math: drop the types output above, keep the rest (e.g. Article on blog posts).
// Our @ids match Rank Math's (#organization, #website), so its remaining entities still link to ours.
// Its default Article on pages is dropped too: shop pages are not articles.
add_filter( 'rank_math/json_ld', function ( $data ) {
	$ours = is_page() || is_front_page() ? [ 'Article', 'BlogPosting', 'NewsArticle' ] : [];
	$ours = array_merge( $ours, [
		'Organization', 'OnlineStore', 'GroceryStore', 'LocalBusiness', 'Store', 'Person', 'Brand', 'Place',
		'WebSite', 'WebPage', 'ItemPage', 'CollectionPage', 'AboutPage', 'ContactPage', 'SearchResultsPage',
		'BreadcrumbList', 'Product', 'ProductGroup', 'ItemList',
	] );
	foreach ( $data as $key => $entity ) {
		if ( is_array( $entity ) && array_intersect( (array) ( $entity['@type'] ?? [] ), $ours ) ) {
			unset( $data[ $key ] );
		}
	}
	// Its primary image of the page, guessed from old page content (e.g. a replaced hero picture at 200 × 200); our
	// WebPage has its own primaryImageOfPage, so nothing links to it.
	if ( isset( $data['primaryImage'] ) ) {
		unset( $data['primaryImage'] );
	}
	return $data;
}, 99 );

// WooCommerce's own structured data (Product, BreadcrumbList, WebSite).
add_action( 'init', function () {
	if ( function_exists( 'WC' ) && WC()->structured_data ) {
		remove_action( 'wp_footer', [ WC()->structured_data, 'output_structured_data' ], 10 );
	}
} );
