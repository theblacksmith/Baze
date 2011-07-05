<?php
/**
 * Arquivo BazeValidator.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
require_once(realpath(dirname(__FILE__)) . '/../../web/form/FormField.php');
require_once(realpath(dirname(__FILE__)) . '/../../Classes/web/Span.php');

/**
 * Classe BazeValidator
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
abstract class BazeValidator {
	/**
	 * @AttributeType web.form.FormField
	 */
	private $fieldToValidate;
	
	/**
	 * @AttributeType Classes.web.Span
	 */
	private $errorMessage;
	
	/**
	 * @AttributeType Classes.web.Span
	 */
	private $successMessage;
	
	private $lastValidation;

	public function __construct($fieldToValidate)
	{
		$ts = time();
		$this->setFieldToValidate($fieldToValidate);
		$this->lastValidation = array('value' => null, 'result' => null, 'isValid' => null);
		$this->errorMessage = new Span();
		$this->errorMessage->set('id', 'validatorError'.$ts);
		$this->successMessage = new Span();
		$this->successMessage->set('id', 'validatorSuccess'.$ts);
	}

	/**
	 * Essa fun��o � sobrescrita aqui para verificar se o campo � v�lido ou n�o e delegar a responsabilidade de renderiza��o para a mensagem apropriada
	 */
	public function getXHTML() {
		$isValid = $this->getLastValidationField('isValid');
		if($isValid === true)
		{
			$result = $this->getLastValidationField('result');
			if(is_bool($result))
			{
				if($result)
					return $this->successMessage->getXHTML();
				else
					return $this->errorMessage->getXHTML();
			}
		}
		
		$result = $this->doValidation();
		if($result)
			return $this->successMessage->getXHTML();
		else
			return $this->errorMessage->getXHTML();
	}

	/**
	 * M�todo que executa a valida��o no campo. 
	 * 
	 * Desenvolvedores do Framework: esse m�todo n�o deve ser sobrescrito. Para personal
	 */
	public function validate() {
		$this->onValidate();
		$res = $this->doValidation();
		if($res === true)
		{
			$this->onValidationSuccess();
		}
		else
		{
			$this->onValidationFail();
		}
	}

	public function onValidate($args) {
		$this->raiseEvent(VALIDATE, $args);
	}

	public function onValidationFail($args) {
		$this->raiseEvent(VALIDATION_FAIL, $args);
	}

	public function onValidationSuccess($args) {
		$this->raiseEvent(VALIDATION_SUCCESS, $args);
	}

	/**
	 * @ReturnType boolean
	 */
	protected abstract function doValidation();

	public function setLastValidationField($field, $value)
	{
		$this->lastValidation[$field] = $value;
	}
	
	public function getLastValidationField($field)
	{
		return $this->lastValidation[$field];
	}
	
	/**
	 * @ParamType fieldToValidate web.form.FormField
	 */
	public function setFieldToValidate(FormField $fieldToValidate) {
		$this->fieldToValidate = $fieldToValidate;
		$this->setLastValidation('isValid', false);
	}

	/**
	 * @ReturnType web.form.FormField
	 */
	public function getFieldToValidate() {
		return $this->fieldToValidate;
	}

	/**
	 * @ParamType errorMessage Classes.web.Span
	 */
	public function setErrorMessage($errorMessage) {
		$this->errorMessage->removeChildren();
		$this->errorMessage->addChild($errorMessage);
	}

	/**
	 * @ReturnType String
	 */
	public function getErrorMessage() {
		$errorChildren = $this->errorMessage->getChildren();
		$errorMessage = '';
		if(count($errorChildren)>0)
		{
			$errorMessage = array_pop($errorChildren);
		}
		return $errorMessage;
	}

	/**
	 * @ParamType successMessage Classes.web.Span
	 */
	public function setSuccessMessage(Span $successMessage) {
		$this->successMessage = $successMessage;
	}

	/**
	 * @ReturnType String
	 */
	public function getSuccessMessage() {
		$successChildren = $this->successMessage->getChildren();
		$successMessage = '';
		if(count($successChildren)>0)
		{
			$successMessage = array_pop($successChildren);
		}
		return $successMessage;
	}
}