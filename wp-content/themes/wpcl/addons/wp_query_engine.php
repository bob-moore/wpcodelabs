<?php
/**
 * Add Custom Templates
 */
function _s_wp_query_register_templates( $templates ) {
	$templates['Default'] = _S_ROOT_DIR . 'template-parts/wp_query/default.php';
	return $templates;
}
add_filter( 'wp_query_engine_templates', '_s_wp_query_register_templates' );
// /**
//  * Override default template
//  */
// function wp_query_default_content( $template_name, $context, $query, $atts ) {
// 	_s_get_template_part( 'content' );
// }
// function wp_query_default_content_wrap_open( $template_name, $context, $query, $atts ) {
// 	return false;
// }
// function wp_query_default_content_wrap_close( $template_name, $context, $query, $atts ) {
// 	return false;
// }