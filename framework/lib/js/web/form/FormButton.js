/**
 * @author Luciano
 */
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FormButton");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FormButton
 * @alias FormButton
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FormButton = function FormButton(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'button';
	}
	
	this.initialize(elem);
};
	
Object.extend(FormButton.prototype, VisualComponent.prototype);
Object.extend(FormButton.prototype, FormField.prototype);

Object.extend(FormButton.prototype,
{
	parent : VisualComponent,
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FormButton_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "button")
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
		}
		else
		{
			alert ("Element " + elem.id + " not a FormButtom Type!");
		}
	}
	
});