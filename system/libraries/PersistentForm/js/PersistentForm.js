(function( pd ) {
	if ( pd.pform !== undefined ) {
		$.error( '$.pd.pform namespace already used' );
		return false;
	}
	
	pd.pform         = {}; // namespace
	var formsStorage = []; // forms storage
	
	// PersistenForm class
	pd.pform.PersistentForm = function(name, backendUrl, onSuccessClient) {
		this.name   			= name;
		this.backendUrl			= backendUrl;
		this.onSuccessClient	= onSuccessClient;
		this.controls   		= [];
		
		var outerObj			= this;
		
		this.getControl = function(controlName) {
			return outerObj.controls[controlName];
		};
		
		this.addControl = function(controlName, control) {
			outerObj.controls[controlName]	= control;
		};
		
		this.callMethod = function(methodName, data, callback, returnType) {
			var url  = outerObj.backendUrl + "/?form-name=" + outerObj.name + "&service=call-method&method=" + methodName;
			$.post(
				url,
				data,
				callback,
				returnType
			);
		};
		
		this.validate = function() {
		};
		
		this.formAction = function() {
			return outerObj.backendUrl + "/?form-name=" + outerObj.name + "&service=handle-submit";
		};
		
		this.formSubmitResult = function(jsonResult) {
			var result = $.parseJSON(jsonResult);
			if ( result.status == 'success' ) {
				// call onSuccessClient
				if ( outerObj.onSuccessClient != null ) {
					window[outerObj.onSuccessClient](result.data);
				}
			}
			else if ( result.status == 'error' ) {
				// errors markators
				var errElmId = outerObj.name + '-errors';
				var errElm   = '<div style="color:red; padding:5px; border:1px solid red;" id="' + errElmId + '">';
				var errHtml  = [];
				for ( var i=0; i < result.controlsErrors.length; i++ ) {
					errHtml[i] = "<b>" + result.controlsErrors[i].name + "</b>:&nbsp;" + result.controlsErrors[i].error; 
				}
				errHtml = errHtml.join("<br />");
				errElm  = errElm + errHtml + "</div>";
				
				if ( $("#" + errElmId).html() !== null ) {
					$("#" + errElmId).html(errHtml);
				}
				else {
					$("#" + outerObj.name).prepend(errElm);	
				}
				
			}
		};
		
	};
	
	// create a PersistentForm instance
	pd.pform.create = function(name, backendUrl, onSuccessClient) {
		var instance 			= new pd.pform.PersistentForm(name, backendUrl, onSuccessClient);
		formsStorage[name] 	= instance;
		return instance;
	};
	
	// get a PersistentForm instance by name
	pd.pform.get = function(formName) {
		return formsStorage[formName];
	};
	
	pd.pform.getControl = function(formName, controlName) {
		return pd.pform.get(formName).getControl(controlName);
	};
	
	
})( jQuery.pd );