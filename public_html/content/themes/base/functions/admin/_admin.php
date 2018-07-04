<?php
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	
	// init process for registering the button
	add_action('init', 'shortcode_cta_button_init');
	function shortcode_cta_button_init () {
	
		//Abort early if the user will never see TinyMCE
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
			return;
		
		//Add a callback to regiser our tinymce plugin
		add_filter('mce_external_plugins', 'register_cta_button_plugin'); 
		
		// Add a callback to add our button to the TinyMCE toolbar
		add_filter('mce_buttons', 'add_tinymce_button');
	}
	
	// This callback registers the plug-in
	function register_cta_button_plugin ($plugin_array) {
		$plugin_array['vm_cta_button'] = get_stylesheet_directory_uri() . '/functions/admin/assets/tinymce-ctabutton/cta-button-shortcode.js';
		return $plugin_array;
	}
	
	// This callback adds the button to the toolbar
	function add_tinymce_button ($buttons) {
	    //Add the button ID to the $button array
		$buttons[] = 'vm_cta_button';
		return $buttons;
	}
	
	
	
	
	
	// ================================
	// Set Yoast box to lowest priority
	// ================================
	function yoast_to_bottom() {
		return 'low';
	}
	add_filter( 'wpseo_metabox_prio', 'yoast_to_bottom');
	
	
	
	// ==================
	// Allow SVG uploads
	// ==================
	function allow_mime_types($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
	add_filter('upload_mimes', 'allow_mime_types');
	
	
	
	
	// ===================================
	// Add Yoast validation on ACF Fields
	// ===================================
	add_action( 'admin_init', 'load_acf_yoast_extension' );
	function load_acf_yoast_extension() {
		if( is_admin() ) {
			// Check if Yoast SEO and ACF are present and active
			if( is_plugin_active('advanced-custom-fields-pro/acf.php') && is_plugin_active('wordpress-seo/wp-seo.php') ) {
				include_once get_template_directory() . '/functions/vendor/acf-content-analysis/yoast-acf-analysis.php';
			}
		}
	}
	
	
?>