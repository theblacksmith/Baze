if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Submit");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Submit
 * @alias Submit
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Submit = function Submit(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('input');
		elem.type = 'submit';
	}
	
	this.initialize(elem);
};

Object.extend(Submit.prototype, VisualComponent.prototype);
Object.extend(Submit.prototype, FormField.prototype);

Object.extend(Submit.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Submit",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "submit")
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
			
			return true;
		}
		return false;
	}
});