if(typeof Baze != "undefined") 
{
	Baze.provide("web.Span");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class Span
 * @alias Span
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
	 * @param {HTMLElement} elem
	 */
Span = function Span(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('span');
	}

	this.initialize(elem);
};

Object.extend(Span.prototype, VisualComponent.prototype);
Object.extend(Span.prototype, Container.prototype);	

Object.extend(Span.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Span",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function Span_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('span');
		}
		
		this.realElement = elem;
	}
});