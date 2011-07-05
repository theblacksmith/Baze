if(typeof Baze !== "undefined")
{
	Baze.provide("web.Container");
	
	Baze.require("web.Component");
	Baze.require("system.commands.Command");
}
/**
 * @class Container
 * @alias Container
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Component
 * @requires Command
 * 
 * @constructor
 */	
Container = function Container() 
{
	this.isContainer = true;
	(Component.bind(this))();
	
	this.onChildAdd = new Baze.Event();
	this.onChildRemove = new Baze.Event();
	this.children = new Collection();
};

Object.extend(Container.prototype, Component);
	
Object.extend(Container.prototype, 
{
	/**
	 * @type {Collection}
	 */
	children : null,
	
	/**
	 * @type {Baze.Event}
	 */
	onChildAdd : null,
	
	/**
	 * @type {Baze.Event}
	 */
	onChildRemove : null,
	
	/**
	 * 
	 * @param {mixed} obj
	 * @param {Object} noRaise
	 */
	addChild : function Container_addChild(obj, noRaise)
	{
		/* Ainda n�o t� implementado
		if(obj instanceof HTMLElement)
		{
			if(document != document.body.ownerDocument)
				document.importNode(obj, true);
			
			if(obj.hasAttribute("phpClass")) {
				var comp = Component.factory(obj.getAttribute("phpClass"), obj); }
			else
			{
				var phpClass = Component.guessType(obj);					
			}
		}
		*/
		
		if(Baze.isComponent(obj))
		{
			var childNode = obj.realElement;
			
			if(obj.realElement.ownerDocument !== document)
			{
				if(typeof document.importNode != "undefined")
					childNode = document.importNode(obj.realElement, true);
			}

			//Adicionando Objeto
			window.damn = this;
			this.children.add(obj.getId(),obj);
			
			
			Baze.addComponent(obj);
			
			try 
			{
				//Adicionando Elemento HTML
				if (childNode.parentNode !== this.realElement)
				{
					this.realElement.appendChild(childNode);
				}
			}
			catch(e) {
				Baze.raise("N�o foi poss�vel adicionar o componente " + obj.getId() + " ao container " + this.id + " ", e);
				return;
			}

			if (noRaise != true) {
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child : obj});
			}
		}
		else if (typeof obj == "string" || typeof obj == "number")
		{
			var lit = new Literal(obj);
			
			//Adicionando Objeto
			this.children.add(lit.getId(), lit);
			
			Baze.addComponent(lit);
			
			//Adicionando Elemento HTML
			this.realElement.appendChild(lit.realElement);
			
			if (noRaise != true)
			{
				this.onChildAdd.raise(this,{ changeType : ChangeType.CHILD_ADDED, child: lit });
			}
		}
	},
			
	/**
	 * @param {Component} obj
	 * @param {boolean} noRaise
	 */
	removeChild : function Panel_removeChild(obj, noRaise)
	{
		if(typeof obj == "string")
			obj = $C(obj);
			
		if(obj == null || !(typeof obj == "object" && Baze.isComponent(obj)))
			return;
		
		if(this.children.get(obj.get("id")) == null)
			return false;
		
		if(obj.constructor === Literal)
		{
			this.children.remove(obj.get("id"));
			
			for(var i=0; i < obj.childNodes.length; i++)
			{
				this.realElement.removeChild(obj.childNodes[i]);
			}
			
			if (noRaise != true)
				this.onChildRemove.raise(this,{ changeType : ChangeType.CHILD_REMOVED, child: obj });
			
			return true;
		}
		else
		{
			this.children.remove(obj.get("id"));
			
			this.realElement.removeChild(obj.realElement);
			
			if (noRaise != true)
				this.onChildRemove.raise(this,{ changeType : ChangeType.CHILD_REMOVED, child: obj });

			return true;
		}

		return false;
	},
	
	/**
	 * @param {HTMLElement} i
	 * @param {boolean} noRaise
	 */
	removeChildByIndex : function Panel_removeChildByIndex (i, noRaise)
	{
		if (i >= 0 && i < this.children.length)
		{
			var aux = this.children[i];
			var auxId = aux.get("id");
			
			this.children.splice(i,i+1);
			aux.realElement = aux.realElement.parentNode.removeChild(aux.realElement);
			
			if (noRaise != true)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		}
		return false;			
	},
	
	removeChildren : function (noRaise) {
		this.children.removeAll();
		this.realElement.innerHTML = '';
	}
});
// Creating commands

Container.CommandEnum = {
	RemoveChildren : 'RemoveContainerChildren' 
};

Baze.registerCommand(new Baze.Command(
{
	id : Container.CommandEnum.RemoveChildren,
		
	name : "RemoveContainerChildren",
	
	/**
	 * 
	 * @param {Container} cont
	 */
	action : function (cont) {
		var comp = null;
		
		if(typeof cont == "string") {
			comp = $C(cont);
		}
		else
			comp = cont;
		
		if(!(typeof comp == "object" && Baze.isContainer(comp)))
		{
			Baze.raise("Erro removendo filhos de um container, container ("+cont+") n�o encontrado.", 
							new Error("Container with id "+cont+" couldn't be found."));
		}
				
		comp.removeChildren(true);
	},

	checkArgumentTypes : false
	//argumentTypes : [Object,"string"]
}));