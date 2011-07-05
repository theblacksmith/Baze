if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RegExValidator"); // informa que esse arquivo foi carregado
	
	Baze.require("web.form.validator.BazeValidator"); // require normal, � tipo o import do Baze
}

/**
 * @class RegExValidator
 * @alias RegExValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RegExValidator = function()
{
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RegExValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RegExValidator.prototype, {
	expression: '',
	
	setExpression: function RegExValidator_setExpression(newExpression) {
		this.expression = newExpression.toString();
		this.setLastValidationField(3, false);
	},
	
	getExpression: function RegExValidator_getExpression() {
		return this.expression;
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
		
		this.setLastValidationField(1, fieldValue);
		this.setLastValidationField(3, true);
		
		var result = false;
		
		var re = new RegExp(this.expression);
		if(fieldValue.match(re))
		{
			result = true;
		}
		
		this.setLastValidationField(2, result);
		return result;
	}
});

RegExValidator.CommonExp = {

	Date : /(0[1-9]|[12][0-9]|3[01])([- \/.])(0[1-9]|1[012])\2(19|20)\d\d$/,
	
	Time : /^(\d{1,2})\:(\d{1,2})\:(\d{1,2})$/,
	
	Alpha : /^[a-zA-Z\.\-]*$/,
	
	AlphaNum : /^\w+$/,
	
	UnsignedInt : /^\d+$/,
	
	Integer : /^[\+\-]?\d*$/,
	
	Real : /^[\+\-]?\d*\.?\d*$/,
	
	UnsignedReal : /^\d*\.?\d*$/,
	
	Email : /^[\w-\.]+\@[\w\.-]+\.[a-z]{2,6}$/,
	
	Phone : /^[\d\.\s\-]+$/
};