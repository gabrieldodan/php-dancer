<?php
class PFC_TextInput extends PFC_Base {

	function __construct($name, $value) {
		$this->name   = $name;
		$this->value  = $value;
		parent::__construct();
	}
	static function newInst($name, $value) {
		return new PFC_TextInput($name, $value);
	}
		
	function buildContent() {
		ob_start();
		?>
		<input 
			type="text" 
			id="<?php echo $this->htmlId() ?>" 
			name="<?php echo $this->htmlName() ?>" 
			value="<?php echo htmlspecialchars($this->value) ?>" 
			<?php echo $this->buildAttrsAsString( $this->attrs, array('type', 'id', 'name', 'value') ) ?> 
		/>
		<?php
		return ob_get_clean();
	}
}
?>