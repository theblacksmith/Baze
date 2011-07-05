<?php
/**
 * This class implements an abstract xml parser.
 * 
 * To use this class to create a xml parser, extend it and overwrite 
 * the methods that handle the parts of the xml.
 *
 */
abstract class XmlParser
{
	/**
	 * @var handler
	 * @desc
	 */
	private $parser;

	/**
	 * XmlParser's contructors instantiate a xml_parser object through xml_parser_create()
	 * NOTE: If you override this constructor you need to call parent::__construct().
	 *
	 * @param string $encoding The xml source encoding
	 * @param array $options Options to the xml_parser. The keys must be valids XML_OPTION_* constants
	 * @param bool $use_ns Wether to use xml_parser_create_ns or not
	 * @param string $ns_separator The namespace separator, default ":"
	 */
	public function __construct($encoding = null, array $options = array(), $use_ns = false, $ns_separator)
	{
		if($use_ns)
			$this->parser = xml_parser_create_ns($encoding, $ns_separator);
		else
			$this->parser = xml_parser_create($encoding);

		foreach ($options as $key => $value) {
			xml_parser_set_option($this->parser, $key, $value);
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
		xml_parser_free($this->parser);
	}

	/**
	 * This function starts the parse process. It can be used to simplify the implementation of a public interface.
	 *
	 * @param string $source The xml source to parse 
	 * @throws XMLParseException
	 */
	protected function _parse($source)
	{
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
		$this->nullHandler($parser, $data, $attribs);
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
		call_user_func_array(array($this, 'defaultHandler'), func_get_args());
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
		call_user_func_array(array($this, 'defaultHandler'), func_get_args());
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
		call_user_func_array(array($this, 'defaultHandler'), func_get_args());
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
		call_user_func_array(array($this, 'defaultHandler'), func_get_args());
	}

	/**
	 *  Sets the unparsed entity declaration handler function for the XML parser parser.
	 * 
	 * The handler will be called if the XML parser encounters an external entity declaration with an NDATA declaration, like the following:
	 * <!ENTITY <parameter>name</parameter> {<parameter>publicId</parameter> | <parameter>systemId</parameter>}
	 *     NDATA <parameter>notationName</parameter>
	 * See @link http://www.w3.org/TR/1998/REC-xml-19980210#sec-external-ent section 4.2.2 of the XML 1.0 spec @endlink for the definition of notation declared external entities.
	 * 
	 * @param resource $parser A reference to the XML parser calling the handler.
	 * @param string $entity_name The name of the entity that is about to be defined.
	 * @param string $base This is the base for resolving the system identifier (systemId ) of the external entity.Currently this parameter will always be set to an empty string.
	 * @param string $system_id This is the base for resolving the system identifier (systemId ) of the external entity.Currently this parameter will always be set to an empty string.
	 * @param string $public_id Public identifier for the external entity.
	 * @param string $notation_name Name of the notation of this entity (@see XmlParser::notationDeclHandler()).
	 */
	protected function unparsedEntityDeclHandler(resource $parser  , string $entity_name  , string $base  , string $system_id  , string $public_id  , string $notation_name )
	{
		call_user_func_array(array($this, 'defaultHandler'), func_get_args());
	}

	/**
	 * This function just throws away the data it receives and can be used to ignore any
	 * content on the xml being parsed
	 *
	 * @param resource $parser
	 * @param string $data
	 */
	protected function nullHandler($parser, $data)
	{}
}