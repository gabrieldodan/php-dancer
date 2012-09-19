<?php
/**
 * Persistent Forms backend. AJAX requests handlers.
 */
class PersistentFormBackend_PagesGroup extends Controller {
	function getLayoutName() {
		return NULL;
	}
	
	function default_page() {
		$formName = Request::get("form-name");
		if ( $formName ) {
			$form = $this->unserializeForm($formName); /* @var $form PersistentForm */
			$form->handleAjaxRequest();
		}
	}
	
	function blankpage_page() {
		
	}
	
	
	
	private function unserializeForm($formName) {
		Sys::importLib("PersistentForm", true);
		$persistData = Sess::get("{$formName}-persistent-form");
		
		$filesToInclude = $persistData['filesToInclude'];
		foreach ($persistData['filesToInclude'] AS $file) {
			require_once $file;	
		}
		
		// unserialize the form 
		return unserialize($persistData['form']);
	}
	
}
?>