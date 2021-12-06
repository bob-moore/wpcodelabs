<?php

/**
 * The plugin file that controls the admin functions
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_wp_cornerstone
 */

namespace mdm\cornerstone;

class EventOrganizer extends Framework implements interfaces\ActionHookSubscriber, interfaces\FilterHookSubscriber {

	/**
	 * Constructor
	 * @since 1.0.0
	 * @access protected
	 */
	protected function __construct() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		// Only Activate if event organizer plugin is active
		$this->enabled = is_plugin_active('event-organiser/event-organiser.php');
	}

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'eventorganiser_save_venue' => 'save_eo_venue_meta_fields' ),
			array( 'eventorganiser_save_event' => 'save_eo_event_meta_fields' ),
			array( 'eventorganiser_metabox_after_core_fields' => 'add_event_core_fields' ),
			array( 'wp' => 'redirect_venu' ),
		);
	}

	/**
	 * Get the shortcode hooks this class subscribes to.
	 * @return array
	 */
	public function get_filters() {
		return array(
			array( 'eventorganiser_venue_address_fields' => 'add_eo_venue_meta' ),
			array( 'wp_query_engine_args_raw' => 'reorder_event_queries' ),
			array( 'term_link' => array( 'replace_venue_url', 10, 3 ) ),
			array( 'post_type_link' => array( 'replace_event_url', 10, 4 ) ),
		);
	}

	public function add_eo_venue_meta( $fields ) {

		$new_fields = array(
			'_website'=> __('Website','eventorganiser'),
		);

		return array_merge( $fields, $new_fields );

	}

	public function save_eo_venue_meta_fields( $venue_id ) {
		if( !isset( $_POST['eo_venue'] ) ) {
			return;
		}

		$venue_meta = $_POST['eo_venue'];

		if( isset( $venue_meta['website'] ) ) {
			eo_update_venue_meta( $venue_id, '_website', $venue_meta['website'] );
		}

		if( isset( $venue_meta['website'] ) ) {
			eo_update_venue_meta( $venue_id, '_website', $venue_meta['website'] );
		}
	}

	public function add_event_core_fields( $post ) {
		$fields = self::sanitize_eo_event_field( get_post_meta( $post->ID, 'eo-event-extra', true ) );
		include self::path( 'assets/html/eo-event-details.php' );
	}

	public function save_eo_event_meta_fields( $post_id ) {
		if( !isset( $_POST['eo-event-extra'] ) ) {
			return;
		}
		update_post_meta( $post_id, 'eo-event-extra', $_POST['eo-event-extra'] );
	}

	public static function sanitize_eo_event_field( $fields ) {
		$default = array(
			'website' => '',
		);
		// First merge
		$fields = wp_parse_args( $fields, $default );
		// Then sanitize
		$fields['website'] = esc_url_raw( $fields['website'] );
		// Return fields
		return $fields;
	}

	public function reorder_event_queries( $atts ) {
		if( isset( $atts['post_type'] ) && ( $atts['post_type'] === 'event' || $atts['post_type'] === array( 'event' ) ) ) {
			$atts['orderby'] = 'meta_value';
			$atts['meta_type'] = 'DATE';
			$atts['meta_key'] = '_eventorganiser_schedule_start_start';
			$atts['meta_query'] = array(
				array(
				      'key' => '_eventorganiser_schedule_start_start', // Check the start date field
				      'value' => date("Y-m-d"), // Set today's date (note the similar format)
				      'compare' => '>=', // Return the ones greater than today's date
				      'type' => 'DATE' // Let WordPress know we're working with date
				),
			);
		}
		return $atts;
	}

	public function redirect_venue() {
		if( is_tax( 'event-venue' ) === false ) {
			return;
		}

		$object = get_queried_object();

		$url = eo_get_venue_meta( $object->term_id, '_website' );

		if( empty( $url ) ) {
			return;
		}

		if ( wp_redirect( esc_url_raw( $url ), 301 ) ) {
			exit;
		}
	}

	public function replace_venue_url( $termlink, $term, $tax ) {
		if( $term->taxonomy !== 'event-venue' ) {
			return $termlink;
		}
		if( !function_exists( 'eo_get_venue_meta' ) ) {
			return $termlink;
		}

		$url = eo_get_venue_meta( $term->term_id, '_website' );

		if( !empty( $url ) ) {
			$termlink = esc_url_raw( $url );
			$termlink = add_query_arg( 'location', 'newtab', $termlink );
		}

		return $termlink;
	}

	function replace_event_url( $post_link, $post, $leavename, $sample ) {

		if( $sample !== false ) {
			return $post_link;
		}

		$meta = get_post_meta( $post->ID, 'eo-event-extra', true );

		if( isset( $meta['website'] ) && !empty( $meta['website'] ) ) {
			$post_link = esc_url_raw( $meta['website'] );
			$post_link = add_query_arg( 'location', 'newtab', $post_link );
		}

		return $post_link;
	}

} // end class