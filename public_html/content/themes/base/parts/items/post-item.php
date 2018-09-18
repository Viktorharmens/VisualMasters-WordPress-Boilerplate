<div class="item-post <?php echo (( isset($content['featured']) ) ? 'item-post--featured' : '') ?> <?php echo (( isset($content['class']) ) ? $content['class'] : '') ?>">
	<a href="<?php the_permalink(); ?>">
	
		<figure class="item-post__image">
			<?php 
				
				if( !isset($content['featured']) ) {
					the_featured_image( 'medium', get_the_id(), 'lazyload', true, 'post' ); 
				} else {
					the_featured_image( 'large', get_the_id(), 'lazyload', true, 'post' ); 
				}
				
			?>
		</figure>
		
		<div class="item-post__wrapper">
			
			<div class="item-post__content">
				<h3 class="heading heading--small js-truncate">
					<?php the_title(); ?>
				</h3>
				<div class="content js-truncate">
					<?php echo get_max_excerpt(400); ?>
					<span class="js-readmore-handle"><?php _e('Lees verder', 'visualmasters'); ?></span>
				</div>
			</div>
			
		</div>
		
	</a>
</div>