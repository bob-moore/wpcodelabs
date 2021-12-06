<div class="site-info">

	<p>Copyright &copy; <?php echo date("Y"); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>

</div>

<nav class="secondary-navigation">

	<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'link_before' => '<span class="nav-text" itemprop="name">', 'link_after', '</span>' ) ); ?>

</nav>