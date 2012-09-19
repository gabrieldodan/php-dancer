<?php
class PFC_Checkbox extends PFC_Base {

	/**
	 * Whether checkbox is selected(checked) or not
	 * @var boolean
	 */
	protected $isSelected	= FALSE; 
	
	
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

	function __construct($name, $isSelected = FALSE, $value	= NULL) {
		$this->name      	= $name;
		$this->isSelected	= $isSelected;
		$this->value     	= $value;
		parent::__construct();
	}
	
	/* chaining helper */
	static function newInst($name, $isSelected = FALSE, $value	= NULL) {
		return new PFC_Checkbox($name, $isSelected, $value);
	}
	
	function buildContent() {
		ob_start();
		?>
		<input 
			type="checkbox" 
			id="<?php echo $this->htmlId() ?>" 
			name="<?php echo $this->htmlName() ?>" 
			<?php echo ($this->value ? 'value="' . htmlspecialchars($this->value) . '"' : '') ?> 
			<?php echo ($this->isSelected ? 'checked="checked"' : '') ?> 
			<?php echo $this->buildAttrsAsString( $this->attrs, array('type', 'id', 'name', 'value', 'checked') ) ?> 
		/>
		<?php
		return ob_get_clean();
	}

}
?>