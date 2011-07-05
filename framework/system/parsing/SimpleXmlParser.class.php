<?php

import('system.diagnostics.Debug');
import('system.exceptions.xml.XMLParseException');

abstract class SimpleXmlParser
{
	/**
	 * @var handler
	 * @desc
	 */
	private $parser;

	/**
	 * @var Debug
	 */
	protected $debug;

	public function __construct(array $options = null)
	{
		$this->debug = new Debug(false);
		$encd = null;

		if(isset($options['encoding']))
		{
			$encd = $options['encoding'];
			unset($options['encoding']);
		}

		$this->parser = xml_parser_create($encd);

		if($options !== null)
		{
			foreach ($options as $key => $value) {
				xml_parser_set_option($this->parser, $key, $value);
			}
		}

		xml_set_object($this->parser, $this);
		xml_set_character_data_handler($this->parser, 'characterDataHandler');
		xml_set_default_handler($this->parser, 'defaultHandler');
		xml_set_element_handler($this->parser, 'startElementHandler', 'endElementHandler');
		xml_set_start_namespace_decl_handler($this->parser, 'startNamespaceDeclHandler');
		xml_set_end_namespace_decl_handler($this->parser, 'endNamespaceDeclHandler');
		xml_set_external_entity_ref_handler($this->parser, 'entityRefHandler');
		xml_set_notation_decl_handler($this->parser, 'notationDeclHandler');
		xml_set_processing_instruction_handler($this->parser, 'processingInstructionHandler');
		xml_set_unparsed_entity_decl_handler($this->parser, 'unparsedEntityDeclHandler');
	}

	public function __destruct()
	{
		$this->debug->msg(__FUNCTION__);
		if($this->parser !== null)
			xml_parser_free($this->parser);
	}

	protected function parse($source)
	{
		// @todo find a better way to remove the require line
		$source = substr($source, strpos($source, '?>')+2);
		$source = '<?xml version="1.0" encoding="utf-8"?>' . $source;
//		echo '<pre>',str_replace('<', '&lt;', $source);
//		exit;

		if (!xml_parse($this->parser, $source, true)) {
			throw new XMLParseException(sprintf("XML error: %s at line %d\n",
				xml_error_string(xml_get_error_code($this->parser)),
				xml_get_current_line_number($this->parser)));
    	}
	}

	/**
	 * The character data handler function for the XML parser.
	 *
	 * Character data handler is called for every piece of a text in the XML document.
	 * It can be called multiple times inside each fragment (e.g. for non-ASCII strings).
	 *
	 * @param resource $parser a reference to the XML parser calling the handler.
	 * @param string $data the character data as a string.
	 */
	protected function characterDataHandler($parser, $data)
	{
		$this->debug->msg(__FUNCTION__);
		$this->defaultHandler($parser, $data);
	}

	/**
	 * The default handler function for the XML parser.
	 *
	 * Default handler is called for every piece of XML which doesn't have a proper handler
	 *
	 * @param resource $parser a reference to the XML parser calling the handler.
	 * @param string $data contains the character data.This may be the XML declaration, document type declaration, entities or other data for which no other handler exists.
	 */
	protected function defaultHandler($parser, $data)
	{
		$this->debug->msg(__FUNCTION__);
		$this->nullHandler($parser, $data);
	}

	/**
	 * Start element handler is called every time the parser finds a tag opening
	 *
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $name The name of the element for which this handler is called.If case-folding is in effect for this parser, the element name will be in uppercase letters.
	 * @param array $attribs An associative array with the element's attributes (if any).The keys of this array are the attribute names, the values are the attribute values.Attribute names are case-folded on the same criteria as element names.Attribute values are not case-folded. The original order of the attributes can be retrieved by walking through attribs the normal way, using each().The first key in the array was the first attribute, and so on.
	 */
	protected function startElementHandler($parser, $name, $attribs)
	{
		$this->debug->msg(__FUNCTION__);
		$this->defaultHandler($parser, $name, $attribs);
	}

	/**
	 * End element handler is called every time the parser finds a closing tag
	 *
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $name The second parameter, name , contains the name of the element for which this handler is called.If case-folding is in effect for this parser, the element name will be in uppercase letters.
	 */
	protected function endElementHandler($parser, $name)
	{
		$this->debug->msg(__FUNCTION__);
		$this->defaultHandler($parser, $name);
	}

	/**
	 * A handler to be called when a namespace is declared.
	 *
	 * Namespace declarations occur inside start tags. But the namespace declaration start handler
	 * is called before the start tag handler for each namespace declared in that start tag.
	 *
	 * The function named must return an integer value. If the value returned from the handler is FALSE (which it will be if no value is returned), the XML parser will stop parsing and xml_get_error_code() will return XML_ERROR_EXTERNAL_ENTITY_HANDLING.
	 *
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $user_data
	 * @param string $prefix
	 * @param string $uri
	 * @return int
	 */
	protected function startNamespaceHandler($parser, $user_data, $prefix, $uri)
	{
		$this->debug->msg(__FUNCTION__);
		$args = func_get_args();
		call_user_func_array(array($this, 'defaultHandler'), $args);
	}

	/**
	 * A handler to be called when leaving the scope of a namespace declaration.
	 *
	 * This will be called, for each namespace declaration, after the handler for the end tag of the
	 * element in which the namespace was declared.
	 *
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $user_data
	 * @param string $prefix The namespace prefix
	 */
	protected function endNamespaceHandler($parser, $user_data, $prefix)
	{
		$this->debug->msg(__FUNCTION__);
		$this->defaultHandler($parser, $user_data, $prefix);
	}

	/**
	 * The external entity reference handler function for the XML parser.
	 *
	 * This function must return a boolean value. If the value returned from the handler is FALSE
	 * (which it will be if no value is returned), the XML parser will stop parsing and xml_get_error_code()
	 * will return XML_ERROR_EXTERNAL_ENTITY_HANDLING.
	 *
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $open_entity_names A space-separated list of the names of the entities that are open for the parse of this entity (including the name of the referenced entity).
	 * @param string $base The base for resolving the system identifier (system_id) of the external entity. Currently this parameter will always be set to an empty string.
	 * @param string $system_id The system identifier as specified in the entity declaration.
	 * @param string $public_id The public identifier as specified in the entity declaration, or an empty string if none was specified; the whitespace in the public identifier will have been normalized as required by the XML spec.
	 */
	protected function entityRefHandler($parser, $open_entity_names, $base, $system_id, $public_id)
	{
		$this->debug->msg(__FUNCTION__);
		$args = func_get_args();
		call_user_func_array(array($this, 'defaultHandler'), $args);
	}

	/**
	 * The notation declaration handler function for the XML parser parser.
	 *
	 * A notation declaration is part of the document's DTD and has the following format:
	 * <code>
	 *    <!NOTATION <parameter>name</parameter>
	 *    { <parameter>systemId</parameter> | <parameter>publicId</parameter>?>
	 * </code>
	 * See @link http://www.w3.org/TR/1998/REC-xml-19980210#Notations section 4.7 of the XML 1.0 spec @endlink for the definition of notation declarations.
	 *
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $notation_name This is the notation's name, as per the notation format described in method doc.
	 * @param string $base This is the base for resolving the system identifier (system_id) of the notation declaration. Currently this parameter will always be set to an empty string.
	 * @param string $system_id Public identifier of the external notation declaration.
	 * @param string $public_id Public identifier of the external notation declaration.
	 */
	protected function notationDeclHandler($parser, $notation_name, $base, $system_id, $public_id)
	{
		$this->debug->msg(__FUNCTION__);
		$args = func_get_args();
		call_user_func_array(array($this, 'defaultHandler'), $args);
	}

	/**
	 * The processing instruction (PI) handler function for the XML parser parser.
	 *
	 * A processing instruction has the following format:
	 * <code>&lt;?<i>target</i> <i>data</i> ?></code>
	 * You can put PHP code into such a tag, but be aware of one limitation: in an XML PI,
	 * the PI end tag (?>) can not be quoted, so this character sequence should not appear
	 * in the PHP code you embed with PIs in XML documents.If it does, the rest of the PHP
	 * code, as well as the "real" PI end tag, will be treated as character data.
	 *
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $target The PI target
	 * @param string $data The PI data
	 */
	protected function processingInstructionHandler($parser, $target, $data)
	{
		$this->debug->msg(__FUNCTION__);
		$args = func_get_args();
		call_user_func_array(array($this, 'defaultHandler'), $args);
	}

	/**
	 *  Sets the unparsed entity declaration handler function for the XML parser parser .

The handler will be called if the XML parser encounters an external entity declaration with an NDATA declaration, like the following:

<!ENTITY <parameter>name</parameter> {<parameter>publicId</parameter> | <parameter>systemId</parameter>}
        NDATA <parameter>notationName</parameter>

See @link http://www.w3.org/TR/1998/REC-xml-19980210#sec-external-ent section 4.2.2 of the XML 1.0 spec @endlink for the definition of notation declared external entities.

parser
    The first parameter, parser, is a reference to the XML parser calling the handler.
entity_name
    The name of the entity that is about to be defined.
base
    This is the base for resolving the system identifier (systemId ) of the external entity.Currently this parameter will always be set to an empty string.
system_id
    System identifier for the external entity.
public_id
    Public identifier for the external entity.
notation_name
    Name of the notation of this entity (see xml_set_notation_decl_handler()).

 *
 * @param resource $parser
 * @param string $entity_name
 * @param string $base
 * @param string $system_id
 * @param string $public_id
 * @param string $notation_name
 */

	protected function unparsedEntityDeclHandler(resource $parser  , string $entity_name  , string $base  , string $system_id  , string $public_id  , string $notation_name )
	{
		$this->debug->msg(__FUNCTION__);
		$args = func_get_args();
		call_user_func_array(array($this, 'defaultHandler'), $args);
	}

	/**
	 * This function just throws away the data it receives and can be used to ignore any
	 * content on the xml being parsed
	 *
	 * @param resource $parser
	 * @param string $data
	 */
	protected function nullHandler($parser, $data)
	{
		$this->debug->msg(__FUNCTION__);
	}
}