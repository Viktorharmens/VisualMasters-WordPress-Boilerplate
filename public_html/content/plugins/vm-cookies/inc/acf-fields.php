<?php
	
	if( function_exists('acf_add_local_field_group') ):
	
		acf_add_local_field_group(array(
			'key' => 'group_5b041712448b4',
			'title' => 'Cookiebar instellingen',
			'fields' => array(
				array(
					'key' => 'field_5b0417204f166',
					'label' => 'Cookie notificatie',
					'name' => 'cookie_notification',
					'type' => 'textarea',
					'instructions' => 'Voer de tekst in voor de cookie melding. De link naar de privacy policy wordt automatisch aan het einde van de notificatie toegevoegd.',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => 'Wij maken gebruik van cookies voor verbetering van de gebruikerservaring en analyse van de website. Deze cookies bewaren geen persoonsgegevens.',
					'placeholder' => '',
					'maxlength' => '',
					'rows' => '',
					'new_lines' => '',
				),
				array(
					'key' => 'field_5b04172f4f167',
					'label' => 'Link naar privacy policy',
					'name' => 'privacy_policy_link',
					'type' => 'page_link',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'post_type' => array(
						0 => 'page',
					),
					'taxonomy' => array(
					),
					'allow_null' => 1,
					'allow_archives' => 0,
					'multiple' => 0,
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'acf-options-cookies',
					),
				),
			),
			'menu_order' => 10,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));
		
		
		if( class_exists( 'GFCommon' ) ) :
		
			acf_add_local_field_group(array(
				'key' => 'group_5b041a0cc533b',
				'title' => 'Verwerking persoonsgegevens via de Gravity Forms plug-in',
				'fields' => array(
					array(
						'key' => 'field_5b041a3c2ee3f',
						'label' => '',
						'name' => 'gravityforms',
						'type' => 'group',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layout' => 'block',
						'sub_fields' => array(
							array(
								'key' => 'field_5b041abb2ee42',
								'label' => 'Data verwerking',
								'name' => '',
								'type' => 'tab',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'placement' => 'left',
								'endpoint' => 0,
							),
							array(
								'key' => 'field_5b041a512ee40',
								'label' => 'Persoonsgegevens inzendingen',
								'name' => 'entries',
								'type' => 'select',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'all' => 'automatisch verwijderen voor alle formulieren',
									'select' => 'automatisch verwijderen voor geselecteerde formulieren',
									'none' => 'niet automatisch verwijderen',
								),
								'default_value' => array(
									0 => 'all',
								),
								'allow_null' => 0,
								'multiple' => 0,
								'ui' => 0,
								'ajax' => 0,
								'return_format' => 'value',
								'placeholder' => '',
							),
							array(
								'key' => 'field_5b041a962ee41',
								'label' => 'Inzendingen verwijderen voor',
								'name' => 'entries_forms',
								'type' => 'gravity_forms_field',
								'instructions' => 'Kies de formulieren waarvan de inzendingen automatisch verwijderd moeten worden.',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_5b041a512ee40',
											'operator' => '==',
											'value' => 'select',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'allow_null' => 0,
								'allow_multiple' => 1,
							),
							array(
								'key' => 'field_5b041ac52ee43',
								'label' => 'Notificatie',
								'name' => '',
								'type' => 'tab',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'placement' => 'left',
								'endpoint' => 0,
							),
							array(
								'key' => 'field_5b041b312ee48',
								'label' => 'Notificatie label',
								'name' => 'notification_label',
								'type' => 'text',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => 'Hoe wij uw gegevens verwerken',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_5b041b3b2ee49',
								'label' => 'Notificatie content',
								'name' => 'notification_content',
								'type' => 'wysiwyg',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => 'Dit formulier wordt per e-mail verzonden naar een centraal e-mailadres. Uw persoonsgegevens worden niet in onze database opgeslagen. De gegevens die u bij dit formulier invult zullen worden gebruikt om contact met u op te nemen. Wij verstrekken nooit zonder uw toestemming gegevens aan derden. Voor meer informatie verwijzen wij u naar onze privacy statement. Uw data wordt veilig verzonden middels een SSL-certificaat, herkenbaar aan het slotje in de URL-balk van uw browser. Het SSL-certificaat versleuteld uw gegevens voordat deze per e-mail aan ons worden verzonden.',
								'tabs' => 'all',
								'toolbar' => 'basic',
								'media_upload' => 0,
								'delay' => 1,
							),
							array(
								'key' => 'field_5b041b052ee47',
								'label' => 'Notificatie tonen',
								'name' => 'notifications',
								'type' => 'select',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'all' => 'automatisch toevoegen aan alle formulieren',
									'select' => 'automatisch toevoegen voor geselecteerde formulieren',
									'none' => 'bij geen enkel formulier tonen',
								),
								'default_value' => array(
									0 => 'all',
								),
								'allow_null' => 0,
								'multiple' => 0,
								'ui' => 0,
								'ajax' => 0,
								'return_format' => 'value',
								'placeholder' => '',
							),
							array(
								'key' => 'field_5b041ae32ee46',
								'label' => 'Notificatie tonen bij',
								'name' => 'notification_forms',
								'type' => 'gravity_forms_field',
								'instructions' => 'Kies de formulieren waarbij de melding moet worden opgenomen',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_5b041b052ee47',
											'operator' => '==',
											'value' => 'select',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'allow_null' => 0,
								'allow_multiple' => 1,
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'acf-options-cookies',
						),
					),
				),
				'menu_order' => 20,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
			));
	
		endif;
	
	endif;
	
?>