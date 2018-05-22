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
	
	
	// Check the Google Authenticator Code and process the data
	if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gauth-submit'])) {
		do_action('verify_code', wp_create_nonce( 'verify-google-authenticator' ) );
	}
	
	// Set the current timezone
	if( !empty(get_option('timezone_string')) ) {
		date_default_timezone_set( get_option('timezone_string') );
	} else {
		date_default_timezone_set( 'Europe/Amsterdam' );
	}
	
	// TO DO: Hier nog een optie inbouwen waarbij javascript checkt of de 
	//       timezone wel hetzelfde is als PHP gebruikt om de begroeting te corrigeren
	
	// Get the current time
    $b = time();

    $hour = date("g", $b);
    $m    = date("A", $b);

    if ($m == "AM") {
		if ($hour == 12) {
			$greeting = __('Goedenavond', VM_TEXTDOMAIN);
		} elseif ($hour < 4) {
			$greeting = __('Goedenavond', VM_TEXTDOMAIN);
		} elseif ($hour > 3) {
			$greeting = __('Goedemorgen', VM_TEXTDOMAIN);;
		}
    } elseif ($m == "PM") {
		if ($hour == 12) {
			$greeting = __('Goedemiddag', VM_TEXTDOMAIN);
		} elseif ($hour < 6) {
			$greeting = __('Goedemiddag', VM_TEXTDOMAIN);
		} elseif ($hour > 5) {
			$greeting = __('Goedenavond', VM_TEXTDOMAIN);
		}
    }
	
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>VisualMasters - <?php _e('Inloggen', VM_TEXTDOMAIN); ?></title>
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
				
				<div class="login-form-wrapper form-wrapper">
					
					<?php 
						
						// Check if it is a regular login
						if(!isset($_GET['google-authenticator'])) {
							echo '<h3>' . $greeting . '!</h3>
								  <p class="description">' . __('Vul je gegevens in om in te loggen.', VM_TEXTDOMAIN) . '</p>';
							
							if(isset($_GET['password-change']) && $_GET['password-change'] == 'true') {
								echo '<p class="error">' . __('Het wachtwoord is gewijzigd. Je kunt nu inloggen met het door jou gekozen nieuwe wachtwoord.', VM_TEXTDOMAIN) . '</p>';
							}
							
							if(isset($_GET['login'])) {
								echo '<p class="error">';
								
								if($_GET['login'] == 'failed') {
									_e('De ingevoerde gebruikersnaam en wachtwoord komen niet overeen. ', VM_TEXTDOMAIN);
									
									// If brute force is active, add message with remaining attempts
									if( isset($_GET['attempts']) ) {
										printf( _n( 'Let op, je hebt nog maar <strong>%d poging</strong>.', 'Let op, je hebt nog maar <strong>%d pogingen</strong>.', $_GET['attempts'], VM_TEXTDOMAIN  ), $_GET['attempts'] );
									}
								} elseif($_GET['login'] == 'empty') {
									_e('Je hebt geen gebruikersnaam of wachtwoord ingevoerd. Probeer opnieuw.', VM_TEXTDOMAIN);
								} else {
									_e('Inloggen mislukt. Probeer opnieuw.', VM_TEXTDOMAIN);
								}
								
								echo '</p>';
							}
							
							
							echo '<form name="loginform" id="loginform" action="' . site_url( '/wp-login.php', 'login' ) . '" method="post">
									<p class="login-username">
										<input type="text" name="log" id="user_login" class="input" value="" size="20">
										<label for="user_login">' . __('Gebruikersnaam', VM_TEXTDOMAIN) . '</label>
									</p>
									<p class="login-password">
										<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
										<label for="user_pass">' . __('Wachtwoord', VM_TEXTDOMAIN) . '</label>
									</p>
									<p class="login-submit">
										<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="' . __('Inloggen', VM_TEXTDOMAIN) . '">
										<input type="hidden" name="redirect_to" value="' . $_GET['redirect_to'] . '">
									</p>
								</form>';
						
						} 
						
						// If not a regular login, then it is a Google Authenticator session
						else {
							
							// First, check if the nonce is still valid
							if( wp_verify_nonce( $_GET['_nonce'], 'google-authenticator' ) ) {
								echo '<h3>'. __('Dubbele factor authenticatie', VM_TEXTDOMAIN) . '</h3>
									  <p class="description">' . __('Dubbele authenticatie is ingeschakeld voor dit account. Voer uw code in.', VM_TEXTDOMAIN) . '</p>';
								
								if(isset($_GET['login'])) {
									echo '<p class="error">';
									
									if($_GET['login'] == 'failed') {
										_e('De ingevoerde code is onjuist. Probeer opnieuw.', VM_TEXTDOMAIN);
									} else {
										_e('Verificatie mislukt. Probeer opnieuw.', VM_TEXTDOMAIN);
									}
								}
								
								echo '<form name="loginform" id="loginform" action="" method="post">			
											<p class="login-username">
												<input type="text" name="authcode" id="google_auth_code" class="input" value="" size="20">
												<label for="google_auth_code">' . __('Code', VM_TEXTDOMAIN) . '</label>
											</p>
											
											<p class="login-submit">
												<input type="submit" name="gauth-submit" id="gauth-submit" class="button button-primary" value="' . __('Code controleren', VM_TEXTDOMAIN) . '">
												<input type="hidden" name="redirect_to" value="' . ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '">
											</p>
											
										</form>';
								
								
							} else {
								echo '<h3>' . __('Oeps, foutje...', VM_TEXTDOMAIN) . '</h3>
									  <p class="description">' . __('Oeps, er ging iets mis met het versturen van de inloggegevens. <a href="/inloggen/">Klik hier</a> om het opnieuw te proberen.', VM_TEXTDOMAIN) . '</p>';
							}
							
							
						}
					?>
					
				</div>
				
				<?php if(!isset($_GET['google-authenticator'])) : ?>
					<a href="<?php _e('/wachtwoord-vergeten/', VM_TEXTDOMAIN); ?>" class="footer-text"><strong><?php _e('wachtwoord vergeten?', VM_TEXTDOMAIN); ?></strong></a>
				<?php endif; ?>
				
			</div>
				
			<div class="slide-wrapper">
				<?php include_once '../parts/part-slider.php'; ?>
			</div>
			
		</div>
		
        <script type="text/javascript" src="<?php echo str_replace('pages/', '', plugin_dir_url(__FILE__)) . 'lib/js/min/plugins.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo str_replace('pages/', '', plugin_dir_url(__FILE__)) . 'lib/js/min/scripts.min.js?ver=' . rand(); ?>"></script>
		
	</body>
</html>
