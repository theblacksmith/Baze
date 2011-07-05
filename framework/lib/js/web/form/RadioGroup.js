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