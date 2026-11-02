<?php
/**
 * Product facts on the product page: hand-drawn icons that explain the product at a glance. Everything comes from
 * data the owner keeps in WooCommerce, so every product gets them the same way:
 * - net quantity from the product name ("Twaróg wiejski 250 g"),
 * - the global attributes Origin, Storage, Diet, Allergens and May contain (Products → Attributes; pick the terms
 *   on the product's Attributes tab),
 * - nutrition from the table in the product description (rows Energy, Fat, of which saturates, Carbohydrate,
 *   of which sugars, Fibre, Protein, Salt, in English, Danish or Polish as on EU labels), with the share of the
 *   reference intake of an average adult (EU 1169/2011, Annex XIII).
 * Missing data shows nothing: no allergen icons never means "no allergens". The nutrition table itself moves from the
 * Description tab into Product facts (under "Show the full nutrition table"), so it is on the page once and complete
 * (kJ and kcal), as the EU rules ask for food sold online.
 * Works in every language (WPML): the texts are theme strings (languages/*.po), and icons are named after the
 * attribute term's original slug (English "milk"), so a translated term (Danish "maelk") gets the same icon.
 * Shortcode [dl_product_facts] in the Single Product template. Icons in assets/images/icons/ (named after the
 * attribute term slug or the nutrient), styles in site.css (=== Product facts ===).
 */

defined( 'ABSPATH' ) || exit;

// Reference intakes of an average adult per day (EU 1169/2011, Annex XIII, Part B).
const DELICJA_REFERENCE_INTAKE = [
	'energy'       => 2000,
	'fat'          => 70,
	'saturates'    => 20,
	'carbohydrate' => 260,
	'sugars'       => 90,
	'protein'      => 50,
	'salt'         => 6,
];

/**
 * A hand-drawn icon, coloured by CSS (mask). Empty when there is no icon with that name.
 */
function delicja_fact_icon( string $name ): string {
	$file = '/assets/images/icons/' . sanitize_file_name( $name ) . '.webp';
	if ( ! file_exists( get_stylesheet_directory() . $file ) ) {
		return '';
	}

	return '<span class="dl-fact-icon" style="--dl-icon: url(\'' . esc_url( get_stylesheet_directory_uri() . $file ) . '\')" aria-hidden="true"></span>';
}

/**
 * Nutrient of a row in the nutrition table, from its label in English, Danish or Polish ("of which saturates",
 * "heraf mættede fedtsyrer", "w tym kwasy tłuszczowe nasycone"), or '' for another row. Saturates and sugars come
 * first, because their labels also name fat and carbohydrate in some languages.
 */
function delicja_nutrient_key( string $label ): string {
	$words = [
		'saturates'    => [ 'saturat', 'mætte', 'nasycon' ],
		'sugars'       => [ 'sugar', 'sukker', 'cukr' ],
		'energy'       => [ 'energ' ],
		'fat'          => [ 'fat', 'fedt', 'tłuszcz' ],
		'carbohydrate' => [ 'carbohydrate', 'kulhydrat', 'węglowodan' ],
		'fibre'        => [ 'fibre', 'fiber', 'błonnik' ],
		'protein'      => [ 'protein', 'białk' ],
		'salt'         => [ 'salt', 'sól' ],
	];
	$label = mb_strtolower( $label );
	foreach ( $words as $key => $list ) {
		foreach ( $list as $word ) {
			if ( str_contains( $label, $word ) ) {
				return $key;
			}
		}
	}

	return '';
}

/**
 * Nutrition rows from the table in the product description: [key => [label, value text, number]].
 * The number is in kcal for energy and in grams for the rest.
 */
function delicja_nutrition( string $html ): array {
	$labels = [
		'energy'       => __( 'Energy', 'delicja' ),
		'fat'          => __( 'Fat', 'delicja' ),
		'saturates'    => __( 'Saturated fat', 'delicja' ),
		'carbohydrate' => __( 'Carbohydrate', 'delicja' ),
		'sugars'       => __( 'Sugars', 'delicja' ),
		'fibre'        => __( 'Fibre', 'delicja' ),
		'protein'      => __( 'Protein', 'delicja' ),
		'salt'         => __( 'Salt', 'delicja' ),
	];

	$nutrition = [];
	preg_match_all( '#<tr[^>]*>(.*?)</tr>#is', $html, $trs );
	foreach ( $trs[1] as $tr ) {
		preg_match_all( '#<t[dh][^>]*>(.*?)</t[dh]>#is', $tr, $cells );
		if ( count( $cells[1] ) < 2 ) {
			continue;
		}
		$key   = delicja_nutrient_key( trim( wp_strip_all_tags( $cells[1][0] ) ) );
		$value = trim( wp_strip_all_tags( $cells[1][1] ) );
		if ( '' === $key || '' === $value || isset( $nutrition[ $key ] ) ) {
			continue;
		}

		$label = $labels[ $key ];
		if ( 'energy' === $key ) {
			if ( ! preg_match( '/([\d.,]+)\s*kcal/i', $value, $kcal ) ) {
				continue;
			}
			$number = (float) str_replace( ',', '.', $kcal[1] );
			$value  = $kcal[1] . ' kcal';
		} else {
			$number = preg_match( '/[\d.,]+/', $value, $grams ) ? (float) str_replace( ',', '.', $grams[0] ) : null;
		}

		$nutrition[ $key ] = [ $label, $value, $number ];
	}

	return $nutrition;
}

/**
 * Icon name of an attribute term: the slug of the term in the language it was created in (with WPML), so the
 * Danish "maelk" and the Polish "mleko" get the icon of the English "milk". Without a translation, its own slug.
 */
function delicja_term_icon( WP_Term $term ): string {
	static $cache = [];
	if ( isset( $cache[ $term->term_id ] ) ) {
		return $cache[ $term->term_id ];
	}

	$slug    = $term->slug;
	$details = apply_filters( 'wpml_element_language_details', null, [ 'element_id' => $term->term_taxonomy_id, 'element_type' => $term->taxonomy ] );
	if ( $details && ! empty( $details->source_language_code ) ) {
		$original = (int) apply_filters( 'wpml_object_id', $term->term_id, $term->taxonomy, false, $details->source_language_code );
		if ( $original ) {
			global $wpdb;
			// Straight from the table: get_term() would hand back the term in the current language again.
			$slug = (string) $wpdb->get_var( $wpdb->prepare( "SELECT slug FROM {$wpdb->terms} WHERE term_id = %d", $original ) ) ?: $slug;
		}
	}

	return $cache[ $term->term_id ] = $slug;
}

/**
 * The product's terms of an attribute taxonomy, or an empty list.
 */
function delicja_fact_terms( WC_Product $product, string $taxonomy ): array {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return [];
	}
	$terms = wc_get_product_terms( $product->get_id(), $taxonomy );

	return is_array( $terms ) ? $terms : [];
}

add_shortcode( 'dl_product_facts', function () {
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		return '';
	}

	$chip = fn ( string $icon, string $text ) => '<li class="dl-fact-chip">' . delicja_fact_icon( $icon ) . '<span>' . esc_html( $text ) . '</span></li>';

	// At a glance: net quantity, origin, storage, diet.
	$glance = [];
	if ( preg_match( '/(\d+(?:[.,]\d+)?)\s?(kg|g|ml|l)\b/i', $product->get_name(), $quantity ) ) {
		$glance[] = $chip( 'weight', $quantity[1] . ' ' . strtolower( $quantity[2] ) );
	}
	foreach ( delicja_fact_terms( $product, 'pa_origin' ) as $term ) {
		/* translators: %s: country, e.g. "Poland" */
		$glance[] = $chip( 'origin', sprintf( __( 'From %s', 'delicja' ), $term->name ) );
	}
	foreach ( [ 'pa_storage', 'pa_diet' ] as $taxonomy ) {
		foreach ( delicja_fact_terms( $product, $taxonomy ) as $term ) {
			$glance[] = $chip( delicja_term_icon( $term ), $term->name );
		}
	}

	$html = $glance ? '<ul class="dl-facts-chips">' . implode( '', $glance ) . '</ul>' : '';

	// Allergens: what the product contains, and what it may contain (traces).
	$allergen_titles = [
		'pa_allergens'   => __( 'Contains', 'delicja' ),
		'pa_may-contain' => __( 'May contain', 'delicja' ),
	];
	foreach ( $allergen_titles as $taxonomy => $title ) {
		$terms = delicja_fact_terms( $product, $taxonomy );
		if ( ! $terms ) {
			continue;
		}
		$chips = implode( '', array_map( fn ( $term ) => $chip( delicja_term_icon( $term ), $term->name ), $terms ) );
		$class = 'pa_allergens' === $taxonomy ? 'dl-facts-allergens' : 'dl-facts-allergens is-traces';
		$html .= '<div class="' . $class . '"><h3 class="dl-facts-title">' . esc_html( $title ) . '</h3><ul class="dl-facts-chips">' . $chips . '</ul></div>';
	}

	// Nutrition per 100 g (or 100 ml), with the share of the reference intake.
	$content   = $product->get_description();
	$nutrition = delicja_nutrition( $content );
	if ( $nutrition ) {
		$unit  = preg_match( '/100\s*ml\b/i', $content ) ? '100 ml' : '100 g';
		$tiles = '';
		foreach ( $nutrition as $key => [ $label, $value, $number ] ) {
			$share = isset( DELICJA_REFERENCE_INTAKE[ $key ] ) && null !== $number ? (int) round( $number / DELICJA_REFERENCE_INTAKE[ $key ] * 100 ) : null;
			$tiles .= '<li class="dl-nutrient">' . delicja_fact_icon( $key )
				. '<span class="dl-nutrient-value">' . esc_html( $value ) . '</span>'
				. '<span class="dl-nutrient-label">' . esc_html( $label ) . '</span>'
				. ( null !== $share ? '<span class="dl-nutrient-share" style="--dl-share: ' . min( 100, $share ) . '%"><span>' . $share . ' %</span></span>' : '' )
				. '</li>';
		}
		$table = preg_match( '#<table.*?</table>#is', $content, $match ) ? wp_kses_post( $match[0] ) : '';
		/* translators: %s: "100 g" or "100 ml" */
		$title = sprintf( __( 'Nutrition per %s', 'delicja' ), $unit );
		$html .= '<div class="dl-facts-nutrition"><h3 class="dl-facts-title">' . esc_html( $title ) . '</h3><ul class="dl-nutrients">' . $tiles . '</ul>'
			. '<p class="dl-facts-note">' . esc_html__( 'Share of the reference intake of an average adult (8400 kJ / 2000 kcal).', 'delicja' ) . '</p>'
			. ( $table ? '<details class="dl-facts-table"><summary>' . esc_html__( 'Show the full nutrition table', 'delicja' ) . '</summary>' . $table . '</details>' : '' )
			. '</div>';
	}

	return $html ? '<section class="dl-facts" aria-label="' . esc_attr__( 'Product facts', 'delicja' ) . '">' . $html . '</section>' : '';
} );

// The nutrition heading and table leave the Description tab: Product facts shows them (see above). The heading is
// the one right above the table, in whatever language ("Nutrition per 100 g", "Næringsindhold pr. 100 g").
add_filter( 'the_content', function ( $content ) {
	if ( ! is_product() || ! in_the_loop() || ! delicja_nutrition( $content ) ) {
		return $content;
	}

	return preg_replace( '#(<h[2-4][^>]*>[^<]*</h[2-4]>\s*)?<table.*?</table>#is', '', $content, 1 );
}, 20 );

