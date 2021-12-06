<?php if( has_action( '_s_sidebar' ) ) : ?>

	<aside id="secondary" class="<?php echo apply_filters( '_s_secondary_classes', 'widget-area' ); ?>">

		<?php do_action( '_s_sidebar_before' ); ?>

		<div class="container">

			<?php do_action( '_s_sidebar' ); ?>

		</div>

		<?php do_action( '_s_sidebar_after' ); ?>

	</aside>

<?php endif; ?>