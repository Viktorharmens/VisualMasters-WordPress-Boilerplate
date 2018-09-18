<?php
	
	get_header();
	
	$post = get_field('404_page', 'option');
		
	$rows = get_field('rows', $post->ID);
	foreach( $rows as $key => $row ) {
		
		get_page_part( 'rows' , $row['acf_fc_layout'], $row );
		
	}

	
	// Reset the postdata
	wp_reset_postdata();
	
	
	get_footer();
	
?>