<?php
class PFC_FileUpload extends PFC_Base {

	function __construct( $name ) {
		$this->name	= $name;
		parent::__construct();
	}
	/* chaining helper */
	static function newInst($name) {
		return new PFC_FileUpload($name);
	}
	
	function buildContent() {
		ob_start();
		?>
		<input 
			type="file" 
			id="<?php echo $this->htmlId() ?>" 
			name="<?php echo $this->htmlName() ?>" 
			<?php echo $this->buildAttrsAsString( $this->attrs, array('type', 'id', 'name') ) ?> 
		/>
		<?php
		return ob_get_clean();
	}

	function setValueOnSubmit() {
		$this->value = @Request::file( $this->form->getName(), $this->name );	
	}
}
?>