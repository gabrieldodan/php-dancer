/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/** @DEPENDENCIES = ["FORMS_LIB/controls/Control.js", "FORMS_LIB/controls/ListControlItem.js"]; */

/* Base class for all list controls */
Pd.Forms.ListControl = Pd.Forms.Control.extend({
	dataSet:[],
	
	items:[],
	
	itemsRenderer:null,
	
	init:function(name, config) {
		this._super(name, config);
	}
});
