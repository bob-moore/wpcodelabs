<?php

namespace Scaffolding\addons;

class FLBuilder {

	public function __construct() {
		if( !class_exists( 'FLBuilderModel' ) ) {
			return false;
		}
	}
}