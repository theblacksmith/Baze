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
class HyperLink extends HtmlComponent
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

	public $tagName = 'a';
	
	/**
	 * @desc Constructor Method
	 *
	 * @param string $href
	 */
	function __construct($href = "")
	{
		$this->Href = $href;
		parent::__construct();
	}
}