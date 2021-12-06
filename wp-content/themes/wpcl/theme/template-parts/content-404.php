<section class="error-404 not-found">

	<?php if( has_action( '_s_entry_content' ) ) : ?>

		<div class="entry-content">

			<?php do_action( '_s_entry_content_before' ); ?>

			<?php do_action( '_s_entry_content' ); ?>

			<?php do_action( '_s_entry_content_after' ); ?>

		</div>

	<?php endif; ?>

</section>