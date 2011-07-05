/**
 * @author saulo
 * @version
 */
if(typeof Baze != "undefined") 
{
	Baze.provide("web.Body");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class Body
 * @alias Body
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.Container
 * 
 * @param {HTMLElement} elem
 */
Body = function Body(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('div');
	}

	this.initialize(elem);
};

Object.extend(Body.prototype, VisualComponent.prototype);
Object.extend(Body.prototype, Container.prototype);	

Object.extend(Body.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Body",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			Baze.raise("N�o � poss�vel criar um componente Body sem um Body! O par�metro recebido foi " + (typeof elem));
		}
		
		this.realElement = elem;
	}
});