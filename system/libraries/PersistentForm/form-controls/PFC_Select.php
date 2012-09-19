<?php
class PFC_Select extends PFC_List {
	
	/**
	 * Whether select is multiple or not
	 * @var boolean
	 */
	protected $isMultiple	= FALSE;
	
	

	/**
	 * @return the $isMultiple
	 */
	public function getIsMultiple() {
		return $this->isMultiple;
	}
	
	/**
	 * @param boolean $isMultiple
	 */
	public function setIsMultiple($isMultiple) {
		$this->isMultiple = $isMultiple;
		return $this;
	}
	
	function __construct($name, $dataSet, $isMultiple = FALSE) {
		$this->name			= $name;
		$this->dataSet		= $dataSet;	
		$this->isMultiple	= $isMultiple;
		$this->value		= array();
		parent::__construct();
	}
	static function newInst($name, $dataSet, $isMultiple=FALSE) {
		return new PFC_Select($name, $dataSet, $isMultiple);
	}
		

	function buildContent() {
		// create items
		foreach ($this->dataSet AS $k => $v) {
			$this->items[] = new PFCI_Option($this, $k, $v, in_array($k, $this->value));
		}
		ob_start();
		?>
		<select
			<?php echo ($this->isMultiple ? 'multiple="multiple"' : '') ?> 
			id="<?php echo $this->htmlId() ?>" 
			name="<?php echo $this->htmlName() . ($this->isMultiple ? "[]" : "") ?>" 
			<?php echo $this->buildAttrsAsString( $this->attrs, array('id', 'name', 'multiple') ) ?>
		>
			<?php echo $this->renderItems() ?>	
		</select>
		<?php
		return ob_get_clean();
	}
	
	protected function renderItems() {
		$callback = $this->getItemsRenderer();
		if ( $callback ) {
			return $callback($this);
		}
		return $this->defaultItemsRenderer();
	}

	protected function defaultItemsRenderer() {
		$item = NULL; /* @var $item PFCI_Option */
		foreach ($this->items AS $item) {
			$item->render();
		}
	}
}
?>