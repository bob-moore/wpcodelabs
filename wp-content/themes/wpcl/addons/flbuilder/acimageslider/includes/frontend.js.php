<?php

// $global_settings  = \FLBuilderModel::get_global_settings();

// $args = array(
// 	'dots' => true,
// 	'infinite' => true,
// 	'slidesToShow' => !empty( $settings->slides_to_show ) ? $settings->slides_to_show : 3,
// 	'speed' => !empty( $settings->speed ) ? $settings->speed : 300,
// 	// 'autoplay' => true,
// 	'centerMode' => true,
// 	'centerPadding' => '0px',
// 	// 'initialSlide' => 2
// );
// $args = array(
// 	'infinite' => true,
// 	'dots' => true,
// 	'slidesToShow' => !empty( $settings->slides_to_show ) ? intval( $settings->slides_to_show ) : 3,
// 	'centerMode' => true,
// 	'centerPadding' => '20%',
// 	'autoplay' => false,
// 	'speed' => !empty( $settings->speed ) ? intval( $settings->speed ) : 300,
// 	// 'centerMode' => true,
// );

// if( !empty( $settings->slides_to_show_medium ) || !empty( $settings->slides_to_show_responsive ) ) {

// 	$args['responsive'] = array();

// 	if( !empty( $settings->slides_to_show_medium ) ) {
// 		$args['responsive'][] = array(
// 			'breakpoint' => intval( $global_settings->medium_breakpoint ),
// 			'settings'   => array(
// 				'slidesToShow' => intval( $settings->slides_to_show_medium ),
// 			),
// 		);
// 	}

// 	if( !empty( $settings->slides_to_show_responsive ) ) {
// 		$args['responsive'][] = array(
// 			'breakpoint' => intval( $global_settings->responsive_breakpoint ),
// 			'settings'   => array(
// 				'slidesToShow' => intval ( $settings->slides_to_show_responsive ),
// 			),
// 		);
// 	}
// }

// <?php

// if( $settings->gallery_type !== 'slider' ) {
// 	return;
// }

$global_settings  = \FLBuilderModel::get_global_settings();


$args = array(
	'infinite' => true,
	'dots' => $settings->dots == '1',
	'slidesToShow' => !empty( $settings->slides_to_show ) ? intval( $settings->slides_to_show ) : 3,
	'autoplay' => $settings->autoplay == '1',
	'speed' => !empty( $settings->speed ) ? intval( $settings->speed ) : 300,
	'arrows' => $settings->arrows == '1',
	'autoplaySpeed' => intval( $settings->autoplaySpeed ),
	'centerMode' => $settings->centermode == '1',
	'centerPadding' => 0,

);

if( $settings->centermode == '1' ) {
	$args['centerPadding'] = '20%';
}

if( !empty( $settings->slides_to_show_medium ) || !empty( $settings->slides_to_show_responsive ) ) {

	$args['responsive'] = array();

	if( !empty( $settings->slides_to_show_medium ) ) {
		$args['responsive'][] = array(
			'breakpoint' => intval( $global_settings->medium_breakpoint ),
			'settings'   => array(
				'slidesToShow' => intval( $settings->slides_to_show_medium ),
			),
		);
	}

	if( !empty( $settings->slides_to_show_responsive ) ) {
		$args['responsive'][] = array(
			'breakpoint' => intval( $global_settings->responsive_breakpoint ),
			'settings'   => array(
				'slidesToShow' => intval ( $settings->slides_to_show_responsive ),
			),
		);
	}
}

?>

jQuery( function ( $ ) {
	$( '.fl-node-<?php echo $id ?> .image-slider' ).slick( <?php echo json_encode( $args ) ?> );
});