<?php 
/**
 * Arquivo da classe ClassNotFoundException
 * 
 * Esse arquivo ainda não foi documentado
 * 
 * @author Saulo Vallory 
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @class system.exception.io
 */

/** 
 * Classe ClassNotFoundException 
 * 
 * Essa classe ainda não foi documentada
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @class system.exception.io
 */
class ClassNotFoundException extends IOException 
{
	/**
	 * The required class
	 *
	 * @var string
	 */
	private $cls;

	/**
	 * Constructor 
	 *
	 * @param string $cls The required class
	 * @param string|ErrorMsg $message $extraParams  String or ErrorMsg instance Custom message
	 * @param array $strings Strings to replace in custom message hooks ({0}, {1}, ...)
	 */
	public function __construct($cls, $message='', $strings=array())
	{
		$this->cls = $cls;
		
		if($message == '')
			$message = ErrorMsg::$Import_ClassNotFound;
			
		$strings['cls'] = $cls;
		
		parent::__construct($message,$strings);
	}
	
	/**
	 * @return string The required class
	 */
	public function getClass() {
		return $this->cls ;
	}
	
	/**
	 * @param string $cls The required class
	 */
	public function setClass($cls) {
		$this->cls = $cls ;
	}

}