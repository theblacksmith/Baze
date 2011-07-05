<?php
/**
 * Arquivo RequiredFieldValidator.php
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
 * Classe RequiredFieldValidator<br />
 * Checa se algo foi preenchido no campo.
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form.validator
 */
class RequiredFieldValidator extends BazeValidator {

	/**
	 * @ReturnType boolean
	 */
	protected function doValidation() {
		$fieldValue = trim($this->getFieldToValidate->get('value'));
		
		$result = false;
		if(!empty($fieldValue))
		{
			$result = true;
		}
				
		return $result;
	}
}