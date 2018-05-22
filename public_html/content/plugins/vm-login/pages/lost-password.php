<?php
	
	// Get absolute path to wp-load file
	$parse_uri = explode( 'plugins', $_SERVER['SCRIPT_FILENAME'] );
	$root_uri = dirname($parse_uri[0]);
	
	// Check if it is a new install
	if( file_exists($root_uri . '/wp/wp-load.php') ) {
		require_once dirname($parse_uri[0]) . '/wp/wp-load.php';
	} else {
		require_once dirname($parse_uri[0]) . '/wp-load.php';
	}
	
	if(is_user_logged_in()) {
		$requested_url = home_url( $wp->request );
	    wp_redirect('/');
	    exit;
	}
	
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		if( !empty($_POST['user_login']) ) {
			
			$username = $_POST['user_login'];
			
			if (username_exists($username)) {
	            $user_exists = true;
	            $user_data = get_userdatabylogin($username);
	        } elseif (email_exists($username)) {
	            $user_exists = true;
	            $user = get_user_by('email', $username);
	        } else {
	            $errors = __('Dit e-mailadres is niet gekoppeld aan een account. Neem contact op met systeembeheer.', VM_TEXTDOMAIN);
	        }
	        
	        // Proceed if the user exists
	        if ($user_exists) {
		        
		        // Get
	            $user_login = $user->user_login;
	            $user_email = $user->user_email;
	            
	            // Retrieve password action
	            do_action('retrieve_password', $user_login);
	            
	            // Set new activation key
	            $key = wp_generate_password( 20, false );
				do_action( 'retrieve_password_key', $user_login, $key );
				
				// Call the hashing method
				require_once ABSPATH . 'wp-includes/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
				
				// Hash the key
				$hashed = $wp_hasher->HashPassword( $key );
				
				// Set hashed key to database
				global $wpdb;
				$wpdb->update( $wpdb->users, array( 'user_activation_key' => time().':'.$hashed ), array( 'user_login' => $user_login ) );
				
				// Set the URL for resetting the password
				$url = str_replace('/wp/', '/', site_url( __('wachtwoord-reset/', VM_TEXTDOMAIN) . "?action=rp&key=$key&login=" . rawurlencode($user_login)));
				
				// Send e-mail to the user
				$message  = sprintf( __('Er is een wachtwoord wijziging aangevraagd via de website voor het account: %s', VM_TEXTDOMAIN) , $user_login).'<br /><br />';
				$message .= __('Als dit niet het geval is kan je deze mail negeren en dan gebeurt er niks. Ontvang je vaker deze meldingen? Neem dan contact op met de systeembeheerder', VM_TEXTDOMAIN) . '.<br /><br />';
				$message .= __('Om uw wachtwoord opnieuw in te stellen ', VM_TEXTDOMAIN);
				$message .= '<a href="'.$url.'" style="color:#00bdf2;text-decoration:none;">' . __('klikt u hier', VM_TEXTDOMAIN) . '</a> ' . __('of als dit niet werkt kopieert en plakt u de volgende link in uw browser:', VM_TEXTDOMAIN) . '<br /><br />';
				$message .= '<a href="'.$url.'" style="color:#000000;text-decoration:none;"><small><em>'.$url.'</em></small></a>';
			
			    $title = __('Wachtwoord opnieuw instellen', VM_TEXTDOMAIN);
			    $title = apply_filters('retrieve_password_title', $title);
			    $message = apply_filters('retrieve_password_message', $message, $key);
			    
			    // Send the mail
			   if( send_mandrill_mail($user_email, $title, $message) ) {
				    $success = true;
			    } else {
				    $errors = __('De mail kon niet worden verzonden. Neem contact op met systeembeheer om dit te melden. Excuses voor het ongemak.', VM_TEXTDOMAIN);
			    }
			    
	        }
	        
			
		} else {
			$errors = __('Je hebt geen e-mailadres ingevuld. Probeer opnieuw.', VM_TEXTDOMAIN);
		}
		
	}
	
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>VisualMasters - <?php _e('Wachtwoord vergeten', VM_TEXTDOMAIN); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <link rel="stylesheet" href="<?php echo str_replace('pages/', '', plugin_dir_url(__FILE__)) . 'lib/style.css?ver=' . rand(); ?>" type="text/css" media="all" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Droid+Serif:400,700|Montserrat:400,700" type="text/css" media="all" />
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    </head>
    <body class="login-page">
	
		<div class="page-wrapper">
			
			<div class="login-form">
							
				<i class="logo"></i>
				<i class="separator"></i>
			
				<div class="form-wrapper">
					<?php if(!isset($success)) { ?>
						
						<div id="password-lost-form" class="lost-password-form">
							
						   	<h3><?php _e('Wachtwoord vergeten?', VM_TEXTDOMAIN); ?></h3>
						 
						    <p class="description"><?php _e('Voer je e-mailadres in en we sturen je een link zodat u een nieuw wachtwoord in kan stellen.', VM_TEXTDOMAIN); ?></p>
						    
						    <?php if(!empty($errors)) echo '<p class="error">'.$errors.'</p>'; ?>
						 
						    <form id="lostpasswordform" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
						        <p class="login-username">
						            <input type="text" name="user_login" id="user_login" />
						            <label for="user_login"><?php _e('E-mailadres', VM_TEXTDOMAIN); ?></label>
						        </p>
						 
						        <p class="login-submit">
						            <input type="hidden" name="reset_pass" value="1" />
						            <input type="submit" name="submit" class="btn lostpassword-button" value="<?php _e('Wachtwoord herstellen', VM_TEXTDOMAIN); ?>" />
						        </p>
						    </form>
						</div>
						
					<?php } else { ?>
					
						<div class="lost-password-confirm">
							<h3><?php _e('E-mail verzonden', VM_TEXTDOMAIN); ?></h3>
							<p class="description"><?php _e('De mail met instructies over hoe je jouw wachtwoord kunt wijzigen is onderweg. Dit kan een paar minuten duren. Heb je de mail niet ontvangen? Controleer dan je SPAM box of neem contact op met systeembeheer.', VM_TEXTDOMAIN); ?></p>
						</div>
					
					<?php } ?>
				</div>
				
				
				<a href="<?php _e('/inloggen/', VM_TEXTDOMAIN); ?>" class="footer-text"><?php _e('Terug naar <strong>inloggen</strong>', VM_TEXTDOMAIN); ?></a>
			
			</div>
			
			<div class="slide-wrapper">
				<?php include_once '../parts/part-slider.php'; ?>
			</div>
			
			
		</div>
		
        <script type="text/javascript" src="<?php echo str_replace('pages/', '', plugin_dir_url(__FILE__)) . 'lib/js/min/plugins.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo str_replace('pages/', '', plugin_dir_url(__FILE__)) . 'lib/js/min/scripts.min.js?ver=' . rand(); ?>"></script>
		
	</body>
</html>
