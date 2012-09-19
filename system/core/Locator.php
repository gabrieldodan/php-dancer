<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */
class LocatorBase {
	
	final function __construct() {
		Sys::errorHandler("Locator instantiation not allowed");
	}
	
	static public function urlHome() {
		static $urlHome = NULL;
		
		
		if ( $urlHome === NULL ) {
			// auto-detect or load from configs
			$cfgBaseUrl = Sys::cfgItem('homeUrl'); 
			if ( $cfgBaseUrl ) {
				// from config
				$urlHome = $cfgBaseUrl;
			}
			else {
				// auto-detect
				$pathBase = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/")+1);
				$urlHome  = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['SERVER_NAME'] . $pathBase;
			}
			$urlHome = rtrim($urlHome, "/");
		} 
		
		return $urlHome;
	}
	static public function urlAppDir() {
		static $urlAppDir = NULL;
		
		if ( $urlAppDir === NULL ) {
			$urlAppDir = Locator::urlHome() . (APP_DIR ? "/" . APP_DIR : "");
		}
		return $urlAppDir;
	}
	
	static public function urlSysDir() {
		static $urlSysDir = NULL;
		
		if ( $urlSysDir === NULL ) {
			$urlSysDir = Locator::urlHome() . "/" . SYS_DIR;
		}
		return $urlSysDir;
	}
	
	static public function pathHome() {
		static $pathHome = NULL;
		
		if ( $pathHome === NULL ) {
			// auto-detect or load from configs
			$cfgBasePath = Sys::cfgItem('homePath');
			if ( $cfgBasePath ) {
				// from config
				$pathHome = $cfgBasePath;
			}
			else {
				// auto-detect
				$pathHome = str_replace(array("\\", "/" . SYS_DIR . "/core"), array("/", ""), dirname(__FILE__));
			}
			$pathHome = rtrim($pathHome, "/");
		} 
		return $pathHome;
	}
	static public function pathAppDir() {
		static $pathAppDir = NULL;
		
		if ( $pathAppDir === NULL ) {
			$pathAppDir = Locator::pathHome() . (APP_DIR ? "/" . APP_DIR : "");
		}
		return $pathAppDir;
	}
	static public function pathSysDir() {
		static $pathSysDir = NULL;
		
		if ( $pathSysDir === NULL ) {
			$pathSysDir = Locator::pathHome() . "/" . SYS_DIR;
		}
		return $pathSysDir;
	}
	
	static public function urlResDir($resourcesOf = 'app') {
		return ($resourcesOf == 'app'?  Locator::urlAppDir() : Locator::urlSysDir()) . "/resources";
	}
	
	static public function pathResDir($resourcesOf = 'app') {
		return ($resourcesOf == 'app'?  Locator::pathAppDir() : Locator::pathSysDir()) . "/resources";
	}
	
	static private function fileBaseUrl($filePath) {
		$dir = dirname( Sys::bs2s($filePath) );
		return Locator::urlHome() . str_replace(Locator::pathHome(), "", $dir);
	}
	
	
	static function urlThisLib(){
		$trace      = debug_backtrace(false); // trace[0] is caller info
		return Locator::fileBaseUrl($trace[0]["file"]);
	}
	static function urlThisComp(){
		$trace      = debug_backtrace(false); // trace[0] is caller info
		return Locator::fileBaseUrl($trace[0]["file"]);
	}
	
	static function pathThisLib(){
		$trace  = debug_backtrace(false); // trace[0] is caller info
		$res    = preg_match('!(.*?)/libraries/([^/]+)(.*)!is', Sys::bs2s( $trace[0]["file"] ), $m);
		if ( $res ) {
			return $m[1] . "/libraries" . (@$m[3] ? "/{$m[2]}" : "");
		}
		return FALSE;
	}
	static function pathThisComp(){
		$trace  = debug_backtrace(false); // trace[0] is caller info
		return dirname( Sys::bs2s($trace[0]["file"]) );
		/*
		
		echo Sys::bs2s($trace[0]["file"]); exit;
		$res    = preg_match('!(.*?)/components/(.+)!is', Sys::bs2s($trace[0]["file"]), $m);
		if ( $res ) {
			return $m[1] . "/components" . (@$m[3] ? "/{$m[2]}" : "");
		}
		return FALSE;
		*/
	}
	
	
	static function thisLibUrlToPage($pageGroup, $page='') {
		$trace      = debug_backtrace(false); // trace[0] is caller info
		$libBaseUrl = Locator::fileBaseUrl($trace[0]["file"]);
	
		$res = preg_match('!(.*?)/libraries/(.*)!is', $libBaseUrl, $m);
		if ( !$res ) {
			return FALSE;
		}
	
		// URL tail
		$urlTail = $m[2] . "/" . $pageGroup . ($page ? "/{$page}" : "");
		if ( strpos($libBaseUrl, Locator::urlSysDir()) !== FALSE ) {
			// framework library
			return Locator::urlHome() . "/__FwLibScript__/" . $urlTail;
		}
		else if ( strpos($libBaseUrl, Locator::urlAppDir()) !== FALSE ) {
			// site library
			return Locator::urlHome() . "/__SiteLibScript__/" . $urlTail;
		}
		return TRUE;
	}
	
	static function thisCompUrlToPage($pageGroup, $page='') {
		$trace      = debug_backtrace(false); // trace[0] is caller info
		$compBaseUrl = Locator::fileBaseUrl($trace[0]["file"]);
	
		$res = preg_match('!(.*?)/components/(.*)!is', $compBaseUrl, $m);
		if ( !$res ) {
			return FALSE;
		}
	
		// URL tail
		$urlTail = $m[2] . "/" . $pageGroup . ($page ? "/{$page}" : "");
		if ( strpos($compBaseUrl, Locator::urlSysDir()) !== FALSE ) {
			// sys library
			return Locator::urlHome() . "/__FwCompScript__/" . $urlTail;
		}
		else if ( strpos($compBaseUrl, Locator::urlAppDir()) !== FALSE ) {
			// site library
			return Locator::urlHome() . "/__SiteCompScript__/" . $urlTail;
		}
		return TRUE;
	}
	
}
/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Locator.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Locator') ) {
	class Locator extends LocatorBase {
	}
}
?>