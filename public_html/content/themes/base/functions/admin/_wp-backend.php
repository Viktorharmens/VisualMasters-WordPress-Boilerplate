<?php
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	/**
	 * Change admin messages in WP footer
	 */
	
	// Remove update message if user can't update
	function footer_shh() {
		if ( ! current_user_can('manage_options') ) {
			remove_filter( 'update_footer', 'core_update_footer' );
		}
	}
	add_action( 'admin_menu', 'footer_shh' );
	
	// Change footer message
	function vm_footer() {
		return 'Built by <a href="https://www.visualmasters.nl/" target="_blank">VisualMasters</a>';
	}
	add_filter( 'admin_footer_text', 'vm_footer', 11 );
	
	/**
	 * Remove WordPress logo from admin-bar
	 */
	
	function remove_wp_logo( $wp_admin_bar ) {
		$wp_admin_bar->remove_node('wp-logo');
	}
	add_action('admin_bar_menu', 'remove_wp_logo', 999);