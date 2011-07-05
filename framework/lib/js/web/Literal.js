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