<?php
/**
 * Authorbox component
 *
 * @class Authorbox
 * @package CustomLayouts\Components
 */

namespace Devkit\CustomLayouts\Components;

use Devkit\CustomLayouts\Framework;
use Devkit\CustomLayouts\Subscriber;

class Authorbox extends Framework
{
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 */
	public function addFilters()
	{
		Subscriber::addFilter( 'devkit/custom_layouts/template_parts', [$this, 'addTemplates'] );
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
				'core/author-box' => 'Author Box',
			]
		);
	}
}