<?php
	
	add_shortcode( 'button', function( $params ) {
		
		return '<div class="btn-container">
					<a href="' . $params['link'] . '" class="btn ' . (( !empty($params['color']) ) ? 'btn--' . $params['color'] : '' ) . ' ' . (( isset($params['class']) ) ? $params['class'] : '') . '" ' . (( !empty( $params['target'] ) ) ? 'target="' . $params['target'] . '"' : '') . '>' . $params['label'] . '</a>
				</div>';
		
	});
	
?>