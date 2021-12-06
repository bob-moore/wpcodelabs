<p>
	<label for="<?php echo $this->get_field_name( 'title' ); ?>">Title</label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
</p>
<p>
	<label for="<?php echo $this->get_field_name( 'sample_option_1' ); ?>">Sample Option 1</label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'sample_option_1' ); ?>" name="<?php echo $this->get_field_name( 'sample_option_1' ); ?>" value="<?php echo esc_attr( $instance['sample_option_1'] ); ?>">
</p>
<p>
	<label for="<?php echo $this->get_field_name( 'sample_option_2' ); ?>">Sample Option 2</label>
	<textarea class="widefat" id="<?php echo $this->get_field_id( 'sample_option_2' ); ?>" name="<?php echo $this->get_field_name( 'sample_option_2' ); ?>" cols="30" rows="10"><?php echo esc_attr( $instance['sample_option_2'] ); ?></textarea>
</p>