<?php
/**
 * Arquivo CompareValidator.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
require_once(realpath(dirname(__FILE__)) . '/../../web/form/FormField.php');
require_once(realpath(dirname(__FILE__)) . '/../../web/form/BazeValidator.php');

/**
 * Classe CompareValidator<br />
 * Compara o valor de um campo com outro. 
 * O tipo de comparação (igual, maior, menor, etc) 
 * é definido por você. A comparação default 
 * é de igualdade.
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
class CompareValidator extends BazeValidator {
	/**
	 * @AttributeType web.form.FormField
	 */
	private $fieldToCompare;
	
	private $comparationType;
	
	const _EQUAL = 1;
	const _NOT_EQUAL = 2;
	const _LESS_THAN = 3;
	const _GREATER_THAN = 4;
	const _LESS_OR_EQUAL = 5;
	const _GREATER_OR_EQUAL = 6;
	
	public function __construct(FormField $fieldToCompare)
	{
		parent::__contruct();
		
		$this->fieldToCompare = $fieldToCompare;
		
		$this->comparationType = _EQUAL;
	}

	protected function doValidation() {
		$fieldValue = $this->getFieldToValidate->get('value');
		$toCompareValue = $this->fieldToCompare->get('value');
		
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
		
		switch($this->comparationType)
		{
			case _NOT_EQUAL:
				if($fieldValue !== $toCompareValue)
				{
					$result = true;
				}
				break;
			case _LESS_THAN:
				$cmp = strcasecmp($fieldValue, $toCompareValue);
				if($cmp < 0)
				{
					$result = true;
				}
				break;
			case _GREATER_THAN:
				$cmp = strcasecmp($fieldValue, $toCompareValue);
				if($cmp > 0)
				{
					$result = true;
				}
				break;
			case _LESS_OR_EQUAL:
				$cmp = strcasecmp($fieldValue, $toCompareValue);
				if($cmp <= 0)
				{
					$result = true;
				}
				break;
			case _GREATER_OR_EQUAL:
				$cmp = strcasecmp($fieldValue, $toCompareValue);
				if($cmp >= 0)
				{
					$result = true;
				}
				break;
			default:
				if($fieldValue === $toCompareValue)
				{
					$result = true;
				}
		}
		
		return $result;
	}
	
	public function setComparationType($type)
	{
		$this->comparationType = $type;
		$this->setLastValidationField('isValid', false);
	}
	
	public function getComparationType()
	{
		return $this->comparationType;
	}
	
	public function setFieldToCompare(FormField $toCompare)
	{
		$this->fieldToCompare = $toCompare;
		$this->setLastValidationField('isValid', false);
	}
	
	public function getFieldToCompare()
	{
		return $this->fieldToCompare;
	}
}