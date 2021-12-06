<?php if( intval( $settings->columns ) > 1 ) : ?>

.fl-node-<?php echo $id; ?> .s-icon-list {
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	-webkit-box-orient: horizontal;
	-webkit-box-direction: normal;
	    -ms-flex-direction: row;
	        flex-direction: row;
	-ms-flex-wrap: wrap;
	    flex-wrap: wrap;
	margin: 0 -13px;
}

.fl-node-<?php echo $id; ?> .s-icon-list li {
	width: <?php echo 100 / intval( $settings->columns ); ?>%;
	padding: 0 13px;
}

<?php else : ?>

	.fl-node-<?php echo $id; ?> .s-icon-list {
		display: block;
		margin: 0;
	}

	.fl-node-<?php echo $id; ?> .s-icon-list li {
		width: 100%;
		padding: 0;
	}

<?php endif; ?>

<?php $global = \FLBuilderModel::get_global_settings(); ?>

<?php if( intval( $settings->columns_medium ) > 1 ) : ?>

	@media ( max-width: <?php echo $global->medium_breakpoint; ?>px ){
		.fl-node-<?php echo $id; ?> .s-icon-list {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-orient: horizontal;
			-webkit-box-direction: normal;
			    -ms-flex-direction: row;
			        flex-direction: row;
			-ms-flex-wrap: wrap;
			    flex-wrap: wrap;
			margin: 0 -13px;
		}

		.fl-node-<?php echo $id; ?> .s-icon-list li {
			width: <?php echo 100 / intval( $settings->columns_medium ); ?>%;
			padding: 0 13px;
		}

	}

<?php else : ?>

	@media ( max-width: <?php echo $global->medium_breakpoint; ?>px ){

		.fl-node-<?php echo $id; ?> .s-icon-list {
			display: block;
			margin: 0;
		}

		.fl-node-<?php echo $id; ?> .s-icon-list li {
			width: 100%;
			padding: 0;
		}
	}

<?php endif; ?>

<?php if( intval( $settings->columns_responsive ) > 1 ) : ?>

	@media ( max-width: <?php echo $global->responsive_breakpoint; ?>px ){
		.fl-node-<?php echo $id; ?> .s-icon-list {
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-orient: horizontal;
			-webkit-box-direction: normal;
			    -ms-flex-direction: row;
			        flex-direction: row;
			-ms-flex-wrap: wrap;
			    flex-wrap: wrap;
			margin: 0 -13px;
		}

		.fl-node-<?php echo $id; ?> .s-icon-list li {
			width: <?php echo 100 / intval( $settings->columns_responsive ); ?>%;
			padding: 0 13px;
		}

	}

<?php else : ?>

		@media ( max-width: <?php echo $global->responsive_breakpoint; ?>px ){

			.fl-node-<?php echo $id; ?> .s-icon-list {
				display: block;
				margin: 0;
			}

			.fl-node-<?php echo $id; ?> .s-icon-list li {
				width: 100%;
				padding: 0;
			}
	}

<?php endif; ?>