<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class WorkflowBase {
	
	const HOOK_CONN_DBS_START           = "hookConnDBsStart";
	const HOOK_CONN_DBS_END             = "hookConnDBsEnd";
	
	const HOOK_AUTOLOAD_LIBS_START		= "hookAutoloadLibsStart";
	const HOOK_AUTOLOAD_LIBS_END		= "hookAutoloadLibsEnd";
	
	const HOOK_INIT_START				= "hookInitStart";
	const HOOK_INIT_END					= "hookInitEnd";
	
	const HOOK_REWRITE_START			= "hookRewriteStart";
	const HOOK_REWRITE_END				= "hookRewriteEnd";
	
	const HOOK_MAPPING_START			= "hookMappingStart";
	const HOOK_MAPPING_END				= "hookMappingEnd";

	/*
	const HOOK_PAGE_CONTENT_LOAD_START  = "phasePageContentLoadStart";
	const HOOK_PAGE_CONTENT_LOAD_END    = "phasePageContentLoadEnd";
	
	const HOOK_PAGE_LAYOUT_LOAD_START   = "phasePageLayoutLoadStart";
	const HOOK_PAGE_LAYOUT_LOAD_END     = "phasePageLayoutLoadEnd";
	*/
	
	const HOOK_GENERATE_OUTPUT_START	= "hookGenerateOutputStart";
	const HOOK_GENERATE_OUTPUT_END		= "hookGenerateOutputEnd";
	
	const HOOK_ACTION_START				= "hookActionStart";
	const HOOK_ACTION_END				= "hookActionEnd";
	
	const HOOK_MAIN_CONTENT_START		= "hookMainContentStart";
	const HOOK_MAIN_CONTENT_END			= "hookMainContentEnd";
	
	
	
	const HOOK_OUTPUT_FLUSH_START		= "hookOutputFlushStart";
	
	final function __construct() {
		Sys::errorHandler("Workflow instantiation not allowed");
	}
	
	static protected function phaseAutoloadLibraries($suppressHooks=FALSE){
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_AUTOLOAD_LIBS_START);
		}
		
		$libraries = Sys::cfgItem("autoload-libraries");
		$libraries = ($libraries === NULL ? array() : $libraries);
		foreach ($libraries AS $library) {
			Sys::importLib( $library[0], $library[1], (isset($library[2]) ? $library[2] : array()) );
		}
		
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_AUTOLOAD_LIBS_END);
		}
	}
	
	static protected function phaseInit($suppressHooks=FALSE) {
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_INIT_START);
		}
		
		ini_set("display_errors", "1");
		error_reporting(E_ALL);
		
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_INIT_END);
		}
	}
	
	static protected function phaseRewrite($suppressHooks=FALSE) {
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_REWRITE_START);
		}
		
		Request::runRewriteRules();
		
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_REWRITE_END);
		}
	}
	
	
	static protected function phaseInitDBs($suppressHooks=FALSE) {
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_CONN_DBS_START);
		}
		
		$dbConfigs = Sys::cfgItem("dbconfig");
		if ( count($dbConfigs) ) {
			Db::init( $dbConfigs );
		} 
		
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_CONN_DBS_END);
		}
	}
	
	/**
	 * mapp the request to a page script
	 */
	static protected function phaseMapping($suppressHooks=FALSE) {
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_MAPPING_START);
		}
		
		Request::doMapping();
		
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_MAPPING_END);
		}
	}
	
	static protected function phaseGenerateOutput($suppressHooks=FALSE) {
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_GENERATE_OUTPUT_START);
		}
		
		$controllerFile				= Request::getControllerFile();
		$controllerPath				= Request::getControllerPath();
		$controllerClass			= Request::getControllerClass();
		$action						= Request::getAction();
		$actionMethod				= Request::getActionMethod();
		
		// check controller file exists
		if ( !is_file($controllerPath) ) {
			Sys::errorHandler("Controller file '" . $controllerFile . "' not found", true);
		}
		
		// make instance
		try {
			require_once $controllerPath;
			$controllerInstance			= new $controllerClass(); /* @var $controllerInstance Controller */
		}
		catch ( Exception $e ) {
			Sys::errorHandler("Error instantiate $controllerClass class", true);
		}
		
		// check action method exists
		if ( !method_exists( $controllerInstance,  $actionMethod) ) {
			Sys::errorHandler("Method action '" . $actionMethod . "' not found", true);
		}
		
		
		
		// run before action, action and after action methods
		ob_start(); ob_implicit_flush(0);
			
		$controllerInstance->beforeAction();
		$controllerInstance->{$actionMethod}();
		$controllerInstance->afterAction();
		
		View::$pageContent = ob_get_clean();
		
		View::replacePlaceholders();
		
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_GENERATE_OUTPUT_END);
		}
	}
	
	static protected function phaseOutputFlush($suppressHooks=FALSE) {
		if ( !$suppressHooks ) {
			HooksManager::runHookActions(Workflow::HOOK_OUTPUT_FLUSH_START);
		}
		
		/* gzip content */
		ini_set("zlib.output_compression", "On");
        
		if ( Sys::cfgItem("enableETagMechanism") === TRUE ) {
			$etag = md5( ob_get_contents() . View::$pageContent );  
			if ( isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag ) {
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
			else {
				header('ETag: ' . $etag);
				echo View::$pageContent;
			}
		}
		else {
			echo View::$pageContent;
		}
		
	}
	
	static public function run() {
		
		/* init databases connections , if exists */
		Workflow::phaseInitDBs();
		
		/* any initializations you need  */
		Workflow::phaseInit();
		
		/* autoload libraries */
		Workflow::phaseAutoloadLibraries();
		
		/* run rewrite rules */
		Workflow::phaseRewrite();
		
		/* do mapping , URL -> page content script */
		Workflow::phaseMapping();
		
		/* load page content  */
		//Workflow::phasePageContentLoad();
		
		/* load page layout */
		//Workflow::phasePageLayoutLoad();

		Workflow::phaseGenerateOutput();
		
		/* output all content */
		Workflow::phaseOutputFlush();
		
		
	}
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Workflow.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Workflow') ) {
	class Workflow extends WorkflowBase {
	}
}
?>