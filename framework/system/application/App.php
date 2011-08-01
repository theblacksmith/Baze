<?php
/**
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */

import('system.application.AppModule');
import('system.configuration.ConfigException');

/**
 * This abstract class implements the basic functionalities of an Application
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */
abstract class App extends BazeObject
{

	// #removeBlock
	// This block of code (up to @endRemoveBlock) will be removed within build.
	// It's here just to allow code completion of magic properties.

	/// @cond user

	/**
	 * @var AppConfig
	 * @desc The application config defined in baseconf file
	 */
	//public $Config;

	/// @endcond

	// #endRemoveBlock

	/// @cond internal

	/**
	 * @var AppConfig
	 * @desc The application config defined in baseconf file
	 */
	protected $_config;

	/**
	 * @var Event
	 * @desc Fired after App.init(AppConfig $cfg)
	 */
	protected $_onInit;

	/// @endcond
	
	protected function __construct(){}
	
	/**
	 * implements the Singleton pattern.
	 *
	 * @static
	 * @return App A reference to the unique BazeApplication object
	 */
	public static function getInstance()
	{
		throw new NotImplementedException("This method should be overwriten by the extending class.");
	}
	
	/**
	 * Initializes the application based on config
	 * @todo this method shouldn't be aware of 'baseconf_browser.xml'
	 * @param AppConfig $cfg
	 * @throws ConfigException
	 */
	abstract public function init(AppConfig $cfg);

	/**
	 * Runs the application to handle the resquest
	 * @todo this method shouldn't receive HTTP paremeters. The system class is assuming every app works over http
	 * @param HttpRequest $req
	 */
	abstract public function run(HttpRequest $req, HttpResponse $resp);

	/**
 	 * Loads the application state from cache
	 */
	protected function loadState()
	{

	}

	/**
	 * Returns the application configuration
	 *
	 * @return AppConfig
	 */
	public static function getConfig()
	{
		throw new NotImplementedException("This method should be overwriten by the extending class.");
	}
}