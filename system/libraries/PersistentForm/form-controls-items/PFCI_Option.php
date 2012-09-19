<?php
class PFCI_Option extends PFCI_Base {

	function __construct($control, $value, $label, $isSelected=FALSE) {
		$this->control		= $control;
		$this->value		= $value;
		$this->label		= $label;
		$this->isSelected	= $isSelected;
	}
	
	function render($returnContent = FALSE) {
		ob_start();
		?>
		<option <?php echo ($this->isSelected ? 'selected="selected"' : '') ?> value="<?php echo htmlspecialchars($this->value) ?>" <?php echo $this->buildAttrsAsString( $this->attrs, array('value', 'selected') ) ?>><?php echo htmlspecialchars($this->label) ?></option>
		<?php
		$content = ob_get_clean();
		if ( $returnContent ) {
			return $content;
		}
		echo $content;
	}
	
}
?>