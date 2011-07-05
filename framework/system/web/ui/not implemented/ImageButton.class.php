<?php
/**
 * Arquivo ImageButton.class.php
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
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.IButton' );

/**
 * Classe ImageButton
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class ImageButton extends HtmlComponent
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
	protected $type;
	//protected $title;
	private $src;
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
	function __construct()
	{
		parent::__construct();
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
		$content = '<img src="' . $this->src . '" />';
				
		$xhtml = '';
		$xhtml .= '<button '.$this->getPropertiesList() . ' >' . $content . $this->getCloseTag();
		return $xhtml;
	}
	
	/**
	 * Function getCloseTag()<br><br>
	 * 
	 * @author Luciano (22/08/06)
	 */
	protected function getCloseTag()
	{
		return '</button>';
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
	
	/**
	 * Function setSrc()<br><br>
	 * 
	 * @author Luciano (22/08/06)
	 */
	public function setSrc($src)
	{
		$this->src = $src;
	}

}