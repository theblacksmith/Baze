if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.OptionItem");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

/**
 * @class OptionItem
 * @alias OptionItem
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
OptionItem = function OptionItem(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();		
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('option');
	}
	this.initialize(elem);
};
	
Object.extend(OptionItem.prototype, VisualComponent.prototype);
Object.extend(OptionItem.prototype, Container.prototype);	
Object.extend(OptionItem.prototype, FormField.prototype);

Object.extend(OptionItem.prototype, 
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "OptionItem",
	
	oldSelectedValue : null,
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function OptionItem_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('option');
		}
		this.realElement = elem;
				
		this.oldSelectedValue = this.realElement.selected;
		
		elem.onchange = this._raiseChange.bind(this);
	},
	
	getParentObject : function OptionItem_getParentObject()
	{
		return this.parentObject;
	},

	/**
	 * @param {Object} obj
	 */		
	setParentObject : function OptionItem_setParentObject(obj)
	{
		this.parentObject = obj;
	},
	
	/**
	 * @return boolean
	 */
	isSelected : function OptionItem_isSelected()
	{
		return (this.realElement.selected === true);
	},
	
	/**
	 * @param {Boolean} trueOrFalse
	 *
	setSelected : function OptionItem_setSelected(trueOrFalse, noRaise)
	{
		this.realElement.selected = trueOrFalse;
		this.set('selected',trueOrFalse);
	},*/
	
	/**
	 * @param string
	 */
	setText : function OptionItem_setText(textValue)
	{
		this.removeChildren();
		
		this.addChild(textValue, true);
		
		this.realElement.innerHTML = textValue;
	},
	
	/**
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selected", oldValue : this.oldSelectedValue});
		this.oldSelectedValue = this.realElement.selected;
	}
});