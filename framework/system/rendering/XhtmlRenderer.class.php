<?php
/**
 * Arquivo da classe XhtmlRender
 *
 * Esse arquivo ainda não foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.rendering
 */
import('system.rendering.IRenderer');
/**
 * Classe XhtmlRender
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.rendering
 */
class XhtmlRenderer implements IRenderer // extends XmlRender
{

	// HTML 4.01 transitional doctype supports all attributes of HTML 4.01,
	// presentational attributes, deprecated elements, and link targets. It is meant to be used for webpages that are transitioning to HTML 4.01 strict:
	const HTML_4_01_TRANS = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';

	// HTML 4.01 Strict is a trimmed down version of HTML 4.01 with emphasis on structure over presentation. Deprecated elements and attributes (including most presentational attributes), frames, and link targets are not allowed. CSS should be used to style all elements:
	const HTML_4_01_STRICT = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';

	// HTML 4.01 frameset is identical to Transitional above, except for the use of <frameset> over <body>:
	const HTML_4_01_FRAME = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';

	// Use XHTML 1.0 Transitional when your webpage conforms to basic XHTML rules,
	// but still uses some HTML presentational tags for the sake of viewers that don't support CSS:
	const XHTML_1_0_TRANS = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

	// Use XHTML 1.0 Strict when your webpage conforms to XHTML rules
	// and uses CSS for full separation between content and presentation:
	const XHTML_1_0_STRICT = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';

	// XHTML 1.0 frameset is identical to Transitional above, except in the use of the <frameset> tag over <body>:
	const XHTML_1_0_FRAME = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';

	// XHTML 1.1 declaration. Visit the WC3 site for an overview and what's changed from 1.0:
	const XHTML_1_1 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';

	/**
	 * An XMLWriter object to avoid concatenating a lot of strings
	 *
	 * @var XMLWriter
	 */
	private $xmlWriter;

	/**
	 * The selected doctype
	 * @var unknown_type $doctype
	 */
	private $doctype;

	/**
	 * The encoding of the rendered content
	 * @var string $encoding
	 */
	private $encoding;
	
	

	public function __construct($doctype = self::XHTML_1_1, $encoding = 'UTF-8')
	{
		$this->doctype = $doctype;
		$this->encoding = $encoding;
	}

	/**
	 * Returns whether an element is a leaf or not
	 *
	 * @return boolean
	 */
	public function isLeaf($name)
	{
		switch($name)
		{
			case 'br':
			case 'img':
			case 'input':
			case 'meta':
			case 'link':
			case 'hr':
			case 'param':
			case 'base':
			case 'area':
				return true;

			default :
				return false;
		}
	}

	/**
	 * Renders a XHTML Document
 	 * @access public
	 * @param IRenderable $object
	 * @param IOutputWriter $writer
	 */
	public function render(IRenderable $object, IWriter $writer)
	{
		if($object instanceof Page)
		{
			$writer->write("<?xml version=\"1.0\" encoding=\"{$this->encoding}\"?>\n");
			$writer->write($this->doctype."\n");
		}
		
		if($object->hasCustomRenderer())
		{
			$renderer = $object->getCustomRenderer();
			$renderer->render($object, $writer);
		}
		else
		{
			$name = $object->getObjectName();
			$atts = $object->getAttributesToRender();
			$joinedAtts = array();

			if(count($atts) > 0)
			{
				foreach ($atts as $key => $val) {
					//if(is_callable($val)) {
					//	$joinedAtts[] = call_user_func($val);
					//}
					//else
						$joinedAtts[] = $key.'="'.$val.'"';
				}

				$openTag = "<$name ".join(' ', $joinedAtts).'>';
			}
			else
				$openTag = "<$name>";

			$writer->write($openTag);

			$object->renderChildren($this, $writer);

			$writer->write("</$name>");
		}
	}
	
// @todo: Decide about the use (or not) of this methods
//
//	/**
//	 * Writes a comment node to $writer
//	 */
//	public function renderComment(IXMLNode $node, IOutputWriter $writer)
//	{
//		$writer->write('<!-- '.$node->getNodeValue().' -->');
//	}
//
//
//	/**
//	 * Writes a tag opening text to $writer
//	 *
//	 * @param unknown_type $name
//	 * @param unknown_type $attributes
//	 * @param IOutputWriter $writer
//	 */
//	public function renderElement(IXMLNode $node, IOutputWriter $writer)
//	{
//		$name = $node->getNodeName();
//		$attStr = '';
//		$attributes = $node->getAttributes();
//
//		foreach($attributes as $n => $val)
//			$attStr .= " $n=\"$val\"";
//
//		if($this->isLeaf($name))
//			$writer->write('<'.$name.$attStr.' />');
//		else
//		{
//			$writer->write('<'.$name.' '.$attStr.'>');
//
//			$children = $node->getChildNodes();
//			foreach($children as $c)
//			{
//				$this->writeNode($c, $writer);
//			}
//
//			$writer->write('</'.$name.'>');
//		}
//	}
//
//	/**
//	 * @param string $text
//	 *
// 	 * @access public
//	 * @param mixed $text
//	 */
//	public function renderText(IXMLTextNode $text,IOutputWriter $writer)
//	{
//		$writer->write($text->getText());
//	}
//
//	/**
//	 * @param string $text O texto a ser impresso dentro da seção CDATA
//	 *
// 	 * @access public
//	 * @param mixed $text
//	 */
//	public function renderCdata(IXMLNode $node, IOutputWriter $writer)
//	{
//		$writer->write('<![CDATA['.$node->getNodeValue().']]>');
//	}
//
//	/**
//	 * @return string
//	 *
// 	 * @access public
//	 */
//	public function flush()
//	{}
}



