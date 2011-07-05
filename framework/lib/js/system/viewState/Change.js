if(typeof Baze !== 'undefined')
{
	Baze.provide("system.viewState.Change");
}

ChangeType = {
	PROPERTY_CHANGED : 1,
	CHILD_ADDED : 2,
	CHILD_REMOVED : 3
};

/**
 * @class Change
 * @alias Change
 * @classDescription A classe Change representa uma altera��o na interface
 * @package system.viewState 
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @requires system.viewState.ClientViewState
 * 
 * @constructor
 * @param {Object} type
 * @param {Object} data
 */
Change = function Change(type, data) {

	this.type = type;
	
	// TODO: checar se os dados requeridos pelo tipo da Change existem em data
	for(var v in data) {
		this[v] = data[v];
	}
};

Object.extend(Change.prototype,  {

	type : null,

	/**
	 * @method getXML
	 * @return {String} xml que representa a altera��o
	 */
	toJSON : function toJSON()
	{
		var o = {};

		switch(this.type)
		{
			case ChangeType.CHILD_ADDED :
				o = '<change type="childAdded" childId="' + this.child.get("id") + '" />';
				break;

			case ChangeType.CHILD_REMOVED :
				o = '<change type="childRemoved" childId="' + this.child.get("id") + '" />';
				break;

			case ChangeType.PROPERTY_CHANGED :
				var propType = "";

				if(this.newValue == null)
					this.newValue = "";
				if(is_array(this.newValue)) {
					propType = "array"; 
				}
				else {
					propType = typeof this.newValue; 
				}

				o = {};
				o[this.propertyName] = this.newValue;
		}

		return o;
	},

	/**
	 * O id foi criado dessa forma para que se possa perceber quando uma altera��o se chocar com outra
	 */
	getId : function ()
	{
		switch(this.type)
		{
			case ChangeType.PROPERTY_CHANGED :
				return "PropChange_" + this.propertyName;

			case ChangeType.CHILD_ADDED :
				return "ChildAddOrRemove_" + this.child.getId();

			case ChangeType.CHILD_REMOVED :
				return "ChildAddOrRemove_" + this.child.getId();
		}
	},

	/**
 	 * Checa se a altera��o desta inst�ncia � anulada pela altera��o do objeto passado como par�metro.
	 * 
	 * @memberOf Change
	 * @param {Change} chg
	 */
	isMirror : function Change_isMirror(chg)
	{
		switch(this.type)
		{
			case ChangeType.PROPERTY_CHANGED :
				if(chg.type == ChangeType.PROPERTY_CHANGED && this.newValue == chg.oldValue && this.oldValue == chg.newValue) {
					return true; }
				break;

			case ChangeType.CHILD_ADDED :
				if(chg.type == ChangeType.CHILD_REMOVED && chg.child.getId() == this.child.getId()) {
					return true; }
				break;

			case ChangeType.CHILD_REMOVED :
				if(chg.type == ChangeType.CHILD_ADDED && chg.child.getId() == this.child.getId()) {
					return true; }
				break;
		}

		return false;
	},

	/**
	 * Soma duas alter��es transformando-as em uma s�.
	 *
	 * @param {Change} chg
	 */
	mergeWith : function Change_mergeWith(chg)
	{
		if(this.type !== ChangeType.PROPERTY_CHANGED) {
			//Baze.raise("Only property changes can be merged", new Error());
			return null;
		}

		if(this.type !== chg.type) {
			Baze.raise("Different types of changes can not be merged", new Error());
			return null;
		}

		this.newValue = chg.newValue;
	}
});