<?php
/**
 * Arquivo Hyperlink.class.php
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


/**
 * Classe Hyperlink
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class HyperLink extends InteractiveContainer
{
	/**#@+
	 * HyperLink Tag Property
	 *
	 * @access protected
	 * @var string
	 * @tag <a></a>
	 */
	protected $accesskey;
	protected $charset;
	//protected $class;
	protected $coords;
	//protected $dir;
	protected $href;
	protected $hreflang;
	//protected $id;		[Propriedade Herdada]
	//protected $lang;
	protected $name;
	protected $rel;
	protected $rev;
	protected $shape;
	//protected $style;		[Propriedade Herdada]
	protected $tabindex;
	protected $target;
	//protected $title;
	protected $type;
	//protected $xmlLang;
	/**#@-*/

	private $textValue;
	/**#@+
	 * Events
	 *
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
	 * @desc Constructor Method
	 *
	 * @param string $href
	 */
	function __construct($href = "")
	{
		$this->set("href", $href);
		parent::__construct();
		$this->noPrintArr[] = 'textValue';
	}
	
	/**
	 * @param DOMElement $elem
	 */
	public function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
	}
			

	/*
	 * @access public
	 * @return string
	 *
	public function getXHTML()
	{
		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       ">" .
		       $this->getChildrenXHTML() .
		       $this->closeTag();
	}*/

	/**
	 * Function getOpenTab()
	 *
	 * @author Luciano (28/06/06)
	 * @return string
	 */
	protected function getOpenTag()
	{
		return '<a '.$this->getPropertiesList().' >';
	}

	/**
	 * Function getAttributes()
	 *
	 * @author Luciano (23/06/06)
	 * @return string
	 */
	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	/**
	 * Function getTagContent()
	 *
	 * @author Luciano (23/06/06)
	 * @return string
	 */
	protected function getTagContent()
	{
		if (! empty($this->textValue))
			return $this->textValue;

		return $this->getChildrenXHTML();
	}

	/**
	 * Function getCloseTag()
	 *
	 * @author Luciano (23/06/06)
	 * @return string
	 */
	protected function getCloseTag()
	{
		return "</a>";
	}

	/**
	 * Function getEntireElement()
	 *
	 * @author Luciano (23/06/06)
	 * @return string
	 */
	protected function getEntireElement()
	{
		if ($this->class == NULL)
		{
			$this->setHTMLClass('fade');
		}
		
		$strOpen = $this->getOpenTag();
		$value = $this->getTagContent();
		$strClose = $this->getCloseTag();

		return $strOpen.$value.$strClose;
	}

	/**
	 * Function setValue()<br>
	 *
	 * @author Luciano (28/06/06)
	 * @param string $value
	 */
	public function setValue($value)
	{
		$value = trim($value);
		if (! empty($value) && $this->textValue !== $value)
		{
			$this->textValue = $value;
			return true;
		}
		return false;
	}

	public function onFocus($args) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args) {
		$this->raiseEvent(BLUR,$args);
	}
}