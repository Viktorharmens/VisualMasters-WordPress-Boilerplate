<?php
	
	
	// Get the Authentication flow and put a step in between to redirect to the login + Google Authenticator
	function redirect_to_google_authenticator($user, $username, $password) {
		
		$redirect = '';
		if( isset($_GET['redirect_to']) ) {
			$redirect = '&redirect_to=' . $_GET['redirect_to'];
		}
		
		if(empty($_POST) && empty($_GET)) {
			wp_safe_redirect('/');
			exit();
		}
		
		if(empty($username) || empty($password)) {
			wp_safe_redirect( __('/inloggen', VM_TEXTDOMAIN) . '/?login=empty' . $redirect );
			exit();
		}
		
		// First check if the login succeeded
		if(empty($user->errors)) {
			// Login succeeded, moving on
			// First store the user-id in a session so we can recall this later
			
			if( !session_id() ) session_start();
			$_SESSION['user_id'] = $user->data->ID;
			
			// Here we check if Google Authenticator is running
			$gacode = get_user_meta($user->data->ID, '_ga_code', true);
			if(!empty($gacode)) {
				$gauth = true;
			}
			
			if($gauth) {
				wp_safe_redirect( __('/inloggen', VM_TEXTDOMAIN) . '?google-authenticator=true&_nonce=' . wp_create_nonce('google-authenticator') . ((isset($_POST['redirect_to'])) ? '&redirect_to=' . $_POST['redirect_to'] : '') );
				exit();
			} else {
				return $user;
			}
		} else {
			return $user;
		}
		
	}
	add_filter('authenticate', 'redirect_to_google_authenticator', 30, 3);
	
	
	
	add_action( 'verify_code', 'verify_google_authenticator_code', 10, 1 );
	function verify_google_authenticator_code($nonce) {
		
		// Set authentication to false on default
		$authentication = false;
		
		// Verify the nonce, to check if user is still the same one
		// as the one that started the login attempt
		if(wp_verify_nonce( $nonce, 'verify-google-authenticator' )) {
			
			// Nonce verified, so signed request and moving on...
			// Now first check the posted code
			if( !session_id() ) session_start();
			$uid = $_SESSION['user_id'];
			
			if(!empty($uid)) {
				
				// Start the Gauth instance
				$ga = new PHPGangsta_GoogleAuthenticator();
				
				// Now get the secret code from the user meta table
				$gauth_secret = get_user_meta( $uid, '_ga_code', true);
				$gauth_code	  = sanitize_text_field($_POST['authcode']);
				
				// Check if the code is a back-up code
				if(strlen($gauth_code) == 6) {
					
					// Normal code
					if($ga->verifyCode($gauth_secret, $gauth_code, 2)) {
						$authentication = true;
					}
					
				} elseif(strlen(trim($gauth_code)) > 6 && strtolower($gauth_secret) == strtolower($_POST['authcode'])) {
					
					// Backup code
					$authentication = true;
					
				}
				
			}	
		}
		
		// Redirect the user accordingly
		if($authentication == true) {
			
			// Set the session to the user
			wp_set_auth_cookie($uid, false, false );
			wp_set_current_user($uid);
			
			// Be sure the user is logged in and then redirect to the admin
			if(is_user_logged_in()) {
				
				// To do:  Add the redirect to to the admin URL for redirection
				wp_safe_redirect( get_admin_url() );
			}
			
		} else {
			wp_safe_redirect( __('/inloggen', VM_TEXTDOMAIN) . '?google-authenticator=true&login=error&_nonce=' . wp_create_nonce('google-authenticator') . ((isset($_POST['redirect_to'])) ? '&redirect_to=' . $_POST['redirect_to'] : '') );
		}
		
		// Force script end
		exit();
	}
	
	
?>