if(typeof Baze !== "undefined")
{
	Baze.provide("web.ListItem");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");	
}

/**
 * @class ListItem
 * @alias ListItem
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
ListItem = function ListItem(elem) 
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('li');
	}
	
	this.initialize(elem);
};
	
Object.extend(ListItem.prototype, VisualComponent.prototype);
Object.extend(ListItem.prototype, Container.prototype);	

Object.extend(ListItem.prototype, 
{	
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "ListItem",
	
	/**
	 * @param {HTMLElement} elem
	 * 
	 * @return boolean
	 */
	initialize : function ListItem_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('li');
		}
			
		this.realElement = elem;
	},
	
	/**
	 * @return {UList}
	 */
	getParentObject : function ListItem_getParentObject ()
	{
		return this.parentObject;
	},

	/**
	 * @param {UList} uList
	 * @return boolean
	 */		
	setParentObject : function ListItem_setParentObject (uList)
	{
		if (uList.realElement.tagName.toLowerCase() == 'ul' || uList.realElement.tagName.toLowerCase() == 'ol')
		{
			this.parentObject = uList;
			return true;
		}
		
		return false;
	}		
});