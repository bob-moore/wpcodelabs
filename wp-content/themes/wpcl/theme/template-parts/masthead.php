<div class="site-branding">

	<?php the_custom_logo(); ?>

	<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>

	<?php $_s_description = get_bloginfo( 'description', 'display' ); ?>



</div>

<button class="menu-toggle toggle-button" id="site-navigation-toggle" aria-controls="primary-menu" aria-expanded="false" data-triggers="#mobile-widget-area"><span class="button-inner"><span class="menu-icon"></span><span class="menu-text">Menu</span></span></button>

<nav id="site-navigation" class="main-navigation">

	<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'link_before' => '<span class="nav-text" itemprop="name">', 'link_after', '</span>' ) ); ?>

</nav>