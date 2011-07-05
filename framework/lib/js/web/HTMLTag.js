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