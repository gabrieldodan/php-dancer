<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class SessBase {
	
	final function __construct() {
		Sys::errorHandler("Sess instantiation not allowed");
	}
	
	static public function set($var, $val) {
		$_SESSION[$var] = $val;
	}
	
	static public function get($var) {
		return @$_SESSION[$var];
	}
	
	static public function remove($var) {
		unset($_SESSION[$var]);
	}
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Sess.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Sess') ) {
	class Sess extends SessBase {
	}
}

// start session
session_start();