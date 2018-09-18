<section class="page-row page-row--header-img">
	
	<figure class="header-image">
		<?php the_featured_image( 'page-header', $content['image'] ); ?>
	</figure>
		
	<div class="header-title">
		<div class="container">
			<?php 
				if( $content['use_page_title'] ) {
					echo '<h1 class="heading heading--white">' . get_the_title() . '</h1>';
				} else {
					echo '<' . $content['heading_type'] . ' class="heading heading--white">' . $content['heading_title'] . '</' . $content['heading_type'] . '>';
				}
			?>
		</div>
	</div>
	
</section>