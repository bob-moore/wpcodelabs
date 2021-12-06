<?php

namespace Scaffolding\addons;

class WPQueryEngine {

	public function __construct() {
		if( !class_exists( '\\WPCL\\QueryEngine\\Plugin'  ) ) {
			return false;
		}

		add_filter( 'wp_query_engine_templates', [$this, 'registerTemplates'] );
	}

	function registerTemplates( $templates ) {
		$templates['Default'] = _S_ROOT_DIR . 'theme/template-parts/wp_query/default.php';
		return $templates;
	}

}