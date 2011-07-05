if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Reset");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Reset
 * @alias Reset
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Reset = function Reset(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'reset';
	}
	
	this.initialize(elem);
};

Object.extend(Reset.prototype, VisualComponent.prototype);
Object.extend(Reset.prototype, FormField.prototype);

Object.extend(Reset.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Reset",
	
	/**
	 * @param {HTMLElement} elem
	 */		
	initialize : function Reset_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "reset")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.realElement = elem;
			
			return true;
		}
		
		return false;
	}
});