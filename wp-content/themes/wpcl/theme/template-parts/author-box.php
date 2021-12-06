<?php
/**
 * Template part for displaying author meta
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package _s
 */

use Scaffolding\TemplateTags;

?>

<div class="author-box entry">
	<header class="author-header">
		<div class="author-avatar">
			<?php echo get_avatar( get_the_author_meta( 'user_email' ), 48 ); ?>
		</div>
		<div class="author-info">
			<h4 class="author-title"><?php echo esc_html__( 'Published by', '_s' ) ?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_the_author() ?></a></h4>
		</div>
	</header>
	<div class="author-content">
		<?php echo apply_filters( '_s_the_content', get_the_author_meta( 'description' ) ); ?>
	</div>
	<footer class="author-footer">
		<div class="author-social">
			<?php TemplateTags::getInstance()->authorSocialLinks(); ?>
		</div>
	</footer>
</div>