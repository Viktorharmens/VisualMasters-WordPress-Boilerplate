<?php
	
	/* This file holds all the functions for the admin panels */
	
	
	function vm_google_authenticator_box( $user ) { 
		
		// Include the Google Authenticator Class
		$ga = new PHPGangsta_GoogleAuthenticator();
		
		// Get the Google Authenticator code
		$gacode = get_user_meta( $user->data->ID, '_ga_code', true ); ?>
		
		<h2><?php _e('Twee factor authenticatie', VM_TEXTDOMAIN) ?></h2>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="enable-google-authenticator">Google Authenticator</label></th>
					<td>
						<label for="enable-google-authenticator">
							<input type="checkbox" name="enable_ga" id="enable-google-authenticator" <?php echo ( (!empty($gacode)) ? 'checked="checked"' : '' ); ?> />
							<?php _e('Google Authenticator gebruiken als tweede factor authenticatie.', VM_TEXTDOMAIN) ?>
							<a href="#" class="js-show-ga-extra-info"><?php _e('Meer informatie?', VM_TEXTDOMAIN); ?></a>
						</label>
						
						<div class="description ga-extra-info" style="display: none;">
							<p>
								<?php _e('Google Authenticator is een Android en iOS App voor op je telefoon welke continu zes cijferige codes genereert. Deze codes vervallen na 30 seconden en worden niet hergebruikt. Door uw account te beveiligen met Google Authenticator voorkomt u ongewenste toegang tot uw account op deze website. U kunt de applicatie eenvoudig downloaden via de Google Play Store of de App Store. Vervolgens genereert u in de volgende stap de beveiligingscode en scant de QR-code via de Google Authenticator app.', VM_TEXTDOMAIN); ?>
							</p>
							<ul>
								<li><a href="https://support.google.com/accounts/answer/1066447?hl=nl&co=GENIE.Platform=Android" target="_blank"><?php _e('Handleiding Android', VM_TEXTDOMAIN); ?></a></li>
								<li><a href="https://support.google.com/accounts/answer/1066447?hl=nl&co=GENIE.Platform%3DiOS&oco=0" target="_blank"><?php _e('Handleiding iOS', VM_TEXTDOMAIN); ?></a></li>
							</ul>
						</div>
					</td>
				</tr>
				
				<tr class="js-show-if-ga-enabled" <?php echo ( (empty($gacode)) ? 'style="display: none;"' : '' ); ?>>
					<th><?php echo ((empty($gacode)) ? __('Code genereren', VM_TEXTDOMAIN) : __('Code opnieuw genereren', VM_TEXTDOMAIN) ); ?></th>
					<td>
						<button type="button" class="button js-generate-google-authenticator-code hide-if-no-js">Google Authenticator <?php echo ((empty($gacode)) ? __('code genereren', VM_TEXTDOMAIN) : __('code opnieuw genereren', VM_TEXTDOMAIN) ); ?></button>
						
						<div class="secret-codes" <?php echo ( (empty($gacode)) ? 'style="display: none;"' : 'style="margin-top: 20px;"' ); ?>>
							<div class="row">
								<label for="backup-code">Back-up code</label>
								<input type="text" name="google-authenticator-secret" id="backup-code" class="js-backup-code" value="<?php echo $gacode; ?>" readonly="" />
							</div>
							
							<div class="qrcode">
								<img src="<?php echo ((!empty($gacode)) ? $ga->getQRCodeGoogleUrl($user->data->user_email, $gacode, 'VisualMasters') : '') ?>" width="200" height="200" class="js-qrcode-link" />
							</div>
						</div>
						
					</td>
				</tr>
				
			
			</tbody>
		</table>
		
		<script type="text/javascript">
			(function($) {
				
				$('.js-show-ga-extra-info').on('click', function(e) {
					e.preventDefault();
					$('.ga-extra-info').slideToggle();
					
					var text = $(this).text();
							   $(this).text(text === "<?php _e('Meer informatie?', VM_TEXTDOMAIN); ?>" ? "<?php _e('Sluit informatie', VM_TEXTDOMAIN); ?>" : "<?php _e('Meer informatie?', VM_TEXTDOMAIN); ?>");
				});
				
				$('#enable-google-authenticator').on('click', function() {
					if($(this).is(':checked')) {
						$('.js-show-if-ga-enabled').show();
					} else {
						$('.js-show-if-ga-enabled').hide();
					}
				});
				
				
				$('.js-generate-google-authenticator-code').on('click', function(e) {
					
					// Disable button
					$(this).prop('disabled', 'disabled');
					var button = $(this);
					
					$('.secret-codes').addClass('loading');
					
					// Get the right response
					$.ajax({
						url: "<?php echo admin_url('admin-ajax.php'); ?>",
						type: 'POST',
						data: { action: 'generate_google_authenticator_code', username: '<?php echo $user->data->user_email; ?>' },
						success: function(response) {
							
							// Parse the result
							var data = JSON.parse(response);
							
							// Set the QR Code link and Back-up code in the right fields
							$('.js-backup-code').val(data.code);
							$('.js-qrcode-link').prop('src', data.qrlink);
							
							// Show the code and QR
							$('.secret-codes').slideDown().animate({ marginTop: '0px' });
							$('.secret-codes').removeClass('loading');
							
							// Remove the button
							button.delay(200).slideUp('fast');
						}
					});
					
				});
				
				
			})(jQuery);
		</script>
		
		<style type="text/css">
			.ga-extra-info {
				background: white;
				border: solid 1px #ddd;
				padding: 10px;
				margin-top: 15px;
			}
			
			.secret-codes .row {
				position: relative;
				overflow: hidden;
			}
			
			.secret-codes .row label {
				display: block;
				font-weight: bold;
			}
			
			.secret-codes .row input[type=text] {
				max-width: 100%;
				width: 240px;
			}
			
			.secret-codes .qrcode {
				position: relative;
				overflow: hidden;
				margin-top: 20px;
			}
			
			.secret-codes.loading {
				-webkit-opacity: 0.5;
				-moz-opacity: 0.5;
				opacity: 0.5;
			}
			
			
		</style>
		<?php
	
	}
	add_action( 'show_user_profile', 'vm_google_authenticator_box' );
	add_action( 'edit_user_profile', 'vm_google_authenticator_box' );
	
	
	
	
	// Save the back-up code in the user meta
	function save_google_authenticator_code($user_id) {
		
		if($_POST['enable_ga']) {
			// Save the secret key as back-up code
			update_usermeta( $user_id, '_ga_code', $_POST['google-authenticator-secret'] );
		} else {
			// Remove the ga code when it is disabled
			delete_user_meta( $user_id, '_ga_code');
		}
		
	}
	add_action( 'personal_options_update', 'save_google_authenticator_code' );
	add_action( 'edit_user_profile_update', 'save_google_authenticator_code' );
	
	
?>