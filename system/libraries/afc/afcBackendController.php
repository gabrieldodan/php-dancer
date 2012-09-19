<?php
class afcBackendController extends Controller {
	
	function defaultAction() {
		$funcName = @$_REQUEST['funcName']; 
		if ( $funcName ) {
			Sys::importLib("afc", true, array('fromBackend'	=>	true));
			
			ob_clean(); 
			ob_start();
			$funcCode = afc::getFunction($funcName);
			eval($funcCode);
			echo $funcName(@$_POST);
			echo ob_get_clean();
		}
	}
}
?>