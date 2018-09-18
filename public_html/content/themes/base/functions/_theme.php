<?php
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
	
	
	// Enable featured images
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' ); 
	add_image_size( 'slideshow', 1500, 800, true ); 
	
	
	
	// Register navigation
	function setup_navs() {
	    register_nav_menus( array(
	        'primary_menu' => 'Primaire navigatie',
	        'secondary_menu' => 'Secundaire navigatie'
	    ) );
	}
	add_action( 'after_setup_theme', 'setup_navs' );
	
	
	
	// Setup option pages
	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page(array(
			'page_title' 	=> 'Thema instellingen',
			'menu_title'	=> 'Site opties',
			'redirect'		=> false,
			'position' 		=> 80,
			'icon_url'		=> 'dashicons-editor-table'
		));
	}

	
	
	
	// Enqueue scripts
	function enqueue_scripts_styles() {
		global $wp_scripts;
		wp_enqueue_style( 'styles', get_stylesheet_directory_uri() . '/dist/css/styles.css', null, THEME_VERSION );
		wp_enqueue_style( 'fonts', 'https://use.typekit.net/jbm2ajd.css', null, THEME_VERSION );
		
		wp_deregister_script('wp-embed');
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://code.jquery.com/jquery-2.2.4.min.js', false, null);
		wp_enqueue_script('jquery', null, null, false);
		
		wp_enqueue_script("plugins", get_stylesheet_directory_uri() . '/dist/js/plugins.js', null, THEME_VERSION, true);
		wp_enqueue_script("scripts", get_stylesheet_directory_uri() . '/dist/js/scripts.js', null, THEME_VERSION, true);
		wp_enqueue_script("theme", get_stylesheet_directory_uri() . '/dist/js/theme.js', null, THEME_VERSION, true);
		
		// Initialize Google Maps
		if( function_exists('get_field') && get_field('googlemaps_apikey', 'option') ) {
			wp_enqueue_script('google_maps', '//maps.googleapis.com/maps/api/js?libraries=places&key=' . get_field('googlemaps_apikey', 'option') . '&callback=initializeMap', null, null, true);
		}
	}
	add_action( 'wp_enqueue_scripts', 'enqueue_scripts_styles' );
	
	
	
	// Set the Google API Key for ACF
	if( function_exists('get_field') && get_field('googlemaps_apikey', 'option') ) {
		add_filter('acf/settings/google_api_key', function () {
		    return get_field('googlemaps_apikey', 'option');
		});
	}
	
	
	
	// Include the ajaxurl
	add_action( 'wp_head', function() {
		echo PHP_EOL.'<script>var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
	});
	
	
	
	// Alter query for the product pages
	function alter_post_per_page( $query ) {
		
	    if ( is_admin() || ! $query->is_main_query() )
	        return;
	
	    /*if ( is_post_type_archive( 'product' ) ||  is_tax( 'type' ) ||  is_tax( 'brand' ) ) {
			$query->set( 'posts_per_page', -1 );
	        return;
	    }*/
	    
	}
	add_action( 'pre_get_posts', 'alter_post_per_page', 1 );
	
	
	
?>
