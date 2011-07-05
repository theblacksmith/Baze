if(typeof Baze != "undefined")
{
	Baze.provide("web.VisualComponent");
	
	Baze.require("web.Component");
}

/**
 * @class VisualComponent
 * @alias VisualComponent
 * @namespace Baze
 * @classDescription A classe VisualComponent define m�todos 
 * inerentes a qualquer componente vis�vel como show, hide e toogle
 * @author Saulo Vallory 
 * @version 0.9
 * 
 * @requires Component
 * 
 * @constructor
 */
VisualComponent = function VisualComponent() {
	this.isVisualComponent = true;
	// chamando o construtor da classe Component
	(Component.bind(this))();
};

Object.extend(VisualComponent.prototype, Component.prototype);
	
Object.extend(VisualComponent.prototype, {
	
	show : function () {
		this.style.set('display','');
	},
	
	hide : function () {
		this.style.set('display','none');
	},
	
	toogle : function () {
		this[this.isVisible() ? 'hide' : 'show']();
	},
	
	isVisible: function() {
	    return this.realElement.style.display != 'none';
	}
});