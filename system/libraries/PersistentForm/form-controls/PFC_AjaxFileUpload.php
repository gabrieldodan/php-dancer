<?php
class PFC_AjaxFileUpload extends PFC_Base {
	
	/**
	 * Called (on server) after file has been uploaded and validated.
	 * @var callback
	 */
	protected $onSuccessServer		= '';
	
	/**
	 * Called (on client) after file has been uploaded and validated. 
	 * @var string JS function name
	 */
	protected $onSuccessClient		= '';
	
	/**
	 * Called on errors 
	 * @var string JS function name
	 */
	protected $onErrorClient 		= '';
	
	/**
	 * JS function called to produce any extra data you want to send along with file
	 * @var string JS function name
	 */
	protected $extraDataProducer	= '';
	
	/**
	 * JS function called before upload start
	 * @var string JS function name
	 */
	protected $beforeUpload			= '';
	
	/**
	 * Whether or not uploading process should start automatically after user choose a file 
	 * @var bool
	 */
	protected $autoUpload 			= TRUE;
	
	
	
	/**
	 * @return the $beforeUpload
	 */
	public function getBeforeUpload() {
		return $this->beforeUpload;
	}

	/**
	 * @param string $beforeUpload
	 */
	public function setBeforeUpload($beforeUpload) {
		$this->beforeUpload = $beforeUpload;
		return $this;
	}

	/**
	 * @return the $extraDataProducer
	 */
	public function getExtraDataProducer() {
		return $this->extraDataProducer;
	}

	/**
	 * @param string $extraDataProducer
	 */
	public function setExtraDataProducer($extraDataProducer) {
		$this->extraDataProducer = $extraDataProducer;
		return $this;
	}

	/**
	 * @return the $onSuccessServer
	 */
	public function getOnSuccessServer() {
		return $this->onSuccessServer;
	}

	/**
	 * @param callback $onSuccessServer
	 */
	public function setOnSuccessServer($onSuccessServer) {
		$this->onSuccessServer = $onSuccessServer;
		return $this;
	}

	/**
	 * @return the $onSuccessClient
	 */
	public function getOnSuccessClient() {
		return $this->onSuccessClient;
	}

	/**
	 * @param string $onSuccessClient
	 */
	public function setOnSuccessClient($onSuccessClient) {
		$this->onSuccessClient = $onSuccessClient;
		return $this;
	}

	/**
	 * @return the $onErrorClient
	 */
	public function getOnErrorClient() {
		return $this->onErrorClient;
	}

	/**
	 * @param string $onErrorClient
	 */
	public function setOnErrorClient($onErrorClient) {
		$this->onErrorClient = $onErrorClient;
		return $this;
	}

	/**
	 * @return the $autoUpload
	 */
	public function getAutoUpload() {
		return $this->autoUpload;
	}

	/**
	 * @param boolean $autoUpload
	 */
	public function setAutoUpload($autoUpload) {
		$this->autoUpload = $autoUpload;
		return $this;
	}

	function __construct( $name, $onSuccessServer) {
		$this->name       		= $name;
		$this->onSuccessServer	= $onSuccessServer;
		parent::__construct();
	}
	
	/* chaining helper */
	static function newInst($name, $onSuccessServer) {
		return new PFC_AjaxFileUpload($name, $onSuccessServer);
	}
	
	protected function propertiesForSerialization() {
		// serialize value property
		return array("value", "onSuccessServer", "isValid");
	}

	function buildContent() {
		View::jsBatchAdd( array(Locator::urlThisLib() . "/js/PFC_AjaxFileUpload.js") );
		$idFormForUpload 	= $this->htmlId() . "-upload-form";
		$idIframeForUpload 	= $this->htmlId() . "-upload-iframe";
		
		$hiddenUploadForm  = '<form style="height:0px; width:0px;" target="' . $idIframeForUpload . '"'; 
		$hiddenUploadForm .= 'id="' . $idFormForUpload . '" method="post" enctype="multipart/form-data"'; 
		$hiddenUploadForm .= 'action="' . PersistentForm::getBackendUrl() . '/?form-name=' . $this->form->getName() . '&service=ajax-file-upload&control-name=' . $this->name . '"></form>';
		
		$hiddenUploadIframe  = '<iframe src="' . PersistentForm::getBackendUrl() . '/blankpage" style="height:0px; width:0px;display:none;" id="' . $idIframeForUpload . '" name="' . $idIframeForUpload . '"></iframe>'; 
		
		$hiddenFormAndIframe = $hiddenUploadForm . $hiddenUploadIframe;
		ob_start();
		?>
		<input 
			type="file" 
			id="<?php echo $this->htmlId() ?>" 
			name="<?php echo $this->htmlName() ?>" 
			<?php echo $this->buildAttrsAsString( $this->attrs, array('type', 'id', 'name', 'onchange') ) ?>
			<?php echo ($this->autoUpload ? " onchange=\"pd.pform.getControl('{$this->getForm()->getName()}', '{$this->name}').startUpload()\"" : '') ?>
		/>
		<div id="<?php echo $this->htmlId() ?>-msgs-box" style="font-size:12px;">&nbsp;</div>
		<script>
		var control = new pd.pform.PFC_AjaxFileUpload(
				pd.pform.get("<?php echo $this->form->getName() ?>"),
				'<?php echo $this->name ?>', 
				'<?php echo $this->htmlName() ?>',
				'<?php echo $this->htmlId() ?>',
				'<?php echo $idFormForUpload ?>',
				'<?php echo $idIframeForUpload ?>',
				'<?php echo $this->onSuccessClient ?>', 
				'<?php echo $this->onErrorClient ?>', 
				'<?php echo $this->extraDataProducer ?>',
				'<?php echo $this->beforeUpload ?>',
				<?php echo ($this->autoUpload ? 'true' : 'false') ?>
		);
		pd.pform.get("<?php echo $this->form->getName() ?>").addControl('<?php echo $this->name ?>', control);
		$(function(){
			var idForm 			= "<?php echo $this->form->getName() ?>";
			var idControl		= "<?php echo $this->htmlId() ?>";
			var idFormForUpload = "<?php echo $idFormForUpload ?>";
			var idMsgsBox		= "<?php echo $this->htmlId() ?>-msgs-box";
			var controlParent   = $("#" + idControl).parent(); 
			var controlClone    = $("#" + idControl).clone();
				
			controlClone.attr("id", "<?php echo $this->htmlId() ?>-clone");
			controlClone.attr("name", "<?php echo $this->htmlId() ?>-clone");
			controlClone.attr("onchange", "");
			controlClone.css("z-index", "0");
			controlClone.css("visibility", "hidden");
			controlClone.attr("disabled", "disabled");
			
			var controlOffset   = $("#" + idControl).offset();	
			
			// add hidden form and iframe 
			$("#" + idForm).parent().append('<?php echo $hiddenFormAndIframe ?>');

			// add control into form for upload
			$("#" + idFormForUpload).append( $("#" + idControl) );

			// hook to initial place
			$("#" + idMsgsBox).before(controlClone);
			$("#" + idControl).offset({top:controlOffset.top, left:controlOffset.left});
		});
		</script>
		<?php
		return ob_get_clean();
	}

	function setValueOnSubmit() {
		$value = array();
		$value['file']		= @Request::file( $this->form->getName(), $this->name );
		$value['extraData']	= @$_POST;
		$this->value 		= $value;
	}
	
	
}
?>