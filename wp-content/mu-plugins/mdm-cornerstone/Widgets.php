<?php

/**
 * The plugin file that controls the widget hooks
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_cornerstone
 */

namespace mdm\cornerstone;

class Widgets extends \mdm\cornerstone\Framework implements \mdm\cornerstone\interfaces\ActionHookSubscriber {

	protected static $widgets = array();

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'widgets_init' => 'add_widgets' ),
			array( 'cornserstone/widgets/input' => array( 'do_input', 10, 3 ) ),
		);
	}

	public function add_widgets() {
		// Get all widgets
		$widgets = self::get_classes( 'widgets/' );
		// Register each
		foreach( $widgets as $widget_name ) {
			// Append namespace to widget
			$widget = '\\mdm\\cornerstone\\widgets\\' . $widget_name;
			// Register with wordpress
			register_widget( $widget );
		}
	}

	public function do_input( $widget, $input, $value ) {
		if( !isset( $input['id'] ) ) {
			return;
		}
		switch ( $input['type'] ) {
			case 'text' :
				$this->do_text_input( $widget, $input, $value );
				break;
			case 'textarea' :
				$this->do_textarea_input( $widget, $input, $value );
				break;
			case 'radio' :
				$this->do_radio_input( $widget, $input, $value );
				break;
			case 'checkbox' :
				$this->do_checkbox_input( $widget, $input, $value );
				break;
			case 'select' :
				$this->do_select_input( $widget, $input, $value );
				break;
			default:
				// Nothing to do here...
				break;
		}
	}

	private function do_text_input( $widget, $input, $value ) {
		/**
		 * Normalize the arguments required for this field type
		 */
		$defaults = array(
			'label' => '',
			'class' => 'widefat',
		);
		$input = array_merge( $defaults, $input );
		/**
		 * Do label
		 */
		printf( '<label for="%s" style="margin-bottom: 5px; display: block;">%s</label>',
			$widget->get_field_name( $input['id'] ),
			esc_attr( $input['label'] )
		);
		/**
		 * Do Input
		 */
		printf( '<input name="%s" id="%s" class="%s" type="text" value="%s"/>',
			$widget->get_field_name( $input['id'] ),
			$widget->get_field_id( $input['id'] ),
			$input['class'],
			esc_attr( $value )
		);
	}

	private function do_textarea_input( $widget, $input, $value ) {
		/**
		 * Normalize the arguments required for this field type
		 */
		$defaults = array(
			'label' => '',
			'class' => 'widefat',
			'rows'   => 10,
			'cols'  => 30,
		);
		$input = array_merge( $defaults, $input );
		/**
		 * Do label
		 */
		printf( '<label for="%s" style="margin-bottom: 5px; display: block;">%s</label>',
			$widget->get_field_name( $input['id'] ),
			esc_attr( $input['label'] )
		);
		/**
		 * Do Input
		 */
		printf( '<textarea name="%s" id="%s" class="%s" rows="%d" cols="%d">%s</textarea>',
			$widget->get_field_name( $input['id'] ),
			$widget->get_field_id( $input['id'] ),
			$input['class'],
			$input['rows'],
			$input['cols'],
			esc_attr( $value )
		);
	}

	private function do_radio_input( $widget, $input, $value ) {
		/**
		 * Normalize the arguments required for this field type
		 */
		$defaults = array(
			'label'   => '',
			'class'   => 'widefat',
			'default' => '1',
			'options' => array(
				'1' => __( 'Option 1', 'wpcl_plugin_scaffolding' ),
				'2' => __( 'Option 2', 'wpcl_plugin_scaffolding' ),
			),
		);
		$input = array_merge( $defaults, $input );
		/**
		 * Open group
		 */
		echo '<radiogroup>';
		/**
		 * do legend
		 */
		printf( '<legend style="margin-bottom: 5px; display: block;">%s</legend>',
			$input['label']
		);
		/**
		 * Do Options
		 */
		foreach( $input['options'] as $input_value => $label ) {
			printf( '<input name="%s" id="%s" class="%s" type="radio" value="%s"%s/>',
				$widget->get_field_name( $input['id'] ),
				$widget->get_field_id( $input['id'] ),
				$input['class'],
				$input_value,
				checked( $value, $input_value, false )
			);
			printf( '<label for="%s">%s</label>',
				$widget->get_field_name( $input['id'] ),
				esc_attr( $label )
			);
			echo '</br>';
		}
		/**
		 * Close group
		 */
		echo '</radiogroup>';
	}

	private function do_checkbox_input( $widget, $input, $value ) {
		/**
		 * Normalize the arguments required for this field type
		 */
		$defaults = array(
			'label' => '',
			'class' => '',
			'value' => '1'
		);
		$input = array_merge( $defaults, $input );
		/**
		 * Do Input
		 */
		printf( '<input name="%s" id="%s" class="%s" type="checkbox" value="%s" %s/>',
			$widget->get_field_name( $input['id'] ),
			$widget->get_field_id( $input['id'] ),
			$input['class'],
			$input['value'],
			checked( $input['value'], $value, false )
		);
		/**
		 * Do label
		 */
		printf( '<label for="%s">%s</label>',
			$widget->get_field_name( $input['id'] ),
			esc_attr( $input['label'] )
		);
	}

	private function do_select_input( $widget, $input, $value ) {
		/**
		 * Normalize the arguments required for this field type
		 */
		$defaults = array(
			'label' => '',
			'class' => 'widefat',
			'options' => array(
				'' => __( 'Select Option', 'wpcl_plugin_scaffolding' ),
			),
		);
		$input = array_merge( $defaults, $input );
		/**
		 * Do label
		 */
		printf( '<label for="%s" style="margin-bottom: 5px; display: block;">%s</label>',
			$widget->get_field_name( $input['id'] ),
			esc_attr( $input['label'] )
		);
		/**
		 * Open select
		 */
		printf( '<select name="%s" id="%s" class="%s">',
			$widget->get_field_name( $input['id'] ),
			$widget->get_field_id( $input['id'] ),
			$input['class']
		);
		/**
		 * Do Options
		 */
		foreach( $input['options'] as $option_value => $label ) {
			printf( '<option value="%s"%s>%s</option>',
				$option_value,
				selected( $value, $option_value, false ),
				$label
			);
		}
		/**
		 * Close Select
		 */
		echo '</select>';
	}
} // end class