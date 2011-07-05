<?php
/**
 * Arquivo MapArea.class.php
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
 * Classe MapArea
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.image
 */
class MapArea extends HtmlComponent
{
	/**#@+
	 * Tag Property <area />
	 *
	 * @access private
	 * @var string
	 */
	protected $acesskey;
	protected $alt;
	//protected $class;
	protected $coords;
	//protected $dir;
	protected $href;
	//protected $id;	[propriedade herdada]
	protected $lang;
	protected $nohref;
	protected $shape;
	//protected $style	[propriedade herdada]
	protected $tabindex;
	//protected $target;
	//protected $title;
	//protected $xmlLang;

	// Propriedades de Eventos
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
	protected $onFocus;
	protected $onBlur;
	/**#@-*/

	/**
	 * @access public
	 * @return string
	 */
	function getXHTML()
	{

		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       $this->closeTag();
	}

	/**
	 * @access private
	 * @return string
	 */
	private function getDefaultXHTML()
	{
		$xhtml = "";
		$style = null;

		if(isset($this->style))
		{
			$style = $this->style->getPropertiesList();

			if($style == " style=\"\"")
			{
				$style = "";
			}

		}

		$xhtml .= "\n<area" . $style;

		return $xhtml;
	}

	/**
	 * @access private
	 * @return string
	 */
	private function closeTag()
	{
		return "/>";
	}

	public function onFocus($args) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args) {
		$this->raiseEvent(BLUR,$args);
	}

}