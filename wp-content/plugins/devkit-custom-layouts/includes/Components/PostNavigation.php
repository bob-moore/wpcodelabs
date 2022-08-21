<?php
/**
 * Post Navigation component
 *
 * @class PostNavigation
 * @package CustomLayouts\Components
 */

namespace Devkit\CustomLayouts\Components;

use Devkit\CustomLayouts\Framework;
use Devkit\CustomLayouts\Subscriber;

use \Carbon_Fields\Field;

class PostNavigation extends Framework {
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 */
	public function addFilters() {
		Subscriber::addFilter( 'devkit/custom_layouts/template_parts', [$this, 'addTemplates'] );
		Subscriber::addFilter( 'devkit/custom_layouts/template_scope', [$this, 'setPostNavigation'] );
	}
	/**
	 * Add template partials to select field
	 *
	 * @param array $templates List of template parts
	 * @return $templates
	 */
	public function addTemplates( array $templates ) : array
	{
		return array_merge(
			$templates,
			[
				'core/post-navigation' => 'Post Navigation',
			]
		);
	}
	/**
	 * Set the author in the timber context
	 *
	 * @param object/array $_scope The scope/context from Timber::get_context
	 * @return array Timber scope/context
	 */
	public function setPostNavigation( array $_scope ) : array
	{

		if ( is_singular() && isset( $_scope['post'] ) ) {

			$navigation = [];

			$defaults = [
				'in_same_term' => false,
				'excluded_terms' => '',
				'taxonomy' => 'category',
			];

			$args = wp_parse_args(
				apply_filters( 'devkit/custom_layouts/post_navigation_args', $defaults, $_scope['post'] ),
				$defaults
			);

			$next = get_next_post( $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'] );

			$prev = get_previous_post( $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'] );

			if ( $prev )
			{
				$navigation['prev'] = [
					'id' => $prev->ID,
					'link' => get_the_permalink( $prev->ID ),
					'title' => get_the_title( $prev->ID ),
					'icon' => 'wpcl-icon wpcl-icon-arrow_back',
					'prefix' => __( 'Previous', 'custom_layouts' )
				];
			}

			if ( $next )
			{
				$navigation['next'] = [
					'id' => $next->ID,
					'link' => get_the_permalink( $next->ID ),
					'title' => get_the_title( $next->ID ),
					'icon' => 'wpcl-icon wpcl-icon-arrow_forward',
					'prefix' => __( 'Next', 'custom_layouts' )
				];
			}

			$_scope['post']->navigation = apply_filters( 'devkit/custom_layouts/post_navigation/scope', $navigation, $_scope );
		}

		return $_scope;
	}
}