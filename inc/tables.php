<?php
/**
 * Wide tables on the information pages (e.g. the privacy policy): a Table block with the class `dl-table-stack`
 * becomes one card per row on phones. Each cell gets its column heading as `data-label`, which site.css shows above
 * the cell (=== Information pages ===). The block's saved content stays as the editor writes it.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'render_block_core/table', function ( $html, $block ) {
	if ( ! str_contains( $block['attrs']['className'] ?? '', 'dl-table-stack' ) ) {
		return $html;
	}
	if ( ! preg_match( '#<thead>(.*?)</thead>#s', $html, $head ) || ! preg_match_all( '#<th[^>]*>(.*?)</th>#s', $head[1], $cells ) ) {
		return $html;
	}
	$labels = array_map( fn( $cell ) => trim( wp_strip_all_tags( $cell ) ), $cells[1] );

	$tags   = new WP_HTML_Tag_Processor( $html );
	$column = 0;
	while ( $tags->next_tag() ) {
		if ( 'TR' === $tags->get_tag() ) {
			$column = 0;
		} elseif ( 'TD' === $tags->get_tag() ) {
			if ( isset( $labels[ $column ] ) ) {
				$tags->set_attribute( 'data-label', $labels[ $column ] );
			}
			++$column;
		}
	}

	return $tags->get_updated_html();
}, 10, 2 );
