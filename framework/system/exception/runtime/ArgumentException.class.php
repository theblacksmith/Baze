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

/** 
 * Classe ArgumentException 
 * 
 * Essa classe ainda nÃ£o foi documentada
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
	 * Constructor
	 *
	 * @param string $arg_name Name of the invalid argument
	 * @param string $arg_value Value of the invalid argument
	 * @param string|ErrorMsg $message $extraParams  String or ErrorMsg instance Custom message
	 * @param array $strings Strings to replace in custom message hooks ({0}, {1}, ...)
	 */
	public function __construct($arg_name, $arg_value, $message='', $strings = array())
	{
		$this->argumentName = $arg_name;
		$this->argumentValue = $arg_value;

		if($message == '')
			$message = ErrorMsg::$InvalidArgument;

		$extraParams['arg_name'] = $arg_name;
		$extraParams['arg_value'] = $arg_value;
		
		parent::__construct($message, $strings);
	}
	
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