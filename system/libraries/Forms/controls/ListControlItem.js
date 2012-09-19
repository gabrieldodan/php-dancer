/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/** @DEPENDENCIES = []; */
Pd.Forms.ListControlItem = Class.extend({
	/* the control this Item belong to,  Pd.Forms.Control*/
	control:null,
	
	attrs:{},
	
	isSelected:false,
	
	value:"",
	
	label:"",
	
	init:function(value, label, isSelected) {
		this.value		= value;
		this.label		= label;
		this.isSelected	= isSelected;
	},
	
	content:function() {
		return "";
	}
});
