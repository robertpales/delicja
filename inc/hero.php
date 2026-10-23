<?php
/**
 * Homepage hero products and the Delicja bag illustration.
 *
 * The hero shows real product cut-outs (Image widgets, files named delicja-hero-*.webp) that float in layers
 * (home.css, Hero; site.js, Hero). The Delicja bag is a smiling shopping bag full of Polish groceries that hops and
 * blinks (site.css, Delicja bag): inline SVG so the colours come from the Elementor global colours. Shortcode
 * [dl_hero_art]: New arrivals tile and the contact page's shop online panel (it moves on hover).
 */

defined( 'ABSPATH' ) || exit;

// The hero products are on the first screen: load them right away (otherwise all but the first images in the page
// are lazy-loaded). Elementor gives the first image in the page the high fetch priority, so the bread, the largest
// product, is the first one in the hero.
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment ) {
	if ( str_starts_with( basename( (string) get_attached_file( $attachment->ID ) ), 'delicja-hero-' ) ) {
		$attr['loading'] = 'eager';
	}

	return $attr;
}, 10, 2 );

add_shortcode( 'dl_hero_art', function () {
	return '<svg class="dl-bag" viewBox="0 0 400 420" role="img" aria-label="A smiling Delicja shopping bag full of Polish groceries: rye bread, a ring of sausage, a jar of pickles and dill">
	<circle class="dl-bag-sun" cx="200" cy="208" r="172"/>
	<ellipse class="dl-bag-shadow" cx="200" cy="398" rx="106" ry="11"/>
	<g class="dl-bag-hop">
		<g class="dl-bag-dill">
			<path d="M282 186c2-34 8-62 22-92"/>
			<path d="M288 146l-16-9M291 131l16-9M295 117l-13-12M299 105l15-5"/>
			<path d="M294 186c6-28 15-48 30-64"/>
			<path d="M305 152l15-2M311 139l-3-16M318 129l12 3"/>
		</g>
		<g class="dl-bag-jar">
			<rect class="dl-bag-glass" x="236" y="122" width="52" height="64" rx="10"/>
			<rect class="dl-bag-lid" x="233" y="108" width="58" height="16" rx="5"/>
			<ellipse class="dl-bag-pickle" cx="252" cy="158" rx="7.5" ry="17" transform="rotate(-10 252 158)"/>
			<ellipse class="dl-bag-pickle" cx="271" cy="160" rx="7.5" ry="17" transform="rotate(12 271 160)"/>
			<path class="dl-bag-shine" d="M244 132v20"/>
		</g>
		<g transform="rotate(-24 150 160)">
			<rect class="dl-bag-bread" x="92" y="128" width="120" height="60" rx="30"/>
			<path class="dl-bag-score" d="M118 144l12 26M144 142l12 26M170 142l12 26"/>
		</g>
		<g class="dl-bag-sausage">
			<path class="dl-bag-ring" d="M172 182a32 32 0 1 1 52 0"/>
			<path class="dl-bag-ring-shine" d="M178 156a24 24 0 0 1 30-20"/>
			<path class="dl-bag-string" d="M168 186l-6 6M228 186l6 6"/>
		</g>
		<path class="dl-bag-paper" d="M106 180h188l10 206a10 10 0 0 1-10 10.5H106a10 10 0 0 1-10-10.5z"/>
		<path class="dl-bag-paper" d="M106 182l5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12 5.88-12 5.88 12v8H106z"/>
		<path class="dl-bag-fold" d="M126 190l-8 196M274 190l8 196"/>
		<path class="dl-bag-band" d="M99.9 326h200.2l1.5 30H98.4z"/>
		<text class="dl-bag-word" x="200" y="348" text-anchor="middle">DELICJA</text>
		<circle class="dl-bag-cheek" cx="150" cy="280" r="12"/>
		<circle class="dl-bag-cheek" cx="250" cy="280" r="12"/>
		<g class="dl-bag-eyes">
			<ellipse cx="172" cy="254" rx="8.5" ry="11.5"/>
			<ellipse cx="228" cy="254" rx="8.5" ry="11.5"/>
		</g>
		<path class="dl-bag-smile" d="M180 280q20 22 40 0"/>
	</g>
</svg>';
} );
