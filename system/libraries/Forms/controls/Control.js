/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */


/* Base class for all controls */
Pd.Forms.Control = Class.extend({
	name		: null,
	
	id			: null,
	
	config		: {},
	
	form		: null,
	
	// constructor 
	init : function(name, config) {
		this.name	= name;
		this.id		= "id_" + this.name;
		this.config	= config || {};
	},
	
	content : function () {
		return "";
	},
	
	getValue : function(){
		return "";
	},
	
	disable : function() {
		// disables control
		$("#" + this.id).attr("disabled", "disabled");
	},
	
	enable : function() {
		// enables control
		$("#" + this.id).removeAttr("disabled");
	},
	
	hide : function() {
		// hides control
		$("#" + this.id).hide();
	},
	
	show : function() {
		// shows control
		$("#" + this.id).show();
	}
	
});
