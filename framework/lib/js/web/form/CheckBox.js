if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.CheckBox");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class CheckBox
 * @alias CheckBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
CheckBox = function CheckBox(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement("input");
		elem.type = "checkbox";
	}	
	
	this.initialize(elem);
};

Object.extend(CheckBox.prototype, VisualComponent.prototype);
Object.extend(CheckBox.prototype, FormField.prototype);

Object.extend(CheckBox.prototype, 
{
	parent : VisualComponent,
	
	phpClass : "CheckBox",
	
	/**
	 * @private
	 * @type {Boolean} elem
	 */
	_oldCheckedValue : "",
	
	actualCheckedValue : "",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function CheckBox_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		this.realElement = elem;
		
		this.actualCheckedValue = elem.checked;
		this._oldCheckedValue = elem.checked;
		
		elem.onclick = this._raiseChange.bind(this);
	},
	
	/**
	 * @method _raiseChange
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this._oldCheckedValue});
		
		this._oldCheckedValue = this.actualCheckedValue; 
		this.actualCheckedValue = this.get("checked");
	}
});