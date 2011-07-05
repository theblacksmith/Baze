if(typeof Baze !== "undefined")
{
	Baze.provide("web.form.Label");
	
	Baze.require("web.VisualComponent");	
	Baze.require("web.Container");	
	Baze.require("web.form.FormField");
}

Label = function Label(elem)
{
	(FormField.bind(this))();
	(VisualComponent.bind(this))();
	(Container.bind(this))();
	
	if (typeof elem == "undefined")
	{
		var elem = document.createElement('label');
	}
	
	this.initialize(elem);
};
	
Object.extend(Label.prototype, VisualComponent.prototype);
Object.extend(Label.prototype, Container.prototype);
Object.extend(Label.prototype, FormField.prototype);
	
Object.extend(Label.prototype,
{
	parent : VisualComponent,
	
	phpClass : "Label",
	
	/**
	 * @param {HTMLElement} elem
	 */
	initialize : function Label_initialize (elem)
	{
		(Component.prototype.initialize.bind(this, elem))();
		
		if (typeof elem == "undefined" || elem == null)
		{
			Baze.raise("Error in initialize Label component.", new Error("Param elem is not defined in Label__initialize."));
		}
		else
		{	
			this.realElement = elem;
		}
	}
});