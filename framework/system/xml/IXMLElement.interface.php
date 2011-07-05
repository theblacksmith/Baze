<?php

require_once 'IXMLNode.interface.php';

interface IXMLElement extends IXMLNode
{
	/**
	 * @param string $name
	 * @return string
	 */
	public function getAttribute($name);

	/* TODO: Require method
	 * @param string $name
	 * @return DOMAttr
	 *
	public function getAttributeNode($name);
	 */

	/**
	 * @param string $name
	 * @return DOMNodeList
	 */
	public function getElementsByTagName($name);

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAttribute($name);

	/**
	 * @param string $name
	 * @return bool
	 */
	public function removeAttribute($name);

	/* TODO: Require method
	 * @param DOMAttr $oldnode
	 * @return bool
	 *
	public function removeAttributeNode(DOMAttr $oldnode);
	*/

	/**
	 * @param string $name
	 * @param string $value
	 *
	 * @return DOMAttr
	 */
	public function setAttribute($name, $value);

	/* TODO: Require method
	 * @param DOMAttr
	 * @return DOMAttr
	 *
	public function setAttributeNode(DOMAttr $attr);
	 */

	/**
	 * Returns the element name
	 *
	 * @return String
	 */
	public function getTagName();
}