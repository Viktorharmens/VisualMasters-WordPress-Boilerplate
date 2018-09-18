<?php
	
	// Set current page link
	global $post;
	$pageid = $post->ID;
	$parentid = $post->post_parent;
	
	// Array to store the links
	$links = array();
	
	
	// Check if only siblings need to be shown
	if( $content['show_default'] ) {
		
		// Find the siblings based on parent id
		// Set parent as top result
		$parents = (( $parentid != 0 ) ? $parentid : $pageid );
		$links[] = array('id' => $parents,
				 		 'link' => get_the_permalink($parents),
				 		 'title' => get_the_title($parents));
		
		// Add the siblings
		$pages = get_pages( array('parent' => (( $parentid != 0 ) ? $parentid : $pageid) , 'showposts' => -1) );
		
		foreach($pages as &$child) {
			$links[] = array('id' => $child->ID,
							 'link' => get_the_permalink($child->ID),
							 'title' => get_the_title($child->ID));
		}
		
	}
	
	// Own links are entered
	else {
		foreach( $content['pagelinks'] as &$link ) {
			
			$links[] = array('id' => url_to_postid( $link['link']['url'] ),
							 'link' => $link['link']['url'],
							 'title' => get_the_title( url_to_postid( $link['link']['url'] ) ));
							 
		}
	}
	
	
?>
<div class="sidebar__block sidebar__block--pagelinks">
	<?php
		
		echo '<ul>';
		
		foreach( $links as &$link ) {
			echo '<li ' . (( $link['id'] == $pageid ) ? 'class="is--active"' : '') . '>
					  <a href="' . $link['link'] . '" ' . (( !empty($link['target']) ) ? 'target="' . $link['target'] . '"' : '') . '>' . $link['title'] . '</a>
				  </li>';
		}
		
		echo '</ul>';
		
		
		// Check if footer needs to be shown
		if( !empty($content['footer_link']) ) {
			echo '<div class="sidebar__block__footer">
					  <a href="' . $content['footer_link']['url'] . '" class="btn btn--inline" ' . (( !empty($content['footer_link']['target']) ) ? 'target="' . $content['footer_link']['target'] . '"' : '') . '>' . $content['footer_link']['title'] . '</a>
				  </div>';
		}
		
	?>
</div>