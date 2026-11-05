<?php
/**
 * Page weight: WordPress's emoji script and styles are left out on the front end (the site uses no emoji images;
 * browsers draw emoji themselves). Saves a script and an inline style on every page.
 * Elementor prints its CSS as files (Elementor → Settings → Performance → CSS Print Method: External, database),
 * so the ~100 kB of kit and page CSS is cached by the browser instead of sent inside every page.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
} );
