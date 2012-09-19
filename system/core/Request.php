<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class RequestBase {
	
	static protected $urlPath			= NULL;
	
	
	
	static protected $controller 		= NULL;
	static protected $controllerFile	= NULL;
	static protected $controllerPath	= NULL;
	static protected $controllerClass	= NULL;
	static protected $action	 		= NULL;
	static protected $actionMethod 		= NULL;
	
	/*
	static protected $action		 		= NULL;
	static protected $actionMethod 		= NULL;
	
	static protected $controller 		= NULL;
	static protected $controllerFile		= NULL;
	static protected $controllerClass	= NULL;
    */
	
	final function __construct() {
		Sys::errorHandler("Request instantiation not allowed");
	}
	
	static public function doMapping() {
		Request::$urlPath = trim(@$_GET['url_path'], "/");
		
		/**
		 * Check first if the request is for a library/component page.
		 */
		if ( preg_match('/^__(SiteLibScript|FwLibScript|SiteCompScript|FwCompScript)__\/(.*)/is', Request::$urlPath, $m) ) {
			$urlPath 	= $m[2];
			if ( !$urlPath ) {
				Sys::errorHandler("Access denied"); exit;
			}
			
			$basePaths = array(
				'SiteLibScript'		=>	Locator::pathAppDir() . "/libraries",
				'FwLibScript'		=>	Locator::pathSysDir() . "/libraries",
				'SiteCompScript'	=>	Locator::pathAppDir() . "/components",
				'FwCompScript'		=>	Locator::pathSysDir() . "/components"
			);
			
			$basePath			= $basePaths[$m[1]];
			$libOrCompName		= Request::getSegment(1, $urlPath);
			
			$controller			= Request::getSegment(2, $urlPath);
			$controller			= ($controller ? $controller : Sys::cfgItem("defaultController"));
			$controllerFile		= $controller . "Controller.php";
			$controllerClass	= $controller . "Controller";
			
			$action				= Request::getSegment(3, $urlPath);
			$action				= ($action ? $action : "default");
			$actionMethod		= $action . "Action";
			
			// check if the controller file is allowed
			$allowedScripts = include_once "{$basePath}/{$libOrCompName}/allowed-scripts.php";
			if ( in_array($controllerFile, $allowedScripts)) {
				Request::$action 				= $action;
				Request::$actionMethod			= $actionMethod;
				Request::$controller			= $controller;
				Request::$controllerFile		= $controllerFile;
				Request::$controllerPath		= "{$basePath}/{$libOrCompName}/{$controllerFile}";
				Request::$controllerClass		= $controllerClass;
				return;
			}
			else {
				Sys::errorHandler("Access denied");
				exit;
			}
		}
		
		$segment_1     = Request::getSegment(1, Request::$urlPath);
		$segment_2     = Request::getSegment(2, Request::$urlPath);
		if ( !$segment_1 ) {
			// default controller and action
			Request::$controller	= Sys::cfgItem("defaultController");
			Request::$action		= "default";
		}
		else if ($segment_1 && !$segment_2 ) {
			Request::$controller	= $segment_1;
			Request::$action		= "default";
		}
		else if ($segment_1 && $segment_2 ) {
			Request::$controller	= $segment_1;
			Request::$action		= $segment_2;
		}
		
		Request::$controllerFile 	= Request::$controller . "Controller.php";
		Request::$controllerPath 	= Locator::pathAppDir() . "/controllers/" . Request::$controllerFile;
		Request::$controllerClass	= Request::$controller . "Controller";
		Request::$actionMethod		= Request::$action . "Action";
	} 
	
	static public function urlPath() {
		return Request::$urlPath;
	} 
	static function getController() {
		return Request::$controller;
	}
	static function getControllerFile() {
		return Request::$controllerFile;
	}
	static function getControllerPath() {
		return Request::$controllerPath;
	}
	static function getControllerClass() {
		return Request::$controllerClass;
	}
	
	static function getAction() {
		return Request::$action;
	}
	static function getActionMethod() {
		return Request::$actionMethod;
	}
	
	
	static public function getSegment($pos, $path=NULL) {
		if ( $path === NULL ) {
			$path = Request::$urlPath;
		}
		$arr = explode('/', $path);
		return @trim($arr[$pos-1]);
	}
	
	static public function countSegments($path) {
		$arr = explode('/', $path);
		$cnt = 0;
		foreach ($arr AS $segment) {
			if ( trim($segment) ) {
				$cnt++;
			}
		}
		return $cnt;
	}
	
	/* to do */
	static public function addRewriteRules() {
		
	}
	
	static public function runRewriteRules() {
		$_GET['url_path'] = @trim($_GET['url_path'], "/");	
		$rules = Sys::cfgItem('rewrite-rules');
		if ( !$rules ) {
			return;
		}
		foreach ($rules AS $rule) {
			$res = preg_match('@' . $rule['pattern'] . '@', @$_GET['url_path'], $matches);
			if ( $res ) {
				$_GET['url_path'] =  preg_replace('@' . $rule['pattern'] . '@', $rule['newPath'], @$_GET['url_path'], 1);
				return;
			}
		}
	}
	
	/**
	 * Get $_POST value. NULL if the key not exists. The method streap slashes if the magic quotes is ON
	 * @param string $key
	 */
	static public function post($key) {
		if ( array_key_exists($key, $_POST) ) {
			return ( get_magic_quotes_gpc() ? stripslashes($_POST[$key]) : $_POST[$key] );
		}
		else {
			return NULL;
		}
	}

	/**
	 * Get $_GET value. NULL if the key not exists
	 * @param string $key
	 */
	static public function get($key) {
		if ( array_key_exists($key, $_GET) ) {
			return ( get_magic_quotes_gpc() ? stripslashes($_GET[$key]) : $_GET[$key] );
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Get $_FILES value. NULL if the key not exists. The method streap slashes if the magic quotes is ON.
	 * @param string $key
	 * @param integer $idx
	 */
	static public function file($key, $idx=NULL) {
		if ( !array_key_exists($key, $_FILES) ) {
			return NULL;
		}
		
		if ( $idx !== NULL ) {
			$fileAttrs = array();
			foreach ($_FILES[$key] AS $attr => $attrValues) {
				$fileAttrs[$attr] = $attrValues[$idx];
			}
			return $fileAttrs;
		}
		else {
			return $_FILES[$key];
		}
	}
	
	static public function param($param) {
		if ( is_int($param) ) {
			static $urlPathWithoutActionAndController = '';
			if ( $urlPathWithoutActionAndController === '' ) {
				$regexp = "!^(" . Request::$controller . (Request::$action != 'default' ? "/" . Request::$action : "") . ")(.*)$!";
				$urlPathWithoutActionAndController = trim( preg_replace($regexp, '$2', Request::urlPath()) , "/");
			}
			return Request::getSegment($param, $urlPathWithoutActionAndController);
		}
		else {
			return @$_GET[$param];
		}
	} 
	
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Request.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Request') ) {
	class Request extends RequestBase {
	}
}
?>