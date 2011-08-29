if(typeof Baze != "undefined")
{
	Baze.provide("system.Event");
}

Baze.Event = function Event() {
	this.listeners = [];
};

Object.extend(Baze.Event.prototype,  {
	listeners : null,

	addListener : function (fn) {
		if(typeof fn !== "function") {
			return null;
		}

		var ind = this.listeners.indexOf(fn);

		if(ind == -1)
			this.listeners.push(fn);
	},

	removeListener : function(fn) {
		var ind = this.listeners.indexOf(fn);

		if(ind == -1)
			delete this.listeners[ind];
	},

	raise : function (obj, args) {
		for(var i=0; i < this.listeners.length; i++) {
			this.listeners[i](obj, args);
		}
	}
});
if(typeof Baze != "undefined") {
	Baze.provide("system.util");
}

wait = function wait(millis)
{
	date = new Date();

	do {
		var curDate = new Date();
	}
	while(curDate - date < millis);
};

uid = function uid(prefix)
{
	var id;

	if(!prefix) prefix = "";

	do {
		id = prefix + Math.round(Math.random()*10000000000).toString();
	}
	while($(id) != null)

	return id;
};
if(typeof Baze != "undefined")
{
	Baze.provide("web.Style");

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

/**
 * @class Component
 * @alias Component
 * @namespace baze.web
 * @author Saulo Vallory
 *
 * @requires system.Event
 * @requires system.util
 * @requires web.Style
 *
 * @constructor
 */
baze.web.Component = Class.Create({

	initialize: function(elem){

		this.isComponent = true;

		this.phpClass = "";

		if(Object.isUndefined(elem))
			this.id = uid("cmp_");
		else {
			this.id = elem.id;
			this.realElement = null;
		}

		this.onPropertyChange = new Baze.Event();

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
			c: this.phpClass,
			id: this.id,
			p: {}
		};

		if(node != null && node.attributes)
		{
			for(var i=0; i < node.attributes.length; i++)
			{
				var att = node.attributes[i];

				if(att.nodeValue)
				{
					obj.p[att.nodeName] = att.nodeValue;
				}
			}
		}

		if(!Object.isUndefined(this.getAttributesToRender))
			this.getAttributesToRender().each(function(pair){
				obj.p[pair.key] = pair.value;
			});

		return obj;
	},

	getId : function Component_getId() {
		return this.id;
	},

	set : function Component_set(name, value) {

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
 * Creates a component based on the phpClass
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
