<?php
/**
 * Contextual Related Posts control class
 *
 * @class ContextualRelatedPosts
 * @package CustomLayouts\Plugins
 */
namespace Devkit\CustomLayouts\Plugins;

use Devkit\CustomLayouts\Framework;
use Devkit\CustomLayouts\Subscriber;

defined( 'ABSPATH' ) || exit;

class ContextualRelatedPosts extends Framework
{
	/**
	 * Construct new instance
	 *
	 * @return object/bool $this or false
	 */
	public function __construct()
	{
		if ( ! $this->isPluginActive( 'contextual-related-posts/contextual-related-posts.php' ) ) {
			return false;
		}
		return parent::__construct();
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addFilters()
	{
		Subscriber::addFilter( 'devkit/custom_layouts/template_parts', [$this, 'addTemplates'] );
		Subscriber::addFilter( 'timber/context', [$this, 'templateScope'] );
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
				'core/contextual-related-posts/grid' => 'Contextual Related Posts - Grid',
				'core/contextual-related-posts/list' => 'Contextual Related Posts - List',
			]
		);
	}
	/**
	 * Add related posts to the timber context
	 *
	 * @param array $_scope The scope/context from Timber::get_context
	 * @return array Timber scope/context
	 */
	public function templateScope( array $_scope ) : array
	{
		$related_raw = get_crp_posts_id();

		if ( empty( $related_raw ) ) {
			$_scope['contextual_related_posts'] = [];
			return $_scope;
		}

		$related_ids = [];

		foreach ( $related_raw as $raw_post ) {

			$related_ids[] = $raw_post->ID;
		}

		$args = [
			'ignore_sticky_posts' => true,
			'post__in' => $related_ids,
			'orderby' => 'post__in',
			'post_type' =>  'any'
		];

		$posts = new \Timber\PostQuery( $args );

		for ( $i = 0; $i < count( $posts ); $i++ ) {

			if ( $posts[$i]->thumbnail === null ) {
				$posts[$i]->thumbnail = new \Timber\Image( apply_filters( 'devkit/custom_layouts/default_thumbnail', DEVKIT_CUSTOMLAYOUTS_URL . 'assets/images/default-post-thumbnail.webp' ) );
			}
		}

		$_scope['contextual_related_posts'] = $posts;

		return $_scope;
	}
}