<div class="navbar">

	<div class="container">

		<button class="menu-toggle toggle-button" id="site-navigation-toggle" aria-controls="primary-menu" aria-expanded="false" data-triggers="#mobile-widget-area"><span class="button-inner"><span class="menu-icon"></span><span class="menu-text">Menu</span></span></button>

		<nav id="site-navigation" class="main-navigation">

			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'link_before' => '<span class="nav-text" itemprop="name">', 'link_after', '</span>' ) ); ?>

		</nav>

	</div>

</div>