(function( pd ) {
	/*
	if ( pd.pform.PFC_AjaxFileUpload !== undefined ) {
		$.error( '$.pd.pform.PFC_AjaxFileUpload namespace already used' );
		return false;
	}
	*/
	pd.pform.PFC_AjaxFileUpload = {};
	// PFC_AjaxFileUpload constructor
	pd.pform.PFC_AjaxFileUpload = function(
			form, 
			name, 
			htmlName, 
			htmlId, 
			idFormForSubmit,
			idIframeForSubmit,
			onSuccess, 
			onError, 
			extraDataProducer,
			beforeUpload,
			autoUpload
	) {
		this.form				= form;
		this.name 				= name;
		this.htmlName 			= htmlName;
		this.htmlId				= htmlId;
		this.idFormForSubmit	= idFormForSubmit;
		this.idIframeForSubmit	= idIframeForSubmit;
		this.onSuccess 			= onSuccess;
		this.onError 			= onError;
		this.extraDataProducer 	= extraDataProducer;
		this.beforeUpload 		= beforeUpload;
		this.autoUpload 		= autoUpload;
		this.idMsgsBox			= htmlId + "-msgs-box";
		
		this.bPreventDefault 	= false;
		
		var outerObj			= this;
		
		this.setForm = function(form) {
			outerObj.form = form;
		};
		
		this.getForm = function() {
			return outerObj.form;
		};
		
		
		
		
		this.preventDefault = function() {
			outerObj.bPreventDefault = true;
		};
		
		this.defaultOnSuccess = function(data) {
			if (outerObj.bPreventDefault) {
				return;
			}
			$("#" + outerObj.idMsgsBox).html('File uploaded successfully');
		};
		
		this.defaultOnError = function(data) {
			if (outerObj.bPreventDefault) {
				return;
			}
			
			$("#" + outerObj.idMsgsBox).css({
				"color":"red"
			});
			$("#" + outerObj.idMsgsBox).html(data);
		};
		
		this.cancelUpload = function() {
			$("#" + outerObj.idIframeForSubmit).attr("src", $("#" + outerObj.idIframeForSubmit).attr("src"));
			
			// reset hidden form
			$("#" + outerObj.idFormForSubmit).each(function(){
		        this.reset();
			});
			$("#" + outerObj.htmlId).removeAttr("disabled");
			
			$("#" + outerObj.idMsgsBox).html('&nbsp;');
		};
		
		this.startUpload = function() {
			
			// remove extradata hidden inputs
			$("#" + outerObj.idFormForSubmit + " > input[type='hidden']").remove();
			
			// add extradata as hidden inputs
			if (outerObj.extraDataProducer !='') {
				var extraData = window[outerObj.extraDataProducer]();
				for ( var key in extraData) {
					$("#" + outerObj.idFormForSubmit).append( '<input type="hidden" name="' + key + '" value="' + extraData[key] + '" />' );
				}
			}
			
			// call before upload function
			if (outerObj.beforeUpload !='') {
				window[outerObj.beforeUpload]();
			}
			// submit hidden form
			$("#" + outerObj.idFormForSubmit).submit();
			
			// disable control
			$("#" + outerObj.htmlId).attr("disabled", "disabled");
			
			// show uploading message
			$("#" + outerObj.idMsgsBox).css({
				"color":"blue"
			});
			
			var idCancelLink = outerObj.htmlId + '-cancel-link';
			$("#" + outerObj.idMsgsBox).html('Uploading, please wait ... <a onclick="pd.pform.getControl(\'' + outerObj.form.name + '\', \'' + outerObj.name + '\').cancelUpload()" id="' + idCancelLink + '" href="javascript:void(-1)">Cancel</a>');
			//$("#" + outerObj.idMsgsBox).html('Uploading, please wait ...');
			
		};
		
		this.fileSubmitResult = function(jsonResult) {
			$("#" + outerObj.htmlId).removeAttr("disabled");
			$("#" + outerObj.idMsgsBox).html('&nbsp;');
			
			var result = $.parseJSON(jsonResult);
			if ( result.status == 'success' ) {
				// call onSuccessClient
				if ( outerObj.onSuccess != '' ) {
					var code = outerObj.onSuccess + ".apply(outerObj, [result.data]);";
					eval(code);
				}
				outerObj.defaultOnSuccess(result.data);
			}
			else if ( result.status == 'error' ) {
				if ( outerObj.onError != '' ) {
					var code = outerObj.onError + ".apply(outerObj, [result.data]);";
					eval(code);
				}
				outerObj.defaultOnError(result.data);
			}		
		};
	};
})( jQuery.pd );
