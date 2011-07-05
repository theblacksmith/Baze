<?php
/**
 * Arquivo RangeValidator.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
require_once(realpath(dirname(__FILE__)) . '/../../web/form/BazeValidator.php');

/**
 * Classe RangeValidator<br />
 * Checa se o valor de um campo está na 
 * faixa de valores definida por você.
 * 
 * falar de string ou int
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
class RangeValidator extends BazeValidator {
	/**
	 * @AttributeType mixed
	 */
	private $minValue;
	/**
	 * @AttributeType mixed
	 */
	private $maxValue;
	/**
	 * @AttributeType boolean
	 * Define se a compara��o deve ser estrita ou n�o. Ou seja, se verdadeiro o valor a do campo s� ser� considerado v�lido se for estritamente maior que minValue e estritamente menor que maxValue
	 */
	private $strictComparison;

	public function __construct()
	{
		$this->strictComparison = false;
		$this->minValue = 0;
		$this->maxValue = -1;
	}
	
	/**
	 * @ParamType minValue mixed
	 */
	public function setMinValue($minValue) {
		if(is_int($minValue) && ($minValue !== $this->minValue))
		{
			$this->minValue = $minValue;
			$this->setLastValidationField('isValid', false);
		}
	}

	/**
	 * @ReturnType mixed
	 */
	public function getMinValue() {
		return $this->minValue;
	}

	/**
	 * @ParamType maxValue mixed
	 */
	public function setMaxValue($maxValue) {
		if(is_int($maxValue) && ($this->maxValue !== $maxValue))
		{
			$this->maxValue = $maxValue;
			$this->setLastValidationField('isValid', false);
		}
	}

	/**
	 * @ReturnType mixed
	 */
	public function getMaxValue() {
		return $this->maxValue;
	}

	/**
	 * @ParamType strictComparison boolean
	 */
	public function setStrictComparison($strictComparison) {
		if(is_bool($strictComparison) && ($this->strictComparison!==$strictComparisson))
		{
			$this->strictComparison = $strictComparison;
			$this->setLastValidationField('isValid', false);
		}
	}

	/**
	 * @ReturnType boolean
	 */
	public function getStrictComparison() {
		return $this->strictComparison;
	}

	/**
	 * @ReturnType boolean
	 */
	protected function doValidation() {
		$fieldValue = $this->getFieldToValidate->get('value');
		
		$validTest = $this->getLastValidationField('isValid');
		
		if($validTest === true)
		{
			$lastValue = $this->getLastValidationField('value');
			if($lastValue === $fieldValue)
			{
				$result = $this->getLastValidationField('result');
				if(is_bool($result))
				{
					return $result;
				}
			}
		}
		
		$this->setLastValidationField('value', $fieldValue);
		$this->setLastValidationField('isValid', true);
		
		$result = false;
		
		$tamValue = strlen($fieldValue);
		
		if($this->strictComparison)
		{
			if($this->minValue >= 0)
			{
				if($tamValue > $this->minValue)
				{
					if($this->maxValue > $this->minValue)
					{
						if($tamValue < $this->maxValue)
						{
							$result = true;
						}
					}
					else
					{
						$result = true;
					}
				}
			}
			else
			{
				if($this->maxValue > 0)
				{
					if($tamValue < $this->maxValue)
					{
						$result = true;
					}
				}
				else
				{
					$result = true;
				}
			}
		}
		else
		{
			if($this->minValue >= 0)
			{
				if($tamValue >= $this->minValue)
				{
					if($this->maxValue >= $this->minValue)
					{
						if($tamValue <= $this->maxValue)
						{
							$result = true;
						}
					}
					else
					{
						$result = true;
					}
				}
			}
			else
			{
				if($this->maxValue >= 0)
				{
					if($tamValue <= $this->maxValue)
					{
						$result = true;
					}
				}
				else
				{
					$result = true;
				}
			}
		}
		
		$this->setLastValidationField('result', $result);
		return $result;
	}
}