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