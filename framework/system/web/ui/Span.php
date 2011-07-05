<?php
/**
 * Arquivo Span.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */


/**
 * Classe Span
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Span extends PageComponent
{
	protected $tagName = 'span';
	
	/**
	* Span Tag Properties
	*/
	//protected $class;
	//protected $dir;
	//protected $id;	
	//protected $lang;
	//protected $style;	
	private $text;
	protected $title;
	//protected $xmlLang;
	protected $accesskey;

	/**
	* Event
	*/
	protected $onFocus;
	protected $onBlur;
	//protected $onclick;
	//protected $ondblclick;
	//protected $onmousedown;
	//protected $onmouseup;
	//protected $onmouseover;
	//protected $onmousemove;
	//protected $onmouseout;
	//protected $onkeypress;
	//protected $onkeydown;
	//protected $onkeyup;

	function __construct()
	{
		$this->attributes = array(
			'php:class' => 'Span'
		);
		
		parent::__construct();
	}
}