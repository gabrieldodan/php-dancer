/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/** @DEPENDENCIES = ["FORMS_LIB/controls/Control.js"]; */
Pd.Forms.TextArea = Pd.Forms.Control.extend({
	init: function(name, config) {
		this._super(name, config);
	},
	
	content : function() {
		var defaultAttrs = {
			id		: this.id,
			name	: this.name
		};
		
		var content = '<textarea ' + Pd.Forms.objAsAttrs(defaultAttrs) + ' ' + Pd.Forms.objAsAttrs(this.config.attrs, Pd.Forms.propsToArray(defaultAttrs)) + '>' + this.config.value + '</textarea>';
		return content + Pd.Forms.codeForBinds(this.id, this.config.binds);
	},
	
	getValue : function() {
		return $("#" + this.id).val();
	}
	
});
