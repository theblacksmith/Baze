if(typeof Baze != "undefined")
{
	Baze.provide("web.form.validator.RangeValidator");
	
	Baze.require("web.form.validator.BazeValidator");
}

/**
 * @class RangeValidator
 * @alias RangeValidator
 * @namespace Baze
 * @author Saulo Vallory
 * @version 0.9
 */
RangeValidator = function RangeValidator(){
	// chamando o construtor da classe pai
	(BazeValidator.bind(this))();
};

// extends Component
Object.extend(RangeValidator.prototype, BazeValidator.prototype);

// defini��o de m�todos e propriedades
Object.extend(RangeValidator.prototype, {
	minValue: 0,
	maxValue: -1,
	strictComparison: false,
	
	setMinValue: function RangeValidator_setMinValue(minValue) {
		if(((typeof minValue) === 'number') && (minValue !== this.minValue))
		{
			this.minValue = minValue;
			this.setLastValidationField(3, false);
		}
	},
	
	getMinValue: function RangeValidator_getMinValue() {
		return this.minValue;
	},

	setMaxValue: function RangeValidator_setMaxValue(maxValue) {
		if(((typeof maxValue) === 'number') && (maxValue !== this.maxValue))
		{
			this.maxValue = maxValue;
			this.setLastValidationField(3, false);
		}
	},

	getMaxValue: function RangeValidator_getMaxValue() {
		return this.maxValue;
	},
	
	setStrictComparison: function RangeValidator_setStrictComparison(strictComparison) {
		if(((typeof strictComparison) === 'boolean') && (this.strictComparison !== strictComparison))
		{
			this.strictComparison = strictComparison;
			this.setLastValidationField(3, false);
		}
	},
	
	getStrictComparison: function RangeValidator_getStrictComparison() {
		return this.strictComparison;
	},
	
	doValidation: function RangeValidator_doValidation() {
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
		
		if(this.strictComparison)
		{
			if(this.minValue >= 0)
			{
				if(fieldValue.length > this.minValue)
				{
					if(this.maxValue > this.minValue)
					{
						if(fieldValue.length < this.maxValue)
						{
							result = true;
						}
					}
					else
					{
						result = true;
					}
				}
			}
			else
			{
				if(this.maxValue > 0)
				{
					if(tamValue < this.maxValue)
					{
						result = true;
					}
				}
				else
				{
					result = true;
				}
			}
		}
		else
		{
			if(this.minValue >= 0)
			{
				if(fieldValue.length >= this.minValue)
				{
					if(this.maxValue >= this.minValue)
					{
						if(fieldValue.length <= this.maxValue)
						{
							result = true;
						}
					}
					else
					{
						result = true;
					}
				}
			}
			else
			{
				if(this.maxValue >= 0)
				{
					if(fieldValue.length <= this.maxValue)
					{
						result = true;
					}
				}
				else
				{
					result = true;
				}
			}
		}
		
		this.setLastValidationField(2, result);
		return result;
	}
});
