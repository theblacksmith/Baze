<?php
/**
 * Arquivo Image.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.image
 */

/**
 * Import
 */
import( 'system.web.ui.HtmlComponent' );

	
/**
 * Classe Image
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.image
 */
class Image extends HtmlComponent
{
	/**#@+
	 * Property
	 *
	 * @access protected
	 * @var string
	 */
	//protected $align;	//<< Propriedade envelhecida para os padrões W3C
	protected $alt;
	protected $border;
	//protected $class;
	protected $height;
	//protected $hspace;	//<< Propriedade envelhecida para os padrões W3C
	//protected $id; 	[propriedade herdada (Object)]
	protected $ismap;
	protected $longdesc;
	protected $name; 	// << Propriedade não consta nos padrões W3C
	protected $src;
	//protected $style; [propriedade herdada (Object)]
	//protected $title;
	protected $usemap;
	//protected $vspace; //<< Propriedade envelhecida para os padrões W3C
	protected $width;
	//protected $xmlLang;
	/**#@-*/

	/**#@+
	 * Events
	 *
	 * @access protected
	 * @var string
	 */
	/*
	protected $onclick;
	protected $ondblclick;
	protected $onmousedown;
	protected $onmouseup;
	protected $onmouseover;
	protected $onmousemove;
	protected $onmouseout;
	protected $onkeypress;
	protected $onkeydown;
	protected $onkeyup;
	*/
	/**#@-*/

	function __construct()
	{
		parent::__construct();
	}

	function initialize(DOMElement $elem)
	{
		$this->disabled = false;
		parent::initialize($elem);
	}

	protected function getEntireElement()
	{
		return '<img '.$this->getPropertiesList().' />';
	}

	///**
	// * @access public
	// * @param string
	// */
	//function createMap($mapName)
	//{
	//	$this->isMap = true;
	//	$this->useMap = new ImageMap($mapName);
	//}
}