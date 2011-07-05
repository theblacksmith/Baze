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