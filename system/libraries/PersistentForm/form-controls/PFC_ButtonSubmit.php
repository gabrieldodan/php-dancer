<?php
class PFC_ButtonSubmit extends PFC_Base {

	function __construct($name, $value) {
		$this->name		= $name;
		$this->value	= $value;
		parent::__construct();
	}
	
	/* chaining helper */
	static function newInst($name, $value) {
		return new PFC_ButtonSubmit($name, $value);
	}
	
	function buildContent() {
		ob_start();
		?>
		<input 
			type="submit" 
			name="<?php echo $this->htmlName() ?>" 
			id="<?php echo $this->htmlId() ?>" 
			value="<?php echo htmlspecialchars( $this->value ) ?>" 
			<?php echo $this->buildAttrsAsString( $this->attrs, array('type', 'id', 'name', 'value') ) ?> 
		/>
		<?php
		return ob_get_clean();
	}

}
?>
