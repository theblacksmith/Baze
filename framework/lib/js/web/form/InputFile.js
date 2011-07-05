/**
 * @author Luciano
 */
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.InputFile");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class InputFile
 * @alias InputFile
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
InputFile = function InputFile(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined')
	{
		var elem  = document.createElement('input');
		elem.type = 'file';
	}
	
	this.initialize(elem);
};

Object.extend(InputFile.prototype, VisualComponent.prototype);
Object.extend(InputFile.prototype, FormField.prototype);

Object.extend(InputFile.prototype,
{	
	phpClass : "FileUpload",
	
	/**
	 * 
	 * @param {HTMLElement} elem
	 */
	initialize : function InputFile_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;
	}
});