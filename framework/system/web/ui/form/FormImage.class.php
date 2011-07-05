<?php
/**
 * Arquivo FormImage.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */

/**
 * Import
 */
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.Button' );
//import( 'system.web.ui.IButton' );

/**
 * Classe FormImage
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class FormImage extends Button// implements IButton
{
	/**
	 * Tag Properties
	 *
	 * @access protected
	 * @var string
	 */
	//protected $align;
	//protected $alt;
	//protected $class;
	//protected $dir;
	protected $disabled;
	//protected $id;		[Propriedade Herdada]
	//protected $lang;
	//protected $name;
	//protected $size;		//<input width="$size" type="image"...
	//protected $src;
	//protected $style;		[Propriedade Herdada]
	//protected $title;
	//protected $type;
	//protected $values;
	//protected $xmlLang;
	protected $tabIndex;

	/**
	 * Event Attributes
	 *
	 * @access protected
	 * @var string
	 */
	protected $onFocus;
	protected $onBlur;
	protected $onSelect;
	protected $onChange;
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
	 * Constructor
	 *
	 * @param string $src
	 */
	function __construct($src = "")
	{
		parent::__construct();
		$this->set("src", $src);
		$this->set("type", "image");
	}

	function initialize(DOMElement $elem)
	{
		$this->disabled = false;
		parent::initialize($elem);
	}

	public function getEntireElement()
	{
		return "\n<input " . $this->getPropertiesList() . " />";
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