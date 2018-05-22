jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.vm_cta_button', {
		init: function( editor, url ) {
			
			// Add button that inserts shortcode into the current position of the editor
			editor.addButton( 'vm_cta_button', {
				title: 'Knop invoegen',
				image: url + '/cta-button-icon.svg',
				onclick: function() {
					
					// Open a TinyMCE modal
					editor.windowManager.open({
						title: 'Button invoegen',
						body: [{
							type: 'textbox',
							name: 'label',
							label: 'Label'
						},{
							type: 'textbox',
							name: 'link',
							label: 'Link'
						},{
							type   : 'listbox',
		                    name   : 'color',
		                    label  : 'Kleur',
		                    values : [
		                        { text: 'Groen', value: 'green' },
		                        { text: 'Wit', value: 'white' },
		                        { text: 'Grijs', value: 'grey' },
		                        { text: 'Zwart', value: 'black' }
		                    ],
		                    value : 'green'
						}],
						onsubmit: function( e ) {
							editor.insertContent( '[button link="' + e.data.link + '" label="' + e.data.label + '" color="' + e.data.color + '"]' );
						}
					});
				}
			});
		},
		
		createControl: function( n, cm ) {
			return null;
		}
	});
    

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('vm_cta_button', tinymce.plugins.vm_cta_button);
    
    
    
});