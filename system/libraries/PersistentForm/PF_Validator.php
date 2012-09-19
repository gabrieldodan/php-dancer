<?php
/**
 * Form Validator
 * @author Gabriel Dodan
 *
 */
class PF_Validator {
	
	const REQUIRED = "required";
	const NUMERIC  = "numeric";
	const RANGE    = "range";
	
	const REGEXP   = "regexp";
	const METHOD   = "method";
	
	/**
	 * 
	 * Validator type. Could be a standard validator: required, numeric, range etc., regexp or func 
	 * @var string
	 */
	protected $type     = NULL;

	/**
	 * Data for validator, for example for a range validator you can pass the start and end values. For a regexp validator data is the regexp, for a function data is the function name. 
	 * @var mixed
	 */
	protected $data     = NULL; 
	
	/**
	 * Error message when invalid 
	 * @var string
	 */
	protected $errorMsg = NULL; 
	
	function getType() {
		return $this->type;
	}
	function getData() {
		return $this->data;
	}
	function getErrorMsg(){
		return $this->errorMsg;
	}
	
	function __construct($type, $data, $errorMsg='') {
		$this->type     = $type;
		$this->data     = $data;
		$this->errorMsg = $errorMsg;
	}
	
	function validate(PFC_Base $control) {
		if ( $this->type == self::REGEXP ) {
			return preg_match( $this->data, $control->getSubmittedValue() );
		}
		else if ( $this->type == self::METHOD ) {
			$func = $this->data;
			$ret  = $func($control);
			if ( $ret === TRUE ) {
				return TRUE;
			}
			else {
				// funtion must return an error message on not valid.
				$this->errorMsg = $ret;
				return FALSE;
			}
		}
		else {
			// standard validators
			
		}
		
	}
}