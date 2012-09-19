<?php
/**
 *
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

class HooksManagerBase {
	static protected $hooksActions         = array();
	static protected $hooksActionsHandlers = array();
	
	final function __construct() {
		Sys::errorHandler("HooksManager instantiation not allowed");
	}
	
	static protected function defaultHandler($actions=array()) {
		foreach ( $actions AS $idx => $actionAndParams ) {
			$action  = $actionAndParams[0];
			$params  = $actionAndParams[1];
			
			call_user_func_array($action, $params);
			
			/*
			if ( is_array($handler) ) {
				// the handler is an object method or static method
				call_user_func_array($handler, $params);
			}
			else if ( is_a($handler, "Closure") ) {
				// the handler is an anonymous function
				call_user_func_array($handler, $params);
			}
			else if ( is_string($handler) ) {
				// the handler is function
				call_user_func_array($handler, $params);
			}
			*/
		}
	}
	
	static function registerNewHook($hookName, $actionsHandler) {
		HooksManager::$hooksActionsHandlers[$hookName] = $actionsHandler;
	}
	
	static function removeHook($hookName) {
		unset( HooksManager::$hooksActionsHandlers[$hookName] );
	}
	
	/**
	 * Add a hook action. The action can be function, anonymous function, object method or static method.
	 * E.g.
	 * - HooksManager::addHookAction( Workflow::HOOK_PAGE_LAYOUT_LOAD_END, "functionName", $handlerParams ); Action as a function
	 * - HooksManager::addHookAction( Workflow::HOOK_PAGE_LAYOUT_LOAD_END, array("className", "staticMethodName"), $handlerParams ); Action as a static public method , you must first include the file that contain the class definition
	 * - HooksManager::addHookAction( Workflow::HOOK_PAGE_LAYOUT_LOAD_END, array($object, "methodName"), $handlerParams ); Action as an object public method
	 * - HooksManager::addHookAction( Workflow::HOOK_PAGE_LAYOUT_LOAD_END, function (){  }, $handlerParams ); Action as an anonymous function, as of PHP 5.3 only!
	 * @param string $hook
	 * @param string/array $handler
	 * @param array $actionParams
	 */
	static function addHookAction($hook, $action, $actionParams=array()) {
		HooksManager::$hooksActions[$hook][] = array($action, $actionParams);
	}
	
	static function runHookActions($hookName) {
		if ( !array_key_exists($hookName, HooksManager::$hooksActions) ) {
			return;
		}
		
		if ( array_key_exists($hookName, HooksManager::$hooksActionsHandlers) ) {
			$call = HooksManager::$hooksActionsHandlers[$hookName];
		}
		else {
			$call = array("HooksManager", "defaultHandler");
		}
		
		//print_r(EventsManager::$eventsHandlers);
		
		call_user_func_array( $call, array(HooksManager::$hooksActions[$hookName]) );
		//call_user_func( $call, EventsManager::$eventsHandlers[$event] );
	}
	
	
	
}

/* extension mechanism */
$extFile = ltrim(APP_DIR . "/fw-core-extension/HooksManager.php");
if ( is_file($extFile) ) {
	require_once($extFile);
}
if ( !class_exists('HooksManager') ) {
	class HooksManager extends HooksManagerBase {
	}
}

?>