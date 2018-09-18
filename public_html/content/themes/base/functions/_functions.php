<?php
	
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	
	// =================================================
	// Function for displaying the breadcrumbs directly
	// =================================================
	function the_breadcrumbs() {
		if ( function_exists('yoast_breadcrumb') ) { 
			
			$prefix = __('U bent hier:', 'visualmasters');
			if(is_search()) $prefix = __('U zocht naar:', 'visualmasters');
			
			yoast_breadcrumb('<span class="breadcrumbs__prefix">' . $prefix . '</span>');
		}
	}
	
	
	
	// ==========================================================================
	// Function to get an excerpt with length input, also available with page ID
	// ==========================================================================
	function get_max_excerpt($charlength, $postid=null) {
		if($postid == null) {
			global $post;
			$postid = $post->ID;
		}
		
		$get_post = get_post($postid);
		$content = $get_post->post_content;
		$excerpt = wp_strip_all_tags($content);
		$charlength++;
		$returnvalue;
		
		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				$returnvalue = rtrim(mb_substr( $subex, 0, $excut ));
			} else {
				$returnvalue = $subex;
			}
			$returnvalue .= '...';
		} else {
			$returnvalue = rtrim($excerpt);
		}
		return $returnvalue;
	}
	
	// Variant with direct echo
	function the_max_excerpt($charlength, $postid=null) {
		echo get_max_excerpt($charlength, $postid);
	}
	
	add_filter('excerpt_more', 'new_excerpt_more');
	function new_excerpt_more( $more ) {
		return '...';
	}

	
	
	// ===================================================================
	// Get the pagination for use on archive, categoru and taxonomy pages
	// ===================================================================
	function get_pagination() {
		global $wp_query;
		$big = 999999999;
		return str_replace('prev page-numbers', 'prev', 
				str_replace('next page-numbers', 'next', 
							paginate_links( array('base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
												  'format' => '?paged=%#%',
												  'current' => max( 1, get_query_var('paged') ),
												  'total' => $wp_query->max_num_pages,
												  'prev_text' => '<span>' . __('Vorige pagina', 'visualmasters') . '</span>',
												  'next_text' => '<span>' . __('Volgende pagina', 'visualmasters') . '</span>'
				) ) ) );
	}
	
	function the_pagination() {
		echo get_pagination();
	}
	
	function has_pagination() {
		global $wp_query;
		return ($wp_query->max_num_pages > 1);
	}

	
	
	// ===================================================
	// Function to check if a post type is equal to input
	// ===================================================
	function is_post_type($type){
	    global $wp_query;
	    if(isset($wp_query->post->ID) && $type == get_post_type($wp_query->post->ID)) return true;
	    return false;
	}
	
	
	
	// ===================================
	// Make post time readable for humans
	// ===================================
	function the_readable_post_time($time = null, $echo = true) {
		global $post;

		$timediff = human_time_diff( (($time == null) ? get_the_time('U', $post) : strtotime($time)) , current_time('timestamp'));		
		if($echo == true) {
			printf( esc_html__( '%s geleden geplaatst', 'visualmasters' ), $timediff );
		} else {
			return sprintf( esc_html__( '%s geleden geplaatst', 'visualmasters' ), $timediff );
		}
	}
	
	
	
	// =====================================================================
	// Get the featured image, if not available: use a fallback placeholder
	// =====================================================================
	function get_featured_image( $size = 'thumbnail', $postid = null, $type = 'lazyload', $scale = true, $fallback = 'post' ) {
		// Get the post id, if null, get post object
		if($postid == null) {
			global $post;
			$postid = $post->ID;
		}
		
		// Build the image string
		$imgstr = '<img ';
		
		// Set the class holder
		$class = array();
		
		// Check if image needs to be scaled
		if($scale == true) {
			$class[] = 'js-image-scale';
			$imgstr .= 'data-scale="best-fill" ';
		}

		
		// Check if post type is attachment
		if( get_post_type( $postid ) == 'attachment' ) {
			$thumbnail_id = $postid;
		} else {
			$thumbnail_id = get_post_thumbnail_id( $postid );
		}		
		
		// Check if the postid is not empty
		if( !empty( $thumbnail_id ) ) {
			
			// Get image data
			$image_src = wp_get_attachment_image_src( $thumbnail_id, $size, false );
			
			// Set variables
			$image_url = $image_src[0];
			$image_width = $image_src[1];
			$image_height = $image_src[2];
			
		} else {
			
			// If the postid is empty, fallback to default image
			// which is set in the site options panel
			$image_src = wp_get_attachment_image_src( get_field('default_placeholder_' . $fallback, 'option'), $size, false );
			
			// Ultra fallback if post is not available
			if( empty($image_src) ) {
				$image_src = wp_get_attachment_image_src( get_field('default_placeholder_post', 'option'), $size, false );
			}
			
			// Set variables
			$image_url = $image_src[0];
			$image_width = $image_src[1];
			$image_height = $image_src[2];
			
		}
		
		
					
		// Set the string based on type
		if($type == 'lazyload') {
			$imgstr .= 'data-original="' . $image_url . '"';
			$class[] = 'lazyload';
		} elseif($type == 'lazyslider') {
			$imgstr .= 'data-lazy="' . $image_url . '"';
		} else {
			$imgstr .= 'src="' . $image_url . '"';
		}
		
		// Set width and height
		$imgstr .= ' width="' . $image_width . '" height="' . $image_height . '"';
		
		// Set title and alt tags
		$imgstr .= ' alt="' . get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true) . '"';
		$imgstr .= ' title="' . get_the_title( $thumbnail_id ) . '"';
		
		// Append the classes to the string
		$imgstr .= ' class="' . implode(' ', $class) . '" ';
		
		// Close off the image and return the image string
		return $imgstr . ' />'; 
		
	}
	
	function the_featured_image( $size = 'thumbnail', $postid = null, $type = 'lazyload', $scale = true, $fallback = 'post' ) { 
		echo get_featured_image($size, $postid, $type, $scale, $fallback);
	}
	
	
	
	
	// ======================================
	// Format the button (with btn container
	// ======================================
	function get_the_button( $label = null, $url = null, $container = false, $classes = null, $target = null ) {
		if( !empty($label) && !empty($url) ) {
			$resultstr  = (($container == true) ? '<div class="btn-container">' : '');
			$resultstr .= '<a href="' . $url . '" class="btn ' . ((!empty($classes)) ? ' ' . $classes : '') . '" ' . ((!empty($target)) ? 'target="' . $target . '"' : '') . '>' . $label .'</a>';
			$resultstr .= (($container == true) ? '</div>' : '');
			
			return $resultstr;
		}
	}
	
	// Function for direct echo-ing the result
	function the_button( $label = null, $url = null, $container = false, $classes = null, $target = null ) {
		echo get_the_button( $label, $url, $container,  $classes, $target );
	}
	
	
	
	// =======================================================
	// Reset default gallery styling and add lightbox support
	// =======================================================
	// Remove default gallery styling
	add_filter( 'use_default_gallery_style', '__return_false' );
	
	// Set rel-attribute to gallery for lightbox loading
	add_filter('wp_get_attachment_link', 'add_rel_attribute');
	function add_rel_attribute($link) {
		global $post;
		return str_replace('<a href', '<a rel="lightbox" data-lightbox="gallery" href', $link);
	}
	
	// Remove the breakline
	add_filter( 'the_content', 'remove_br_gallery', 11, 2);
	function remove_br_gallery($output) {
	    return preg_replace('/<br style=(.*)>/mi','',$output);
	}
	
	
	
	// ===============================================
	// Function put the zipcode field before the city
	// ===============================================
	add_filter( 'gform_address_display_format', 'address_format' );
	function address_format( $format ) {
	    return 'zip_before_city';
	}
	
	
	
	
	// ====================================
	// Get the page row and pass variables
	// ====================================
	function get_page_part( $part, $file, $content = null ) {
	    // Load the file 
	    include( locate_template( '/parts/' . $part . '/' . $file . '.php', false, false ) ); 
	}
	
	
	
	// ======================================
	// Set the page row margins by data attr
	// ======================================
	function the_row_margins( $row ) {
		$content = '';
		$margins = $row['margins'];
		if( !empty($margins) ) {
			foreach( $margins as $key => $margin ) {
				if( !empty($margin) ) {
					$content .= ' data-' . $key . '="' . intval($margin) . '"';
				}
			}
		}
		
		$paddings = $row['paddings'];
		if( !empty($paddings) ) {
			foreach( $paddings as $key => $padding ) {
				if( !empty($padding) ) {
					$content .= ' data-' . $key . '="' . intval($padding) . '"';
					$paddingSet = true;
				}
			}
		}
		
		if( isset($paddingSet) ) {
			$content .= ' data-target="' . $row['paddings']['padding-target'] . '"';
		}
		
		echo $content;
	}
	
	
	
	
	// ===============================================
	// Set the heading weight and type (h1, h2, etc.)
	// ===============================================
	function the_heading( $heading ) {
			
		if( !empty($heading['title']) ) {
			if( empty($heading['type']) ) { $heading['type'] = 'h2'; }
			if( empty($heading['weight']) ) { $heading['weight'] = 'large'; }
			
			echo '<' . $heading['type'] . ' class="heading heading--' . $heading['weight'] . '">' . $heading['title'] . '</' . $heading['type'] . '>';
		}
		
	}
	
	
	
	
	
	
	