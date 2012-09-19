<?php
class PersistentForm {
	
	/**
	 * 
	 * @var PersistentFormModel
	 */
	protected $model					= NULL;
	
	protected $formControls				= array();

	protected $formContent				= '';

	protected $name						= '';
	
	protected $onSuccessServer			= '';
	
	/**
	 * Client callback function that is called after the form is submitted and everything is valid. 
	 * You could add a javascript redirect or hide the form and display a success message.  
	 * @var string
	 */
	protected $onSuccessClient			= '';
	
	/**
	 * Whether the form is valid or not.
	 * @var boolean
	 */
	protected $isValid					= TRUE;

	/**
	 * Error message when invalid 
	 * @var string
	 */
	protected $errorMsg					= '';
	
	private $idIframe					= '';
	
	private $idPersistance				= '';
	
	protected $template					= '';
	
	protected $cssDir					= '';
	
	/**
	 * AJAX requests URL, used to submit data or upload files. It's the controller of the PersistentForm library.
	 * @var string
	 */
	static private $backendUrl			= '';
	
	
	static private $allFormNames		= array();
	
	
	
	const TPL_LABEL_LEFT				= 'tpl-label-left';
	
	const TPL_LABEL_TOP					= 'tpl-label-top';
	
	
	

	function getIdIframe() {
		return $this->idIframe;
	}
	
	public function getName() {
		return $this->name;
	}
	
	function getOnSuccessServer(){
		return $this->onSuccessServer;
	}
	function setOnSuccessServer($onSuccessServer){
		$this->onSuccessServer = $onSuccessServer;
		return $this;
	}
	
	
	function getOnSuccessClient(){
		return $this->onSuccessClient;
	}
	function setOnSuccessClient($onSuccessClient){
		$this->onSuccessClient = $onSuccessClient;
		return $this;
	}
	
	
	/**
	 * 
	 * @param string $controlName
	 * @return PFC_Base
	 */
	public function getControl($controlName) {
		return @$this->formControls[$controlName]; 
	}
	
	public function getControls() {
		return $this->formControls;
	}
	
	public function isValid() {
		return $this->isValid;
	}
	
	public function getErrorMsg() {
		return $this->errorMsg;
	}
	
	/**
	 * @return the $template
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @param string $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
		return $this;
	}
	
	function getCssDir() {
		return $this->cssDir;
	}
	function setCssDir($cssDir) {
		$this->cssDir = $cssDir;
		return $this;
	}
	
	/**
	 * @return the $model
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * @param PersistentFormModel $model
	 */
	public function setModel($model) {
		$this->model = $model;
		return $this;
	}
	
	
	static function getBackendUrl() {
		return self::$backendUrl;
	}
	
	static function validName($name) {
		return preg_match('/^[a-z_][a-z0-9_\-]+$/i', $name);
	}

	function __construct($name, $model) {
		
		if ( !PersistentForm::validName($name) ) {
			Sys::errorHandler("PersistentForm: Form name '" . $this->name . "' not valid, form name must contain only letter , digit , _  or - char. Must begin with a letter or _ char");
			exit;
		}
		// check if the form name already exists
		if ( in_array($name, self::$allFormNames) ) {
			Sys::errorHandler("The form name '" . $name . "' already exists, please choose another name for the form");
			exit;
		}
		else {
			$this->name       = $name;
			self::$allFormNames[] = $name;
		}
		
		$this->model			= $model;
		$this->model->setForm($this);
		
		$this->idIframe			= "{$this->name}-iframe";
		$this->idPersistance    = "{$this->name}-persistent-form";
		self::$backendUrl 		= Locator::thisLibUrlToPage("PersistentFormBackend");
	}
	

	
	static function formControlsAutoloader($className) {
		if ( substr($className, 0, 4) === "PFC_" ) {
			// persistent form control
			require_once Sys::bs2s(dirname(__FILE__)) . "/form-controls/{$className}.php";
		}
		else if ( substr($className, 0, 5) === "PFCI_" ) {
			// persistent form control item
			require_once Sys::bs2s(dirname(__FILE__)) . "/form-controls-items/{$className}.php";
		}
		else if ( $className == "PF_Validator" ) {
			require_once Sys::bs2s(dirname(__FILE__)) . "/{$className}.php";
		}
		else if ( $className == "PersistentFormModel" ) {
			require_once Sys::bs2s(dirname(__FILE__)) . "/{$className}.php";
		}
		
	}

	public function beginForm() {
		ob_start(); ob_implicit_flush(0);
		?>
		<form 
			method="post" 
			id="<?php echo $this->name ?>" 
			name="<?php echo $this->name ?>" 
			enctype="multipart/form-data"  
			target="<?php echo $this->idIframe ?>"
			action=""
			class="PersistentForm"
		>
		<?php
		
		// add jQuery.js phpdancer.js, PersistentForm.js and PersistentForm.css if not already added
		if ( count(self::$allFormNames) == 1 ) {
			View::jsBatchAddJquery();
			View::jsBatchAdd( array(Locator::urlResDir('sys') . "/js/phpdancer.js"));
			View::jsBatchAdd( array(Locator::urlThisLib() . "/js/PersistentForm.js"));
			
			View::cssBatchAdd( array($this->cssDir . "/PersistentForm.css") );
		}
	}
	
	public function endForm() {
		?>
		</form>
		<iframe 
			src="<?php echo self::$backendUrl ?>/blankpage" 
			id="<?php echo $this->idIframe ?>" 
			name="<?php echo $this->idIframe ?>"
			style="display:none;"
		></iframe>
		<?php
		$this->formContent = ob_get_clean();
	}




	public function invlidate($errorMsg) {
		$this->isValid  = FALSE;
		$this->errorMsg = $errorMsg;
	}
	
	
	/**
	 * 
	 * @param PFC_Base $formControl
	 * @return PFC_Base
	 */
	public function addControl(PFC_Base $formControl) {
		$formControl->setForm($this);

		$this->formControls[$formControl->getName()] = $formControl;
		return $formControl;
	}
	
	/*
	public function renderControl(PFC_Base $formControl) {
		
		$this->addControl($formControl);

		$formControl->render();
	}
	*/


	/**
	 * Renders the form
	 */
	public function render($template=NULL, $cssDir=NULL, $returnContent=FALSE) {
		$this->template = ($template ? $template : Locator::pathThisLib() . "/templates/" . PersistentForm::TPL_LABEL_TOP . ".php");
		$this->cssDir	= ($cssDir ? $cssDir : Locator::urlThisLib() . "/css");
		
		$this->model->buildForm();
		
		$this->beginForm();
		$form = $this;
		require_once $this->template;
		$this->endForm();
		
		// match form parts
		$matches = preg_match('!(.*?<form [^>]+>)(.*)(</form>.*)!is', $this->formContent, $mForm);
		if ( $matches > 0 ) {
			$formBegin         = $mForm[1];	
			$formInnerHtml     = $mForm[2];
			$formEnd           = $mForm[3];
		}
		else {
			Sys::errorHandler("No match for <form> tags. Please add <form> tags");
		}
		
		
		// create JavaScript PersistentForm object
		ob_start(); ob_implicit_flush(0);
		?>
		<script type="text/javascript">
			pd.pform.create('<?php echo $this->name ?>', '<?php echo self::$backendUrl ?>', <?php echo ($this->onSuccessClient ? "'" . $this->onSuccessClient . "'" : 'null') ?>);
		</script>
		<?php
		$formInnerHtml = ob_get_clean() . $formInnerHtml;
		
		// add hidden field
		ob_start(); ob_implicit_flush(0);
		?>
		<input type="hidden" name="form-name" value="<?php echo $this->name ?>" />
		<?php
		$formInnerHtml .= ob_get_clean();
		
		// add JS submit handler
		ob_start(); ob_implicit_flush(0);
		?>
		<script type="text/javascript">
			// add submit handler
			$('#<?php echo $this->name ?>').submit(function() {
				$('#<?php echo $this->name ?>').attr("action", pd.pform.get("<?php echo $this->name ?>").formAction());	
				return true;
			});			
		</script>
		<?php
		$formEnd .= ob_get_clean();
		
		// check for file controls and add hidden form for upload

		// serialize the form
		$this->serialize();
		
		$this->formContent = $formBegin . $formInnerHtml .  $formEnd;
		
		if ( $returnContent ) {
			return $this->formContent;
		}
		echo $this->formContent;
		?>
		<script>
		// trigger form rendered
		$('#<?php echo $this->name ?>').trigger('formRendered');
		</script>
		<?php
		return NULL;
	}
	
	function serialize() {
		$filesToInclude = array();
		
		$reflector = new ReflectionClass($this->model);
		$filesToInclude[] = Sys::bs2s( $reflector->getFileName() );
		
		$persistData = array(
			'filesToInclude'	=> $filesToInclude, 	
			'form'				=> serialize($this)			
		);
		Sess::set($this->idPersistance, $persistData);
	}

	
	function handleAjaxRequest() {
		$this->checkAbort();
		$service	= Request::get("service");
			
		if ( $service == 'handle-submit' ) {
			$controls        = $this->getControls();
			$control         = NULL; /* @var $control PFC_Base */
			$allControlsValid = TRUE;	
			foreach ($controls AS $control) {
				/*
				if ( is_a($control, "PFC_AjaxFileUpload") ) {
					continue;
				}
				*/
				$control->setValueOnSubmit();
				$control->validate();
				if ( !$control->isValid() ) {
					$allControlsValid = FALSE;
				}
			}
				
			if ( $allControlsValid ) {
				// process data
				$onSuccessMethod = $this->getOnSuccessServer();
				if ( !$onSuccessMethod ) {
					$this->responseOnSuccess();
					return;
				}
					
				$ret = $this->model->{$onSuccessMethod}($this);
				if ( $ret !== FALSE ) {
					$this->responseOnSuccess($ret);
				}
				else {
					$this->responseOnError();
				}
			}
			else {
				$this->responseOnError();
			}
		}
		else if ( $service == 'call-method' ) {
			$methodName = Request::get("method");
			ob_clean(); ob_start();
			
			echo $this->model->{$methodName}(@$_POST);
				
			echo ob_get_clean();
		}
		else if ($service == 'ajax-file-upload') {
			$ajaxFileUploadControl = $this->getControl($_GET['control-name']); /* @var $ajaxFileUploadControl PFC_AjaxFileUpload */ 
			
			$ajaxFileUploadControl->setValueOnSubmit();
			$ret = $ajaxFileUploadControl->validate();
			
			if ( !$ajaxFileUploadControl->isValid() ) {
				$this->ajaxFileUploadResponseOnError($ajaxFileUploadControl);
				return;
			}
			
			$onSuccessMethod 	= $ajaxFileUploadControl->getOnSuccessServer();
			$ret 				= $this->model->{$onSuccessMethod}($ajaxFileUploadControl);
			if ( $ret !== FALSE ) {
				$this->ajaxFileUploadResponseOnSuccess($ajaxFileUploadControl, ($ret === NULL ? "" : $ret));
				$this->serialize();
			}
			else {
				$this->ajaxFileUploadResponseOnError($ajaxFileUploadControl);
				return;
			}
		}
	}
	
	protected function checkAbort() {
		ob_start();
		echo " ";
		ob_end_flush();
		if ( connection_aborted() ) {
			exit;
		}
	}
	
	protected function ajaxFileUploadResponseOnSuccess(PFC_AjaxFileUpload $control, $data) {
		$return['status']	= 'success';
		$return['data'] 	= $data;
		$json = addslashes( Sys::jsonEncode($return) );
		?>
		<script>
		parent.pd.pform.get('<?php echo $this->name ?>').getControl('<?php echo $control->getName() ?>').fileSubmitResult("<?php echo $json ?>");
		</script>
		<?php
	}
	
	protected function ajaxFileUploadResponseOnError(PFC_AjaxFileUpload $control) {
		$return["status"]	= "error";
		$return["data"]		= $control->getErrorMsg();
		$json = addslashes( Sys::jsonEncode($return) );
		?>
		<script>
		parent.pd.pform.get('<?php echo $this->name ?>').getControl('<?php echo $control->getName() ?>').fileSubmitResult("<?php echo $json ?>");
		</script>
		<?php
	}
	
	protected function responseOnSuccess($data='') {
		$return['status']	= 'success';
		$return['data'] 	= $data;
		$json = addslashes( Sys::jsonEncode($return) );
		?>
		<script>
		parent.pd.pform.get('<?php echo $this->name ?>').formSubmitResult("<?php echo $json ?>");
		</script>
		<?php
	}
			
	protected function responseOnError() {
		$controls	= $this->getControls();
		$control	= NULL; /* @var $control PFC_Base */
		
		$return["status"]         = "error";
		$return["controlsErrors"] = array();
		foreach ($controls AS $control) {
			if ( !$control->isValid() ) {
				$return["controlsErrors"][] = array( 'name' => $control->getName(), 'error' => $control->getErrorMsg() );
			}
		}
		
		if ( !$this->isValid() ) {
			$return["formError"] = $this->getErrorMsg();
		}
		
		$json = addslashes( Sys::jsonEncode($return) );
		?>
		<script>
		parent.pd.pform.get('<?php echo $this->name ?>').formSubmitResult("<?php echo $json ?>");
		</script>
		<?php
	}
	
	
	/**
	 * Tell which properties should be serialized.
	 * Derived classes can override this magic method to serialize more properties.
	 */
	function __sleep() {
		return array('name', 'formControls', 'onSuccessServer', 'onSuccessClient', 'model');
	}


}
spl_autoload_register( array("PersistentForm", "formControlsAutoloader") );
?>