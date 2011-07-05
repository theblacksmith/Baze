if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.TextArea");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
	Baze.require("web.form.FormField");
}

/**
 * @class TextArea
 * @alias TextArea
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
TextArea = function TextArea(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('textarea');
	}
	
	this.initialize(elem);	
};

Object.extend(TextArea.prototype, VisualComponent.prototype);
Object.extend(TextArea.prototype, FormField.prototype);
Object.extend(TextArea.prototype, Container.prototype);

Object.extend(TextArea.prototype,  
{
	parent : VisualComponent,
	
	actualValue : "",
	
	oldValue : "",
	
	phpClass : "TextArea",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function TextArea_initialize(elem)
	{		
		if (elem.tagName.toLowerCase() == "textarea")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.value;
			this.actualValue = elem.value;
			this.realElement = elem;
			
			elem.onchange = this._raiseChange.bind(this);
			
			return true;
		}
		
		Baze.raise("n�o foi poss�vel criar o textarea");
		
		return false;
	},
	
	removeChildren : function TextArea_removeChildren()
	{
		this.realElement.innerHTML = '';
		this.realElement.value = '';
		
		this._raiseChange.bind(this);
	},

	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.actualValue;
		this.actualValue = this.realElement.value;
	}
});