<?php

/**
 * Arquivo Button.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.table
 */

/**
 * Import
 */

import( 'system.web.ui.IButton' );

/**
 * Classe Button
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.table
 */
class Button extends InteractiveContainer// implements IButton
{
	/**
	 * Button Tag Properties
	 * @access protected
	 * @tag <button></button>
	 */
	protected $accessKey;
	//protected $class;
	//protected $dir;
	protected $disabled;
	//protected $id;	[Propriedade Herdada]
	//protected $lang;
	protected $name;
	//protected $style;	[Propriedade Herdada]
	protected $tabindex;
	//protected $title;
	protected $type;
	protected $value;
	//protected $xmlLang;

	/**
	 * Events
	 */
	protected $onFocus;
	protected $onBlur;
	protected $onSelect;
	protected $onChange;
	//protected $onClick;
	//protected $onDblClick;
	//protected $onMouseDown;
	//protected $onMouseUp;
	//protected $onMouseOver;
	//protected $onMouseMove;
	//protected $onMouseOut;
	//protected $onKeyPress;
	//protected $onKeyDown;
	//protected $onKeyUp;

	/**
	 * Constructor
	 */
	function __construct($type = "button")
	{
		parent::__construct();

		$this->type = $type;
	}

	function initialize(DOMElement $elem)
	{
		$this->disabled = false;
		parent::initialize($elem);
	}
	/**
	 * Function getEntireElement
	 * @access private
	 * @return string
	 */
	protected function getEntireElement()
	{
		$xhtml = '';
		$xhtml .= '<button '.$this->getPropertiesList() . '>';
		
		if(sizeof($this->children) > 0)
			$xhtml .= $this->getChildrenXHTML();
		else if(($v = $this->get("value")) != null)
			$xhtml .= $v;
			
		$xhtml .= '</button>';
		
		return $xhtml;
	}

	public function onFocus($args) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args) {
		$this->raiseEvent(BLUR,$args);
	}

	public function onSelect($args) {
		$this->raiseEvent(SELECT,$args);
	}

	public function onChange($args) {
		$this->raiseEvent(CHANGE,$args);
	}

}