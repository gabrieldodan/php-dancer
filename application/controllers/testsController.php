<?php
class testsController extends Controller {
	
	function formsAction() {
		//View::layoutFile("default");
		//View::mainContentFile("forms");
		View::renderLayout("default", "forms");
	}
}
?>
