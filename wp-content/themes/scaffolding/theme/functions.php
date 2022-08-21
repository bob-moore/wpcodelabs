<?php
use \Wpcl\Scaffolding\Theme;
/**
 * Composer autoloader
 * @see https://getcomposer.org/doc/01-basic-usage.md#autoloading
 */
require dirname( get_template_directory() ) . '/Lib/vendors/autoload.php';

Theme::init();


// $input = '<div class="entry-footer"> ';

// $config = HTMLPurifier_Config::createDefault();
// $purifier = new HTMLPurifier($config);
// $clean_html = $purifier->purify($input);

// var_dump($clean_html);
// $config = array(
//            'indent'         => true,
//            'output-xhtml'   => true,
//            'wrap'           => 200);
// $tidy = new tidy;
// $tidy->parseString($input, $config, 'utf8');
// $tidy->cleanRepair();

// add_action( 'loop/enter', 'the_post' );
// add_filter( 'timber_compile_result', 'trim' );/

// add_filter('timber/locations', function($locs){

// 	return $locs;
// })
