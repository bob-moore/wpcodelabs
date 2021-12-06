<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( '_s_entry_before' ); ?>



	<?php if( has_action( '_s_entry_header' ) ) : ?>

		<?php do_action( '_s_entry_header_before' ); ?>

		<header class="entry-header"><?php do_action( '_s_entry_header' ); ?></header>

		<?php do_action( '_s_entry_header_after' ); ?>

	<?php endif; ?>



	<?php if( has_action( '_s_entry_content' ) ) : ?>

		<?php do_action( '_s_entry_content_before' ); ?>

		<div class="entry-content"><?php do_action( '_s_entry_content' ); ?></div>

		<?php do_action( '_s_entry_content_after' ); ?>

	<?php endif; ?>



	<?php if( has_action( '_s_entry_footer' ) ) : ?>

		<?php do_action( '_s_entry_footer_before' ); ?>

		<footer class="entry-footer"><?php do_action( '_s_entry_footer' ); ?></footer>

		<?php do_action( '_s_entry_footer_after' ); ?>

	<?php endif; ?>

	<?php do_action( '_s_entry_after' ); ?>

</article>