if(typeof Baze !== "undefined")
{
	Baze.provide("web.HyperLink");
	
	Baze.require("web.VisualComponent");	
	Baze.require("web.Container");	
}

/**
 * @class HyperLink
 * @alias HyperLink
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
HyperLink = function HyperLink(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('a');
	}
	
	this.initialize(elem);
};
		
Object.extend(HyperLink.prototype, VisualComponent.prototype);
Object.extend(HyperLink.prototype, Container.prototype);
	
Object.extend(HyperLink.prototype,
{
	parent : VisualComponent,
	
	phpClass : "HyperLink",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function HyperLink_initialize(elem)
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