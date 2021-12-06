<?php do_action( '_s_start' ); ?>



<?php get_header(); ?>

<?php do_action( '_s_main_before_open' ); ?>

<main id="main" class="site-main">

<?php do_action( '_s_main_after_open' ); ?>

	<div class="container">

		<div id="primary" class="content-area">

			<?php do_action( '_s_loop' ); ?>

		</div>

		<?php get_sidebar(); ?>

	</div>

<?php do_action( '_s_main_before_close' ); ?>

</main>

<?php do_action( '_s_main_after_close' ); ?>

<?php get_footer(); ?>

<?php do_action( '_s_end' ); ?>

