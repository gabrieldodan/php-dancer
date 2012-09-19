<?php
class PFCI_Checkbox extends PFCI_Base {

	function __construct($control, $value, $label, $isSelected=FALSE) {
		$this->control		= $control;
		$this->value		= $value;
		$this->label		= $label;
		$this->isSelected	= $isSelected;
	}
	
	
	function render($returnContent = FALSE) {
		ob_start();
		?>
		<input 
			type="checkbox" 
			value="<?php echo htmlspecialchars($this->value) ?>" 
			name="<?php echo $this->control->htmlName() ?>[]"
			<?php echo ($this->isSelected ? 'checked="checked"' : '') ?> 
			<?php echo $this->buildAttrsAsString( $this->attrs, array('id', 'value', 'checked', 'name') ) ?>
		/>
		<?php
		$content = ob_get_clean();
		if ( $returnContent ) {
			return $content;
		}
		echo $content;
	}
	
}
?>