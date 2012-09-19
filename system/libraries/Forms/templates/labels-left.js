/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

Pd.Forms.Templates["labels-left"] = function(form) {
	var defaultAttrs = {
		method		: "post",
		enctype		: "multipart/form-data",
		id			: form.id,
		name		: form.name,
		action		: "",
		"class"		: form.cssClass
	};
	
	var content = '' + 
		'<form ' +  Pd.Forms.objAsAttrs(defaultAttrs) + ' ' + Pd.Forms.objAsAttrs(form.config.attrs, Pd.Forms.propsToArray(defaultAttrs)) + ' >' +
			'<table>' +
				(function(form){
					var content = '';
					var control	= null;
					for( var controlName in form.controls) {
						control = form.controls[controlName];
						content += '' + 
							'<tr>' + 
								'<td>' + control.config.label + '</td><td>' + control.content() + '</td>' +
							'</tr>'
						;
					}
					return content;
				})(form) +
			'</table>' +
		'</form>'
	;
	
	return content + Pd.Forms.codeForBinds(form.id, form.config.binds);
}