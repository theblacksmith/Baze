if(typeof Baze != "undefined")
{
	Baze.provide("web.form.FormField");
}

FormField = function(){};

Object.extend(FormField.prototype,  
{
	checkChanges : function TextBox_checkChanges()
	{
		// this function should be overwrited in child class
	}
});