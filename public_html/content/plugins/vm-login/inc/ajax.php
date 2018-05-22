<?php
	
	add_action( 'wp_ajax_generate_google_authenticator_code', 'generate_google_authenticator_code' );
	function generate_google_authenticator_code() {
		
		// Load the Google Authenticator 
		$ga = new PHPGangsta_GoogleAuthenticator();
		
		// Generate a code
		$secret = $ga->createSecret();
		$qrlink = $ga->getQRCodeGoogleUrl($_POST['username'], $secret, 'VisualMasters');
		
		
		echo json_encode(array('code' => $secret, 'qrlink' => $qrlink));
		wp_die();
	}


	
?>