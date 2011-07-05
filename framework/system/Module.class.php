<?php

/**
 * Module class file.
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package system
 */

/**
 * IModule defines the interface for all system modules
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.web
 */
interface IModule
{
	/**
	 * @return string Id of the service
	 */
	public function getId();

	/**
	 * @param string Id of the service
	 */
	public function setId($id);
}

/**
 * Module class.
 *
 * Module implements the basic methods required by IModule and may be
 * used as the basic class for application modules.
 *

 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package system
 */
abstract class Module extends Component
{
	/**
	 * @var string module id
	 */
	private $_id;

	/**
	 * @return string id of this module
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param string id of this module
	 */
	public function setId($value)
	{
		$this->_id=$value;
	}
}