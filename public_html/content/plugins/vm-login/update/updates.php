<?php
	
	// Set update variables	
	$api_url = 'https://api.visual-masters.nl/plugins/vm-login/';
	$plugin_slug = basename( realpath(__DIR__ . '/..') );
	
	set_transient( 'update_plugins', null );
	
	function vm_login_autoupdate ( $transient ) {
		global $api_url, $plugin_slug, $wp_version;
		
		//Comment out these two lines during testing.
		if (empty($transient->checked))
			return $transient;
		
		$args = array(
			'slug' => $plugin_slug,
			'version' => $transient->checked[$plugin_slug .'/'. $plugin_slug .'.php'],
		);
		
		$request_string = array(
			'body' => array(
				'action' => 'basic_check', 
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
		
		// Start checking for an update
		$raw_response = wp_remote_post($api_url, $request_string);
		
		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
			$response = unserialize($raw_response['body']);
		}
		
		// Feed the update data into WP updater
		if (is_object($response) && !empty($response)) {
			$transient->response[$plugin_slug .'/'. $plugin_slug .'.php'] = $response;
		}
		
		return $transient;
		
	}
	
	// Take over the Plugin info screen
	function plugin_api_call($def, $action, $args) {
		global $plugin_slug, $api_url, $wp_version;
		
		if (!isset($args->slug) || ($args->slug != $plugin_slug))
			return false;
		
		// Get the current version
		$plugin_info = get_site_transient('update_plugins');
		$current_version = $plugin_info->checked[$plugin_slug .'/'. $plugin_slug .'.php'];
		$args->version = $current_version;
		
		$request_string = array(
				'body' => array(
					'action' => $action, 
					'request' => serialize($args),
					'api-key' => md5(get_bloginfo('url'))
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			);
		
		$request = wp_remote_post($api_url, $request_string);
		
		if (is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
		} else {
			$res = unserialize($request['body']);
			
			if ($res === false)
				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
		}
		
		return $res;
	}
	
	function register_current_version() {
		global $plugin_version;
		add_option( 'vm_login_plugin_version', $plugin_version );
	}
	
	function set_current_version($plugin_version) {
		update_option( 'vm_login_plugin_version', $plugin_version );
	}
	
	function check_upgrade_tasks( $plugin_version ) {
		
		global $plugin_version;
		
		// Get the current version stored in the database
		$currversion = get_option( 'vm_login_plugin_version' );
		
		if( $currversion != $plugin_version || empty($currversion) ) {
		
			// Flush the rewrite rules
			flush_rewrite_rules( true );
			
			// Clear WP Rocket cache (if active)
			if( function_exists('rocket_clean_domain') ) {
				rocket_clean_domain();
			}
			
			// Update to the new version
			set_current_version( $plugin_version );
			
		}
	}
	
	

	
?>