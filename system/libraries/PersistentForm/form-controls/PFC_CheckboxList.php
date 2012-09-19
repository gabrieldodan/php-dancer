<?php
class PFC_CheckboxList extends PFC_List {
	/**
	 * 
	 * @var string either horizontal or vertical
	 */
	protected $itemsLayout			= 'vertical';
	
	/**
	 * 
	 * @var string either left or right
	 */
	protected $itemsLabelPosition	= 'right';
	

	/**
	 * @return the $itemsLayout
	 */
	public function getItemsLayout() {
		return $this->itemsLayout;
	}

	/**
	 * @return the $itemsLabelPosition
	 */
	public function getItemsLabelPosition() {
		return $this->itemsLabelPosition;
	}

	/**
	 * @param string $itemsLayout
	 */
	public function setItemsLayout($itemsLayout) {
		$this->itemsLayout = $itemsLayout;
		return $this; 
	}

	/**
	 * @param string $itemsLabelPosition
	 */
	public function setItemsLabelPosition($itemsLabelPosition) {
		$this->itemsLabelPosition = $itemsLabelPosition;
		return $this;
	}

	function __construct($name, $dataSet, $value = array() ) {
		$this->name		= $name;
		$this->dataSet	= $dataSet;	
		$this->value	= $value;
		parent::__construct();
	}
	/* chaining helper */
	static function newInst($name, $dataSet, $value = array()) {
		return new PFC_CheckboxList($name, $dataSet, $value);
	}
	

	function buildContent() {
		// create items
		foreach ($this->dataSet AS $k => $v) {
			$this->items[] = new PFCI_Checkbox($this, $k, $v, in_array($k, $this->value));
		}
		
		echo $this->renderItems();
	}
	
	protected function renderItems() {
		$callback = $this->getItemsRenderer();
		if ( $callback ) {
			return $callback($this);
		}
		return $this->defaultItemsRenderer();
	}

	protected function defaultItemsRenderer() {
		$item = NULL; /* @var $item PFCI_Checkbox */
		if ( $this->itemsLayout == 'vertical' ) {
			?>
			<table>
			<?php	
			foreach ($this->items AS $item) {
				?>
				<tr>
					<td>
						<?php echo ($this->itemsLabelPosition == 'right' ? $item->render(true) : htmlspecialchars($item->getLabel()) ) ?>
					</td>
					<td>
						<?php echo ($this->itemsLabelPosition == 'right' ? htmlspecialchars($item->getLabel()) : $item->render(true) ) ?>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
			<?php
		}
		else if ( $this->itemsLayout == 'horizontal' ) {
			?>
			<table>
			<tr>
			<?php	
			foreach ($this->items AS $item) {
				?>
				<td>
					<?php 
					echo ($this->itemsLabelPosition == 'right' ? $item->render(true) : htmlspecialchars($item->getLabel()) );
					echo ($this->itemsLabelPosition == 'right' ? htmlspecialchars($item->getLabel()) : $item->render(true) ); 
					?>
				</td>
				<?php
			}
			?>
			</tr>
			</table>
			<?php
		}
	}
}
?>