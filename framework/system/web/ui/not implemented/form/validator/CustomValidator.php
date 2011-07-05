<?php
/**
 * Arquivo CustomValidator.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
require_once(realpath(dirname(__FILE__)) . '/../../Classes/system/Delegate.php');
require_once(realpath(dirname(__FILE__)) . '/../../web/form/BazeValidator.php');

/**
 * Classe CustomValidator<br />
 * Permite definir uma função para validação do campo.
 * A função deve receber um parâmetro (o valor a validar)
 * e retornar true ou false.
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
class CustomValidator extends BazeValidator {
	/**
	 * @AttributeType Classes.system.Delegate
	 */
	private $validateFunction;
	
	private $jsFunction;
	
	/**
	 * @ParamType validateFunction Classes.system.Delegate
	 */
	public function setValidateFunction(Delegate $validateFunction) {
		$this->validateFunction = $validateFunction;
		$this->jsFunction = null;
		$this->setLastValidationField('isValid', false);
	}

	/**
	 * @ReturnType Classes.system.Delegate
	 */
	public function getValidateFunction() {
		return $this->validateFunction;
	}
	
	public function setJSFunction($jsFunction)
	{
		$this->jsFunction = $jsFunction;
		$this->setLastValidationField('isValid', false);
	}
	
	public function getJSFunction()
	{
		return $this->jsFunction;
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
		
		$result = $this->validateFunction->call(array($fieldValue));
		
		if(is_bool($result))
		{
			$this->setLastValidationField('value', $fieldValue);
			$this->setLastValidationField('isValid', true);
			$this->setLastValidationField('result', $result);
			return $result;
		}		
		
		return false;
	}
}