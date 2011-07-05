<?php
/**
 * Arquivo Iframe.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */


/**
 * Classe Iframe
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Iframe extends InteractiveContainer
{
	/**#@+
	 * Property
	 *
	 * @access protected
	 * @var string
	 */
	protected $align;
	//protected $class;
	protected $frameBorder;
	protected $height;
	//protected $id;	[Propriedade Herdada]
	protected $longdesc;
	protected $marginheight;
	protected $marginwidth;
	protected $name;
	protected $scrolling;
	protected $src;
	//protected $style;	[Propriedade Herdada]
	//protected $title;
	protected $width;
	/**#@-*/

	/**#@+
	 * Event
	 *
	 * @access protected
	 * @var string
	 */
	 // não há Propriedades de Evento para esta classe
	/**#@-*/

	/**
	 * @return string
	 */
	protected function getEntireElement()
	{
		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       ">" .
		       $this->get("value") .
		       $this->getCloseTag();
			   //$this->getChildrenXHTML() .
		//switch ($part)
		//{
		//	case XML_PART_ATTRIBUTES :
		//	return $this->getPropertiesList();
		//
		//	case XML_PART_CLOSE_TAG :
		//	return "\n</iframe>";
		//
		///**
		// *	Notice: This element doesn't have children
		// */
		//case XML_PART_ENTIRE_ELEMENT :
		//
		///**
		// * Warning: Invalid argument supplied for foreach()
		// */
		//foreach ($this->chidren as $child)
		//{
		//	$xhtml .= $child->getXHTML(XML_PART_ENTIRE_ELEMENT);
		//}
		//return $this->getXHTML(XML_PART_OPEN_TAG) .
		//$this->getXHMTL(XML_PART_ATTRIBUTES) .
		//$this->getXHTML(XML_PART_CLOSE_TAG);
		//
		//
		//case XML_PART_OPEN_TAG :
		//if($this->renderXSL != null)
		//{
		//	$xslProc = new XSLTProcessor();
		//	$xslProc->importStylesheet(DOMDocument::loadXML($this->renderXSL));
		//	return $xslProc->transformToXml($this->getDefaultXHTML());
		//}
		//else
		//	return $this->getDefaultXHTML();
		//
		//
		//case XML_PART_TAG_CONTENT :
		//foreach ($this->chidren as $child)
		//{
		//	$xhtml .= $child->getXHTML(XML_PART_ENTIRE_ELEMENT);
		//}
		//
		//	return $xhtml;
		//}
	}

	private function getDefaultXHTML()
	{
		$xhtml = "";

		if(isset($this->style))
		{
			$style = $this->style->getPropertiesList();

			if($style == " style=\"\"")
			{
				$style = "";
			}

		}

		$xhtml .= "\n<iframe" . $style;

		return $xhtml;
	}

	protected function getCloseTag()
	{
		return "\n</iframe>";
	}
}