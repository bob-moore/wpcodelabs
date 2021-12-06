<?php
use \Scaffolding\Templates;

?>

<?php do_action( '_s_comments_before' ); ?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>

		<?php do_action( '_s_comments_header_before' ); ?>

		<?php do_action( '_s_comments_header' ); ?>

		<header class="comments-header">

			<?php do_action( '_s_comments_header' ); ?>

			<h2 class="comments-title">
				<?php
				$_s_comment_count = get_comments_number();
				if ( '1' === $_s_comment_count ) {
					printf(
						/* translators: 1: title. */
						esc_html__( 'One thought on &ldquo;%1$s&rdquo;', '_scaffolding' ),
						'<span>' . get_the_title() . '</span>'
					);
				} else {
					printf( // WPCS: XSS OK.
						/* translators: 1: comment count number, 2: title. */
						esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $_s_comment_count, 'comments title', '_scaffolding' ) ),
						number_format_i18n( $_s_comment_count ),
						'<span>' . get_the_title() . '</span>'
					);
				}
				?>
			</h2>

			<?php the_comments_navigation(); ?>

		</header>

		<?php do_action( '_s_comments_header_after' ); ?>

		<?php do_action( '_s_comments_list_before' ); ?>

		<ol class="comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'callback' => [Templates::getInstance(), 'comment'], 'avatar_size' => 76 ) ); ?>
		</ol>

		<?php do_action( '_s_comments_list_after' ); ?>

		<?php the_comments_navigation(); ?>

		<?php if ( !comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', '_scaffolding' ); ?></p>
		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( '_s_comments_form_before' ); ?>

	<?php comment_form(); ?>

	<?php do_action( '_s_comments_form_after' ); ?>

</div>

<?php do_action( '_s_comments_after' ); ?>