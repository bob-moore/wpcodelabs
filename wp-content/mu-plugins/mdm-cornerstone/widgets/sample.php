<?php

namespace mdm\cornerstone\Widgets;

use \mdm\cornerstone\Framework;

class Sample extends \WP_Widget {

	public $widget_id_base;
		public $widget_name;
		public $widget_options;
		public $control_options;

		/**
		 * Constructor, initialize the widget
		 * @param $id_base, $name, $widget_options, $control_options ( ALL optional )
		 * @since 1.0.0
		 */
		public function __construct() {
			// Construct some options
			$this->widget_id_base = 'sample_widget_id';
			$this->widget_name    = 'Sample Widget';
			$this->widget_options = array(
				'classname'   => 'sample_widget_class',
				'description' => 'Sample Widget' );
			$this->fields = array(
				array(
					'id'   => 'title',
					'type' => 'text',
					'label' => __( 'Title', 'essential_addons' ),
					'default' => '',
				),
				array(
					'id'   => 'my_textarea',
					'type' => 'textarea',
					'label' => __( 'Select Textarea', 'essential_addons' ),
					'default' => '',
				),
				array(
					'id'   => 'my_select',
					'type' => 'select',
					'label' => 'Sample Select',
					'default' => '',
					'options' => array(
						'' => __( 'Select Option', 'essential_addons' ),
						'1' => __( 'Option 1', 'essential_addons' ),
						'2' => __( 'Option 2', 'essential_addons' ),
					),
				),
				array(
					'id'   => 'my_checkbox',
					'type' => 'checkbox',
					'label' => 'Sample Checkbox',
					'default' => false,
					'description' => __( 'This is a sample description', 'essential_addons' ),
				),
				array(
					'id'   => 'my_radio',
					'type' => 'radio',
					'label' => 'Sample Radio',
					'default' => '1',
					'options' => array(
						'1' => __( 'Option 1', 'essential_addons' ),
						'2' => __( 'Option 2', 'essential_addons' ),
					),
				),
			);
			// Construct parent
			parent::__construct( $this->widget_id_base, $this->widget_name, $this->widget_options );
		}

		/**
		 * Create back end form for specifying image and content
		 * @param $instance
		 * @see https://codex.wordpress.org/Function_Reference/wp_parse_args
		 * @since 1.0.0
		 */
		public function form( $instance ) {
			printf( '<div class="%s_widget_form" style="padding-top: 10px; padding-bototm: 10px;">', $this->id_base );
			/**
			 * Loop through each field and add to widget form
			 */
			foreach( $this->fields as $field => $args ) {
				/**
				 * Set value, or default
				 */
				if( !isset( $instance[ $args['id'] ] ) ) {
					$instance[ $args['id'] ] = $args['default'];
				}

				echo '<div class="field" style="margin-bottom: 14px;">';

					do_action( 'cornserstone/widgets/input', $this, $args, $instance[ $args['id'] ] );

					if( isset( $args['description'] ) && !empty( $args['description'] ) ) {

						printf( '<p class="description">%s</p>', esc_attr( $args['description'] ) );

					}

				echo '</div>';
			}

			echo '</div>';
		}

		/**
		 * Update form values
		 * @param $new_instance, $old_instance
		 * @since 1.0.0
		 */
		public function update( $new_instance, $old_instance ) {
			/**
			 * Loop through each field and sanitize
			 */
			foreach( $this->fields as $field => $args ) {
				if( isset( $args['sanitize'] ) && function_exists( $args['sanitize'] ) ) {
					$instance[$args['id']] = call_user_func( $args['sanitize'], $new_instance[$args['id']] );
				} else {
					$instance[$args['id']] = sanitize_text_field( $new_instance[$args['id']] );
				}
			}
			return $instance;
		}

		/**
		 * Output widget on the front end
		 * @param $args, $instance
		 * @since 1.0.0
		 */
		public function widget( $args, $instance ) {
			// Display before widget args
			echo $args['before_widget'];
			// Display Title
			if( !empty( $instance['title'] ) ) {
				$instance['title']  = apply_filters( 'widget_title', $instance['title'], $instance, $this->widget_id_base );
				// Again check if filters cleared name, in the case of 'dont show titles' filter or something
				$instance['title']  = ( !empty( $instance['title']  ) ) ? $args['before_title'] . $instance['title']  . $args['after_title'] : '';
				// Display Title
				echo $instance['title'];
			}

			/**
			 * DO WIDGETY STUFF
			 */
			echo '<ul>';
			foreach( $this->fields as $field => $field_args ) {
				if( isset( $instance[$field_args['id']] ) ) {
					printf( '<li><strong>%s:</strong> %s</li>', $field_args['id'], esc_attr( $instance[$field_args['id']] ) );
				}
			}
			echo '</ul>';

			// Display after widgets args
			echo $args['after_widget'];
		} // end widget()

} // end class