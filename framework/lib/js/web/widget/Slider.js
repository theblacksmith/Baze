if(typeof Baze !== "undefined")
{
	Baze.provide("web.widget.Slider");
	Baze.require("web.VisualComponent");
}

/**
 * @class Slider
 * @alias Slider
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {HTMLElement} elem
 */
Slider = function Slider(elem)
{
	(VisualComponent.bind(this))();

	if (typeof elem != "undefined")
	{
		//this.initialize(elem);
	}
};

Object.extend(Slider.prototype, VisualComponent.prototype);

Object.extend(Slider.prototype,
{
	parent : VisualComponent,
	
	leftUp : "",
	
	rightDown : "",
	
	oldXValue : "",
	
	oldYValue : "",
	
	tick : "",

	phpClass : "Slider",
	
	/**
	 * @param {HTMLElement}elem
	 * @return {boolean}
	 */
	initialize : function initialize (elem)
	{
		(VisualComponent.prototype.initialize.bind(this, elem))();

		var sliderbgID = elem.id;
		var sliderthumbID = elem.getElementsByTagName('div')[0].id;
		
		this.oldXValue 	= elem.getAttribute('xvalue');
		this.oldYValue 	= elem.getAttribute('yvalue');
		this.leftUp		= elem.getAttribute('leftUp');
		this.rightDown	= elem.getAttribute('rightDown');
		this.tick		= elem.getAttribute('tick');
		this.locked		= elem.getAttribute('locked');
		
		alert(	"xvalue: " + this.oldXValue + 
				' - yvalue: ' + this.oldYValue + 
				' - leftUp: ' + this.leftUp + 
				' - rightDown: ' + this.rightDown + 
				' - tick: ' + this.tick + 
				' - locked: ' + this.locked );
		alert(sliderbgID + " > " + sliderthumbID);
		
		this.realElement = YAHOO.widget.Slider.getHorizSlider(sliderbgID, sliderthumbID, this.leftUp, this.rightDown, this.tick);
		this.realElement.setValue(this.oldXValue);
		
		if (this.locked == 1)
		{			
			this.realElement.subscribe("change", this.setLock.bind(this));
			this.realElement.setValue(this.oldXValue);
		}
		
		this.realElement.subscribe("change", this.raiseChange.bind(this));
	},
	
	raiseChange : function Slider_raiseChange(offSet)
	{	
		alert ("raiseChange");
		this.onPropertyChange.raise(this, {changeType : ChangeType.PROPERTY_CHANGED, propertyName : "xvalue", oldValue : this.oldXValue});
		this.oldXValue = offSet;
	},
	
	/**
	 * @param {int} offSet
	 */
	setLock : function Slider_setLock(offSet)
	{
		this.oldXValue = offSet;
		
		this.realElement.lock();
	}
});