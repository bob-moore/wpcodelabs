<aside id="mobile-widget-area" class="widget-area">

	<?php do_action( '_s_mobile_sidebar_before' ); ?>

	<div class="container">

		<?php the_custom_logo(); ?>

		<nav id="mobile-navigation" class="main-navigation">

			<?php wp_nav_menu( array( 'theme_location' => 'mobile', 'container' => false, 'link_before' => '<span class="nav-text" itemprop="name">', 'link_after', '</span>' ) ); ?>

		</nav>

		<?php if ( is_active_sidebar( 'sidebar-mobile' ) ) : ?>

			<?php dynamic_sidebar( 'sidebar-mobile' ); ?>

		<?php endif; ?>

	</div>

	<?php do_action( '_s_mobile_sidebar_after' ); ?>

</aside>

