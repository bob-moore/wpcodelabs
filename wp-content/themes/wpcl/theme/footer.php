	<?php do_action( '_s_footer_before' ); ?>

	<?php if( has_action( '_s_colophon' ) ) : ?>

		<footer id="colophon" class="site-footer">

			<?php do_action( '_s_colophon_before' ); ?>

			<div class="container">

				<?php do_action( '_s_colophon' ); ?>

			</div>

			<?php do_action( '_s_colophon_after' ); ?>

		</footer>

	<?php endif; ?>

	<?php do_action( '_s_footer_after' ); ?>

</div><!-- #page -->

<?php do_action( '_s_page_after' ); ?>

<?php wp_footer(); ?>

</body>

</html>