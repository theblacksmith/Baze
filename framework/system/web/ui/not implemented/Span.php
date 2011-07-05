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
class Span extends InteractiveContainer
{
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

	/**
	 * Construct Method()<br>
	 *
	 * @author Luciano (12/06/06)
	 */
	public function __construct()
	{
		parent::__construct();	
	}

	function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
	}
	
	/**
	 * Function getAttributes()
	 *
	 * @return string
	 */
	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	/**
	 * Function getEntireElement()
	 *
	 * @return string
	 */
	protected function getEntireElement()
	{
		return $this->getOpenTag() . $this->getChildrenXHTML() . $this->getCloseTag();
	}

	/**
	 * Function getOpenTab()
	 *
	 * @return string
	 */
	protected function getOpenTag()
	{
		return '<span '.$this->getPropertiesList().' >';
	}

	/**
	 * Function getCloseTag()
	 *
	 * @return string
	 */
	protected function getCloseTag()
	{
		return '</span>';
	}

	public function onFocus($args) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args) {
		$this->raiseEvent(BLUR,$args);
	}
}