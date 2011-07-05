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