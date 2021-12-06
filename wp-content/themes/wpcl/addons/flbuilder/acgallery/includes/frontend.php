<div class="ac-gallery ac-<?php echo $settings->gallery_type; ?>-gallery ac_slick<?php echo $settings->icon == '1' ? ' icon-enabled' : ''; ?><?php echo $settings->centermode == '1' ? ' centermode' : ''; ?>">
	<?php echo do_shortcode( sprintf( '[gallery %s]', $module->get_shortcode_args() ) ); ?>
</div>