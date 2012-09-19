/**
 * @author Gabriel Dodan , gabriel.dodan@gmail.com
 * @copyright Copyright (c) 2012 Gabriel Dodan
 * @license The MIT License http://www.opensource.org/licenses/mit-license.php , see LICENSE.txt file also
 */

/** @DEPENDENCIES = ["FORMS_LIB/controls/ListControl.js"]; */
/* wrapper for <select> tag */
Pd.Forms.Select = Pd.Forms.ListControl.extend({
	
	init: function(name, config) {
		this._super(name, config);
	},
	
	content : function() {
		var defaultAttrs = {
			id		: this.id,
			name	: (this.config.multipleSelect ? this.name + "[]" : this.name)
		};
		if ( this.config.multipleSelect ) {
			defaultAttrs.multiple="multiple";
		}
		
		var excludeAttrs = Pd.Forms.propsToArray(defaultAttrs);
		excludeAttrs.push("multiple");
		
		var val, label;
		for(var i=0; i<this.config.dataSet.length; i++) {
			val		= this.config.dataSet[i][0];
			label	= this.config.dataSet[i][1];
			this.items.push( 
				new Pd.Forms.SelectItem(val, label, Pd.Forms.inArray(val, this.config.selectedValues)) 
			);
		}
		var content = '' + 
			'<select ' + Pd.Forms.objAsAttrs(defaultAttrs) + ' ' + Pd.Forms.objAsAttrs(this.config.attrs, excludeAttrs) + '>\n' + 
				(this.config.itemsRenderer != null ? this.config.itemsRenderer(this) : this.defaultItemsRenderer(this) ) + '\n' +
			'</select>'
		;
		return content + Pd.Forms.codeForBinds(this.id, this.config.binds);
	},
	
	defaultItemsRenderer : function(control) {
		var content = [];
		for (var i=0; i<control.items.length; i++) {
			content.push( control.items[i].content() );
		}
		return content.join("\n");
	},
	
	getValue : function() {
		return $("#" + this.id).val();
	},
	
	getSelectedValues : function() {
		return this.getValue();
	}
	
});

/* wrapper for <option> tag */
Pd.Forms.SelectItem = Pd.Forms.ListControlItem.extend({
	
	init:function(value, label, isSelected) {
		this._super(value, label, isSelected);
	},
	
	content:function() {
		var defaultAttrs = {
			value:this.value
		};
		if ( this.isSelected ) {
			defaultAttrs.selected="selected";
		}
		var excludeAttrs = Pd.Forms.propsToArray(defaultAttrs);
		excludeAttrs.push("selected");
		
		return '<option ' + Pd.Forms.objAsAttrs(defaultAttrs) + ' ' + Pd.Forms.objAsAttrs(this.attrs, excludeAttrs) + '>' + Pd.Forms.escape(this.label) + '</option>';
	}
	
});


/*
config = {
	name:"name",
	dataSet:[],
	selectedValues:[],
	multipleSelect:true|false,
	itemsRenderer:function(control){},
	attrs:{
		
	},
	binds:{
		
	},
	validators:{
		
	}
}
*/
