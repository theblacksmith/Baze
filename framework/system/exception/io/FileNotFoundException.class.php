<?php 
/**
 * Arquivo da classe FileNotFoundException
 * 
 * Esse arquivo ainda nÃ£o foi documentado
 * 
 * @author Saulo Vallory 
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package system.exception.io
 */

/** 
 * Classe FileNotFoundException 
 * 
 * Essa classe ainda nÃ£o foi documentada
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package system.exception.io
 */
class FileNotFoundException extends IOException
{
	/**
	 * Path of the required file
	 *
	 * @var string
	 */
	private $path;
	
	/**
	 * Constructor
	 *
	 * @param string $path Path of the required file
	 * @param string[optional] $message Custom message
	 * @param string $strings Strings to replace in custom message hooks ({0}, {1}, ...)
	 */
	public function __construct($path, $message='', $strings = array())
	{
		if($message == '')
			$message = ErrorMsg::$FileNotFound;

		$this->path = $path;

		parent::__construct($message, $strings);

	}

	/**
	 * Gets the path of the required file
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Defines the path of the required file
	 */
	public function setPath($path)
	{
		return $this->path = $path;
	}
}