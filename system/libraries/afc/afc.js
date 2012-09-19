Pd.afc = {
	backendUrl : '',
	
	setBackendUrl : function(url) {
		this.backendUrl = url;
	},
	
	call : function( funcName, data, callback, returnType ) {
		$.post(
			this.backendUrl + "?funcName=" + funcName,
			data,
			callback,
			returnType
		);
	}
};



