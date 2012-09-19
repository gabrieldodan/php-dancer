<?php
class defaultController extends Controller {
	
	function beforeAction() {
		//echo "before";
	}
	
	function afterAction() {
		//echo "after";
	}
	
	function defaultAction() {
		
		//echo "bbbb";
		
		$inlineContent = "inline content";
		
		View::renderLayout("default", "default");
		
		//View::setTitle("aaaa");
	}
	
	function page1Action() {
		//$this->layout("inner");
		//$this->mainContent("page1");
		
		
		
		//View::layout();
		//View::mainContent();
		
		//View::layoutFile($fileName);
		//View::mainContentFile($fileName);
		
	}
}
?>
