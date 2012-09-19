<?php
/**
 * 
 * Base class for all Form Controls 
 * @author Gabriel Dodan
 *
 */
abstract class PFC_Base {
	
	/**
	 * Control value. Could be a single value in case of text input, text area, single select etc. OR multiple values (array) in case of multiple select, tree, dual list box etc.  
	 * @var mixed
	 */
	protected $value				= NULL;
	
	/**
	 * Control label
	 * @var string
	 */
	protected $label				= '';
	

	/**
	 * Control attributes, 'key' => 'val' array.  
	 * @var array
	 */
	protected $attrs     			= array();
	
	/**
	 * The form this control belong to.
	 * @var PersistentForm
	 */
	protected $form					= NULL;
	
	/**
	 * Control name
	 * @var string
	 */
	protected $name					= '';

	/**
	 * Control validators, an array of type PF_Validator
	 * @var array
	 */
	protected $validators			= array();
	
	/**
	 * Error message when invalid 
	 * @var string
	 */
	protected $errorMsg				= '';
	
	/**
	 * Whether the control is valid or not.
	 * @var boolean
	 */
	protected $isValid				= TRUE;
	
	/**
	 * HTML that is added before control aoutput
	 * @var string
	 */
	protected $beforeControlHtml	= '';
	
	/**
	 * HTML that is added after control aoutput
	 * @var string
	 */
	protected $afterControlHtml		= '';	
	

	function htmlId() {
		if ( $this->getForm() && $this->getName() ) {
			return $this->getForm()->getName() . "-" . $this->getName();
		}
		return NULL;
	}
	
	function htmlName() {
		if ( $this->getForm() && $this->getName() ) {
			return $this->getForm()->getName() . "[" . $this->getName() . "]";
		}
		return NULL;
	}
	
	
	
	/**
	 * GEt control value
	 * @return mixed
	 */
	function getValue() {
		return $this->value;	
	}
	/**
	 * Set control value
	 * @param mixed $value
	 * @return PFC_Base
	 */
	function setValue($value) {
		$this->value = $value;
		return $this;
	}
	
	/**
	 * @return the $label
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}
	
	
	/**
	 * Get a control attribute
	 * @param mixed $attr
	 */
	function getAttr($attr) {
		return @$this->attrs[$attr];
	}
	/**
	 * Set a control attribute 
	 * @param string $attr
	 * @param mixed $val
	 * @return PFC_Base
	 */
	function setAttr($attr, $val) {
		$this->attrs[$attr] = $val;
		return $this;
	}
	/**
	 * Get all control attributes
	 * @return array
	 */
	function getAttrs() {
		return $this->attrs;
	}
	
	function setAttrs($attrs) {
		$this->attrs = $attrs;
		return $this;
	}
	
	
	/**
	 * Get the form this control belong to.
	 * @return PersistentForm
	 */
	function getForm(){
		return $this->form;
	}
	/**
	 * Set the form this control belong to.
	 * @param PersistentForm $form
	 */
	function setForm(PersistentForm $form){
		$this->form = $form;
		return $this;
	}
	
	/**
	 * Get contol name
	 * @return string
	 */
	function getName() {
		return $this->name;
	}
	/**
	 * Set control name
	 * @param string $name
	 * @return PFC_Base
	 */
	function setName($name) {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * Get control validators
	 * @return array
	 */	
	function getValidators() {
		return $this->validators;
	}
	/**
	 * Set control validators
	 * @param array $validators
	 * @return PFC_Base
	 */
	function setValidators(array $validators) {
		$this->validators = $validators;
		return $this;
	}
	
	/**
	 * Get error message 
	 * @return string
	 */
	function getErrorMsg(){
		return $this->errorMsg;
	}
	
	
	/**
	 * Get is valid    
	 * @return string
	 */
	function isValid(){
		return $this->isValid;
	}
	
	
	/**
	 * @return the $beforeControlHtml
	 */
	public function getBeforeControlHtml() {
		return $this->beforeControlHtml;
	}

	/**
	 * @param string $beforeControlHtml
	 */
	public function setBeforeControlHtml($beforeControlHtml) {
		$this->beforeControlHtml = $beforeControlHtml;
		return $this;
	}

	/**
	 * @return the $afterControlHtml
	 */
	public function getAfterControlHtml() {
		return $this->afterControlHtml;
	}

	/**
	 * @param string $afterControlHtml
	 */
	public function setAfterControlHtml($afterControlHtml) {
		$this->afterControlHtml = $afterControlHtml;
		return $this;
	}
	
	function __construct() {
		if ( !PersistentForm::validName($this->name) ) {
			Sys::errorHandler("PFC_Base: Control name '" . $this->name . "' not valid, control name must contain only letter , digit , _  or - char. Must begin with a letter or _ char");
			exit;
		}
	}
	
	/**
	 * Build the control content
	 */
	abstract public function buildContent();
		
	/**
	 * Renders the control
	 * @param boolean $returnContent
	 */
	function render($returnContent=FALSE) {
		$output = $this->getBeforeControlHtml() . $this->buildContent() . $this->getAfterControlHtml();
		if ( $returnContent ) {
			return $output;
		}
		echo $output;
	}
	
	/**
	 * Tell which properties should be serialized beside default properties: $form, $name and $validators. 
	 * This method must return an indexed array of properties names.
	 */
	protected function propertiesForSerialization() {
		return array();
	}

	
	
	function setValueOnSubmit() {
		$frmData = @Request::post( $this->getForm()->getName() );
		$this->setValue( @$frmData[$this->getName()] );
	}
	
	/*
	function getSubmittedValue() {
		return Request::post( $this->getName() );
	}
	*/
	
	/**
	 * Invalidate control
	 * @param string $errorMsg
	 */
	function invalidate($errorMsg) {
		$this->isValid  = FALSE;
		$this->errorMsg = $errorMsg;
	}
	

	
	
	/**
	 * Control validation
	 */
	function validate() {
		$validators = $this->getValidators();
		$validator  = NULL; /* @var $validator PF_Validator */
		
		foreach ($validators AS $validator) {
			if ( $validator->getType() == PF_Validator::REGEXP ) {
				$this->isValid  = (boolean)preg_match( $validator->getData(), $this->getValue() );
				if ( !$this->isValid ) {
					$this->invalidate( $validator->getErrorMsg() );
				}
			}
			else if ( $validator->getType() == PF_Validator::METHOD ) {
				$method	= $validator->getData();
				return $this->form->getModel()->{$method}( $this ); 
			}
			else {
				// standard validators
				
			}
			
			if ( !$this->isValid ) {
				break;
			}
		}
	}
	
	protected function buildAttrsAsString($attrs, $excludeAttrs) {
		$result = array();
		foreach ( $attrs AS $k => $v ) {
			if ( !in_array($k, $excludeAttrs) ) {
				$result[] = "{$k}=\"{$v}\""; 
			}
		}
		return implode(" ", $result);
		//$attrs  = $this->getA
	}
	

	/**
	 * Tell which properties should be serialized. By default only $form, $name and $validators properties are serialized.
	 * Derived classes can override  the propertiesToSerialize() method to serialize adition properties
	 */
	function __sleep() {
		$arr = array_unique( array_merge( array('form', 'name', 'validators'), $this->propertiesForSerialization() ) );
		return $arr;
	}

}
?>