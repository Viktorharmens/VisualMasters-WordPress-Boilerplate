<?php
	
	// Set global variables
	$textdomain = 'vm_login_plugin';
	
	// Set the textdomain as a constand
	define('VM_TEXTDOMAIN', $textdomain);
	
	// Get absolute path to wp-load file
	$parse_uri = explode( 'plugins', $_SERVER['SCRIPT_FILENAME'] );
	$root_uri = dirname($parse_uri[0]);
	
	// Set language directory
	add_action('plugins_loaded', 'wan_load_textdomain');
	function wan_load_textdomain() {
		load_plugin_textdomain( 'vm_login_plugin', false, '/vm-login/languages/' );
	}
	
	
	// Send a mail via Mandrill in VisualMasters styling
	function send_mandrill_mail( $email, $subject, $content ) {
		
		// Setup mandrill
		$mandrill = new Mandrill( 'TqjZ62sNhPcLwptu8SSa7A' );
		
		// Send the message
		try {
			$template_name = 'visualmasters';
		    $template_content = array(array('name' => 'main_content', 
		    							 	'content' => $content));
		    						  		
		    $mandrill = new Mandrill('q8wbpg3zpH17qKoGRMWaJg');
		    $message = array(
		        'subject' => $subject,
		        'from_email' => 'no-reply@visualmasters.nl',
		        'from_name' => 'VisualMasters',
		        'to' => array(
		            array(
		                'email' => $email,
		                'type' => 'to'
		            )
		        ),
		        'headers' => array('Reply-To' => 'support@visualmasters.nl'),
		        'tags' => $tags,
		        'template' => $template_name,
		        'subaccount' => 'visualmasters',
		    );
		    
		    $async = false;
		    $ip_pool = 'Main Pool';
		    $send_at = '';
		    $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message, $async, $ip_pool, $send_at);
		    
		    return true;
		    
		} catch(Mandrill_Error $e) {
			// There was an error, silently log this into the PHP error log
		    error_log( $e->getMessage() );
		    
		    return false;
		}
		
		
	}
	
	
?>
