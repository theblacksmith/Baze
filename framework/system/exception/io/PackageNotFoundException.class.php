<?php 
/**
 * Arquivo da classe PackageNotFoundException
 * 
 * Esse arquivo ainda não foi documentado
 * 
 * @author Saulo Vallory 
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package system.exception.io
 */

/** 
 * Classe PackageNotFoundException 
 * 
 * Essa classe ainda não foi documentada
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package system.exception.io
 */
class PackageNotFoundException extends IOException 
{
	/**
	 * The required package
	 *
	 * @var string
	 */
	private $pkg;

	/**
	 * Constructor 
	 *
	 * @param string $pkg The required package
	 * @param string|ErrorMsg $message $extraParams  String or ErrorMsg instance Custom message
	 * @param array $strings Strings to replace in custom message hooks ({0}, {1}, ...)
	 */
	public function __construct($pkg, $message='', $strings=array())
	{
		$this->pkg = $pkg;
		
		if($message == '')
			$message = ErrorMsg::$Import_PackageNotFound;
			
		$strings['pkg'] = $pkg;
		
		parent::__construct($message,$strings);
	}
	
	/**
	 * @return string The required package
	 */
	public function getPackage() {
		return $this->pkg ;
	}
	
	/**
	 * @param string $pkg The required package
	 */
	public function setPackage($pkg) {
		$this->pkg = $pkg ;
	}

}