if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.CustomValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}
/**
 * @class CustomValidator
 * @alias CustomValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
CustomValidator = function CustomValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(CustomValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(CustomValidator.prototype, {
	jsFunction: null,
	validateFunction: null,
	
	setJSFunction: function CustomValidator_setJSFunction(jsFunction)
	{
		this.jsFunction = jsFunction;
		this.setLastValidationField(3, false);
	},
	
	getJSFunction: function CustomValidator_getJSFunction()
	{
		return this.jsFunction;
	},
	
	doValidation: function RegExValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
	
		var validTest = this.getLastValidationField(3);
		
		if(validTest === true)
		{
			var lastValue = this.getLastValidationField(1);
			if(lastValue === fieldValue)
			{
				var result1 = this.getLastValidationField(2);
				if((typeof result1) === 'boolean')
				{
					return result1;
				}
			}
		}
		
		var result;
		eval("result = " + this.jsFunction + "('" + fieldValue + "');");
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(2, result);
		this.setLastValidationField(3, true);			
		
		return result;
	}
});