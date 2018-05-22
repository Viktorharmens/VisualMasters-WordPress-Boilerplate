<?php
	
	
	/*
		Plugin Name: VisualMasters Security
		Plugin URI: https://www.visualmasters.nl
		Description: Beveiligd actief de toegang tot de WordPress beheeromgeving
		Author: VisualMasters
		Author URI: https://www.visualmasters.nl
		License: GNU License
		Version: 1.1.1
	*/
	
	$plugin_version = '1.1.1';
	
		
	// Autoload file for external classes
	require_once __DIR__ . '/classes/vendor/autoload.php';

	// Load plugin functions
	include_once 'plugin-functions.php';
	
	// Check the brute force
	require_once 'inc/bruteforce.php';
	
	// Setup the user flow rules
	require_once 'inc/htaccess.php';
	require_once 'inc/redirects.php';
	
	// Require the admin files for authentication
	require_once 'inc/authentication.php';
	require_once 'inc/admin.php';
	require_once 'inc/ajax.php';
	
	// Register the client role
	require_once 'inc/roles.php';
	
	// See if there is an update available
	require_once 'update/updates.php';
	
	
	
	/// Set (de)activation hooks
	register_activation_hook( __FILE__, 'vm_login_set_htaccess_content' );
	register_deactivation_hook(__FILE__, 'vm_reset_htaccess_content');
	
    register_activation_hook( __FILE__, 'add_client_role' );
	register_deactivation_hook( __FILE__, 'remove_client_role' );
	
    register_activation_hook( __FILE__, 'register_current_version' );
	
	
	// Set action hooks
	add_action('generate_rewrite_rules', 'vm_login_set_htaccess_content', 10, 0);
	
	add_action( 'show_user_profile', 'vm_google_authenticator_box' );
	add_action( 'edit_user_profile', 'vm_google_authenticator_box' );
	
	add_action( 'personal_options_update', 'save_google_authenticator_code' );
	add_action( 'edit_user_profile_update', 'save_google_authenticator_code' );
	
	// Set hook to check for updates
	add_filter( 'pre_set_site_transient_update_plugins', 'vm_login_autoupdate' );
	add_filter( 'plugins_api', 'plugin_api_call', 10, 3 );
	
	// Add an action when plugin is upgraded
	add_action( 'init', 'check_upgrade_tasks', 10, 1 );
	
	
	/* Silence is golden */	
	
?>
