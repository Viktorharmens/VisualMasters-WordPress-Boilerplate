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
	
	
	// Check the key
	$checkkey = check_password_reset_key($_GET['key'], $_GET['login']);
	
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		
		// Check key again in case it is fraudulously changed
		if( !is_wp_error($checkkey) ) {
			
			// Key is valid, continue process
			if(!empty($_POST['pass1']) && !empty($_POST['pass2'])) {
				
				if($_POST['pass1'] == $_POST['pass2']) {
					
					$uid = $checkkey->data->ID;
					$setPassword = wp_set_password($_POST['pass1'], $uid);
					
					wp_redirect( __('/inloggen', VM_TEXTDOMAIN) . '/?password-change=true');
					exit();
					
				} else {
					$errors = __('De wachtwoorden komen niet overeen. Probeer opnieuw.', VM_TEXTDOMAIN);
				}
				
			} else {
				$errors = __('De wachtwoorden zijn niet correct ingevuld. Probeer opnieuw.', VM_TEXTDOMAIN);
			}
			
		} else {
			$errors = __('Er is iets misgegaan bij het verwerken van uw nieuwe wachtwoord. Sluit dit venster en open de link uit de e-mail opnieuw. Blijft dit probleem bestaan? Neem dan contact op met systeembeheer.', VM_TEXTDOMAIN);
		}
		
	}
	
	
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>VisualMasters - <?php _e('Reset je wachtwoord', VM_TEXTDOMAIN); ?></title>
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
				
					<?php if ( isset($_GET['login']) && isset($_GET['key']) && !is_wp_error($checkkey) ) { ?>
					
						<form name="resetpassform" id="resetpassform" class="reset-password" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" autocomplete="off">
					        
					        <h3><?php _e('Wachtwoord instellen', VM_TEXTDOMAIN); ?></h3>
					         
					        <p class="description"><?php echo wp_get_password_hint(); ?></p>
					        
					       <?php if(!empty($errors)) echo '<p class="error">'.$errors.'</p>'; ?>
					        
					        <p class="login-password">
					            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
					            <label for="pass1"><?php _e('Nieuw wachtwoord', VM_TEXTDOMAIN); ?></label>
					        </p>
					        <p class="login-password">
					            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
					            <label for="pass2"><?php _e('Herhaal wachtwoord', VM_TEXTDOMAIN); ?></label>
					        </p>
					         
					        <p class="login-submit">
					        	<input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />
					        	<input type="hidden" name="rp_key" value="<?php echo esc_attr( $_GET['key'] ); ?>" />
					        	
					            <input type="submit" name="submit" id="resetpass-button" class="btn" value="<?php _e('Wachtwoord opnieuw instellen', VM_TEXTDOMAIN); ?>" />
					        </p>
					    </form>
					
					<?php } else { ?>
					
						<div class="lost-password-confirm">
							<h3><?php _e('Helaas!', VM_TEXTDOMAIN); ?></h3>
							<p class="description"><?php _e('De link is verlopen of niet geldig.', VM_TEXTDOMAIN); ?> <a href="<?php _e('/wachtwoord-vergeten/', VM_TEXTDOMAIN); ?>"><?php _e('Klik hier', VM_TEXTDOMAIN); ?></a> <?php _e('om je wachtwoord opnieuw te resetten.', VM_TEXTDOMAIN); ?></p>
						</div>
					
					<?php } ?>
				</div>
			
				<a href="/inloggen/" class="footer-text"><?php _e('Terug naar <strong>inloggen</strong>', VM_TEXTDOMAIN); ?></a>
				
			</div>
			
			<div class="slide-wrapper">
				<?php include_once '../parts/part-slider.php'; ?>
			</div>
			
			
		</div>
		
        <script type="text/javascript" src="<?php echo str_replace('pages/', '', plugin_dir_url(__FILE__)) . 'lib/js/min/plugins.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo str_replace('pages/', '', plugin_dir_url(__FILE__)) . 'lib/js/min/scripts.min.js?ver=' . rand(); ?>"></script>
		
	</body>
</html>
