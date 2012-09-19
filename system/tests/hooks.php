<?php

function f($p) {
	echo "f() {$p} <br />";
}

class cls {
	function objMethod($p) {
		echo "objMethod() {$p} <br>";
	}
	static function classMethod($p) {
		echo "classMethod() {$p} <br>";
	}
}

class hookHandler {
	static function hHandler($actions){
		foreach ( $actions AS $idx => $actionAndParams ) {
			$action  = $actionAndParams[0];
			$params  = $actionAndParams[1];

			call_user_func_array($action, $params);
			echo "q obj static";
		}
	}

}
function hookHandler($actions){
	foreach ( $actions AS $idx => $actionAndParams ) {
		$action  = $actionAndParams[0];
		$params  = $actionAndParams[1];
			
		call_user_func_array($action, $params);
		echo "q";
	}
}

HooksManager::addHookAction(Workflow::HOOK_OUTPUT_FLUSH_START, "f", array("aaaa"));
HooksManager::addHookAction(Workflow::HOOK_OUTPUT_FLUSH_START, array(new cls(), "objMethod"), array("bbb"));
HooksManager::addHookAction(Workflow::HOOK_OUTPUT_FLUSH_START, array("cls", "classMethod"), array("ccc"));
HooksManager::addHookAction(Workflow::HOOK_OUTPUT_FLUSH_START, function($p){
	echo $p . " wwww";
}, array("uuiuiui"));

HooksManager::registerNewHook("myhook", array("hookHandler", "hHandler"));

HooksManager::addHookAction("myhook", "f", array("aaaaupd"));
HooksManager::runHookActions("myhook");
?>