/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/** @DEPENDENCIES = ["FORMS_LIB/controls/Control.js", "JQUERY_UI/ui.datepicker.js"]; */

Pd.Forms.DatePicker = Pd.Forms.Control.extend({
	
	init: function(name, config) {
		this._super(name, config);
	},
	
	content : function() {
		var defaultAttrs = {
			type	: "text",
			id		: this.id,
			name	: this.name,
			value	: (this.config.value || '')
		};
		
		
		var content = '' + 
			'<input ' + Pd.Forms.objAsAttrs(defaultAttrs) + ' ' + Pd.Forms.objAsAttrs(this.config.attrs, Pd.Forms.propsToArray(defaultAttrs)) + ' />' + 
			'<input type="hidden" id="' + this.id + '-ymd" name="' + this.id + '-ymd" />'
		;
		
		var js = '' + 
			'<script>' + 
			'$("#' + this.id + '").datepicker({altField:"#' + this.id + '-ymd", altFormat:"yy-mm-dd"});' + 
			'$("#' + this.id + '").datepicker();' + 
			'$("#ui-datepicker-div").wrap(\'<div class="' + this.form.cssClass + '"></div>\');' +
			'<\/script>'
		;
		return content + js;
	},
	
	getValue : function() {
		return $("#" + this.id).val();
	},
	
	isChecked : function() {
		return $("#" + this.id).is(":checked");
	}
	
});
