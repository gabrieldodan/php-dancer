<?php
/**
 * 
 */

/* SAMPLE */
/*
$actions[Workflow::HOOK_OUTPUT_FLUSH_START] = array(
	'beautifyOutput'	=> array($param1, $param2), // 'beautifyOutput' it's a static public method of HookActions class, array($param1, $param2) is an array with parameters to be send to static method 
	'addExtraOutput'	=> array($param)
);
*/


class HookActions {
	final function __construct() {
		Sys::errorHandler("HookActions instantiation not allowed");exit;
	}
	
	/**
	 * Add hook actions as static methods
	 */
}
?>