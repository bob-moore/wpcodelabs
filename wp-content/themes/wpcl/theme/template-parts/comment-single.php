<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

	<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">

		<header class="comment-meta">

			<div class="comment-author vcard">

				<?php if( 0 != $args['avatar_size'] ) : ?>

					<div class="avatar">

						<?php echo get_avatar( $comment, $args['avatar_size'] ); ?>

					</div>

				<?php endif; ?>

				<div class="comment-metadata">

					<span class="author"><?php echo get_comment_author_link( $comment ); ?></span>
					<span class="date"><?php printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment ), get_comment_time() ); ?></span>


				</div>

			</div>

		</header>

		<div class="comment-content">

			<?php if( $comment->comment_approved == '0' ) : ?>

				<em class="comment-awaiting-moderation"><?php echo $moderation_note; ?></em>

			<?php else : ?>

				<?php comment_text(); ?>

			<?php endif; ?>

		</div>

		<footer class="comment-footer">

			<?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>

			<?php
			comment_reply_link(
				array_merge(
					$args,
					array(
						'add_below' => 'div-comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="reply">',
						'after'     => '</div>',
					)
				)
			);
			?>

		</footer>

	</article>