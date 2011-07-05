<?php

interface IXMLNode
{
	/* @TODO: implement following methods

		//public function cloneNode($deep=false);
		//public function DOMNode insertBefore( DOMNode $newnode, DOMNode $refnode=null);
		//public function bool isDefaultNamespace(string $namespaceURI);
		//public function bool isSupported( string $feature , string $version );
		//public function string lookupNamespaceURI( string $prefix );
		//public function string lookupPrefix( string $namespaceURI );
		//public function normalize();
		//public function DOMNode replaceChild( DOMNode $newnode , DOMNode $oldnode );

		//public function DOMNode getpreviousSibling();
		//public function DOMNode getNextSibling();
		//public function string getNamespaceURI();
		//public function string getBazeURI();
	*/

	/**
	 * @return string
	 */
	//public function getPrefix();

	/**
	 * @param string $prefix
	 */
	//public function setPrefix($prefix);


	/**
	 * @return string
	 */
	//public function getTextContent();

	/**
	 * @param string $value
	 */
	//public function setTextContent($value);


	/**
	 * @return string
	 */
	//public function getNodeValue();

	/**
	 * @param string $value
	 */
	//public function setNodeValue($value);


	/**
	 * @return string
	 */
	//public function getNodeName();

	/**
	 * @return int
	 */
	//public function getNodeType();

	/**
	 * @return DOMNode
	 */
	//public function getParentNode();

	/**
	 * @return DOMNodeList
	 */
	//public function getChildNodes();

	/**
	 * @return DOMNode
	 */
	//public function getFirstChild();

	/**
	 * @return DOMNode
	 */
	//public function getLastChild();

	/**
	 * @return DOMNamedNodeMap
	 */
	//public function getAttributes();

	/**
	 * @return DOMDocument
	 */
	//public function getOwnerDocument();

	/**
	 * @return string
	 */
	//public function getLocalName();

	/**
	 * @param DOMNode
	 * @return DOMNode
	 */
	//public function appendChild(DOMNode $newnode);

	/**
	 * @return bool
	 */
	//public function hasAttributes();

	/**
	 * @return bool
	 */
	//public function hasChildNodes();

	/**
	 * @param DOMNode
	 * @return bool
	 */
	//public function isSameNode( DOMNode $node );

	/**
	 * @param DOMNode
	 * @return DOMNode
	 */
	//public function removeChild(DOMNode $oldnode);
}