<div class="sidebar__block sidebar__block--cta">
	<a href="<?php echo $content['cta']['url']; ?>" <?php echo (( !empty($content['cta']['target']) ) ? 'target="' . $content['cta']['target'] . '"' : '') ?> class="btn btn--blue">
		<?php echo $content['cta']['title']; ?>
	</a>
</div>