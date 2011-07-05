<?php
/**
 * Arquivo EmbeddedObject.class.php
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
 * Classe EmbeddedObject
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class EmbeddedObject extends InteractiveContainer
{
	/**#@+
	 * EmbeddedObject Tag Properties
	 *
	 * @access protected
	 * @var string
	 * @tag <object></object>
	 */
	protected $align;
	protected $archive;
	protected $border;
	protected $class;
	protected $classid;
	protected $codebase;
	protected $codetype;
	protected $data;
	protected $declare;
	protected $dir;
	protected $embedHeight;			//<object...><embed height="$embedHeight...
	protected $embedPluginspage;	//<object...><embed height="$embedPluginspage...
	protected $embedQuality;		//<object...><embed height="$embedQuality...
	protected $embedSrc;			//<object...><embed height="$embedSrc...
	protected $embedType;			//<object...><embed height="$embedType...
	protected $embedWidth;			//<object...><embed height="$embedWidth...
	protected $height;
	protected $hspace;
	//protected $id;	[Propriedade Herdada]
	protected $lang;
	protected $name;
	protected $standby;
	//protected $style;	[Propriedade Herdada]
	protected $title;
	protected $type;
	protected $usemap;
	protected $vspace;
	protected $width;
	protected $xmlLang;
	/**#@-*/

	/**
	 * Event Attributes
	 *
	 * @access protected
	 * @var string
	 */
	 protected $accesskey;
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
	 protected $tabindex;

	function __construct()
	{
		$this->set("classid", "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000");
		$this->set("codebase", "http://active.macromedia.com/flash2/cabs/swflash.cab");
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getXHTML()
	{

		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       ">" .
		       $this->getChildrenXHTML() .
		       $this->closeTag();
	}

	/**
	 * @access private
	 * @return string
	 */
	private function getDefaultXHTML()
	{
		$xhtml = null;

		$xhtml .= "\n<object";

		return $xhtml;
	}

	/**
	 * @access private
	 * @return string
	 */
	private function closeTag()
	{
		return "\n</object>";
	}

	/**
	 * @access public
	 * @param Param $object
	 */
	function addChild(Param $object)
	{
		parent::addChild($object);
	}
}