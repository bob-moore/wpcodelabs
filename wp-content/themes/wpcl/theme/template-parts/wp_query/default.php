<?php add_filter( 'wp_query_include_loop', '__return_false' ); ?>

<?php if ( $query->have_posts() ) : ?>

	<div class="row">

		<?php while ( $query->have_posts() ) : $query->the_post(); ?>

			<div class="scol-phone-6 scol-tablet-wide-4">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'archive-entry' ); ?>>

						<header class="entry-header">

							<?php _s_the_post_thumbnail(); ?>

							<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

							<div class="entry-meta">

								<?php _s_posted_on( '' ); ?>

							</div>

						</header>

						<div class="entry-content">

							<?php the_excerpt(); ?>

						</div>

				</article>

			</div>

		<?php endwhile; ?>

	</div>

<?php endif; ?>