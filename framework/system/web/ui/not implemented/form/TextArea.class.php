<?php
/**
 * Arquivo TextArea.class.php
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */




/**
 * Classe TextArea
 * 
 * @author 		Luciano AJ
 * @copyright  	2007 Neoconn Networks
 * @license    	http://baze.saulovallory.com/license
 * @version    	SVN: $ID$
 * @since      	0.9
 * @package 	Baze.classes.web.form
 */
class TextArea extends InteractiveContainer implements IFormField
{
	/**
	 * TextArea Properties
	 * @access protected
	 */
	protected $class;
	protected $cols;
	protected $maxlength;
	protected $name;
	protected $readonly;
	protected $disabled;
	protected $rows;		
	protected $size;
	protected $title;
	protected $value;
	protected $wrap;
	protected $accesskey;
	protected $tabindex;

	/**
	 * Event Attributes
	 * @access protected
	 */
	protected $onFocus;
	protected $onBlur;
	protected $onSelect;
	protected $onChange;

	/**
	 * @author Luciano
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->phpclass = "TextArea";
	}
	

	/**
	 * @param DOMELement $elem
	 */
	function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
	}

	/**
	 * @author Luciano
	 * @version 1.0
	 * 
	 * @return string XHTML
	 */
	protected function getEntireElement()
	{
		return $this->getOpenTag() . $this->getChildrenXHTML() . '</textarea>';
	}
	
	/**
	 * @author Luciano
	 * @version 1.0
	 * 
	 * @return string XHTML
	 */
	protected function getOpenTag()
	{
		return '<textarea ' . $this->getPropertiesList() . '>';
	}
	
	/**
	 * @author Luciano
	 * @version 1.0
	 * 
	 * @return bool
	 */
	public function addChild($mixed)
	{
		if (is_scalar($mixed))
		{
			parent::addChild($mixed);
			return true;
		}
		
		return false;
	}
	
	/**
	 * @author Luciano
	 * @version 1.0
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->getChildrenXHTML();
	}

	/**
	 * @author Luciano
	 * @version 1.0
	 * 
	 * @param mixed $mixed
	 * @return bool
	 */
	public function setValue($mixed = null)
	{
		if (count($this->children) > 0)
		{
			$this->children = array();
		}
		
		return $this->addChild($mixed);
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