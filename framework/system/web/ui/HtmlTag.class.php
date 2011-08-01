<?php
/**
 * Arquivo HTMLTag.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

/**
 * Import
 */
require_once 'system/web/ui/HtmlComponent.class.php';

/**
 * Classe HTMLTag
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class HtmlTag extends HtmlComponent
{
	/**
	 * The tag name
	 *
	 * @access protected
	 * @var string
	 */
	private $name;

	public function __construct($name)
	{
		$this->tagName = $name;
	}

	public function getObjectName()
	{
		return $this->tagName;
	}
}