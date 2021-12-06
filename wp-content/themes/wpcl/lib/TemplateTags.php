<?php

namespace Scaffolding;

class TemplateTags extends Framework {

	protected function __construct() {

		add_action( '_s_postedOn', [$this, 'postedOn'] );
		add_action( '_s_postedBy', [$this, 'postedBy'] );
		add_action( '_s_categoryList', [$this, 'categoryList'] );
		add_action( '_s_tagList', [$this, 'tagList'] );
		add_action( '_s_taxonomyList', [$this, 'taxonomyList'] );
		add_action( '_s_commentInfo', [$this, 'commentInfo'] );
		add_action( '_s_postThumbnail', [$this, 'postThumbnail'] );
		add_action( '_s_title', [$this, 'postTitle'] );
		/**
		 * Add filters
		 */
		add_filter( '_s_the_content', 'wptexturize'        );
		add_filter( '_s_the_content', 'convert_smilies'    );
		add_filter( '_s_the_content', 'convert_chars'      );
		add_filter( '_s_the_content', 'wpautop'            );
		add_filter( '_s_the_content', 'shortcode_unautop'  );
		add_filter( '_s_the_content', 'prepend_attachment' );
	}

	public function postedOn( $args = [] ) {

		$args = wp_parse_args( $args, [
			'before' => '',
		] );

		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated screen-reader-text" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		echo '<span class="posted-on">';

		if( !empty( $args['before'] ) ) {

			printf( '<span class="meta-label">' . esc_html__( '%1$s', '_s' ) . ' </span>', $args['before'] );

		}

		printf(
			esc_html_x( '%s', 'post date', '_scaffolding' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '</span>'; // WPCS: XSS OK.
	}

	/**
	 * Prints HTML with meta information for the current author.
	 */
	public function postedBy( $args = [] ) {

		$args = wp_parse_args( $args, [
			'before' => '',
		] );

		global $post;

		echo '<span class="byline">';

		if( !empty( $args['before'] ) ) {

			printf( '<span class="meta-label">' . esc_html__( '%1$s', '_s' ) . ' </span>', $args['before'] );

		}

		printf(
			esc_html_x( '%s', 'post author', '_scaffolding' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( $post->post_author ) ) . '">' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</a></span>'
		);

		echo '</span>';
	}

	public function categoryList( $args = [] ) {

		$args = wp_parse_args( $args, [
			'prefix' => '',
		] );

		if ( get_post_type() === 'post' ) {

			$categories_list = get_the_category_list( '' );

			if ( $categories_list ) {

				echo '<div class="entry-categories">';

					if( !empty( $prefix ) ) {

						printf( '<span class="meta-label">' . esc_html__( '%1$s', '_s' ) . ' </span>', $args['prefix'] );

					}

					echo $categories_list;

				echo '</div>';

			}
		}
	}

	public function tagList( $prefix = '' ) {

		if ( get_post_type() === 'post' ) {

			$tags_list = get_the_tag_list( '<ul class="tags-list"><li>','</li><li>','</li></ul>' );

			if ( $tags_list ) {

				echo '<div class="entry-tags">';

					if( !empty( $prefix ) ) {

						printf( '<span class="meta-label">' . esc_html__( '%1$s', '_scaffolding' ) . ' </span>', $prefix );

					}

					echo $tags_list;

				echo '</div>';

			}
		}
	}

	public function taxonomyList( $args = '' ) {

		$args = wp_parse_args( $args, [
			'tax' => 'category',
			'sep' => '',
			'before' => '',
			'after' => '',
			'before_item' => '',
			'after_item' => '',
			'wrapper' => 'div',
		] );
		/**
		 * Setup the prefix
		 */
		if ( !empty( $args['before'] ) ) {
			$args['before'] = sprintf( '<span class="meta-label label-before">%s</span>', $args['before'] );
		}
		/**
		 * Setup the suffix
		 */
		if ( !empty( $args['after'] ) ) {
			$args['after'] = sprintf( '<span class="meta-label label-after">%s</span>', $args['after'] );
		}
		/**
		 * Setup seperator
		 */
		if ( empty( $args['sep'] ) ) {
			$args['sep'] = '</li><li>';
			$args['before'] .= '<ul class="entry-term-list"><li>';
			$args['after'] = '</li></ul>' . $args['after'];
		} else {
			$args['sep'] = '<span class="sep">' . $args['sep'] . '</span>';
		}

		$terms = get_the_term_list( get_the_id(), $args['tax'], $args['before'], $args['sep'], $args['after']);

		if( $terms ) {
			/**
			 * Maybe add before and after items
			 */
			if( !empty( $args['before_item'] ) ) {
				$terms = str_replace( 'rel="tag">', 'rel="tag">' . $args['before_item'], $terms );
			}
			if( !empty( $args['after_item'] ) ) {
				$terms = str_replace( '</a>', $args['before_item'] . '</a>', $terms );
			}

			// <a href="http://ifreakinglovefishingcom.local/category/jetpack/" rel="tag">Jetpack</a>

			printf( '<%1$s class="entry-terms term-type-%2$s">%3$s</%1$s>',$args['wrapper'], $args['tax'], $terms );
		}

	}

	public function commentInfo( $prefix = '' ) {
		if ( ( comments_open() || get_comments_number() ) ) {

			echo '<span class="comments-link">';

			if( !empty( $prefix ) ) {

				printf( '<span class="meta-label">' . esc_html__( '%1$s', '_scaffolding' ) . ' </span>', $prefix );

			}

			comments_popup_link(
				sprintf(
					wp_kses(
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', '_s' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}
	}

	public function postThumbnail( $class='', $size = 'post-thumbnail', $echo = true ) {
		/**
		 * Attempt to get the thumbnail
		 * @var [type]
		 */
		$thumbnail = get_the_post_thumbnail(
			false,
			$size,
			array(
				'alt' => the_title_attribute( array( 'echo' => false ) ),
				'class' => $class,
			)
		);
		/**
		 * If no thumbnail is set, allow filters to set a default ID
		 */
		if( !$thumbnail && !is_singular() ) {

			$default_args = [
				'image' => '',
				'width' => '',
				'height' => '',
			];

			$default = apply_filters( '_s_default_thumbnail', $default_args );

			if( isset( $default['image'] ) && !empty( $default['image'] ) ) {

				$args = is_array( $default ) ? wp_parse_args( $default, $default_args ) : $default_args;

				/**
				 * If we're passed an ID
				 */
				if( intval( $args['image'] ) ) {
					$thumbnail = wp_get_attachment_image(
						$args['image'],
						$size,
						array(
							'alt' => the_title_attribute( array( 'echo' => false ) ),
							'class' => $class,
						)
					);
				}
				/**
				 * If we're passed a string (url)
				 */
				else {
					printf( '<a class="post-thumbnail" href="%s" aria-hidden="true" tabindex="-1"><img src="%s" alt="%s"%s%s%s></a>',
						get_the_permalink(),
						esc_url_raw( $args['image'] ),
						the_title_attribute( array( 'echo' => false ) ),
						!empty( $args['width'] ) ? ' width="' . intval( $args['width'] ) . 'px"' : '',
						!empty( $args['height'] ) ? ' height="' . intval( $args['height'] ) . 'px"' : '',
						!empty( $class ) ? ' class="' . esc_attr( $class ) . '"' : '',
					);
				}
			}
		}
		/**
		 * Maybe output the thumbnail
		 */
		if( $thumbnail ) {

			if( $echo === false ) {
				return $thumbnail;
			}

			else {
				if( Theme::getInstance()->getView() !== 'single' ) {
					printf( '<a class="post-thumbnail" href="%s" aria-hidden="true" tabindex="-1">%s</a>', get_the_permalink(), $thumbnail );
				}
				/**
				 * We have to double check has_post thumbnail on single views for
				 * avoiding double images when jetpack default thumbnails is enabled
				 */
				else {
					printf( '<div class="post-thumbnail">%s</div>', $thumbnail );
				}
			}
		}
	}

	public function postTitle() {

		$title = '';

		switch ( Theme::getInstance()->getView() ) {
			case 'frontpage':
				$title = get_bloginfo( 'name' );
				break;
			case 'blog':
				$title = get_the_title( get_option( 'page_for_posts', true ) );
				break;
			case 'archive':
				$title = get_the_archive_title();
				break;
			case 'search':
				$title = get_search_query();
				break;
			case 'single':
				$title = get_the_title();
				break;
			case '404':
				$title = '404';
				break;
			case 'woocommerce/shop':
				$title = woocommerce_page_title();
				break;
			default:
				$title = get_the_title();
				break;
		}
		// var_dump(Theme::getInstance()->getView());
		echo $title;
	}

	public function postNavigation() {
		$previous = get_previous_post();
		$next = get_next_post();

		$previous_link = '';
		$next_link = '';

		if( !empty( $previous ) ) {
			$previous_link = sprintf( '<a href="%s" rel="prev" class="post-navigation-prev">%s</a>',
				get_permalink( $previous->ID ),
				__( 'Previous', '_s' )
			);
		}

		if( !empty( $next ) ) {
			$next_link = sprintf( '<a href="%s" rel="next" class="post-navigation-next">%s</a>',
				get_permalink( $next->ID ),
				__( 'Next', '_s' )
			);
		}

		if( !empty( $previous_link ) || !empty( $next_link ) ) {
			printf( '<div class="post-navigation">%s%s</div>',
				$previous_link,
				$next_link,
			);
		}
	}

	public function authorSocialLinks() {
		$contact_methods = array(
			'url' => '_s_icon-chrome',
			'facebook' => '_s_icon-facebook',
			'twitter' => '_s_icon-twitter1',
			'instagram' => '_s_icon-instagram',
			'linkedin' => '_s_icon-linkedin',
			'myspace' => '_s_icon-myspace',
			'pinterest' => '_s_icon-pinterest-p',
			'youtube' => '_s_icon-youtube',
			'soundcloud' => '_s_icon-soundcloud',
			'tumblr' => '_s_icon-tumblr',
			'wikipedia' => '_s_icon-wikipedia-w',
			'github' => '_s_icon-github',
			'wordpress' => '_s_icon-wordpress',
		);

		$output = '';

		foreach( $contact_methods as $key => $icon ) {
			$url = get_the_author_meta( $key );

			if( empty( $url ) ) {
				continue;
			}
			/**
			 * Clean up URL
			 */
			$url = $key === 'twitter' ? esc_url_raw( 'https://www.twitter.com/' . $url ) : esc_url_raw( $url );
			/**
			 * Construct output
			 */
			$output .= sprintf( '<li class="%1$s"><a href="%2$s" ref="noopener noreferrer" target="_blank"><span class="_s_icon %3$s"></span><span class="screen-reader-text">%1$s</span></a></li>',
				$key,
				$url,
				$icon
			);
		}

		if( !empty( $output ) ) {
			echo '<ul class="author-social-links">' . $output . '</ul>';
		}
	}
}