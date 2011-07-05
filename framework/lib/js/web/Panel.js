if(typeof Baze != "undefined") 
{
	Baze.provide("web.Panel");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Panel = function Panel(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('div');
	}

	this.initialize(elem);
};

Object.extend(Panel.prototype, VisualComponent.prototype);
Object.extend(Panel.prototype, Container.prototype);	

Object.extend(Panel.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Panel",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('panel');
		}
		
		this.realElement = elem;
		
	}
});