<?php
/**
 * Arquivo RegExValidator.php
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
 * Classe RegExValidator<br />
 * Checa se o valor do campo atende à expressão.
 * O valor só é considerado válido se a expressão engloba
 * o valor inteiro do campo.
 * Ex.: Um campo preenchido com "abcde" é considerado
 * inválido para a expressão "abc"
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
class RegExValidator extends BazeValidator {
	/**
	 * @AttributeType string
	 */
	private $expression = '//';

	/**
	 * @ParamType expression string
	 */
	public function setExpression($expression) {
		$this->expression = $expression;
		$this->setLastValidationField('isValid', false);
	}

	/**
	 * @ReturnType string
	 */
	public function getExpression() {
		return $this->expression;
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
		
		if(preg_match($this->expression, $fieldValue))
		{
			$result = true;
		}
		
		$this->setLastValidationField('result', $result);
		return $result;
	}
}