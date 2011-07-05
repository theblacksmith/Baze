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
class XhtmlRender_Old // extends XmlRender
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

	public function __construct($doctype = self::XHTML_1_1)
	{
		$this->doctype = $doctype;
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
	 * @param IRenderable $node
	 * @param IOutputWriter $writer
	 */
	public function render(IXMLNode $node, IOutputWriter $writer) {
		if($node instanceof Page)
			$writer->write($this->doctype."\n");
			
		$this->writeNode($node, $writer);
	}

	public function writeNode(IXMLNode $node, IOutputWriter $writer)
	{
		switch($node->getNodeType())
		{
			case XML_ELEMENT_NODE :
				$this->writeElement($node, $writer);
				break;

			case XML_TEXT_NODE :
				$this->writeText($node, $writer);
				break;

			case XML_ATTRIBUTE_NODE :
				return "XML_ATTRIBUTE_NODE writing not implemented";
				break;

			case XML_CDATA_SECTION_NODE :
				return "XML_CDATA_SECTION_NODE writing not implemented";
				break;

			case XML_ENTITY_REFERENCE_NODE :
				return "XML_ENTITY_REFERENCE_NODE writing not implemented";
				break;

			case XML_ENTITY_NODE :
				return "XML_ENTITY_NODE writing not implemented";
				break;

			case XML_PROCESSING_INSTRUCTION_NODE :
				return "XML_PROCESSING_INSTRUCTION_NODE writing not implemented";
				break;

			case XML_COMMENT_NODE :
				return "XML_COMMENT_NODE writing not implemented";
				break;

			case XML_DOCUMENT_NODE :
				return "XML_DOCUMENT_NODE writing not implemented";
				break;

			case XML_DOCUMENT_TYPE_NODE :
				return "XML_DOCUMENT_TYPE_NODE writing not implemented";
				break;

			case XML_DOCUMENT_FRAG_NODE :
				return "XML_DOCUMENT_FRAG_NODE writing not implemented";
				break;

			case XML_NOTATION_NODE :
				return "XML_NOTATION_NODE writing not implemented";
				break;
		}
	}

	/**
	 * Writes a comment node to $writer
	 */
	public function writeComment(IXMLNode $node, IOutputWriter $writer)
	{
		$writer->write('<!-- '.$node->getNodeValue().' -->');
	}


	/**
	 * Writes a tag opening text to $writer
	 *
	 * @param unknown_type $name
	 * @param unknown_type $attributes
	 * @param IOutputWriter $writer
	 */
	public function writeElement(IXMLNode $node, IOutputWriter $writer)
	{
		$name = $node->getNodeName();
		$attStr = '';
		$attributes = $node->getAttributes();

		foreach($attributes as $n => $val)
			$attStr .= " $n=\"$val\"";

		if($this->isLeaf($name))
			$writer->write('<'.$name.$attStr.' />');
		else
		{
			$writer->write('<'.$name.' '.$attStr.'>');

			$children = $node->getChildNodes();
			foreach($children as $c)
			{
				$this->writeNode($c, $writer);
			}

			$writer->write('</'.$name.'>');
		}
	}

	/**
	 * @param string $text
	 *
 	 * @access public
	 * @param mixed $text
	 */
	public function writeText(IXMLTextNode $text,IOutputWriter $writer)
	{
		$writer->write($text->getText());
	}

	/**
	 * @param string $text O texto a ser impresso dentro da seção CDATA
	 *
 	 * @access public
	 * @param mixed $text
	 */
	public function writeCdata(IXMLNode $node, IOutputWriter $writer)
	{
		$writer->write('<![CDATA['.$node->getNodeValue().']]>');
	}

	/**
	 * @return string
	 *
 	 * @access public
	 */
	public function flush()
	{}
}



