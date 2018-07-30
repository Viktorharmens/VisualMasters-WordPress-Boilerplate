<?php
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit; 
	
	
	// Enable featured images
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' ); 
	//add_image_size( 'page-portrait', 500, 800, true ); 
	
	
	
	// Register navigation
	function setup_navs() {
	    register_nav_menus( array(
	        'primary_menu' => 'Primaire navigatie',
	        'secondary_menu' => 'Secundaire navigatie',
	        'footer_menu_left' => 'Footer links - links',
	        'footer_menu_right' => 'Footer links - rechts'
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
			'icon_url'		=> 'dashicons-admin-tools'
		));
		
		acf_add_options_page(array(
			'page_title' 	=> 'Homepage',
			'menu_title'	=> 'Homepage',
			'redirect'		=> false,
			'position' 		=> 4,
			'icon_url'		=> 'dashicons-admin-home'
		));
	}
	
	
	
	// Enqueue scripts
	function enqueue_scripts_styles() {
		global $wp_scripts;
		wp_enqueue_style( 'styles', get_stylesheet_directory_uri() . '/dist/css/styles.css', null, THEME_VERSION );
		wp_enqueue_style( 'fonts', '' );
		
		wp_deregister_script('wp-embed');
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://code.jquery.com/jquery-2.2.4.min.js', false, null);
		wp_enqueue_script('jquery', null, null, false);
		
		wp_enqueue_script("plugins", get_stylesheet_directory_uri() . '/dist/js/plugins.js', null, THEME_VERSION, true);
		wp_enqueue_script("scripts", get_stylesheet_directory_uri() . '/dist/js/scripts.js', null, THEME_VERSION, true);
		
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
	
	
	
	// Include the ajaxurl and the the favicon
	add_action( 'wp_head', function() {
		echo PHP_EOL.'<script>var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
		/*
		echo PHP_EOL.'<link rel="apple-touch-icon" sizes="57x57" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-57x57.png">
					  <link rel="apple-touch-icon" sizes="60x60" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-60x60.png">
					  <link rel="apple-touch-icon" sizes="72x72" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-72x72.png">
					  <link rel="apple-touch-icon" sizes="76x76" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-76x76.png">
					  <link rel="apple-touch-icon" sizes="114x114" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-114x114.png">
					  <link rel="apple-touch-icon" sizes="120x120" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-120x120.png">
					  <link rel="apple-touch-icon" sizes="144x144" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-144x144.png">
					  <link rel="apple-touch-icon" sizes="152x152" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-152x152.png">
					  <link rel="apple-touch-icon" sizes="180x180" href="'.get_template_directory_uri().'/lib/img/favicon/apple-icon-180x180.png">
					  <link rel="icon" type="image/png" sizes="192x192"  href="'.get_template_directory_uri().'/lib/img/favicon/android-icon-192x192.png">
					  <link rel="icon" type="image/png" sizes="32x32" href="'.get_template_directory_uri().'/lib/img/favicon/favicon-32x32.png">
					  <link rel="icon" type="image/png" sizes="96x96" href="'.get_template_directory_uri().'/lib/img/favicon/favicon-96x96.png">
					  <link rel="icon" type="image/png" sizes="16x16" href="'.get_template_directory_uri().'/lib/img/favicon/favicon-16x16.png">
					  <link rel="manifest" href="'.get_template_directory_uri().'/lib/img/favicon/manifest.json">
					  <meta name="msapplication-TileColor" content="' . get_field('theme_color_base', 'option') . '">
					  <meta name="msapplication-TileImage" content="'.get_template_directory_uri().'/lib/img/favicon/ms-icon-144x144.png">
					  <meta name="theme-color" content="' . get_field('theme_color_base', 'option') . '">';*/
	});
	
		
	
	
	
	
?>
