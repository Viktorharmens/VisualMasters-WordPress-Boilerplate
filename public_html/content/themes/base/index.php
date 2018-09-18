<?php
	
	get_header();
	
	$rows = get_field('rows');
	foreach( $rows as $key => $row ) {
		
		get_page_part( 'rows' , $row['acf_fc_layout'], $row );
		
	}
	
	get_footer();
	
?>