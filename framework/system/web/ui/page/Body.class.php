<?php

/**
 * Arquivo Body.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */

/**
 * Classe Body
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
class Body extends PageComponent
{
	protected $alinkColor;
	protected $bgColor;
	protected $fgColor;
	protected $linkColor;
	protected $vlinkColor;

	/**
	 * @var Event
	 * @desc Occurs when a page is finished loading
	 */
	protected $_onLoad;

	/**
	 * @var Event
	 * @desc Occurs when the user exits the page
	 */
	protected $_onUnload;

	public function getObjectName()
	{
		return 'body';
	}
}