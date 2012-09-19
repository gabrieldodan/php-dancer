<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class SysBase {
	
	static protected $coreClasses = array('Db', 'Glb', 'View', 'Request', 'Sess', 'Locator', 'Workflow', 'HooksManager', 'Controller', 'Model');
		
	final function __construct() {
		Sys::errorHandler("Sys instantiation not allowed");
	}
	
	static function importLib($library, $fromSys=FALSE, $params=array()) {
		$library = trim( $library, "/" );
		$file    = ($fromSys ? Locator::pathSysDir() : Locator::pathAppDir() ) . "/libraries/{$library}/{$library}.php";
		
		static $libsLoaded = array();
		if ( in_array($file, $libsLoaded) ) {
			// already loaded
			return;
		}
		
		if ( !is_file($file) ) {
			Sys::errorHandler("Library '{$library}' not found");
			return NULL;
		}
		
		/* add params into lib scope */
		foreach ($params AS $key => $val) {
			${$key} = $val;
		}
		
		require_once($file);
		
		$libsLoaded[] = $file;
	}
	
	
	static function cfgItem($item, $cfgFile='') {
		static $loadedConfigFiles = array();
	
		$cfgFile = ($cfgFile ? $cfgFile : "main");
		$key     = $cfgFile;
		if ( !isset($loadedConfigFiles[$key]) ) {
			$loadedConfigFiles[$key] = array();
			$config                  = array();
			//$file                    = Locator::srcDirBasePath() . "/configs/" . $cfgFile . ".php";
			$file                    = Sys::bs2s( dirname(__FILE__) ) . "/../../" . ltrim(APP_DIR . "/configs/{$cfgFile}.php"); 
			if ( !is_file($file) ) {
				Sys::errorHandler("Config file '{$cfgFile}' not found");
				return NULL;
			}
			
			require($file);
			
			foreach ($config AS $k => $v) {
				$loadedConfigFiles[$key][$k] = $v;
			}
		}
		if ( isset($loadedConfigFiles[$key][$item]) ) {
			return $loadedConfigFiles[$key][$item];
		}
		return NULL;
	}
	
	static function loadModel($model) {
		static $loadedModels = array();
	
		$key  = $model;
		if ( !isset($loadedModels[$key]) ) {
			$file = Locator::pathAppDir() . '/models/' . $model . ".php";
			if ( !is_file($file) ) {
				Sys::errorHandler("Model '{$model}' not found");
				return NULL;
			}
			
			require($file);
			
			$className 			= "model_{$model}";
			$loadedModels[$key]	= new $className();
		}
		return $loadedModels[$key];
	}
	
	
	static public function getRealPath($file, $relativeTo='workingDir') {
		$file = str_replace( array("\\", Locator::pathHome()), array("/", ""), $file );
		if ( $relativeTo == "workingDir" ) {
			// relative to workingDir
			$relativeToPath = Sys::bs2s(getcwd());
		}
		else if ( $relativeTo == "sitePath" ) {
			// relative to sitePath
			$relativeToPath = Locator::pathHome();
		}
		else if ( $relativeTo == "thisFile" ) {
			// relative to caller file
			$trace          = debug_backtrace(false); // trace[1] is caller caller info
			$relativeToPath = Sys::bs2s( dirname($trace[0]["file"]) );
		}
		return Sys::bs2s( realpath($relativeToPath . "/" . $file) );
	}
	
	static protected function collectHookActions() {
		static $hookActionsLoaded = FALSE;
		
		if ( $hookActionsLoaded === FALSE ) {
			$hookActionsLoaded = TRUE;
			$actions           = array();
			$file              = Locator::pathAppDir() . "/hook-actions/hook-actions.php";
		
			require_once($file);
			
			foreach ( $actions AS $hook => $hookActions ) {
				foreach ($hookActions AS $action => $actionParams) {
					HooksManager::addHookAction( $hook, array("HookActions", $action), $actionParams );
				}
			}
		}
		
		//print_r(EventsManager::$eventsHandlers);
	}

	
	
	
	
	
	static protected function coreClassAutoloader($className) {
		//$className = strtolower($className);
		if ( in_array($className, Sys::$coreClasses) ) {
			require_once(SYS_DIR . "/core/{$className}.php");
		}
	}
	static function run() {
		/* start output buffering first */
		ob_start();
		
		/* register autoloader for core classes */ 
		spl_autoload_register(array("Sys", "coreClassAutoloader"));
		
		/* set errors handler */
		//set_error_handler( array("self", "errorsHandler") );
		
		/* collect hook actions defined on fw-core-extension/hook-actions.php file */
		Sys::collectHookActions();
		
		/* run the workflow */
		Workflow::run();
	}
	
	static function bs2s($str) {
		return str_replace("\\", "/", $str);
	}
	
	static public function buildUrl($path, $query='') {
		$path = ltrim( trim($path), "/" );
		if ( !$path ) {
			return Locator::urlHome() . $query;
		}
		return Locator::urlHome() . "/{$path}" . $query;
	}
	
	
	static public function redirect($url) {
		if ( !preg_match('@^https?://@i', $url) ) {
			$url = Sys::buildUrl($url);
		}
		header("Location: ". $url, TRUE);
		exit;
	}
	
	static function jsonEncode($data) {
		return json_encode($data);
	}
	
	static function jsonDecode($json) {
		return json_decode($json);
	}
	
	static public function errorHandler($errMsg, $err404=false) {
		if ( ob_get_length() !== FALSE ) { // output buffering is started
			ob_end_clean();
		}
		
		if ( $err404 ) {
			header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
		}
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type"  content="application/xhtml+xml; charset=UTF-8" />
		<title>Error</title>
		<style type="text/css">
			body {
				background-color:#FFFFFF;
			}
			#content {
				border:1px solid #e5e5e5;
				background-color:#F5f5f5;
				margin-left:auto;
				margin-right:auto;
				width:500px;
				margin-top:50px;
				padding:10px;
			}
		</style>
	</head>
	<body>
		<div id="content">
			<?php echo $errMsg ?>
		</div>
	</body>
</html>
		<?php
		exit;
	}
	
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/Sys.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('Sys') ) {
	class Sys extends SysBase {
	}
}
?>