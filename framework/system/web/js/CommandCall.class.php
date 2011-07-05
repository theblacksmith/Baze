<?php
/**
 * Arquivo BackTrace.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

/**
 * Classe BackTrace<br />
 * This class is only a structure to store the data of a command that will be sent to client.
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class CommandCall {

	/**
	 * @type Array
	 */
	public $arguments = array();

	/**
	 * @type MessageParsePhaseEnum
	 */
	public $executeOn;

	/**
	 * @type string
	 */
	public $id;

	/**
	 * @var string 
	 */
	public $name;
	
	public function __construct($options)
	{
		$this->checkArgumentTypes = true;
		$this->id = uniqid("CMD_");
		
		foreach($options as $prop => $value)
			$this->$prop = $value;
	}
}