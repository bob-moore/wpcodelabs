<?php
/**
 * Admin class
 *
 * Control admin functions
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

use \Carbon_Fields\Carbon_Fields;
use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

defined( 'ABSPATH' ) || exit;

class Admin extends Lib\Framework
{
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'after_setup_theme', ['\\Carbon_Fields\\Carbon_Fields', 'boot' ] );
		Subscriber::addAction( 'carbon_fields_register_fields', [$this, 'singularFields'] );
		Subscriber::addAction( 'carbon_fields_register_fields', [$this, 'archiveFields'] );
		Subscriber::addAction( 'carbon_fields_post_meta_container_saved', [$this, 'processPostMeta'], 100, 2 );
		Subscriber::addAction( 'carbon_fields_term_meta_container_saved', [$this, 'processTermMeta'], 100, 2 );

	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void {}
	/**
	 * Process all fields attached to our Carbon Fields container into a single
	 * usable array of values, and save as post meta
	 *
	 * This lets us offload the logic of getting the values onto the save action
	 * instead of during a frontend request
	 *
	 * @param int $id The post ID
	 * @param object $container Carbon Fields container object
	 * @return void
	 */
	public function processPostMeta( int $id, object $container )
	{
		/**
		 * If not our container, we can bail before even starting
		 */
		if ( $container->get_id() !== 'carbon_fields_container_scaffolding_post_meta' )
		{
			return;
		}
		/**
		 * Array to hold processed meta data
		 * @var array
		 */
		$meta = [];
		/**
		 * Fields attached to this container
		 * @var array
		 */
		$fields = $container->get_fields();
		/**
		 * Loop through and process each field individually
		 */
		foreach ( $fields as $index => $field )
		{
			$field_name = $this->getFieldName( $field );

			$meta[str_replace( '_scaffolding_', '', $field->get_name() )] = $this->cleanMeta( carbon_get_post_meta( $id, $field_name ) );
		}

		update_post_meta( $id, 'scaffolding_options', $meta );
	}
	/**
	 * Process all fields attached to our Carbon Fields container into a single
	 * usable array of values, and save as post meta
	 *
	 * This lets us offload the logic of getting the values onto the save action
	 * instead of during a frontend request
	 *
	 * @param int $id The post ID
	 * @param object $container Carbon Fields container object
	 * @return void
	 */
	public function processTermMeta( int $id, object $container )
	{
		/**
		 * If not our container, we can bail before even starting
		 */
		if ( $container->get_id() !== 'carbon_fields_container_scaffolding_term_meta' )
		{
			return;
		}
		/**
		 * Array to hold processed meta data
		 * @var array
		 */
		$meta = [];
		/**
		 * Fields attached to this container
		 * @var array
		 */
		$fields = $container->get_fields();
		/**
		 * Loop through and process each field individually
		 */
		foreach ( $fields as $index => $field )
		{
			$field_name = $this->getFieldName( $field );

			$meta[str_replace( '_scaffolding_', '', $field->get_name() )] = $this->cleanMeta( carbon_get_term_meta( $id, $field_name ) );
		}

		update_term_meta( $id, 'scaffolding_options', $meta );
	}
	/**
	 * Get the processed name of a single field
	 *
	 * @param object $field Mixed classes of carbon fields Field object
	 */
	protected function getFieldName( object $field ) : string
	{
		/**
		 * Name of the field, previxed with '_'
		 * @var [type]
		 */
		$name = $field->get_name();
		/**
		 * Replace prefix '_'
		 */
		$prefix = '_';

		if ( substr( $name, 0, strlen( $prefix ) ) === $prefix) {

			$name = substr( $name, strlen( $prefix ) );
		}

		return $name;
	}
	/**
	 * Clean undesired values from the meta before saving
	 *
	 * Carbon fields insert additional data useful to them, but not to us. We want
	 * to remove it now so we don't have to when requested on the frontend.
	 *
	 * Called recursively on arrays to check and remove extra indexes
	 *
	 * @param string/array $meta Post meta value, as returned by carbon fields
	 * @return string/array cleaned meta value
	 */
	protected function cleanMeta( $meta )
	{
		if ( ! is_array( $meta ) ) {
			return $meta;
		}

		unset( $meta['_type'] );

		foreach ( $meta as $index => $item ) {
			$meta[$index] = $this->cleanMeta( $item );
		}

		return $meta;
	}
	/**
	 * Add post meta fields
	 *
	 * @see https://docs.carbonfields.net/learn/containers/post-meta.html
	 */
	public function singularFields() : void
	{

	if ( apply_filters( 'scaffolding/do_meta', true ) === false )
	{
		return;
	}
	Container::make( 'post_meta', 'scaffolding_post_meta',__( 'Theme Options', 'scaffolding' ) )
		->set_context( 'side' )
		// ->where( 'post_type', 'IN', [$this, 'postTypes'] )
		->add_fields( apply_filters( 'scaffolding/metafields/singular', [
			'content_width' =>
				Field::make( 'select', 'scaffolding_content_width', __( 'Content Width', 'scaffolding' ) )
					->set_options( apply_filters( 'scaffolding/metafields/post/width',
						[
							'' => __( 'Customizer Setting', 'scaffolding' ),
							'narrow' => __( 'Narrow', 'scaffolding' ),
							'wide' => __( 'Wide', 'scaffolding' ),
							'full' => __( 'Full', 'scaffolding' ),
						]
					) )
					->set_default_value( 'default' ),
			'layout' =>
				Field::make( 'select', 'scaffolding_layout', __( 'layout', 'scaffolding' ) )
					->set_options( apply_filters( 'scaffolding/metafields/post/layout',
						[
							'' => __( 'Customizer Setting', 'scaffolding' ),
							'right-sidebar' => __( 'Right Sidebar', 'scaffolding' ),
							'left-sidebar' => __( 'Left Sidebar', 'scaffolding' ),
							'duel-sidebar' => __( 'Duel Sidebar', 'scaffolding' ),
							'fullwidth' => __( 'No Sidebar', 'scaffolding' ),
						]
					) )
					->set_default_value( 'default' ),
			'disabled_components' =>
				Field::make( 'set', 'scaffolding_disabled_components', __( 'Disabled', 'scaffolding' ) )
					->set_help_text( __( 'Disable individual template components', 'scaffolding' ) )
					->add_options( apply_filters( 'scaffolding/metafields/term/post/components',
						[
							'masthead' => __( 'Header', 'scaffolding' ),
							'entry/header' => __( 'Entry Header', 'scaffolding' ),
							'entry/footer' => __( 'Entry Footer', 'scaffolding' ),
							'colophon' => __( 'Footer', 'scaffolding' ),
						]
					) ),
			'body_class' =>
				Field::make( 'text', 'scaffolding_body_class', __( 'Body Class', 'scaffolding' ) ),
		] ) );
	}
	/**
	 * Add term
	 * @return [type] [description]
	 */
	public function archiveFields()
	{
		if ( apply_filters( 'scaffolding/do_meta', true ) === false )
		{
			return;
		}
		Container::make( 'term_meta', 'scaffolding_term_meta', __( 'Theme Options', 'scaffolding' ) )
			->add_fields( apply_filters( 'scaffolding/metafields/archive', [
				'content_width' =>
					Field::make( 'select', 'scaffolding_content_width', __( 'Content Width', 'scaffolding' ) )
						->set_options( apply_filters( 'scaffolding/metafields/term/width', [
							'' => __( 'Customizer Setting', 'scaffolding' ),
							'narrow' => __( 'Narrow', 'scaffolding' ),
							'wide' => __( 'Wide', 'scaffolding' ),
							'full' => __( 'Full', 'scaffolding' ),
						] ) )
						->set_default_value( 'default' ),
				'layout' =>
					Field::make( 'select', 'scaffolding_layout', __( 'Layout', 'scaffolding' ) )
					->set_options( apply_filters( 'scaffolding/metafields/term/layout',
						[
							'' => __( 'Customizer Setting', 'scaffolding' ),
							'right-sidebar' => __( 'Right Sidebar', 'scaffolding' ),
							'left-sidebar' => __( 'Left Sidebar', 'scaffolding' ),
							'duel-sidebar' => __( 'Duel Sidebar', 'scaffolding' ),
							'fullwidth' => __( 'No Sidebar', 'scaffolding' ),
						]
					) )
						->set_default_value( 'default' ),

				'disabled_components' =>
					Field::make( 'set', 'scaffolding_disabled_components', __( 'Disabled', 'scaffolding' ) )
						->set_help_text( __( 'Disable individual template components', 'scaffolding' ) )
						->add_options( apply_filters( 'scaffolding/metafields/term/components',
							[
								'masthead' => __( 'Header', 'scaffolding' ),
								'colophon' => __( 'Footer', 'scaffolding' ),
							]
						) ),
				'body_class' =>
					Field::make( 'text', 'scaffolding_body_class', __( 'Body Class', 'scaffolding' ) ),
			] ) );
	}

	public function postTypes()
	{
		return ['post', 'page'];
		$post_types = get_post_types( [ 'public' => true ] );

		return array_keys( $post_types );
	}
}