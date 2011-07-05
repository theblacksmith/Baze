<?php
/**
 * Arquivo da classe WebApplication
 *
 * Esse arquivo ainda não foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */

/**
 * Classe WebApplication
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */
class WebApp extends App {

	/**
	 * @property Client
	 */
	public $user;

	/**
	 * @var string
	 */
	private $defaultEncode = 'UTF-8';

	/**
	 * implements the Singleton pattern.
	 *
	 * @static
	 * @return WebApp A reference to the unique WebApp object
	 */
	public static function getInstance()
	{
		if(isset($this->instance))
			return $this->instance;

		$this->instance = new WebApp();

		return $this->instance;
	}

	/**
 	 * @access public
	 * @param mixed $file
	 */
	public function addUploadedFile($file) {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access public
	 */
	public function processRequest() {
		throw new NotImplementedException(__method__);
	}
}