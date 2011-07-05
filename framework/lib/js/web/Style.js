if(typeof Baze != "undefined")
{
	Baze.provide("web.Style");
	
	// Assumindo que o jext est� sempre l�!
	// Baze.require("system.jext");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {Object} style
 */
Style = function Style(style) {
	this.realObject = style;
};

Object.extend(Style.prototype, {

	realObject : null,
	
	_owner : null,
	
	/**
	 * @method get
	 * @param {String} prop
	 */
	get : function get(prop) {
		return this.realObject[prop];
	},
	
	/**
	 * @method set
	 * @param {String} name
	 * @param {Object} value
	 */
	set : function set(name, value) {
		if(typeof this.realObject[name] != "undefined")
			var oldValue = this.realObject[name];
		else
			var oldValue = undefined;
		
		if(value != oldValue)
		{
			this.realObject[name] = value;
			if(this._owner)
			{
				var oldCss = this.realElement.style.cssText;
				this._owner.onPropertyChange.raise(this, { changeType : ChangeType.PROPERTY_CHANGED, propertyName : "style", oldValue : oldCss});
			}
		}
	},
	
	/**
	 * @method setOwner
	 * @param {Component} comp
	 * @return {boolean}
	 */
	setOwner : function setOwner(comp)
	{
		if(!Baze.isComponent(comp))
		{
			Baze.raise("Par�metro incorreto", new Error("A fun��o Style.setOwner espera um Component como par�metro mas recebeu um " + (typeof comp)));
			return false;
		}
			
		this._owner = comp;
		return true;
	},
	
	/**
	 * @method getOwner
	 * @return {Component} comp
	 */
	getOwner : function getOwner()
	{		
		return this._owner;
	}
});