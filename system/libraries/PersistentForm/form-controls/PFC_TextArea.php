<?php
class PFC_TextArea extends PFC_Base {

	function __construct($name, $value) {
		$this->name   = $name;
		$this->value  = $value;
		parent::__construct();
	}
	static function newInst($name, $value) {
		return new PFC_TextArea($name, $value);
	}
		
	function buildContent() {
		ob_start();
		?>
		<textarea id="<?php echo $this->htmlId() ?>" name="<?php echo $this->htmlName() ?>" <?php echo $this->buildAttrsAsString( $this->attrs, array('id', 'name') ) ?>><?php echo htmlspecialchars($this->value) ?></textarea>
		<?php
		return ob_get_clean();
	}
}
?>