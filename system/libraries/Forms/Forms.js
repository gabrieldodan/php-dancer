/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

// Namespace for forms
Pd.Forms			= {};

// Namespace for templates
Pd.Forms.Templates	= {};

Pd.Forms.RequiredScripts = {
	'TextInput'		: ['Control.js', 'TextInput.js'],
	'TextArea'		: ['Control.js', 'TextArea.js'],
	'ButtonSubmit'	: ['Control.js', 'ButtonSubmit.js'],
	'CheckBox'		: ['Control.js', 'CheckBox.js'],
	'Radio'			: ['Control.js', 'Radio.js'],
	'Select'		: ['Control.js', 'ListControl.js', 'ListControlItem.js', 'Select.js'],
	'DatePicker'	: ['Control.js', 'DatePicker.js']
};

Pd.Forms.RequiredCss = {
};


Pd.Forms.Form = Class.extend({
	name			: null,
	
	id				: null,
	
	config			: {},
	
	controls		: {},
	
	controlsDef		: {},
	
	userControls	: "",
	
	template		: 'labels-left',
	
	theme			: 'default',
	
	cssClass		: "",
	
	/* constructor */
	init : function(config, controlsDef) {
		this.config			= config;
		this.controlsDef	= controlsDef;
		this.name			= this.config.name;
		this.id				= "id_" + this.name;
		
		this.template		= this.config.template || 'labels-left';
		this.theme			= this.config.theme || 'default';
		this.cssClass		= "theme-" + this.theme;
		this.userControls	= this.config.userControls || "";
		
		this.loadResources(function(){
			this.render();
		});
		
	},
	
	render : function() {
		var control		= null;
		var controlDef	= null;
		for ( var i=0; i<this.controlsDef.length; i++) {
			controlDef						= this.controlsDef[i];
			control							= new Pd.Forms[controlDef.type](controlDef.name, controlDef.config);
			control.form					= this;
			this.controls[controlDef.name]	= control;
		}
		var content = Pd.Forms.Templates[this.template](this);
		$("#" + this.config.renderTo).append(content);
	},
	
	getControl:function(controlName){
		return this.controls[controlName];
	},
	loadResources : function(callback) {
		var controls = [];
		for ( var i=0; i<this.controlsDef.length; i++) {
			controls.push( this.controlsDef[i].type );
		}
		var form = this;
		
		/*
		$.ajax(
			Pd.Forms.URL_BACKEND + "?controls=" + controls.join(",") + 
			"&userControlsPath=" + this.userControls.replace(Pd.Forms.PATH_HOME, "") + 
			"&template=" + this.template.replace(Pd.Forms.PATH_HOME, "") + 
			"&theme=" + this.theme.replace(Pd.Forms.PATH_HOME, ""),
			{
			async:true,
			cache:($.browser.msie ? false : true),
			dataType:'text',
			success:function(data) {
				data = $.parseJSON(data);
				
				var styleRules = data.css;
				var style = document.createElement('style');
				style.setAttribute("type", "text/css");
				if ( $.browser.msie ) {
					style.styleSheet.cssText = styleRules;
				}
				else {
					style.innerHTML = styleRules;
				}
				document.getElementById(form.config.renderTo).appendChild(style);
				
				var script = document.createElement("script");
				script.text = data.js;
				document.getElementById(form.config.renderTo).appendChild(script);
				
				if ( callback ) {
					callback.call(form);
				}
			}
		});
		*/
	   
		
		if ( !Pd.Forms.resCallbacksCnt ) {
			Pd.Forms.resCallbacksCnt = 0;
		}
		Pd.Forms.resCallbacksCnt++;
		Pd.Forms["responseCallback" + Pd.Forms.resCallbacksCnt] = function(data) {
			var styleRules = data.css;
			var style = document.createElement('style');
			style.setAttribute("type", "text/css");
			if ( $.browser.msie ) {
				style.styleSheet.cssText = styleRules;
			}
			else {
				style.innerHTML = styleRules;
			}
			document.getElementById(form.config.renderTo).appendChild(style);
				
			var script = document.createElement("script");
			script.text = data.js;
			document.getElementById(form.config.renderTo).appendChild(script);
				
			if ( callback ) {
				callback.call(form);
			}
		};
		
		$.ajax(
			Pd.Forms.URL_BACKEND + "?controls=" + controls.join(",") + 
			"&userControlsPath=" + this.userControls.replace(Pd.Forms.PATH_HOME, "") + 
			"&template=" + this.template.replace(Pd.Forms.PATH_HOME, "") + 
			"&theme=" + this.theme.replace(Pd.Forms.PATH_HOME, ""),
			{
			cache:true,
			dataType:'jsonp',
			jsonp: 'callback',
			crossDomain:true, // force to use <script> tag instead of XHTMLHttpRequest
			jsonpCallback: 'Pd.Forms["responseCallback' + Pd.Forms.resCallbacksCnt + '"]'
		});
		
		/*
		var script = document.createElement("script");
		script.src =	Pd.Forms.URL_BACKEND + "?controls=" + controls.join(",") + 
						"&userControlsPath=" + this.userControls.replace(Pd.Forms.PATH_HOME, "") + 
						"&template=" + this.template.replace(Pd.Forms.PATH_HOME, "") + 
						"&theme=" + this.theme.replace(Pd.Forms.PATH_HOME, "") + "&callback=";

		document.getElementById(form.config.renderTo).appendChild(script);
		*/	   
		
	},
	
	loadScripts : function() {
		var formsLibPath	= Pd.Forms.URL_FORM_LIB.replace(Pd.Forms.URL_HOME, "");
		var scripts			= [];
		var controlScripts	= null;
		var url				= "";
		for ( var i=0; i<this.controlsDef.length; i++) {
			controlScripts = Pd.Forms.RequiredScripts[this.controlsDef[i].type] || [];
			for (var j=0; j<controlScripts.length; j++) {
				url = formsLibPath + "/js/controls/" + controlScripts[j];
				if ( !Pd.Forms.inArray(url, scripts) ) {
					scripts.push( url );
				}
			}
		}
		
		// template script
		scripts.push( formsLibPath + "/js/templates/" + this.template + ".js" );
		
		$.ajax({
			url:Pd.Forms.URL_SYS_DIR + "/batchJS.php?f=" + scripts.join(","),
			async:false,
			cache:true,
			dataType:'script',
			success:function(data) {
				//console.log(data);
			}
		});
		
	}
});


// Some useful functions
Pd.Forms.objAsAttrs = function(object, excludeProps) {
	var excludeProps	= arguments[1] || [];
	var result			= [];
	for ( var propName in object ) {
		if ( !Pd.Forms.inArray(propName, excludeProps) ) {
			result.push(propName + '="' + Pd.Forms.escape( object[propName].toString() ) + '"');
		}
	}
	if ( result.length ) {
		return result.join(" ");
	}
	return "";
};

	
Pd.Forms.escape = function(string) {
	return string.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
};

Pd.Forms.inArray = function(value, array) {
	return ( $.inArray(value, array) == -1 ? false : true );
};

Pd.Forms.propsToArray = function(object) {
	var result = [];
	for ( var propName in object ) {
		result.push(propName);
	}
	return result;
};
	
Pd.Forms.scriptTag = function(jsCode) {
	return ( jsCode ? '\n<script>\n' + jsCode + '\n<\/script>\n' : '');
};
	
Pd.Forms.codeForBinds = function(id, binds) {
	var jsCode = '';
	for(var eventName in binds ) {
		jsCode += '$("#' + id + '").on("' + eventName + '", ' + binds[eventName] + ');\n';
	}
	return ( jsCode ? '\n<script>\n' + jsCode + '\n<\/script>\n' : '');
};
Pd.Forms.isString = function (value) {
	if ( typeof value === "string" ) {
		return true;
	}
	return false;
};
	
Pd.Forms.hasProp = function(prop, object) {
	if ( typeof object[prop] !== undefined ) {
		return true;
	}
	return false;
};
