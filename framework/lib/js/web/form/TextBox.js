if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.TextBox");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class TextBox
 * @alias TextBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
TextBox = function TextBox(elem)
{
	(FormField.bind(this))();
	(VisualComponent.bind(this))();
	
	if (typeof elem == "undefined") 
	{
		elem = document.createElement("input");
		elem.type = "text";
	}
	
	this.initialize(elem);		
};

Object.extend(TextBox.prototype, VisualComponent.prototype);
Object.extend(TextBox.prototype, FormField.prototype);

Object.extend(TextBox.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	actualValue : "",

	phpClass : "TextBox",
	
	/**
	 * @param {HTMLElement}elem
	 * @return {boolean}
	 */
	initialize : function TextBox_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.actualValue = elem.value;
		this.realElement = elem;
		
		var oldOnChange = this.realElement.onchange;
		
		this.realElement.onchange = null;
		
		if (window.addEventListener) // Mozilla like
		{
			this.realElement.addEventListener('change', this._raiseChange.bind(this),false);
			
			if(oldOnChange)
				this.realElement.addEventListener('change', oldOnChange,false);
		}
		else if (window.attachEvent) // IE
		{
			this.realElement.attachEvent('onchange', this._raiseChange.bind(this));
			
			if(oldOnChange)
				this.realElement.attachEvent('onchange', oldOnChange);
		}
		
		
		return true;
	},
	
	checkChanges : function TextBox_checkChanges()
	{
		if(this.oldValue != this.realElement.value)
			this._raiseChange();
	},
	
	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.actualValue;
		this.actualValue = this.realElement.value;
	}
});