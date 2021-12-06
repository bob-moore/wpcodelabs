<ul class="cs-icon-list">

	<?php foreach( $settings->rows as $li ) : ?>

		<?php $icon = !empty( $li->icon ) ? $li->icon : $settings->icon; ?>

		<li>

			<span class="bullet"><span class='cs-icon <?php echo $icon; ?>'></span></span>

			<span class="cs-icon-list-content"><?php echo $li->content; ?></span>

		</li>

	<?php endforeach; ?>

</ul>