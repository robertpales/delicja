<?php
/**
 * Homepage hero illustration: a Delicja shopping bag with a face, full of Polish groceries (rye bread, a ring of
 * sausage, a jar of pickles, dill). It hops, the sausage jumps out and spins, the bag blinks (home.css).
 * Inline SVG so the colours come from the Elementor global colours; used with the shortcode [dl_hero_art].
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'dl_hero_art', function () {
	return '<svg class="dl-bag" viewBox="0 0 400 420" role="img" aria-label="A smiling Delicja shopping bag full of Polish groceries: rye bread, a ring of sausage, a jar of pickles and dill">
	<circle class="dl-bag-sun" cx="200" cy="208" r="172"/>
	<ellipse class="dl-bag-shadow" cx="200" cy="398" rx="106" ry="11"/>
	<g class="dl-bag-hop">
		<g class="dl-bag-dill">
			<path d="M270 186c2-34 8-62 22-92"/>
			<path d="M276 146l-16-9M279 131l16-9M283 117l-13-12M287 105l15-5"/>
			<path d="M284 186c6-28 17-50 34-68"/>
			<path d="M295 151l15-2M301 138l-3-16M309 127l13 3"/>
		</g>
		<g class="dl-bag-jar">
			<rect class="dl-bag-glass" x="230" y="122" width="52" height="64" rx="10"/>
			<rect class="dl-bag-lid" x="227" y="108" width="58" height="16" rx="5"/>
			<ellipse class="dl-bag-pickle" cx="246" cy="158" rx="7.5" ry="17" transform="rotate(-10 246 158)"/>
			<ellipse class="dl-bag-pickle" cx="265" cy="160" rx="7.5" ry="17" transform="rotate(12 265 160)"/>
			<path class="dl-bag-shine" d="M238 132v20"/>
		</g>
		<g transform="rotate(-24 156 160)">
			<rect class="dl-bag-bread" x="98" y="128" width="120" height="60" rx="30"/>
			<path class="dl-bag-score" d="M124 144l12 26M150 142l12 26M176 142l12 26"/>
		</g>
		<g class="dl-bag-sausage">
			<circle class="dl-bag-ring" cx="196" cy="148" r="33"/>
			<path class="dl-bag-ring-shine" d="M172 132a29 29 0 0 1 36-12"/>
			<path class="dl-bag-string" d="M190 112l6-10 6 10"/>
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
