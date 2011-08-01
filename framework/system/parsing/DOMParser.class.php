<?php

import('system.diagnostics.Debug');
import('system.application.services.pageService.HtmlFragment');
import('system.exceptions.xml.XMLParseException');

abstract class DOMParser
{
	/**
	 * @var handler
	 * @desc
	 */
	private $document;

	/**
	 * @var Debug
	 */
	protected $debug;

	public function __construct(array $options = null)
	{
		$this->debug = new Debug(false);

		$this->document = new DOMDocument();

		if(isset($options['preserveWhiteSpace']))
			$this->document->preserveWhiteSpace = $options['preserveWhiteSpace'];
	}

	public function __destruct()
	{
		$this->debug->htmlMsg(__FUNCTION__);
		unset($this->document);
	}

	protected function parse($source)
	{
		try {
			if($source != "")
			{
				// @todo find a better way to remove the require line
				$source = substr($source, strpos($source, '?>')+2);
				$source = '<?xml version="1.0" encoding="utf-8"?>' . $source;
				$this->document->loadXML($source);
			}
			else
				throw new XMLParseException(Msg::EmptyXmlDoc, null, 1);
		}
		catch(DOMException $e)
		{
			throw XMLParseException::fromException($e);
		}

		$pageElem = $this->document->documentElement;

		$this->_recursiveParse($pageElem);
	}

	private function _recursiveParse(DOMNode $node)
	{
		$this->debug->htmlMsg('parsing node. Type: '.$node->nodeType.'. Name: '.$node->nodeName.'. Value: '.$node->nodeValue);

		switch ($node->nodeType)
		{
			case XML_ELEMENT_NODE :
				$this->startElementHandler($this, $node->nodeName, $node->attributes);
				foreach ($node->childNodes as $child)
					$this->_recursiveParse($child);
				$this->endElementHandler($this, $node->nodeName);
				break;

			case XML_CDATA_SECTION_NODE :
				$this->characterDataHandler($this, $node->nodeValue);
				break;

			case XML_COMMENT_NODE :
				$this->commentHandler($this, $node->nodeValue);
				break;

			case XML_DOCUMENT_NODE :
				$this->documentNodeHandler($this, $node->nodeName, $node->attributes);
				break;

			case XML_DOCUMENT_TYPE_NODE :
				$this->documentTypeHandler($this, $node);
				break;

			case XML_TEXT_NODE :
				$this->textNodeHandler($this, $node->nodeValue);
				break;

			// @todo check which node types we should test
			case XML_DTD_NODE :
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_DTD_NODE'));
				break;

			case XML_ENTITY_NODE :
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_ENTITY_NODE'));
				break;

			case XML_ENTITY_REF_NODE :
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_ENTITY_REF_NODE'));
				break;

			case XML_ENTITY_DECL_NODE :
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_ENTITY_DECL_NODE'));
				break;

			case XML_ELEMENT_DECL_NODE :
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_ELEMENT_DECL_NODE'));
				break;

			case XML_NAMESPACE_DECL_NODE :
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_NAMESPACE_DECL_NODE'));
				break;

			case XML_NOTATION_NODE :
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_NOTATION_NODE'));
				break;

			case XML_PI_NODE:
				throw new NotImplementedException('Parsing {{nodeType}} nodes is not supported.', array('XML_PI_NODE'));
				break;
		}
	}

	/**
	 * The character data handler function for the XML parser.
	 *
	 * Character data handler is called for every piece of a text in the XML document.
	 * It can be called multiple times inside each fragment (e.g. for non-ASCII strings).
	 *
	 * @param DOMParser $parser a reference to the XML parser calling the handler.
	 * @param string $data the character data as a string.
	 */
	protected function characterDataHandler(DOMParser $parser, $data)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $data);
	}

	/**
	 * The default handler function for the XML parser.
	 *
	 * Default handler is called for every piece of XML which doesn't have a proper handler
	 *
	 * @param DOMParser $parser a reference to the XML parser calling the handler.
	 * @param string $data contains the character data.This may be the XML declaration, document type declaration, entities or other data for which no other handler exists.
	 */
	protected function defaultHandler(DOMParser $parser, $data)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $data);
	}

	/**
	 * Enter description here...
	 *
	 * @param DOMParser $parser
	 * @param string $data
	 */
	protected function commentHandler(DOMParser $parser, $data)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $data);
	}

	/**
	 * Enter description here...
	 *
	 * @param DOMParser $parser
	 * @param string $data
	 * @param DOMNodeList $attribs
	 */
	protected function documentNodeHandler(DOMParser $parser, $node, DOMNodeList $attribs)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $node, $attribs);
	}

	/**
	 * Enter description here...
	 *
	 * @param DOMParser $parser
	 * @param unknown_type $data
	 */
	protected function documentTypeHandler(DOMParser $parser, DOMDocumentType $node)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $node);
	}

	/**
	 * Enter description here...
	 *
	 * @param DOMParser $parser
	 * @param string $data
	 */
	protected function textNodeHandler(DOMParser $parser, $data)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $data);
	}

	/**
	 * Start element handler is called every time the parser finds a tag opening
	 *
	 * @param DOMParser $parser A reference to the XML parser calling the handler.
	 * @param string $name The name of the element for which this handler is called.If case-folding is in effect for this parser, the element name will be in uppercase letters.
	 * @param array $attribs An associative array with the element's attributes (if any).The keys of this array are the attribute names, the values are the attribute values.Attribute names are case-folded on the same criteria as element names.Attribute values are not case-folded. The original order of the attributes can be retrieved by walking through attribs the normal way, using each().The first key in the array was the first attribute, and so on.
	 */
	protected function startElementHandler(DOMParser $parser, $name, DOMNodeList $attribs)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $name, $attribs);
	}

	/**
	 * End element handler is called every time the parser finds a closing tag
	 *
	 * @param DOMParser $parser A reference to the XML parser calling the handler.
	 * @param string $name The second parameter, name , contains the name of the element for which this handler is called.If case-folding is in effect for this parser, the element name will be in uppercase letters.
	 */
	protected function endElementHandler(DOMParser $parser, $name)
	{
		$this->debug->htmlMsg(__FUNCTION__);
		$this->defaultHandler($parser, $name);
	}

	/**
	 * This function just throws away the data it receives and can be used to ignore any
	 * content on the xml being parsed
	 *
	 * @param DOMParser $parser
	 * @param string $data
	 */
	protected function nullHandler(DOMParser $parser, $data)
	{
		$this->debug->htmlMsg(__FUNCTION__);
	}
}