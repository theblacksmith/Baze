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