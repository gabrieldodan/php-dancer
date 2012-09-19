<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/**
 * A util class for storing variables that are accesible from anyware. Is intended to be used instead of $GLOBALS superglobal. 
 */
class GlbBase {
	static protected $data         = array();
	
	final function __construct() {
		Sys::errorHandler("Glb instantiation not allowed");
	}
	
	static public function get($var) {
		return @Glb::$data[$var]; 
	}
	
	static public function set($var, $val) {
		Glb::$data[$var] = $val;
	}
	
	static public function remove($var) {
		unset( Glb::$data[$var] );
	}
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Glb.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Glb') ) {
	class Glb extends GlbBase {
	}
}
