<?php
	
	/*
		Plugin Name: VisualMasters Cookies
		Plugin URI: https://www.visualmasters.nl
		Description: Toont de cookie-melding aan de bezoeker en GDPR-meldingen bij Gravity Form formulieren.
		Author: VisualMasters
		Author URI: https://www.visualmasters.nl
		License: GNU License
		Version: 1.0.0
	*/
	
	$vm_cookies_plugin_version = '1.0.0';
	
	
	// Load plugin functions
	include_once 'plugin-functions.php';
	include_once 'inc/acf-fields.php';
	
	// See if there is an update available
	require_once 'update/updates.php';
	
	
	// Register the current version
    register_activation_hook( __FILE__, 'vm_cookies_register_current_version' );
	
	
	// Set hook to check for updates
	add_filter( 'pre_set_site_transient_update_plugins', 'vm_cookies_autoupdate' );
	add_filter( 'plugins_api', 'vm_cookies_plugin_api_call', 10, 3 );
	
	// Add an action when plugin is upgraded
	add_action( 'init', 'vm_cookies_check_upgrade_tasks', 10, 1 );
	
	
	/* Silence is golden */	



	
?>