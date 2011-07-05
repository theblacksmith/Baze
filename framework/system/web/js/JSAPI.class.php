<?php
/**
 * Arquivo JSAPI.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

/**
 * Classe JSAPICommand<br />
 * Enum of available commands in JS API of Baze
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class JSAPICommand
{
	const CallFunction = "CallFunction";
	const Redirect = "Redirect";	
}

/**
 * Classe JSAPI
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class JSAPI {

	private $_commands;
	
	/**
	 * single instance of this class
	 *
	 * @var JSAPI
	 */
	private static $instance;
	
	private function __construct()
	{
	}
	
	public function getInstance()
	{
		if(!self::$instance)
			self::$instance = new JSAPI();
		
		return self::$instance;
	}
	
	public function __get($name)
	{
		$getter='get'.$name;
		if(method_exists($this,$getter))
		{
			// getting a property
			return $this->$getter();
		}
		else
		{
			throw new Exception("Undefined property " . get_class($this) . "::" . $name);
		}
	}
	
	public function __set($name,$value)
	{
		$setter='set'.$name;
		
		if(method_exists($this,$setter))
		{
			$this->$setter($value);
		}
		else if(method_exists($this,'get'.$name))
		{
			throw new Exception("The " . get_class($this) . "::" . $name . " property is read only");
		}
		else
		{
			throw new Exception("Undefined property " . get_class($this) . "::" . $name);
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @return JSCommandsEnum
	 */
	public function getCommand()
	{
		return $this->_commands;
	}
}