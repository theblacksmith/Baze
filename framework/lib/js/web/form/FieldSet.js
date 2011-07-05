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