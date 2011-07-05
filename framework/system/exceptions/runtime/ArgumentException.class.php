<?php
/**
 * Arquivo da classe ArgumentException
 *
 * Esse arquivo ainda nÃ£o foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package system.exception.runtime
 */

require_once 'BazeRuntimeException.class.php';

/**
 * This is the base class for all Argument related exceptions.
 *
 * {@inheritdoc }}
 * {{arg_name}} - The name of the argument that caused the exception
 * {{arg_value}} - The value of this argument
 *
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package system.exception.runtime
 */
class ArgumentException extends BazeRuntimeException
{
	/**
	 * Name of the invalid argument
	 *
	 * @var string
	 */
	private $argumentName;

	/**
	 * Value of the invalid argument
	 *
	 * @var string
	 */
	private $argumentValue;

	/**
	 * Gets the value of the invalid argument
	 *
	 * @return mixed The value of the invalid argument
	 */
	public function getArgumentValue()
	{
		return $this->argumentValue;
	}

	/**
	 * Sets the value of the invalid argument
	 *
	 * @param mixed $arg_value The value of the invalid argument
	 */
	public function setArgumentValue($arg_value)
	{
		$this->argumentValue = $arg_value;
	}

	/**
	 * Gets the name of the invalid argument
	 *
	 * @return mixed The name of the invalid argument
	 */
	public function getArgumentName()
	{
		return $this->argumentName;
	}

	/**
	 * Sets the name of the invalid argument
	 *
	 * @param mixed $arg_name The name of the invalid argument
	 */
	public function setArgumentName($arg_name)
	{
		$this->argumentName = $arg_name;
	}
}