<?php
abstract class PersistentFormModel {
	/**
	 * 
	 * @var PersistentForm
	 */
	protected $form	= NULL;
	
	
	/**
	 * @return the $form
	 */
	public function getForm() {
		return $this->form;
	}

	/**
	 * @param PersistentForm $form
	 */
	public function setForm($form) {
		$this->form = $form;
		return $this;
	}
	
	function __construct() {
		
	}
	
	abstract function buildForm();
	
	protected function initFrontEnd() {
		
	}
	
	protected function initBackEnd() {
		
	}
	
	
	/*
	public function validator(PFC_Base $control) {
		if ( $validationOk ) {
			return TRUE;
		}
		else {
			$control->invalidate("The error message");
			return FALSE;
		}
	}
	
	public function onSuccessServer(PersistentForm $form) {
		$controls = $form->getControls();
		
		// process controlls data
		
		if ( $everythingIsOk ) {
			return "Data for JS onSuccessClient() function"; // e.g redirect URL
		}
		else {
			$this->form->invlidate("The error message");
			return FALSE;			
		}
	} 
	
	public function itemsRenderer(PFC_List $control) {
		// Sample for a PFC_Select control
		$items = $control->getItems(); 
		$item  = NULL; // @var $item PFCI_Option
		foreach ($items AS $item) {
			if ( $item->getIsSelected() ) {
				$item->setAttr("style", "color:red;");
			}
			$item->render();
		}
	} 
	
	public function ajaxFileUploadOnSuccessServer(PFC_AjaxFileUpload $control) {
		$value			= $control->getValue();
		
		$fileUploaded	= $value['file'];
		$extraData		= $value['extraData'];
		
		// move temp file $fileUploaded['tmp_name'] to a new location
		if (  $everythingIsOk  ) {
			// set tmp_name to the new location
			$value['file']['tmp_name'] = $newTempFile;
			return "Data for JS ajaxFileUploadOnSuccessClient() function"; //e.g. URL of the uploaded file if you want to display a preview of the uploaded file
		} 
		else {
			$control->invalidate("The error message");
			return FALSE;
		}
	}
	
	public function remoteCalledMethod($postData) {
		return "Any data";
	}
	*/
}
?>