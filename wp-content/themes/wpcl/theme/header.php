<!doctype html>

<html <?php language_attributes(); ?>>

<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>

</head>



<body <?php body_class(); ?>>

<?php do_action( 'wp_body_open' ); ?>



<div id="page" class="site">

	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', '_s' ); ?></a>

	<?php do_action( '_s_header_before' ); ?>

		<?php if( has_action( '_s_masthead' ) ) : ?>

				<header id="masthead" class="<?php echo apply_filters( '_s/classes/masthead', 'site-header' ); ?>">

					<div class="masthead-inner">

						<?php do_action( '_s_masthead_before' ); ?>

						<div class="container">

							<?php do_action( '_s_masthead' ); ?>

						</div>

						<?php if( has_action( '_s_hero' ) ) : ?>

							<div id="hero">

								<div class="container">

									<?php do_action( '_s_hero' ); ?>

								</div>

							</div>

						<?php endif; ?>

						<?php do_action( '_s_masthead_after' ); ?>

					</div>

				</header>

		<?php endif; ?>

	<?php do_action( '_s_header_after' ); ?>