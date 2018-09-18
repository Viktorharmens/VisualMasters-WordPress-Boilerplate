<div class="sidebar__block sidebar__block--recent-posts">
	
	<?php 
		
		// Set the title
		echo (( !empty($content['title']) ) ? '<span class="heading heading--small">' . $content['title'] . '</span>' : '');
		
		
		// Get posts
		$args = array('post_type' => (( !isset($content['posttype']) ) ? 'post' : $content['posttype']),
					  'showposts' => (( !empty($content['showposts']) ) ? $content['showposts'] : 3 ),
					  'orderby' => 'post_date',
					  'order' => 'DESC');
		
		if( !empty($content['categories']) ) {
			$args['tax_query'] = array('relation' => 'OR',
										array('taxonomy' => 'category',
											  'field' => 'term_id',
											  'terms' => $content['categories'],
											  'include_children' => false,
											  'operator' => 'IN'));
		}
		
		
		$query = new WP_Query($args);
		if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
			
			$post_category = wp_get_post_terms( get_the_id(), 'category' );
			
			echo '<a href="' . get_the_permalink() . '" class="recent-post">
					  <span class="recent-post__meta">' . (( !empty($post_category) ) ? ucfirst( $post_category[0]->slug ) . ' - ' : '' ) . get_the_time( 'j F Y', get_the_id() ) . '</span>
					  <span class="recent-post__heading">' . get_the_title() . '</span>
				  </a>';
			
		endwhile; endif;
		
		
		// Set the footer
		if ( !empty($content['link']) ) {
			echo '<div class="sidebar__block__footer">
					  <a href="' . $content['link']['url'] . '" ' . (( !empty($content['link']['target']) ) ? 'target="' . $content['link']['target'] . '"' : '') . ' class="btn btn--inline">
					  	 ' . $content['link']['title'] . '
					  </a>
				  </div>';
		}  
		
	?>
	
</div>