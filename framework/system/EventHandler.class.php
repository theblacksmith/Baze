<?php
/**
 * Arquivo EventHandler.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
require_once NB_ROOT . '/system/lang/Delegate.class.php';

/**
 * Classe EventHandler
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class EventHandler extends Delegate
{
	private static $isHandlingEvents = false;

	/**
	* Constructor
	*
	* @param string|Array $function The function to be subscribed to the event
	*/
	function __construct($function)
	{
		$this->paramCount = 2;

		$this->paramType = array('Component',null);

		// @todo só checar os tipos dos parâmetros em tempo de desenvolvimento (otimização)
		// @todo só reportar erros quando identificar que ocorrerá um erro em tempo de execução, ou seja,
		// passam a ser aceitas, por exemplo, funções sem parâmetros declarados

		if($this->setFunction($function))
		{
			self::$isHandlingEvents = true;

			return true;
		}

		return false;
	}
}