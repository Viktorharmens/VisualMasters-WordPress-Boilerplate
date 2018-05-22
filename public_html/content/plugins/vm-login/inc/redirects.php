<?php
	
	// Redirects the user to the lost password form
	add_filter( 'lostpassword_url', 'custom_lostpassword_url', 10, 0 );
	function custom_lostpassword_url() {
	    return home_url( __('/wachtwoord-vergeten/', VM_TEXTDOMAIN) );
	}
	
	
	// Redirects the user to the reset password form
	add_action( 'login_form_rp', 'redirect_to_custom_password_reset' );
	add_action( 'login_form_resetpass', 'redirect_to_custom_password_reset' );
	
	
	
	function redirect_to_custom_password_reset() {
	    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
	        // Verify key / login combo
	        $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
	        if ( ! $user || is_wp_error( $user ) ) {
	            if ( $user && $user->get_error_code() === 'expired_key' ) {
	                wp_redirect( home_url( __('/wachtwoord-reset/', VM_TEXTDOMAIN) . '?login=expiredkey' ) );
	            } else {
	                wp_redirect( home_url( __('/wachtwoord-reset/', VM_TEXTDOMAIN) . '?login=invalidkey' ) );
	            }
	            exit;
	        }
	 
	        $redirect_url = home_url( __('/wachtwoord-reset/', VM_TEXTDOMAIN) );
	        $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
	        $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );
	 
	        wp_redirect( $redirect_url );
	        exit;
	    }
	}
	
	
	// Do not allow access to the WP Admin for users who can't edit
	function subscriber_redirect_admin(){
		if ( ! current_user_can( 'edit_posts' ) && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && $file != 'admin-ajax.php'){
			wp_redirect( '/' );
			exit;		
		}
	}
	add_action( 'admin_init', 'subscriber_redirect_admin' );
	
	
	// Redirect Admin users to the WP Admin
	function redirect_by_role( $redirect_to, $request, $user ) {
		global $user;
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
			if ( in_array( 'administrator', $user->roles ) || in_array( 'client', $user->roles ) ) {
				return parse_url( get_admin_url() , PHP_URL_PATH);
			} else {
				if(isset($_REQUEST['redirect_to']) && $file != 'admin-ajax.php') {
				 	return $_REQUEST['redirect_to'];
    			}
			}
		}
	}
	add_filter( 'login_redirect', 'redirect_by_role', 10, 3 );
	
	
	
	// Redirect to login page after logout
	add_action( 'wp_logout', 'redirect_on_logout');
	function redirect_on_logout(){
		
		$redirect = '';
		if( isset($_GET['redirect_to']) ) {
			$redirect = '?redirect_to=' . $_GET['redirect_to'];
		}
		
		wp_redirect( __('/inloggen', VM_TEXTDOMAIN) . $redirect );
		exit();
	}
	
	
	
?>