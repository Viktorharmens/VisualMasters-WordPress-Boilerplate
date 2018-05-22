<?php
	
	/* This file is for the generation of the client role, so that the client can only handle what he or she is allowed to */
	
	
	
	// Add a custom user role
	function add_client_role() {
		global $wp_roles;
		
		// Admin capabilities
		$baserole = $wp_roles->get_role('editor');
		
		// Adding a new_role with all admin caps
		$wp_roles->add_role('client', 'Client', $baserole->capabilities);
		
		$client = get_role('client');
		$client->add_cap('manage_options');
		$client->add_cap('edit_theme_options');
		$client->add_cap( 'gform_full_access' );
		
		$client->remove_cap('customize');
		
	}
	
	// Delete the role when deactivating the plugin
	function remove_client_role() {
		remove_role('client');
		
		// To do: Set clients to admin users when removing this role
	}
	
	
?>