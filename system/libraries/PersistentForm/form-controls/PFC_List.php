<?php
abstract class PFC_List extends PFC_Base {
	/**
	 * Control dataset
	 * @var array
	 */
	protected $dataSet 			= array();
	
	/**
	 * Control items 
	 * @var array
	 */
	protected $items			= array();

	/**
	 * 
	 * @var callback
	 */
	protected $itemsRenderer	= '';
	
	/**
	 * @return the $dataSet
	 */
	public function getDataSet() {
		return $this->dataSet;
	}

	/**
	 * @param mixed $dataSet
	 */
	public function setDataSet($dataSet) {
		$this->dataSet = $dataSet;
		return $this;
	}

	/**
	 * @return the $items
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @param mixed $items
	 */
	public function setItems($items) {
		$this->items = $items;
		return $this;
	}
	
	/**
	 * @return the $itemsRenderer
	 */
	public function getItemsRenderer() {
		return $this->itemsRenderer;
	}

	/**
	 * @param callback $itemsRenderer
	 */
	public function setItemsRenderer($itemsRenderer) {
		$this->itemsRenderer = $itemsRenderer;
		return $this;
	}

	function buildContent() {
		
	}

}
?>