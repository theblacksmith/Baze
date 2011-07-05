<?php
/**
 * Arquivo FileReceiver.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
require_once("System.class.php");

/**
 * Classe FileReceiver
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class FileReceiver
{
	private static $instance;
	
	public function __construct()
	{
		if(!array_key_exists("page", $_GET) )
			return;
		
		$system = System::getInstance();
		
	}
	
	public static function getInstance()
	{
		if(FileReceiver::$instance)
			return FileReceiver::$instance;
		
		FileReceiver::$instance = new FileReceiver();
		
		return FileReceiver::$instance;
	}
}

if(count($_FILES) > 0)
	FileReceiver::getInstance();