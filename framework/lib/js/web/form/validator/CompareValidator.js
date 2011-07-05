if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.CompareValidator"); // informa que esse arquivo foi carregado
	
	Baze.require("web.form.validator.BazeValidator"); // require normal, � tipo o import do Baze
}

/**
 * @class CompareValidator
 * @alias CompareValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 * 
 * @param {FormField} fieldToCompare
 */
CompareValidator = function CompareValidator(fieldToCompare)
{
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
	
	if(fieldToCompare instanceof FormField)
	{
		this.fieldToCompare = fieldToCompare;
	}
};

// extends Component
Object.extend(CompareValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(CompareValidator.prototype, 
{
	_EQUAL: 1,
	_NOT_EQUAL: 2,
	_LESS_THAN: 3,
	_GREATER_THAN: 4,
	_LESS_OR_EQUAL: 5,
	_GREATER_OR_EQUAL: 6,
	
	fieldToCompare: null,
	comparationType: 1,
	
	setComparationType: function CompareValidator_setComparationType(type)
	{
		this.comparationType = type;
		this.setLastValidationField(3, false);
	},
	
	getComparationType: function CompareValidator_getComparationType()
	{
		return this.comparationType;
	},
	
	setFieldToCompare: function CompareValidator_setFieldToCompare(toCompare)
	{
		if(toCompare instanceof FormField)
		{
			this.fieldToCompare = toCompare;
			this.setLastValidationField(3, false);
		}
	},
	
	setFieldToCompare: function CompareValidator_getFieldToCompare()
	{
		return this.fieldToCompare;
	},
	
	doValidation: function CompareValidator_doValidation() {
		var fieldValue = this.getFieldToValidate.get(1);
		var toCompareValue = this.fieldToCompare.get('value');
	
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
		
		switch(this.comparationType)
		{
			case this._NOT_EQUAL:
				if(fieldValue !== toCompareValue)
				{
					result = true;
				}
				break;
			case this._LESS_THAN:
				if(fieldValue < toCompareValue)
				{
					result = true;
				}
				break;
			case this._GREATER_THAN:
				if(fieldValue > toCompareValue)
				{
					result = true;
				}
				break;
			case this._LESS_OR_EQUAL:
				if(fieldValue <= toCompareValue)
				{
					result = true;
				}
				break;
			case this._GREATER_OR_EQUAL:
				if(fieldValue >= toCompareValue)
				{
					result = true;
				}
				break;
			default:
				if(fieldValue === toCompareValue)
				{
					result = true;
				}
		}
		
		return result;
	}
});