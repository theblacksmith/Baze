if(typeof Baze != "undefined")
{
	Baze.provide("web.Style");
	
	// Assumindo que o jext est� sempre l�!
	// Baze.require("system.jext");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {Object} style
 */
Style = function Style(style) {
	this.realObject = style;
};

Object.extend(Style.prototype, {

	realObject : null,
	
	_owner : null,
	
	/**
	 * @method get
	 * @param {String} prop
	 */
	get : function get(prop) {
		return this.realObject[prop];
	},
	
	/**
	 * @method set
	 * @param {String} name
	 * @param {Object} value
	 */
	set : function set(name, value) {
		if(typeof this.realObject[name] != "undefined")
			var oldValue = this.realObject[name];
		else
			var oldValue = undefined;
		
		if(value != oldValue)
		{
			this.realObject[name] = value;
			if(this._owner)
			{
				var oldCss = this.realElement.style.cssText;
				this._owner.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : "style", oldValue : oldCss});
			}
		}
	},
	
	/**
	 * @method setOwner
	 * @param {Component} comp
	 * @return {boolean}
	 */
	setOwner : function setOwner(comp)
	{
		if(!Baze.isComponent(comp))
		{
			Baze.raise("Par�metro incorreto", new Error("A fun��o Style.setOwner espera um Component como par�metro mas recebeu um " + (typeof comp)));
			return false;
		}
			
		this._owner = comp;
		return true;
	},
	
	/**
	 * @method getOwner
	 * @return {Component} comp
	 */
	getOwner : function getOwner()
	{		
		return this._owner;
	}
});
if(typeof Baze != "undefined") {	
	Baze.provide("web.Component");
	
	Baze.require("system.Event");
	Baze.require("system.util");
	Baze.require("web.Style");
	// Assumir que j� est� inclu�do
	// Baze.require("system.jext");
}

/**
 * @class Component
 * @alias Component
 * @namespace Baze
 * @author Saulo Vallory
 * 
 * @requires system.jext
 * @requires system.Event
 * @requires system.util
 * @requires web.Style
 * 
 * @constructor
 */
Component = function Component()
{
	this.isComponent = true;
	
	this.onPropertyChange = new Baze.Event();
	
	this.id = uid("cmp_");
};

Object.extend(Component.prototype,  {

	id : "",
	
	onPropertyChange : null,
	
	style : null,
	
	realElement : null,
		
	phpClass : "",

	initialize : function Component_initialize(elem) {
		this.id = elem.id;
		this.style = new Style(elem.style);
	},
	
	get : function Component_get(name) {

		if(typeof this["get"+name.capitalize()] == "function") {
			return this["get"+name.capitalize()](); }
		else {
			if(this[name])
				return this[name];
				
			if(this["realElement"] == null || typeof this["realElement"] == "undefined") {
				return this[name];
			}
			
			return this.realElement[name];
		}
	},
	
	getHTML : function Component_getHTML() {
		
		if(this.realElement.nodeType == DOCUMENT_FRAGMENT_NODE)
		{
			var temp = document.createElement("div");
			
			temp.appendChild(this.realElement);
			
			var html = temp.innerHTML;
			delete temp;
			
			return html;
		}	
		else
			return this.realElement.outerHTML;
	},
	
	/**
	 * @memberOf {Component}
	 * @alias getXML
	 */
	getSyncObj : function Component_getSyncObj()
	{
		var node = this.realElement;
		
		obj = {
			klass: this.phpClass,
			id: this.id,
			properties: [] 
		};
		
		if(node != null && node.attributes)
		{
			for(var i=0; i < node.attributes.length; i++)
			{
				var att = node.attributes[i];
				
				if(att.nodeValue)
				{
					obj.properties.push({n: att.nodeName, v: att.nodeValue});
				}
			}
		}
		
		return obj;
	},

	getId : function Component_getId() {
		return this.id;
	},

	set : function Component_set(name, value) {
	
		// s� � necess�rio fazer o tracking para elementos na p�gina
		if(this["realElement"] == null || typeof this["realElement"] == "undefined") {
			this[name] = value;
			return;
		}
	
		var oldValue = this.get(name);
			
		if(value != oldValue)
		{				
			if(typeof this["set"+name.capitalize()] == "function")
				return this["set"+name.capitalize()](value);
			else
				this.realElement[name] = value;
			
			this.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : name, oldValue : oldValue});
		}
	},

	/**
	 * @memberOf Component
	 * @param {string} id
	 */
	setId : function Component_setId(id){
		this.id = id;
		
		if(this.realElement)
		{
			this.realElement["id"] = id;
		}
	},
	
	getStyle : function Component_getStyle()
	{
		return this.realElement.style.cssText;
	},
	
	/**
	 * Nota para desenvolvedores do framewor: Essa fun��o N�O deve utilizar a fun��o set do objeto style.
	 * A fun��o Style.set j� checa a exist�ncia do Componente que a cont�m e comunica a altera��o
	 * @memberOf {Component}
	 * @method setStyle
	 * @param {string} txt
	 */
	setStyle : function setStyle(prop, value) {
		if(value == null)
			this.realElement.style.cssText = prop;
		else
		{
			var oldValue = this.realElement.style[prop];
			
			if(value != oldValue)
			{
				var oldCss = this.realElement.style.cssText;
				this.realElement.style[prop] = value;
				this.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : "style", oldValue : oldCss});
			}
		}
	}
});

/**
 * 
 * @param {String} phpClass
 * @param {HTMLElement} node
 * @return {Component}
 */
Component.factory = function factory(phpClass, node)
{
	if(phpClass.toLowerCase() == "image")
		phpClass = "Picture";
	
	var validClasses = ["body", "button", "dropdownlist", "datepicker", "icon", "menu", "style", "page",
						"checkbox", "form", "hiddenfield", "hyperlink", "formbutton", "formimage", 
						"htmltag", "image", "inputfile", "label", "listbox", "listitem", "literal",
						"optionitem", "panel", "password", "radio", "radiobutton", "radiogroup", 
						"reset", "slider", "span", "submit", "textarea", "textbox", "uidig", "picture", "ulist"];

	if(validClasses.indexOf(phpClass.toLowerCase()) != -1) {
		var __comp = null;
		var classExists = false;
		var __constructor = null;

		eval('classExists = (typeof ' + phpClass + ' == "function");');

		if(!classExists) {
			Baze.raise("constructor for component " + phpClass + " not found. (Component.factory)", new Error());
			return null;
		}

		eval('__constructor = ' + phpClass + ';');

		__comp = new __constructor(node);

		return __comp;
	}
	else
	{
		alert ('O componente ' + phpClass + ' n�o existe!');
		return null;
	}
};
if(typeof Baze != "undefined")
{
	Baze.provide("web.VisualComponent");
	
	Baze.require("web.Component");
}

/**
 * @class VisualComponent
 * @alias VisualComponent
 * @namespace Baze
 * @classDescription A classe VisualComponent define m�todos 
 * inerentes a qualquer componente vis�vel como show, hide e toogle
 * @author Saulo Vallory 
 * @version 0.9
 * 
 * @requires Component
 * 
 * @constructor
 */
VisualComponent = function VisualComponent() {
	this.isVisualComponent = true;
	// chamando o construtor da classe Component
	(Component.bind(this))();
};

Object.extend(VisualComponent.prototype, Component.prototype);
	
Object.extend(VisualComponent.prototype, {
	
	show : function () {
		this.style.set('display','');
	},
	
	hide : function () {
		this.style.set('display','none');
	},
	
	toogle : function () {
		this[this.isVisible() ? 'hide' : 'show']();
	},
	
	isVisible: function() {
	    return this.realElement.style.display != 'none';
	}
});
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
if(typeof Baze != "undefined") 
{
	Baze.provide("web.HTMLTag");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
}

/**
 * Class HTMLTag
 * 
 * @author Saulo
 * @version 0.1
 * 
 * @param {HTMLElement} elem
 */
HTMLTag = function HTMLTag(elem)
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();

	if (typeof elem == "undefined" || elem == null) {
		elem = document.createElement('htmltag');
	}
	
	this.initialize(elem);
};	
	
Object.extend(HTMLTag.prototype, VisualComponent.prototype);
Object.extend(HTMLTag.prototype, Container.prototype);

Object.extend(HTMLTag.prototype,
{	
	parentObject : null,
	
	phpClass : "HTMLTag",
			
	tagName : '',
	
	/**
	 * 
	 * @param {HTMLElement} elem
	 */
	intialize : function(elem)
	{
		this.tagName = elem.localName;
		
		this.realElement = elem;
	},
	
	/**
	 * Transforma este elemento no elemento da tag passada
	 *  
	 * ATEN��O: Essa fun��o cria um novo elemento e sobrescreve
	 * o elemento original. Todos os atributos ser�o perdidos
	 * 
	 * @param {String} tag
	 */
	setTagName : function HTMLTag_setTagName(tag) 
	{
		var elem = document.createElement(tag);
		elem.attributes = this.realElement.attributes;
		while(this.realElement.childNodes[0])
		{
			elem.appendChild(this.realElement.childNodes[0]);
		}
		
		if(this.realElement != null)
			this.realElement.parent.replaceChild(elem, this.realElement);
		
		(Container.initialize.bind(this, elem))();
		
		this.initialize(elem);
	},
	
	getTagName : function HTMLTag_setTagName()
	{
		return this.tagName;
	}
});
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
if(typeof Baze != "undefined") {
	Baze.provide("web.Literal");
	
	Baze.require("web.Component");
}

/**
 * @class Literal
 * @alias Literal
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Literal = function Literal(elem)
{
	(Component.bind(this))();
	
	if (typeof elem == "undefined") {
		var txtN;
		
		try {
			this.realElement = document.createDocumentFragment("");
		}
		catch(e) {
			Baze.raise("Text node could not be created.", e);
		}
	}
	else if(typeof elem.nodeType != "undefined") {
		switch(elem.nodeType)
		{
			case DOCUMENT_FRAGMENT_NODE :
				this.realElement = elem;
				
				for(var i=0; i < elem.childNodes.length; i++) {
					this.childNodes[i] = elem.childNodes[i];
				}
				
			break;
		
			case TEXT_NODE :
				this.realElement = this.parseHtml(elem.text || elem.textContent);
				
				for(var i=0; i < this.realElement.childNodes.length; i++) {
					this.childNodes[i] = this.realElement.childNodes[i];
				}
			break;
		}	
	}
	else if(typeof elem == "string" || typeof elem == "number")
	{
		this.realElement = this.parseHtml(elem);
		
		for(var i=0; i < this.realElement.childNodes.length; i++) {
			this.childNodes[i] = this.realElement.childNodes[i];
		}
	}
};
	
Object.extend(Literal.prototype, Component.prototype);

Object.extend(Literal.prototype, {

	childNodes : [],
	
	value : null,
	
	phpClass : "Literal",

	getId : function Literal_getId() { return this.id; },
	
	getValue : function Literal_getValue() { return this.text || this.textContent; },

	get : function Literal_get(name) 
	{
		switch(name.toLowerCase()) 
		{
			case "value" :
				return this.getValue();
				break;
				
			case "id" :
				return this.getId();
				break;
				
			default :

				if(this["realElement"] != null && typeof this["realElement"] != "undefined") {
					if(typeof this.realElement[name] != "undefined" && this.realElement[name] != null)
						return this.realElement[name];
				}

				return this[name];
		}
	},

	set : function Literal_set(name, value) {

		var oldValue = this.get(name);
		
		if(value == oldValue) return;
			
		switch(name.toLowerCase())
		{
			case "value" :
				this.setValue(value);
				break;

			case "id" :
				this.setId(value);
				break;

			default :

				if(this.realElement[name] == null || typeof this.realElement[name] == "undefined")
					this[name] = value;
				else
					this.realElement[name] = value;
		}

		if(this.get(name) != oldValue) {
			this.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : name, oldValue : oldValue});
		}
	},

	setId : function Literal_setId(id) { this.id = id; },
	
	setValue : function Literal_setValue(val) {
		this.realElement = this.parseHtml(val);
		
		this.childNodes = [];
		
		for(var i=0; i < this.realElement.childNodes.length; i++) {
			this.childNodes[i] = this.realElement.childNodes[i];
		}
	},
	
	/**
	 * 
	 * @param {String} html
	 * @return DocumentFragment
	 */
	parseHtml : function Literal_parseHtml(html)
	{	
		var tempEl = document.createElement("div");
		tempEl.innerHTML = html;
		
		Baze._findServerObjects(tempEl, Baze._serverObjs);
		
		var doc = document.createDocumentFragment();
				
		while(tempEl.hasChildNodes()) {	
		    doc.appendChild(tempEl.childNodes[0]);
		}
		
		return doc;
	}
});
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
if(typeof Baze !== "undefined")
{
	Baze.provide("web.widget.Slider");
	Baze.require("web.VisualComponent");
}

/**
 * @class Slider
 * @alias Slider
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Slider = function Slider(elem)
{
	(VisualComponent.bind(this))();

	if (typeof elem != "undefined")
	{
		//this.initialize(elem);
	}
};

Object.extend(Slider.prototype, VisualComponent.prototype);

Object.extend(Slider.prototype,
{
	parent : VisualComponent,
	
	leftUp : "",
	
	rightDown : "",
	
	oldXValue : "",
	
	oldYValue : "",
	
	tick : "",

	phpClass : "Slider",
	
	/**
	 * @param {HTMLElement}elem
	 * @return {boolean}
	 */
	initialize : function initialize (elem)
	{
		(VisualComponent.prototype.initialize.bind(this, elem))();

		var sliderbgID = elem.id;
		var sliderthumbID = elem.getElementsByTagName('div')[0].id;
		
		this.oldXValue 	= elem.getAttribute('xvalue');
		this.oldYValue 	= elem.getAttribute('yvalue');
		this.leftUp		= elem.getAttribute('leftUp');
		this.rightDown	= elem.getAttribute('rightDown');
		this.tick		= elem.getAttribute('tick');
		this.locked		= elem.getAttribute('locked');
		
		alert(	"xvalue: " + this.oldXValue + 
				' - yvalue: ' + this.oldYValue + 
				' - leftUp: ' + this.leftUp + 
				' - rightDown: ' + this.rightDown + 
				' - tick: ' + this.tick + 
				' - locked: ' + this.locked );
		alert(sliderbgID + " > " + sliderthumbID);
		
		this.realElement = YAHOO.widget.Slider.getHorizSlider(sliderbgID, sliderthumbID, this.leftUp, this.rightDown, this.tick);
		this.realElement.setValue(this.oldXValue);
		
		if (this.locked == 1)
		{			
			this.realElement.subscribe("change", this.setLock.bind(this));
			this.realElement.setValue(this.oldXValue);
		}
		
		this.realElement.subscribe("change", this.raiseChange.bind(this));
	},
	
	raiseChange : function Slider_raiseChange(offSet)
	{	
		alert ("raiseChange");
		this.onPropertyChange.raise(this, {changeType : ChangeType.PROPERTY_CHANGED, propertyName : "xvalue", oldValue : this.oldXValue});
		this.oldXValue = offSet;
	},
	
	/**
	 * @param {int} offSet
	 */
	setLock : function Slider_setLock(offSet)
	{
		this.oldXValue = offSet;
		
		this.realElement.lock();
	}
});
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
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.FormField");
}

FormField = function(){};

Object.extend(FormField.prototype,  
{
	checkChanges : function TextBox_checkChanges()
	{
		// this function should be overwrited in child class
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.CheckBox");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class CheckBox
 * @alias CheckBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
CheckBox = function CheckBox(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement("input");
		elem.type = "checkbox";
	}	
	
	this.initialize(elem);
};

Object.extend(CheckBox.prototype, VisualComponent.prototype);
Object.extend(CheckBox.prototype, FormField.prototype);

Object.extend(CheckBox.prototype, 
{
	parent : VisualComponent,
	
	phpClass : "CheckBox",
	
	/**
	 * @private
	 * @type {Boolean} elem
	 */
	_oldCheckedValue : "",
	
	actualCheckedValue : "",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function CheckBox_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		this.realElement = elem;
		
		this.actualCheckedValue = elem.checked;
		this._oldCheckedValue = elem.checked;
		
		elem.onclick = this._raiseChange.bind(this);
	},
	
	/**
	 * @method _raiseChange
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this._oldCheckedValue});
		
		this._oldCheckedValue = this.actualCheckedValue; 
		this.actualCheckedValue = this.get("checked");
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FieldSet");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FieldSet
 * @alias FieldSet
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FieldSet = function FieldSet(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('fieldset');
	}
	
	this.initialize(elem);
};
	
Object.extend(FieldSet.prototype, VisualComponent.prototype);
Object.extend(FieldSet.prototype, FormField.prototype);

Object.extend(FieldSet.prototype,
{
	parent : VisualComponent,
	
	legend : null,
	
	items : null,
	
	phpClass : "FieldSet",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FieldSet_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;
		
		//Por padr�o, a propriedade "id" do elemento LEGEND � o id do elemento FIELDSET mais uma constante string "Legend" 
		this.legend = document.getElementById(elem.id + "Legend" );

		this.items = [];
	},
	
	/**
	 * @param {Object} obj
	 */
	addItem : function FieldSet_addItem (obj, noRaise)
	{
		//Adicionando Objeto
		this.items[this.items.length] = obj;
		
		//Adicionando Elemento HTML
		this.realElement.appendChild(obj.realElement);
		
		if (noRaise == undefined)
			this.onChildAdd.raise(this,{changeType : ChangeType.CHILD_ADDED, child : obj});
	},
	
	/**
	 * @param {Object} obj
	 * @param {boolean} noRaise
	 */
	removeItem : function FieldSet_removeItem (obj, noRaise)
	{
		j = false;
		
		for (var i = 0; i<this.items.length && j!=false; i++)
		{
			if (this.items[i].get("id") == objectItem.get("id"))
				j = i;
		}
		
		if(j != false)
			return this.removeItemByIndex(j, noRaise);
		
		return false;	
	},
	
	/**
	 * @param {int} i
	 * @param {boolean} noRaise
	 */
	removeItemByIndex : function FieldSet_removeItemByIndex(i, noRaise)
	{
		if (i>0 && i<(this.items.length - 1))
		{
			var aux = this.items[i];
			var auxId = aux.get("id");
			
			//Removendo Objeto
			this.items.splice(i,i+1);
			
			//Removendo Elemento HTML
			this.realElement.removeChild(aux.realElement);
			
			if ( noRaise == undefined || noRaise == false)
				this.onChildRemove.raise( this, {changeType : ChangeType.CHILD_REMOVED, child : aux} ); 
			
			return true;
		}
		return false;
	},
	
	/**
	 * @return {boolean}
	 */
	removeLegend : function FieldSet_removeLegend()
	{
		if (this.legend !== null)
		{
			this.legend.parentNode.removeChild(this.legend);
			this.legend = null;
			
			return true;
		}
		
		return false;
	},
	
	/**
	 * @param {HTMLElement} legend
	 */
	setLegend : function FieldSet_setLegend(legend)
	{
		if (typeof(legend) == "object")
		{
			this.legend = legend;
		}
		else if (typeof(legend) == "string")
		{
			legend = document.getElementById(legend);
		}
		
		this.realElement.appendChild(legend);
	}
	
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Form");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Form =function Form(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();		
	
	this.items = [];
	this.modifiedItems = [];
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('form');
	}
	
	this.initialize(elem);
};
	
Object.extend(Form.prototype, VisualComponent.prototype);
Object.extend(Form.prototype, Container.prototype);	
Object.extend(Form.prototype, FormField.prototype);

Object.extend(Form.prototype,
{
	parent : VisualComponent,
	
	items : null,
	
	modifiedItems : null,
	
	phpClass : "Form",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function Form_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == 'form')
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
			
			if (window.attachEvent) // IE
			{
				var oldSubmit = this.realElement.onsubmit; // estranhamente, se jogar direto pra this.onChangeListeners n�o funciona no IE
				this.realElement.onsubmit = null;
				
				// no IE o �ltimo evento adicionado USANDO attachEvent � o primeiro 
				// a ser executado. Eventos adicionados pelo html s�o executados 
				// antes dos eventos adicionados por attachEvent
				this.realElement.attachEvent('onsubmit', oldSubmit);
				this.realElement.attachEvent('onsubmit', this.checkFields.bind(this));
			}
			
			return true;
		}
		
		return false;
	},
	
	/**
	 * Check fields for modifications
	 */
	checkFields : function Form_recheckFields()
	{
		var fields = this.realElement.elements;
		
		for(var i=0; i < fields.length; i++)
		{
			var comp = $C(fields[i].id);
			
			if(comp != null)
				comp.checkChanges();
		}
	},
	 
	/**
	 * @param {Object} item
	 */
	addItem : function Form_addItem (item, noRaise)
	{
		//Adicionando Objeto
		this.items[this.optionItems.length] = item;
		
		//Adicionando Element HTML
		this.realElement.appendChild(item.realElement);
		
		if (noRaise == undefined || noRaise == false)
			this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : item } );
	},
	
	/**
	 * @param {Object} item
	 * @return boolean
	 */
	removeItem : function Form_removeItem (objectItem, noRaise)
	{
		var j = false;
		
		for (var i = 0; i<this.items.length && j!=false; i++)
		{
			if (this.items[i].get("id") == objectItem.get("id"))
				j = i;
		}
		
		if(j != false)
			return this.removeItemByIndex(j, noRaise);
		
		return false;			
	},
	
	/**
	 * @param {int} i
	 * @return boolean
	 */
	removeItemByIndex : function Form_removeItemByIndex (i, noRaise)
	{
		if (i>0 && i<(this.items.length - 1))
		{
			var aux = this.items[i];
			var auxId = aux.get("id");
			
			//Removendo Objeto
			this.items.splice(i,i+1);
			
			//Removendo Elemento HTML
			aux.realElement.parentNode.removeChild(aux.realElement);
			
			if ( noRaise == undefined || noRaise == false )
				this.onChildRemove.raise( this, {changeType : ChangeType.CHILD_REMOVED, child : aux} ); 
			
			return true;
		}
		return false;
	},

	/**
	 * @private
	 * @param {Event} e
	 */	
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.realElement.value;
	}
});
/**
 * @author Luciano
 */
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FormButton");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FormButton
 * @alias FormButton
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FormButton = function FormButton(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'button';
	}
	
	this.initialize(elem);
};
	
Object.extend(FormButton.prototype, VisualComponent.prototype);
Object.extend(FormButton.prototype, FormField.prototype);

Object.extend(FormButton.prototype,
{
	parent : VisualComponent,
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FormButton_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "button")
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
		}
		else
		{
			alert ("Element " + elem.id + " not a FormButtom Type!");
		}
	}
	
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.FormImage");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class FormImage
 * @alias FormImage
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
FormImage = function FormImage(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'image';
	}
	
	this.initialize(elem);
};
	
Object.extend(FormImage.prototype, VisualComponent.prototype);
Object.extend(FormImage.prototype, FormField.prototype);

Object.extend(FormImage.prototype,
{
	parent : VisualComponent,
	
	phpClass : "FormImage",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function FormImage_initialize (elem)
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
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.HiddenField");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class HiddenField
 * @alias HiddenField
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
HiddenField = function HiddenField(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'hidden';
	}
	
	this.initialize(elem);
};

Object.extend(HiddenField.prototype, VisualComponent.prototype);
Object.extend(HiddenField.prototype, FormField.prototype);

Object.extend(HiddenField.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	phpClass : "HiddenField",

	/**
	 * @param {HTMLElement}elem
	 */
	initialize : function HiddenField_initialize (elem)
	{
		if (elem.type.toLowerCase() == "hidden")
		{
			(Component.initialize.bind(this, elem))();
			this.oldValue = elem.value;
			this.realElement = elem;
			
			Event.observe(elem, "change", this._raiseChange.bind(this));
			
			return true;
		}
		
		return false; 
	},

	/**
	 * @param {Event} e
	 */
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.realElement.value;
	}
});
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
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Label");
	
	Baze.require("web.VisualComponent");	
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

Label = function Label(elem)
{
	(FormField.bind(this))();
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('label');
	}
	
	this.initialize(elem);
};
	
Object.extend(Label.prototype, VisualComponent.prototype);
Object.extend(Label.prototype, Container.prototype);
Object.extend(Label.prototype, FormField.prototype);
	
Object.extend(Label.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Label",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function Label_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			Baze.raise("Error in initialize Label component.", new Error("Param elem is not defined in Label__initialize."));
		}
		else
		{	
			this.realElement = elem;
		}
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.OptionItem");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

/**
 * @class OptionItem
 * @alias OptionItem
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
OptionItem = function OptionItem(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();		
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('option');
	}
	this.initialize(elem);
};
	
Object.extend(OptionItem.prototype, VisualComponent.prototype);
Object.extend(OptionItem.prototype, Container.prototype);	
Object.extend(OptionItem.prototype, FormField.prototype);

Object.extend(OptionItem.prototype, 
{
	parent : VisualComponent,
	
	parentObject : null,
	
	phpClass : "OptionItem",
	
	oldSelectedValue : null,
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function OptionItem_initialize(elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			var elem = document.createElement('option');
		}
		this.realElement = elem;
				
		this.oldSelectedValue = this.realElement.selected;
		
		elem.onchange = this._raiseChange.bind(this);
	},
	
	getParentObject : function OptionItem_getParentObject()
	{
		return this.parentObject;
	},

	/**
	 * @param {Object} obj
	 */		
	setParentObject : function OptionItem_setParentObject(obj)
	{
		this.parentObject = obj;
	},
	
	/**
	 * @return boolean
	 */
	isSelected : function OptionItem_isSelected()
	{
		return (this.realElement.selected === true);
	},
	
	/**
	 * @param {Boolean} trueOrFalse
	 *
	setSelected : function OptionItem_setSelected(trueOrFalse, noRaise)
	{
		this.realElement.selected = trueOrFalse;
		this.set('selected',trueOrFalse);
	},*/
	
	/**
	 * @param string
	 */
	setText : function OptionItem_setText(textValue)
	{
		this.removeChildren();
		
		this.addChild(textValue, true);
		
		this.realElement.innerHTML = textValue;
	},
	
	/**
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selected", oldValue : this.oldSelectedValue});
		this.oldSelectedValue = this.realElement.selected;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.DropDownList");
	
	Baze.require("web.form.OptionItem");
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
	Baze.require("web.form.FormField");
}

/**
 * @class DropDownList
 * @alias DropDownList
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.Container
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * @requires Baze.web.form.OptionItem
 * 
 * @param {HTMLElement} elem
 */
DropDownList = function DropDownList(elem) 
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined")
		elem = document.createElement("select");
	
	this.initialize(elem);
	this.options = this.children;
};

Object.extend(DropDownList.prototype, VisualComponent.prototype);
Object.extend(DropDownList.prototype, FormField.prototype);
Object.extend(DropDownList.prototype, Container.prototype);
	
Object.extend(DropDownList.prototype, 
{	
	parent : VisualComponent,
	
	options : null,
	
	oldSelectedIndex : null,
	
	phpClass : "DropDownList",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function DropDownList_initialize(elem) 
	{
		if(typeof elem["tagName"] == "undefined" || elem.tagName.toLowerCase() != 'select')
		{
			console.error("DropDownList::addOption :> First parameter should be an HTMLSelectElement");
			return false;
		}
		
		(Component.prototype.initialize.bind(this, elem))();
		
		this.realElement = elem;

		// carregando os filhos
		for(var i=0; i < elem.options.length; i++)
		{
			var op = Baze.getComponentById(elem.options[i].id);
			
			if (op == null)
			{
				op = new OptionItem(elem.options[i]);
				Baze.addComponent(op);
			} 
			
			this.addChild(op, true);
			op.setParentObject(this);
		}
			
		//definindo valores iniciais
		this.oldSelectedIndex = elem.selectedIndex;
		
		var oldOnChange = this.realElement.onchange; // estranhamente, se jogar direto pra this.onChangeListeners n�o funciona no IE
		this.realElement.onchange = null;
		
		if (window.addEventListener) // Mozilla like
		{
			if(oldOnChange)
				this.onChangeListeners = oldOnChange;
				
			this.realElement.addEventListener('change', this._raiseChange.bind(this),false);
		}
		else if (window.attachEvent) // IE
		{				
			if(oldOnChange)
				this.onChangeListeners = oldOnChange;
				
			this.realElement.attachEvent('onchange', this._raiseChange.bind(this));
		}
		
		return true;
	},
	
	/**
	 * @param {OptionItem, HTMLElement} opt
	 * @return boolean
	 */
	addOption : function DropDownList_addOptionItem (opt, noRaise)
	{
		if(!opt || typeof opt != "object" || (Baze.isComponent(opt) && opt.get("phpClass") != "OptionItem"))
		{
			console.error("DropDownList::addOption :> First parameter should be an HTMLOptionElement or an OptionItem");
			return false;
		}
		
		if(typeof opt["tagName"] != "undefined")
		{
			if(opt.tagName.toLowerCase() != "option")
			{
				console.error("DropDownList::addOption :> First parameter should be an HTMLOptionElement or an OptionItem");
				return false;
			}

			if(!opt.id)
			{
				console.error("DropDownList::select :> It's impossible to find the component for an element without id");
				return false;
			}

			var comp = Baze.getComponentById(opt.id);
			
			if(!comp)
			{
				console.warn("DropDownList::select :> Component for the element " + opt.id + " could not be found. Creating a new one.");
				opt = new OptionItem(opt);
			}
			else
				opt = comp;
		}
					
		//Adicionando Objeto	
		this.options.add(this.options.count(),opt);
		
		//Adicionando Elemento HTML
		if(Baze.environment.browser.name == "IE")
			this.realElement.add(opt.realElement);
		else
			this.realElement.add(opt.realElement, null);
		
		//Adjustando propriedade "parentObject"
		opt.setParentObject(this);
		
		if (noRaise == undefined || noRaise == false)
		{
			this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : opt});
		}
		
		return true;
	},

	/**
	 * @param {int} i
	 */
	getOption : function DropDownList_getOption(i)
	{
		return this.options.get(i);
	},
	
	getSelectedOption : function DropDownList_getSelectedOption()
	{
		var index = this.realElement.selectedIndex;
		
		if(index < 0) return null;
		
		return this.options.get(this.realElement.selectedIndex);
	},
	
	/**
	 * Remove uma opc�o por �ndice, pelo Elemento HTML ou pelo pr�prio objeto
	 * @param {int,HTMLElement,OptionItem} obj
	 * @return OptionItem
	 */
	removeOption : function DropDownList_removeOption(elem, noRaise)
	{
		var index = -1;
		
		if(typeof elem == "number") {
			index = elem;
		}
		else if(typeof obj == "object" && Baze.isComponent(obj)) {
			index = elem.get("index");
		}
		else if(typeof elem.tagName == "undefined" && elem.tagName.toLowerCase() == 'option')	{
			index = elem.index;
		}
		
		if(index < 0 || index > this.options.count())
			return null;

		this.realElement.remove(index);
		var opt = this.options.remove(index);
		
		if (noRaise == undefined || noRaise == false)
		{
			this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : opt} );
		}
			
		return opt;
	},
	
	/**
	 * @param {OptionItem,int} opt
	 * @return {boolean}
	 */
	select : function DropDownList_select(opt)
	{
		if(typeof opt == "number")
		{
			if(opt < 0) {
				console.warn("DropDownList::select :> The index should positive");
				return false;
			}
			if(opt > this.options.count()) {
				console.warn("DropDownList::select :> Index out of bounds");
				return false;
			}

			opt = this.options.get(opt);
		}
		else if(typeof opt != "object") {
			return false;
		}		
		else if(typeof opt["tagName"] != "undefined" && opt.tagName.toLowerCase() == "option")
		{
			if(!opt.id)
			{
				console.error("DropDownList::select :> It's impossible to find the component for an element without id");
				return false;
			}
				
			var comp = Baze.getComponentById(opt.id);
			
			if(!comp)
			{
				console.warn("DropDownList::select :> Component for the element " + opt.id + " could not be found. Creating a new one.");
				opt = new OptionItem(opt);
			}
			else
				opt = comp;
		}
		
		this.oldSelectedIndex = this.realElement.selectedIndex;
		this.realElement.selectedIndex = opt.get("index");
		
		this.onPropertyChange.raise(this, {changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
		
		return true;
	},
	
	/**
	 * @param {Event} e
	 * @private
	 */
	_raiseChange: function _raiseChange(e)
	{	
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
	
		this.oldSelectedIndex = this.realElement.selectedIndex;
		
		if(this.onChangeListeners)
			this.onChangeListeners(e);
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.ListBox");
	
	Baze.require("web.form.OptionItem");
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
	Baze.require("web.form.FormField");
}

/**
 * @class ListBox
 * @alias ListBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
ListBox = function ListBox(elem) 
{
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	(FormField.bind(this))();
	
	if ( (typeof elem == "undefined") || elem == null)
	{
		var elem = document.createElement('select');
	}
	
	this.initialize(elem);
};

Object.extend(ListBox.prototype, VisualComponent.prototype);
Object.extend(ListBox.prototype, FormField.prototype);
Object.extend(ListBox.prototype, Container.prototype);	

Object.extend(ListBox.prototype, 
{	
	parent : VisualComponent,
	
	isMultiple : null,
	
	optionItems : [],
	oldSelectedOptionItems : [],
	selectedOptionItems : null,
	
	oldSelectedIndex : null,
	selectedIndex : null,
	
	phpClass : "ListBox",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function ListBox_initialize (elem)
	{
		if ( (elem != null)  &&  elem.tagName.toUpperCase() == 'SELECT')
		{
			// construtor da classe pai 
			(Component.prototype.initialize.bind(this, elem))();
			
			this.realElement = elem;
			
			// instanciando arrays de op��es e �ndices
			this.optionItems = [];
			
			if( elem.multiple )
			{
				this.oldSelectedOptionItems = [];
				this.selectedOptionItems = [];
				
				this.oldSelectedIndex = [];
				this.selectedIndex = [];
			}
			else
			{
				this.oldSelectedOptionItems = null;
				this.selectedOptionItems = elem.selectedIndex > -1 ? elem.options[elem.selectedIndex] : null;
				
				this.oldSelectedIndex = null;
				this.selectedIndex = elem.selectedIndex;
			}
			
			this.isMultiple = elem.multiple;

			// instanciando as op��es do select
			for (var i=0; i < elem.options.length; i++)
			{
				// verifica se o componente j� foi instanciado
				var op = Baze.getComponentById(elem.options[i].id);
				
				// se n�o foi, cria e adiciona
				if (typeof op == "undefined" || op == null)
				{
					op = new OptionItem(elem.options[i]);
					Baze.addComponent(op);
				} 
				
				// adiciono o filho e a referencia para o pai					
				this.optionItems[this.optionItems.length] = op;					
				op.setParentObject(this);					
				
				// pegando o array de itens selecionados, caso o valor seja "multiple"
				if (this.isMultiple)
				{
					if (op.realElement.selected == true)
					{
						this.selectedIndex.push(i);
						this.selectedOptionItems.push(elem.options[i]);
					}						
				}
			}
/*
			if ((this.selectedOptionItems == null || this.selectedOptionItems.length == 0) 
					&& this.optionItems.length > 0 )
			{
				this.setSelectedOption(this.optionItems[0]);
			}
*/

			var oldOnChange = this.realElement.onchange; // estranhamente, se jogar direto pra this.onChangeListeners n�o funciona no IE
			this.realElement.onchange = null;
			
			if (window.addEventListener) // Mozilla like
			{
				if(oldOnChange)
					this.onChangeListeners = oldOnChange;
					
				this.realElement.addEventListener('change', this._raiseChange.bind(this),false);
			}
			else if (window.attachEvent) // IE
			{				
				if(oldOnChange)
					this.onChangeListeners = oldOnChange;
					
				this.realElement.attachEvent('onchange', this._raiseChange.bind(this));
			}
			
			return true;
		}
		return false;
	},

	/**
	 * @param {HTMLElement} elem
	 * @param {boolean} noRaise
	 * 
	 * @return {boolean}
	 */		
	addOption : function ListBox_addOption (elem, noRaise)
	{
		if (elem.tagName.toLowerCase() == "option")
		{
			var op = new OptionItem(elem);
			
			return this.addOptionItem(op, noRaise);
		}

		return false;
	},

	/**
	 * @classDescription Adicionando um novo OptionItem
	 * @param {OptionItem} op
	 * @param {boolean} noRaise
	 */
	addOptionItem : function ListBox_addOptionItem (op, noRaise)
	{
		if (op.get("tagName") == "OPTION")
		{
			if (op.get("selected"))
				this.setSelectedOption(op);
		
			//Adicionando Objeto
			this.optionItems[this.optionItems.length] = op;
			
			//Adicionando Elemento HTML
			this.realElement.add(op.realElement, null);
			
			//Setando propriedade "parentObject"
			op.setParentObject(this);
			
			if (typeof(noRaise) == "undefined" || noRaise == false)
				this.onChildAdd.raise(this, {changeType : Change.CHILD_ADDED, child : op} );
			
			return true;	
		}
		return false;
	},
	
	changeSelected : function ListBox_changeSelected ()
	{
		if(!this.isMultiple)
		{
			this.oldSelectedIndex = this.selectedIndex;
			this.selectedIndex = this.realElement.selectedIndex;

			if(this.selectedIndex != -1)
			{
				this.oldSelectedOptionItems = this.selectedOptionItems;
				this.selectedOptionItems = this.realElement.options[this.selectedIndex];
			}
		}
		else
		{
			var newSelected = [];
			var newSelectedIndex = [];

			for (var i = 0; i < this.realElement.options.length; i++) {
				if (this.realElement.options[i].selected == true) {
					newSelected.push(this.optionItems[i]);
					newSelectedIndex.push(i);
				}
			}
			
			this.oldSelectedIndex = this.selectedIndex;
			this.selectedIndex = newSelectedIndex;

			this.oldSelectedOptionItems = this.selectedOptionItems;
			this.selectedOptionItems = newSelected;
		}
	},

	/**
	 * @return {[int] | [array]}
	 */
	getOldValue : function ListBox_getOldValue ()
	{
		if (this.isMultiple == false)
		{
			return this.oldSelectedIndex;
		}
		
		var arraySelInd = [];
		
		for (var i = 0; i < this.oldSelectedOptionItems.length; i++)
		{
			arraySelInd[i] = this.oldSelectedOptionItems[i].get("index");
		}
		
		return arraySelInd;
	},
	
	getSelectedIndex : function ListBox_getSelectedIndex()
	{
		return this.realElement.selectedIndex;
	},
	
	/**
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	isChild : function ListBox_isChild (op)
	{
		var found = false;
		
		for (var i = 0; (i < this.optionItems.length) && found == false; i++)
		{
			if (this.optionItems[i].get("id") == op.get("id"))
				found = true;
		}
		
		return found;
	},

	/**
	 * @classDescription Se o objeto estiver selecionado ent�o retorna o seu indice no array de elementos selecionados
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	isSelected : function ListBox_isSelected (op)
	{
		var indexSelected = -1;
		
		if (this.isMultiple)
		{
			
			for (var i=0; (i < this.selectedOptionItems.length) && (indexSelected == -1); i++)
			{
				if (this.selectedOptionItems[i].get("id") == op.get("id"))
					indexSelected = i;
			}
		}
		else
		{
			if (this.isChild(op) && this.selectedIndex == op.get("index"))
				indexSelected = op.get("index");
		}
		
		return indexSelected;
	},

	/**
	 * @classDescription Removendo, por �ndice, um OptionItem do array "optionItems"
	 * @param {int} i
	 * @return {boolean}
	 */
	removeOptionByIndex : function ListBox_removeOptionByIndex (i, noRaise)
	{
		if (0<=i && i<this.optionItems.length)
		{
			var aux = this.optionItems[i];
			var auxId = aux.get("id");
			
			//Removendo Objeto
			this.optionItems.splice(i,i+1);
			
			if (aux.get("selected"))
				this.setUnselectedOption(aux);
			
			//Removendo Elemento HTML
			this.realElement.remove(i);
			
			if (noRaise == undefined || noRaise == false)
				this.onChildRemove.raise(this, {changeType : Change.CHILD_REMOVED, child : aux});
			
			return true;
		} 
		return false;
	},

	
	/**
	 * @classDescription Removendo, por OptionItem, um OptionItem do array "optionItems"
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	removeOptionItem : function ListBox_removeOptionItem (op, noRaise)
	{
		if (op.get("tagName") == 'OPTION')
		{
			var ind = this.isSelected(op);
			 
			if (ind != -1)
				this.setUnselectByIndex(ind);
			
			return this.removeByIndex(op.realElement.get("index"), noRaise);
		}
		return false;
	},
	
	/**
	 * @param {OptionItem} op
	 * @param {array} args
	 * @return {boolean}
	 */
	setSelectedOption : function ListBox_setSelectedOption(op)
	{
		if (op.get("tagName").toLowerCase() == "option")
		{
			op.set("selected",true);
			
			if (this.isMultiple == true)
			{
				this.oldSelectedOptionItems = this.selectedOptionsItems;
				this.selectedOptionsItems[this.selectedOptionsItems.length] = op;
			}
			else
			{
				this.oldSelectedIndex = this.selectedIndex;
				this.selectedIndex = op.get("index");
			}
			
			return true;
		}
		return false;
	},

	
	/**
	 * @param {OptionItem} op
	 * @return {boolean}
	 */
	setUnselect : function ListBox_setUnselect(op)
	{			
		if (op.get("tagName").toLowerCase() == "option")
		{
			op.set("selected",false);
			
			if (this.isMultiple == true)
			{
				var ind = this.isSelected(op);
				
				if (ind != -1)
				{
					this.oldSelectedOptionItems = this.SelectedOptionsItems;
					this.selectedOptionsItems.splice(ind,1);
				}
			}
			else
			{
				this.oldSelectedIndex = this.selectedIndex;
				this.selectedIndex = this.get("selectedIndex");
			}
			
			return true;
		}
		return false;
	},
	
	checkChanges : function TextBox_checkChanges()
	{
		if(this.oldSelectedIndex !== this.realElement.selectedIndex)
		{
			this.changeSelected();
		
			this.onPropertyChange.raise(this, {changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
		}
	},
	
	/**
	 * @param {Event} e
	 */
	_raiseChange : function ListBox_raiseChange(e)
	{
		this.changeSelected();
		
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "selectedIndex", oldValue : this.oldSelectedIndex} );
		
		if(this.onChangeListeners)
			this.onChangeListeners(e);
		
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Password");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Password
 * @alias Password
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Password = function Password(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'password'; 
	}
	
	this.initialize(elem);
};
	
Object.extend(Password.prototype, VisualComponent.prototype);
Object.extend(Password.prototype, FormField.prototype);

Object.extend(Password.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	phpClass : "PasswordField",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "password")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.value;
			this.realElement = elem;
			
			elem.onchange = this._raiseChange.bind(this);
			
			return true;
		}
		return false;
	},
	
	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.get("value");
	}
});
if(typeof Baze !== 'undefined')
{
	Baze.provide("web.form.RadioGroup");

	Baze.require("web.Component");
	Baze.require("web.form.FormField");
	Baze.require("web.Container");
	
	// Note: Possui uma depend�ncia circular com RadioButton
}

/**
 * @class RadioGroup
 * @alias RadioGroup
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * @requires Baze.web.Container
 * @requires Baze.web.form.Radio
 */
RadioGroup = function RadioGroup()
{
	(Component.bind(this))();
	(Container.bind(this))();		
	(FormField.bind(this))();		
	
	this.radios = [];		
};

Object.extend(RadioGroup.prototype, Component.prototype);
Object.extend(RadioGroup.prototype, FormField.prototype);	
Object.extend(RadioGroup.prototype, Container.prototype);


Object.extend(RadioGroup.prototype,
{
	radios : null,
	
	groupName : null,
	
	oldCheckedRadio : null,
	
	phpClass : "RadioGroup",
	
	/**
	 * @param {Array} radios
	 */
	initialize : function RadioGroup_initialize (groupName)
	{
//		(Component.prototype.initialize.bind(this))();
		
		this.setGroupName(groupName);
		
		//Chamada abaixo comentada, pois cada radio � respons�vel em se inscrever em seu RadioGroup 
		//this.findRadios(groupName);	
	},
	
	/**
	 * @classDescription Adiciona novo membro ao grupo. O flag "forceChangeName" � boleano, mudar� a propriedade "name" do Radio recebido
	 *  
	 * @param {Radio} rb
	 * @param {boolean} forceChangeName
	 * @return {boolean}
	 */
	addRadio : function RadioGroup_addRadio (rb, forceChangeName, noRaise)
	{
		//Somente objeto Radio com o mesmo valor na propriedade "name" podem ser adicionado ao RadioGroup.
		//Caso necessite, o par�metro "forceChangeName" altera a propriedade "name" do elemento HTML
		if ( (forceChangeName == null || forceChangeName == 0) && rb.get("name") != this.groupName)
			return false;
		
		rb.set("name", this.groupName);
		
		//Se Radio estiver marcado, atualizar valor antigo e atual
		if (rb.get("checked"))
		{	
			this.oldCheckedRadio = rb;
		}
		
		//Adicionando Objeto Radio
		this.radios[this.radios.length] = rb;

		//Setando o grupo no Objeto Radio			
		rb.setRadioGroup(this);
		
		
		if (noRaise == undefined || noRaise == false)
		{
			this.onChildAdd.raise(this, {changeType : ChangeType.CHILD_ADDED, child : rb});
		}
		
		return true;
	},
	
	
	/**
	 * @classDescription Percorre todo o documento buscando os 'radios' que cont�m o nome da propriedade "groupName" 
	 * @param {String} groupName
	 */
	findRadios : function RadioGroup_findRadios (groupName)
	{
		this.radios.splice(0);

		if (groupName == undefined)			
			var radios = document.getElementsByName(this.groupName);
		else
		{
			this.setGroupName(groupName);
			var radios = document.getElementsByName(groupName);
		}
		
		var numRadios  = radios.length;
		
		for (var i=0; i < numRadios; i++)
		{
			var rb = Baze.getComponentById(radios[i].id);
			
			if (typeof(rb) !== "object")
			{
				var rb = new RadioButton(radios[i]);
				Baze.addComponent(rb);
			}
			this.addRadio(rb,0,true);
		}
	},
	
	/**
	 * @classDescription Um dos 'radios' sofreu um evento de altera��o, geralmente um "onclick" ("onchange" por ter recebido um "onclick").
	 * O elemnto avisa ao seu RadioGroup que mudou. O RadioGroup chama o 'raiseChange' do elemento "elementChecked",
	 * chama o 'raiseChange' do elemento que mudou e guarda este novo elemento em "elementChecked"
	 *  
	 * @param {Event} e
	 * @param {Radio} rb
	 * @return {boolean}
	 */
	_raiseChange : function RadioGroup_raiseChange (rb, e)
	{
		if (rb == this.oldCheckedRadio)
		{
			return false;
		}

		this.oldCheckedRadio.forceRaiseChange(e);
		
		this.onPropertyChange.raise(this.oldCheckedRadio, {event : e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this.oldCheckedRadio.get("id") });
		
		this.oldCheckedRadio = rb;
		
		return true;
	},

	/**
	 * @return {string}
	 */		
	getOldValue : function RadioGroup_getOldValue ()
	{
		return this.oldCheckedRadio.get("value");
	},
	
	/**
	 * @param {Radio} r
	 * @return {boolean}
	 */
	removeRadio : function RadioGroup_removeRadio (r, noRaise)
	{
		var found = false;

		for (var i = 0; i<this.radios.length && found == false; i++)
		{
			if (this.radios[i].get("id") == r.get("id"))
				found == true;
		}
		
		if (found == true)
		{				
			var aux = this.radios[i];
			var auxId = aux.get("id");

			//Removendo Objeto				
			this.radios.splice(i,i+1);
			
			//Removendo Elemento HTML
			aux.realElement.parentNode.removeChild(aux.realElement);
			
			if (noRaise == undefined || noRaise == false)
			{
				this.onChildRemove.raise(this, {changeType : ChangeType.CHILD_REMOVED, child : aux} );
			}
			
			return true;
		}
		
		return false;
	},
	
	/**
	 * @classDescription define novo nome do grupo de 'radios' para o RadioGroup
	 * @param {String} newGroupName
	 */
	setGroupName : function RadioGroup_setGroupName ( newGroupName )
	{
		this.groupName = newGroupName;
	}		
});
if(typeof Baze !== 'undefined')
{
	Baze.provide("web.form.RadioButton");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
	Baze.require("web.form.RadioGroup");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 *
 * @param {Object} elem
 */
RadioButton = function RadioButton(elem) 
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == 'undefined' || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'radio';
	}
	
	this.initialize(elem);
};

Object.extend(RadioButton.prototype, VisualComponent.prototype);
Object.extend(RadioButton.prototype, FormField.prototype);

Object.extend(RadioButton.prototype,
{
	parent : VisualComponent,
	
	radioGroup : null,
	
	oldValue : null,
	
	phpClass : "RadioButton",

	/**
	 * @method initialize
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function Radio_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "radio")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.checked;	
			this.realElement = elem;
			
			if (this.radioGroup == null)
			{
				var rdg = Baze.getComponentById(elem.name);
				
				if (rdg == null)
				{
					rdg = new RadioGroup();
					
					rdg.initialize(elem.name);
					
					rdg.setId(elem.name);
					
					Baze.addComponent(rdg);
				}
				
				this.radioGroup = rdg;
			}
			
			elem.onchange = this._raiseChange.bind(this);
			
			//Adicionando o radio criado ao seu grupo
			return this.radioGroup.addRadio(this, false, true);
		}			
		return false;
	},
	
	/**
	 * @param {Event} e
	 */
	forceRaiseChange : function Radio_forceRaiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this.oldValue});
		this.oldValue = this.get("checked"); 
	},
	
	/**
	 * @method getRadioGroup
	 * @return {RadioGroup}
	 */
	getRadioGroup : function Radio_getRadioGroup()
	{
		return this.radioGroup;
	},

	/**
	 * @method setRadioGroup
	 * @param {RadioGroup} radioGroup
	 */		
	setRadioGroup : function Radio_setRadioGroup(radioGroup)
	{
		this.radioGroup = radioGroup;
	},
	
	/**
	 * @param {Event} e
	 * @private
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "checked", oldValue : this.oldValue});

		if (this.radioGroup !== null)
		{		
			this.radioGroup._raiseChange(this, e);
		}
		else
		{
			alert('radioGroup � nulo');
		}
		
		this.oldValue = this.get("checked");
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Reset");
		
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Reset
 * @alias Reset
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Reset = function Reset(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined" || elem == null)
	{
		var elem = document.createElement('input');
		elem.type = 'reset';
	}
	
	this.initialize(elem);
};

Object.extend(Reset.prototype, VisualComponent.prototype);
Object.extend(Reset.prototype, FormField.prototype);

Object.extend(Reset.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Reset",
	
	/**
	 * @param {HTMLElement} elem
	 */		
	initialize : function Reset_initialize (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "reset")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.realElement = elem;
			
			return true;
		}
		
		return false;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Submit");
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class Submit
 * @alias Submit
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
Submit = function Submit(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('input');
		elem.type = 'submit';
	}
	
	this.initialize(elem);
};

Object.extend(Submit.prototype, VisualComponent.prototype);
Object.extend(Submit.prototype, FormField.prototype);

Object.extend(Submit.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Submit",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function (elem)
	{
		if (elem.tagName.toLowerCase() == "input" && elem.type.toLowerCase() == "submit")
		{
			(Component.prototype.initialize.bind(this, elem))();
			this.realElement = elem;
			
			return true;
		}
		return false;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.TextArea");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.Container");
	Baze.require("web.form.FormField");
}

/**
 * @class TextArea
 * @alias TextArea
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
TextArea = function TextArea(elem)
{
	(VisualComponent.bind(this))();
	(FormField.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('textarea');
	}
	
	this.initialize(elem);	
};

Object.extend(TextArea.prototype, VisualComponent.prototype);
Object.extend(TextArea.prototype, FormField.prototype);
Object.extend(TextArea.prototype, Container.prototype);

Object.extend(TextArea.prototype,  
{
	parent : VisualComponent,
	
	actualValue : "",
	
	oldValue : "",
	
	phpClass : "TextArea",
	
	/**
	 * @param {HTMLElement} elem
	 * @return {boolean}
	 */
	initialize : function TextArea_initialize(elem)
	{		
		if (elem.tagName.toLowerCase() == "textarea")
		{
			(Component.prototype.initialize.bind(this, elem))();
			
			this.oldValue = elem.value;
			this.actualValue = elem.value;
			this.realElement = elem;
			
			elem.onchange = this._raiseChange.bind(this);
			
			return true;
		}
		
		Baze.raise("n�o foi poss�vel criar o textarea");
		
		return false;
	},
	
	removeChildren : function TextArea_removeChildren()
	{
		this.realElement.innerHTML = '';
		this.realElement.value = '';
		
		this._raiseChange.bind(this);
	},

	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange: function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.actualValue;
		this.actualValue = this.realElement.value;
	}
});
if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.TextBox");	
	
	Baze.require("web.VisualComponent");
	Baze.require("web.form.FormField");
}

/**
 * @class TextBox
 * @alias TextBox
 * @namespace Baze.web.form
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires Baze.web.VisualComponent
 * @requires Baze.web.form.FormField
 * 
 * @param {HTMLElement} elem
 */
TextBox = function TextBox(elem)
{
	(FormField.bind(this))();
	(VisualComponent.bind(this))();
	
	if (typeof elem == "undefined") 
	{
		elem = document.createElement("input");
		elem.type = "text";
	}
	
	this.initialize(elem);		
};

Object.extend(TextBox.prototype, VisualComponent.prototype);
Object.extend(TextBox.prototype, FormField.prototype);

Object.extend(TextBox.prototype,
{
	parent : VisualComponent,
	
	oldValue : "",
	
	actualValue : "",

	phpClass : "TextBox",
	
	/**
	 * @param {HTMLElement}elem
	 * @return {boolean}
	 */
	initialize : function TextBox_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		this.actualValue = elem.value;
		this.realElement = elem;
		
		var oldOnChange = this.realElement.onchange;
		
		this.realElement.onchange = null;
		
		if (window.addEventListener) // Mozilla like
		{
			this.realElement.addEventListener('change', this._raiseChange.bind(this),false);
			
			if(oldOnChange)
				this.realElement.addEventListener('change', oldOnChange,false);
		}
		else if (window.attachEvent) // IE
		{
			this.realElement.attachEvent('onchange', this._raiseChange.bind(this));
			
			if(oldOnChange)
				this.realElement.attachEvent('onchange', oldOnChange);
		}
		
		
		return true;
	},
	
	checkChanges : function TextBox_checkChanges()
	{
		if(this.oldValue != this.realElement.value)
			this._raiseChange();
	},
	
	/**
	 * @private
	 * @param {Event} e
	 */
	_raiseChange : function _raiseChange(e)
	{
		this.onPropertyChange.raise(this, {event:e, changeType : ChangeType.PROPERTY_CHANGED, propertyName : "value", oldValue : this.oldValue});
		this.oldValue = this.actualValue;
		this.actualValue = this.realElement.value;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.BazeValidator");
}
	
BazeValidator = function BazeValidator()
{};
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.CompareValidator"); // informa que esse arquivo foi carregado
	
	Baze.require("web.form.validator.BazeValidator"); // require normal, � tipo o import do Baze
}

/**
 * @class CompareValidator
 * @alias CompareValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {FormField} fieldToCompare
 */
CompareValidator = function CompareValidator(fieldToCompare)
{
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
	
	if(fieldToCompare instanceof FormField)
	{
		this.fieldToCompare = fieldToCompare;
	}
};

// extends Component
Object.extend(CompareValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(CompareValidator.prototype, 
{
	_EQUAL: 1,
	_NOT_EQUAL: 2,
	_LESS_THAN: 3,
	_GREATER_THAN: 4,
	_LESS_OR_EQUAL: 5,
	_GREATER_OR_EQUAL: 6,
	
	fieldToCompare: null,
	comparationType: 1,
	
	setComparationType: function CompareValidator_setComparationType(type)
	{
		this.comparationType = type;
		this.setLastValidationField(3, false);
	},
	
	getComparationType: function CompareValidator_getComparationType()
	{
		return this.comparationType;
	},
	
	setFieldToCompare: function CompareValidator_setFieldToCompare(toCompare)
	{
		if(toCompare instanceof FormField)
		{
			this.fieldToCompare = toCompare;
			this.setLastValidationField(3, false);
		}
	},
	
	setFieldToCompare: function CompareValidator_getFieldToCompare()
	{
		return this.fieldToCompare;
	},
	
	doValidation: function CompareValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
		var toCompareValue = this.fieldToCompare.get('value');
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(3, true);
		
		var result = false;
		
		switch(this.comparationType)
		{
			case this._NOT_EQUAL:
				if(fieldValue !== toCompareValue)
				{
					result = true;
				}
				break;
			case this._LESS_THAN:
				if(fieldValue < toCompareValue)
				{
					result = true;
				}
				break;
			case this._GREATER_THAN:
				if(fieldValue > toCompareValue)
				{
					result = true;
				}
				break;
			case this._LESS_OR_EQUAL:
				if(fieldValue <= toCompareValue)
				{
					result = true;
				}
				break;
			case this._GREATER_OR_EQUAL:
				if(fieldValue >= toCompareValue)
				{
					result = true;
				}
				break;
			default:
				if(fieldValue === toCompareValue)
				{
					result = true;
				}
		}
		
		return result;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.CustomValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}
/**
 * @class CustomValidator
 * @alias CustomValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
CustomValidator = function CustomValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(CustomValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(CustomValidator.prototype, {
	jsFunction: null,
	validateFunction: null,
	
	setJSFunction: function CustomValidator_setJSFunction(jsFunction)
	{
		this.jsFunction = jsFunction;
		this.setLastValidationField(3, false);
	},
	
	getJSFunction: function CustomValidator_getJSFunction()
	{
		return this.jsFunction;
	},
	
	doValidation: function RegExValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		var result;
		eval("result = " + this.jsFunction + "('" + fieldValue + "');");
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(2, result);
		this.setLastValidationField(3, true);			
		
		return result;
	}
});
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RangeValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}

/**
 * @class RangeValidator
 * @alias RangeValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RangeValidator = function RangeValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RangeValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RangeValidator.prototype, {
	minValue: 0,
	maxValue: -1,
	strictComparison: false,
	
	setMinValue: function RangeValidator_setMinValue(minValue) {
		if(((typeof minValue) === 'number') && (minValue !== this.minValue))
		{
			this.minValue = minValue;
			this.setLastValidationField(3, false);
		}
	},
	
	getMinValue: function RangeValidator_getMinValue() {
		return this.minValue;
	},

	setMaxValue: function RangeValidator_setMaxValue(maxValue) {
		if(((typeof maxValue) === 'number') && (maxValue !== this.maxValue))
		{
			this.maxValue = maxValue;
			this.setLastValidationField(3, false);
		}
	},

	getMaxValue: function RangeValidator_getMaxValue() {
		return this.maxValue;
	},
	
	setStrictComparison: function RangeValidator_setStrictComparison(strictComparison) {
		if(((typeof strictComparison) === 'boolean') && (this.strictComparison !== strictComparison))
		{
			this.strictComparison = strictComparison;
			this.setLastValidationField(3, false);
		}
	},
	
	getStrictComparison: function RangeValidator_getStrictComparison() {
		return this.strictComparison;
	},
	
	doValidation: function RangeValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(3, true);
		
		var result = false;
		
		if(this.strictComparison)
		{
			if(this.minValue >= 0)
			{
				if(fieldValue.length > this.minValue)
				{
					if(this.maxValue > this.minValue)
					{
						if(fieldValue.length < this.maxValue)
						{
							result = true;
						}
					}
					else
					{
						result = true;
					}
				}
			}
			else
			{
				if(this.maxValue > 0)
				{
					if(tamValue < this.maxValue)
					{
						result = true;
					}
				}
				else
				{
					result = true;
				}
			}
		}
		else
		{
			if(this.minValue >= 0)
			{
				if(fieldValue.length >= this.minValue)
				{
					if(this.maxValue >= this.minValue)
					{
						if(fieldValue.length <= this.maxValue)
						{
							result = true;
						}
					}
					else
					{
						result = true;
					}
				}
			}
			else
			{
				if(this.maxValue >= 0)
				{
					if(fieldValue.length <= this.maxValue)
					{
						result = true;
					}
				}
				else
				{
					result = true;
				}
			}
		}
		
		this.setLastValidationField(2, result);
		return result;
	}
});

if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RegExValidator"); // informa que esse arquivo foi carregado
	
	Baze.require("web.form.validator.BazeValidator"); // require normal, � tipo o import do Baze
}

/**
 * @class RegExValidator
 * @alias RegExValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RegExValidator = function()
{
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RegExValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RegExValidator.prototype, {
	expression: '',
	
	setExpression: function RegExValidator_setExpression(newExpression) {
		this.expression = newExpression.toString();
		this.setLastValidationField(3, false);
	},
	
	getExpression: function RegExValidator_getExpression() {
		return this.expression;
	},
	
	doValidation: function RegExValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(3, true);
		
		var result = false;
		
		var re = new RegExp(this.expression);
		if(fieldValue.match(re))
		{
			result = true;
		}
		
		this.setLastValidationField(2, result);
		return result;
	}
});

RegExValidator.CommonExp = {

	Date : /(0[1-9]|[12][0-9]|3[01])([- \/.])(0[1-9]|1[012])\2(19|20)\d\d$/,
	
	Time : /^(\d{1,2})\:(\d{1,2})\:(\d{1,2})$/,
	
	Alpha : /^[a-zA-Z\.\-]*$/,
	
	AlphaNum : /^\w+$/,
	
	UnsignedInt : /^\d+$/,
	
	Integer : /^[\+\-]?\d*$/,
	
	Real : /^[\+\-]?\d*\.?\d*$/,
	
	UnsignedReal : /^\d*\.?\d*$/,
	
	Email : /^[\w-\.]+\@[\w\.-]+\.[a-z]{2,6}$/,
	
	Phone : /^[\d\.\s\-]+$/
};
if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RequiredFieldValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RequiredFieldValidator = function RequiredFieldValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RequiredFieldValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RequiredFieldValidator.prototype, {
	
	doValidation : function RequiredFieldValidator_doValidation() {
		fieldValue = this.getFieldToValidate.get('value');
		fieldValue = fieldValue.toString();
		
		result = false;
		if(fieldValue.length > 0)
		{
			result = true;
		}
				
		return result;
	}
});

