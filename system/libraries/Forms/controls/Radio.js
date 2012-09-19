/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/** @DEPENDENCIES = ["FORMS_LIB/controls/Control.js"]; */
Pd.Forms.Radio = Pd.Forms.Control.extend({
	init: function(name, config) {
		this._super(name, config);
	},
	
	content : function() {
		var defaultAttrs = {
			type	: "radio",
			id		: this.id,
			name	: this.name,
			value	: (this.config.value || '')
		};
		
		var content = '<input ' + Pd.Forms.objAsAttrs(defaultAttrs) + ' ' + Pd.Forms.objAsAttrs(this.config.attrs, Pd.Forms.propsToArray(defaultAttrs)) + ' />';
		return content + Pd.Forms.codeForBinds(this.id, this.config.binds);
	},
	
	getValue : function() {
		return $("#" + this.id).val();
	}
	
});
