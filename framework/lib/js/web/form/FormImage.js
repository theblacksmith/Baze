if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FormImage");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FormImage
 * @alias FormImage
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FormImage = function FormImage(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'image';
	}
	
	this.initialize(elem);
};
	
Object.extend(FormImage.prototype, VisualComponent.prototype);
Object.extend(FormImage.prototype, FormField.prototype);

Object.extend(FormImage.prototype,
{
	parent : VisualComponent,
	
	phpClass : "FormImage",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FormImage_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == 'undefined')
		{
			Baze.raise("Erro criando HyperLink", new Error("Param elem is not defined in HyperLink_initialize"));
		}
		else
		{
			this.realElement = elem;
		}
	}
});