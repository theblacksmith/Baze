<?php
/**
 * Arquivo Panel.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */


/**
 * Classe Panel
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Panel extends HtmlComponent
{
	protected $tagName = 'div';
	protected $caption;
	
	/**
	 * @var Event
	 * @desc Occurs when mouse clicks an object
	 */
	protected $onClick;

	function __construct($caption = null)
	{
		parent::__construct();
		
		$this->caption = $caption;
		
		$this->attributes = array_merge($this->attributes, array(
			'php:class' => 'Panel',
			'php:runat' => 'server'
		));
	}
}