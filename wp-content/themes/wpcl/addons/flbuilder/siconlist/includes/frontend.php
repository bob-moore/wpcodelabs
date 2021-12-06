<ul class="s-icon-list">
	<?php foreach( $settings->rows as $row ) : ?>

		<li>

			<?php if( !empty( $row->bullet ) ) : ?>

				<span class="list-bullet"><?php echo $row->bullet; ?></span>

			<?php endif; ?>

				<span class="list-content">

					<?php echo $row->content; ?>

				</span>

		</li>

	<?php endforeach; ?>
</ul>