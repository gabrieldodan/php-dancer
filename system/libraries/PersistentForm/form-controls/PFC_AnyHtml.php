<?php
class PFC_AnyHtml extends PFC_Base {
	
	static protected $count = 1;
	
	function __construct($html) {
		$this->name		= "anyhtml_" . PFC_AnyHtml::$count;
		$this->value 	= $html;
		parent::__construct();	
	}
	
	static function newInst($html) {
		return new PFC_AnyHtml($html);
	}
	
	final function validate() {
		return TRUE;
	}
	
	function buildContent() {
		return $this->value;
	}
}
?>