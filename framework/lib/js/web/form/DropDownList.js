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