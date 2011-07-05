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