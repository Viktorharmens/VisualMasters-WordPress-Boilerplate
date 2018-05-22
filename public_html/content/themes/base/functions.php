<?php
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	
	// Get the theme settings
	$active_theme = wp_get_theme();
	define( 'THEME_VERSION', $active_theme->get('Version') );
	
	
	// Disablers and cleanup
	include_once 'functions/admin/_auto-update.php';
	include_once 'functions/admin/_wp-disabler.php';
	include_once 'functions/admin/_wp-backend.php';
	include_once 'functions/admin/_admin.php';
	
	// Include main function files
	include_once 'functions/_theme.php';
	include_once 'functions/_functions.php';
	include_once 'functions/_customposts.php';
	include_once 'functions/_shortcodes.php';
	include_once 'functions/_ajax.php';
	
	
	
	// External vendor includes
	include_once 'functions/vendor/gravityforms-acf-population/acf-gravity_forms.php';
	
	
	
?>