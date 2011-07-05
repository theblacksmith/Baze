if(typeof Baze != 'undefined')
{
	Baze.provide("web.image.Image");
	Baze.require("web.Component");
}

/**
 * @class Picture
 * @alias Picture
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Picture = function Picture(elem)
{
	(VisualComponent.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('img');
	}

	this.initialize(elem);
};
	
Object.extend(Picture.prototype, VisualComponent.prototype);

Object.extend(Picture.prototype,
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "Image",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function Image_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('img');
		}
		
		this.realElement = elem;
	}
});