<?php
	
	
	// Check if ACF is included in the theme, otherwise load as dependency
	if( !function_exists('acf_add_options_page') ) {
        include_once plugin_dir_path( __FILE__ ) . '/app/advanced-custom-fields-pro/acf.php';
        
        if( !function_exists('include_field_types_Gravity_Forms') ) {
        	include_once plugin_dir_path( __FILE__ ) . '/app/gravityforms-acf-population/acf-gravity_forms.php';
        }
    }
    
    // Delete standard privacy menu link
    add_action( 'admin_menu', 'remove_privacy_page' );
    function remove_privacy_page() {
    	remove_submenu_page( 'options-general.php', 'privacy.php' );
    }
    
    
    // Setup option pages
	if( function_exists('acf_add_options_page') ) {
		
		// Register the options page as a subpage
		acf_add_options_page(array(
			'page_title' 	=> 'Cookies',
			'menu_title'	=> 'Cookies',
			'redirect'		=> false,
			'position' 		=> 3,
			'parent_slug' 	=> 'options-general.php'
		));
		
	}
	
	
	
	
	
	if( function_exists('get_field') ) {
		
		
		// ==========================================
		// Add the cookie notification to the footer
		// ==========================================
		add_action( 'wp_footer', 'load_cookie_notification' );
		function load_cookie_notification() {
			
			echo '<div id="cookie-notification">
					<div class="notification">
						<div class="notification__content">
							' . get_field('cookie_notification', 'option') . '
							' . (( empty(get_field('privacy_policy_link', 'option')) ) ? '' : '<a href="' . get_field('privacy_policy_link', 'option') . '">' . __('Klik hier', 'vm-cookies') . '</a> voor de privacy policy.' ) . '
						</div>
					</div>
					<a href="#" class="js-close-cookie-notification notification__close">&times;</a>
				</div>';
					
					
			// Include javascript and css for the notification
			add_action( 'wp_enqueue_scripts', 'vm_cookies_enqueue_scripts_styles' );
			
		}
		
		
		
		
		
		// ====================================================
		// Triggers an entry removal if applicable to the form
		// ====================================================
		add_filter('gform_after_submission', 'remove_gravityforms_entry', 20, 2);
		function remove_gravityforms_entry( $entry, $form ) {
			
			// Get the settings
			$gdpr_settings = get_field('gravityforms', 'option');
			
			if( $gdpr_settings['entries'] == 'all' ) {
				GFAPI::delete_entry( $entry['id'] );
			} 
			
			if( $gdpr_settings['entries'] == 'select' ) {
				foreach( $gdpr_settings['entries_forms'] as $key => $gform ) {
					if( $gform['id'] == $form['id'] ) {
						GFAPI::delete_entry( $entry['id'] );
					}
				}
			}
			
		}
		
		
		// =========================================
		// Shows the GDPR content beneath the forms
		// =========================================
		add_action( 'init', 'load_gdpr_content' );
		function load_gdpr_content() {
			$gdpr_settings = get_field('gravityforms', 'option');
			
			if( $gdpr_settings['notifications'] == 'all' ) {
				add_filter( 'gform_submit_button', 'add_gdpr_form_footer', 10, 2 );
				add_filter( 'gform_next_button', 'add_gdpr_form_footer', 10, 2 );
				add_action( 'wp_enqueue_scripts', 'vm_cookies_enqueue_scripts_styles' );
			} 
			
			if( $gdpr_settings['notifications'] == 'select' ) {
				foreach( $gdpr_settings['notification_forms'] as $key => $form ) {
					
					add_filter( 'gform_submit_button_' . $form['id'], 'add_gdpr_form_footer', 10, 2 );
					add_filter( 'gform_next_button_' . $form['id'], 'add_gdpr_form_footer', 10, 2 );
					
					// Include javascript and css for the notification
					add_action( 'wp_enqueue_scripts', 'vm_cookies_enqueue_scripts_styles' );
					
				}
			}
		}
		
		
		// =====================================
		// Show the notice at the correct place
		// =====================================
		function add_gdpr_form_footer( $button, $form ) {
			
			$content = get_field('gravityforms', 'option');
			
			// Build HTML
			$gdpr = '<div class="gform_gdpr-notice">
						<a href="#" class="gform_gdpr-notice__title js-toggle-gdpr">' . $content['notification_label'] . '</a>
						<div class="gform_gdpr-notice__content">' . $content['notification_content'] . '</div>
					 </div>';
			
			// Add button and GDPR content
			return $button . $gdpr;
			
		}
		
		
		// ========================================
		// Add the JS and CSS for the notification
		// ========================================
		function vm_cookies_enqueue_scripts_styles() {
			global $wp_scripts;
			wp_enqueue_style( 'vm-cookies', plugin_dir_url( __FILE__ ) . '/lib/vm-cookies.css', null, '1.0.0' );
			wp_enqueue_script( 'vm-cookies-scripts', plugin_dir_url( __FILE__ ) . '/lib/js/vm-cookies.js', 'jquery', '1.0.0', true );
		}
		
	
	}
    
    
    
	
	
?>