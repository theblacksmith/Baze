if(typeof Baze !== "undefined")
{
	Baze.provide("web.Button");
	
	Baze.require("web.VisualComponent");
}

/**
 * @class Button
 * @alias Button
 * @namespace Baze.web
 * @author Saulo Vallory 
 * @version 0.9
 * 
 * @requires VisualComponent
 * 
 * @constructor
 */
Button = function Button(elem)
{
	(VisualComponent.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('button');
		elem.type = 'button';
	}
	this.initialize(elem);
};

Object.extend(Button.prototype, VisualComponent.prototype);

Object.extend(Button.prototype,
{	
	items : null,

	phpClass : "Button",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function Button_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;
		this.items = [];
	},
	
	/**
	 * @param {Object} item
	 */
	addItem : function Button_addItem(item, noRaise)
	{
		if (typeof(item) == "object" && item.getAttribute("phpclass") == "Image")
		{
			//Adicionando Objeto
			this.items[this.items.length] = item;
			
			//Adicionando Elemento HTML
			this.realElement.appendChild(item.realElement);
			
			if (typeof(noRaise) !== "undefined" || noRaise == false)
			{
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child : item});
			}
		}
		else if (typeof(item) == "string")
		{
			this.items[this.items.length] = item;
			
			if (typeof(noRaise) !== "undefined" || noRaise == false)
			{
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child: item });
			}
		}
	},
	
	/**
	 * @param {Object} item
	 * @param {boolean} noRaise
	 */
	removeItem : function Button_removeItem(item, noRaise)
	{
		var found = false;
		
		for (var i = 0; (i < this.items.length) && found == false; i++)
		{
			if (this.items[i] == item)
				found == true;
		}
					
		if (found == true)
		{
			return this.removeItemByIndex(i, noRaise);
		}
		
		return false;
	},
	
	/**
	 * @param {HTMLElement} i
	 * @param {boolean} noRaise
	 */
	removeItemByIndex : function Button_removeItemByIndex (i, noRaise)
	{
		if (0<=i && i<this.items.length)
		{
			var aux = this.items[i];
			var auxId = aux.get("id");
			
			this.items.splice(i,i+1);
			aux.realElement.parentNode.removeChild(aux.realElement);
			
			if (typeof(noRaise) == "undefined" || noRaise == false)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		} 
			
		return false;			
	}
});