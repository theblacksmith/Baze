if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RequiredFieldValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}

/**
 * @class Style
 * @alias Style
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RequiredFieldValidator = function RequiredFieldValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RequiredFieldValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RequiredFieldValidator.prototype, {
	
	doValidation : function RequiredFieldValidator_doValidation() {
		fieldValue = this.getFieldToValidate.get('value');
		fieldValue = fieldValue.toString();
		
		result = false;
		if(fieldValue.length > 0)
		{
			result = true;
		}
				
		return result;
	}
});
