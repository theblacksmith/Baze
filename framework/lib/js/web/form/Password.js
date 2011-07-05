if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Password");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Password
 * @alias Password
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Password = function Password(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'password'; 
	}
	
	this.initialize(elem);
};
	
Object.extend(Password.prototype, VisualComponent.prototype);
Object.extend(Password.prototype, FormField.prototype);

Object.extend(Password.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	phpClass : "PasswordField",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "password")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.value;
			this.realElement = elem;
			
			elem.onchange = this._raiseChange.bind(this);
			
			return true;
		}
		return false;
	},
	
	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.get("value");
	}
});