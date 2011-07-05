if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.HiddenField");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class HiddenField
 * @alias HiddenField
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
HiddenField = function HiddenField(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'hidden';
	}
	
	this.initialize(elem);
};

Object.extend(HiddenField.prototype, VisualComponent.prototype);
Object.extend(HiddenField.prototype, FormField.prototype);

Object.extend(HiddenField.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	phpClass : "HiddenField",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function HiddenField_initialize (elem)
	{
		if (elem.type.toLowerCase() == "hidden")
		{
			(Component.initialize.bind(this, elem))();
			this.oldValue = elem.value;
			this.realElement = elem;
			
			Event.observe(elem, "change", this._raiseChange.bind(this));
			
			return true;
		}
		
		return false; 
	},

	/**
	 * @param {Event} e
	 */
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.realElement.value;
	}
});