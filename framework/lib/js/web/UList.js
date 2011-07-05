if(typeof Baze !== "undefined")
{
	Baze.provide("web.UList");
	
	Baze.require("web.ListItem");
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * @class UList
 * @alias UList
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 *
 * @param {HTMLElement} elem
 */
UList = function UList(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('ul');
	}
	this.initialize(elem);
};
	
Object.extend(UList.prototype, VisualComponent.prototype);
Object.extend(UList.prototype, Container.prototype);

Object.extend(UList.prototype, 
{	
	parent : VisualComponent,
	
	parentObject : null,
	
	listItems : null, 
	
	phpClass : "UList",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function UList_initialize(elem) 
	{
		if (elem.tagName.toLowerCase() == 'ul')
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.realElement = elem;
			
			this.listItems = [];
			
			var numChildren = elem.childNodes.length;
			
			for (var i=0; i < numChildren; i++)
			{
				if (typeof elem.childNodes[i] == "object")
				{
					if (elem.childNodes[i].nodeName.toLowerCase() == 'li')
					{
						var listItem = Baze.getComponentById(elem.childNodes[i].id);
					
						if (typeof listItem == "undefined" || listItem == null)
						{
							listItem = new ListItem(elem.childNodes[i]);
							Baze.addComponent(listItem);	
						} 
			
						this.listItems[this.listItems.length] = listItem;
						this.addChild(listItem, true);
						listItem.setParentObject(this);
					}
				}					
			}
							
			return true;
		}
		return false;
	},
	
	/**
	 * @classDescription Criando e adicionando um ListItem recebendo um HTMLElement
	 * @param {HTMLElement} elem
	 * @return boolean
	 */
	addItem : function UList_addItem(elem, noRaise)
	{
		if (elem.tagName.toLowerCase() == "li")
		{
			var myListItem = new ListItem(elem);
			
			this.addListItem(myListItem, noRaise);
			return true;
		}
		return false;
	},

	/**
	 * @param {ListItem} listItem
	 */
	addListItem : function UList_addListItem(listItem, noRaise)
	{
		if (listItem.get("tagName").toLowerCase() == "li")
		{
			
			//Adicionando Objeto	
			this.listItems[this.listItems.length] = listItem;
			
			//Adicionando Elemento HTML
			this.realElement.add(listItem.realElement, null);
			
			//Adjustando propriedade "parentObject"
			listItem.setParentObject(this);	
			
			if (noRaise == undefined || noRaise == false)
			{
				this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : listItem});
			}	
			return true;
		}
		
		return false;
	},

	/** 
	 * @param {HTMLElement} elem
	 * @return int
	 */
	getListItemIndex : function UList_getListItemIndex(elem)
	{
		var numItems = this.listItems.length;
		
		var j = -1;
		
		for (var i = 0; i < numItems && (j == -1); i++)
		{
			if (this.listItems[i].get('id') == elem.id)
			{
				j = i;
			}
		}
		
		return j;
	},
	
	/**
	 * @classDescription Removendo, por �ndice, um ListItem do array "listItems" 
	 * @param {int} i
	 */
	removeListItemByIndex : function UList_removeListItemByIndex(i, noRaise)
	{
		if ( 0 <= i && i<this.listItems.length)
		{
			var aux = this.listItems[i];
			var auxId = aux.get("id");
			
			this.listItems.splice(i,i+1);
							
			this.realElement.removeChild(aux);
			
			if (noRaise == undefined || noRaise == false)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		} 
		
		return false;
	},
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	removeListItemByHTMLElement : function UList_removeListItemByHTMLElement(elem, noRaise)
	{
		if (elem.tagName.toLowerCase() == 'li')
		{
			var i = this.getListItemIndex(elem);
			
			if (i != -1)
			{
				return this.removeListItemByIndex(i, noRaise);					
			}
		}
		return false;
	},
	
	/**
	 * @param {ListItem} listItem
	 */
	removeListItem : function UList_removeListItem(listItem, noRaise)
	{
		return this.removeListItemByHTMLElement(listItem.realElement, noRaise);
	},
	
	/**
	 * @return {Object}
	 */
	getParentObject : function ListItem_getParentObject()
	{
		return this.parentObject;
	},

	/**
	 * @param {Object} obj
	 */		
	setParentObject : function ListItem_setParentObject(obj)
	{
		this.parentObject = obj;
	}		
});