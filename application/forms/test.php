<?php
Sys::importLib("PersistentForm", true);
class LoginForm extends PersistentFormModel {
	
	function buildForm() {
		$this->form->addControl(
			PFC_TextInput::newInst("email", "gabriel.dodan@gmail.com")
			->setValidators( 
					array(
						new PF_Validator(PF_Validator::REGEXP, "/.*dodan.*/", "Must contain 'dodan'"),
						new PF_Validator(PF_Validator::METHOD, "validator_checkEmail")	 
					)
			)
			->setAttr("style", "border-color:red;")
			->setAttr("class", "classbeton")
			->setAfterControlHtml("qwweq")
			->setLabel("Email")
		);
		
		$this->form->addControl(
			PFC_TextArea::newInst("detalii", "dads ada asd ")
			->setAttr("cols", "50")
			->setAttr("rows", "10")
			->setLabel("Detalii")
		);		
		
		$this->form->addControl(
			PFC_AjaxFileUpload::newInst("avatar1", "avatar1_onSucessServer")
			->setOnSuccessClient("avatar1_onSucessClient")
			->setOnErrorClient("avatar1_onErrorClient")
			->setExtraDataProducer("avatar1_extraDataProducer")
			->setLabel("Avatar 1")	
		);
		
		$this->form->addControl(
			PFC_AjaxFileUpload::newInst("avatar2", "avatar2_onSucessServer")
			->setLabel("Avatar 2")	
		);
		
		ob_start();
		?>
		<script>
		function avatar1_onSucessClient(data) {
			//console.log(data);
			//this.preventDefault();
		}
		function avatar1_extraDataProducer() {
			return {name:"file name", type:"file type"};
		}
		function avatar1_onErrorClient(data) {
			//console.log(data);
			//this.preventDefault();
		}
		function avatar1_beforeUpload() {
			alert("before upload");
		}
		</script>
		<?php
		$html = ob_get_clean();
		
		$this->form->addControl(
			PFC_AnyHtml::newInst($html)	
		);
		
		$this->form->addControl(
			PFC_ButtonSubmit::newInst("submit", "Submit")	
		);
		
		new PFC_Group("aa", "bb");
		
		$this->form->setOnSuccessServer("onSuccessServer");
	}
	
	function validator_checkEmail(PFC_Base $control) {
		
		if ( strlen($control->getValue()) < 8 ) {
			$control->invalidate("Must be 8 chars length");
			return FALSE;
		}
		return TRUE;
	}
	
	function onSuccessServer() {
		/*
		$this->invlidate("qweqwe");
		return FALSE;
		*/
		
		return "ok ok ok";
	}

	function avatar1_onSucessServer(PFC_AjaxFileUpload $control) {
		sleep(5);
		/*
		$control->invalidate("Errrrrrrr ooooo rrrr");
		return FALSE;
		*/
		
		$val = $control->getValue();
		return $val['file']['name'];
	}
	function avatar2_onSucessServer(PFC_AjaxFileUpload $control) {
		sleep(5);
		return TRUE;
	}
}
?>