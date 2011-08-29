<?php
/**
 * Arquivo Literal.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
require_once 'system/Component.class.php';

/**
 * Classe Literal
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Literal extends PageComponent
{
	/**
	 * @var mixed
	 * @desc the literal value
	 */
	public $value;

	/**
	 * Constructor
	 *
	 * @param string $value
	 */
	function __construct($value = "")
	{
		$this->value = $value;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function setValue($val)
	{
		$this->value = $val;
	}
}