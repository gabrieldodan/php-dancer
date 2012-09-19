<?php
/**
 * Persisten form control item base class
 * @author Gabriel
 *
 */
class PFCI_Base {
	
	/**
	 * The control this item belong to
	 * @var PFC_Base
	 */
	protected $control		= NULL;
	
	/**
	 * Item attributes
	 * @var array
	 */
	protected $attrs		= array();
	
	
	/**
	 * Whether this item is slected or not
	 * @var boolean
	 */
	protected $isSelected	= FALSE;
	
	/**
	 * Item value
	 * @var string
	 */
	protected $value		= NULL;
	
	/**
	 * Item label
	 * @var string
	 */
	protected $label		= '';
	
	
	/**
	 * @return the $value
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value) {
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
	 * @return the $isSelected
	 */
	public function getIsSelected() {
		return $this->isSelected;
	}

	/**
	 * @param boolean $isSelected
	 */
	public function setIsSelected($isSelected) {
		$this->isSelected = $isSelected;
		return $this;
	}

	/**
	 * @return the $control
	 */
	public function getControl() {
		return $this->control;
	}

	/**
	 * @param PFC_Base $control
	 */
	public function setControl($control) {
		$this->control = $control;
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
	 * @return PFCI_Base
	 */
	function setAttr($attr, $val) {
		$this->attrs[$attr] = $val;
		return $this;
	}
	/**
	 * Get all item attributes
	 * @return array
	 */
	function getAttrs() {
		return $this->attrs;
	}
	
	/**
	 * 
	 * @param @return PFCI_Base
	 */
	function setAttrs($attrs) {
		$this->attrs = $attrs;
		return $this;
	}
	
	protected function buildAttrsAsString($attrs, $excludeAttrs) {
		$result = array();
		foreach ( $attrs AS $k => $v ) {
			if ( !in_array($k, $excludeAttrs) ) {
				$result[] = "{$k}=\"{$v}\""; 
			}
		}
		return implode(" ", $result);
	}
	
}
?>