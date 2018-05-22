<?php
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	// Disable admin bar
	add_filter('show_admin_bar', '__return_false');

	// Remove emoji-support
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );