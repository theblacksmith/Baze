<?php

interface IXMLTextNode extends IXMLCharacterData
{
	/**
	 * Returns the text contained in this text node
	 * 
	 * @return string
	 */
	public function getText();

	/* TODO:
	 * Indicates whether this text node contains whitespace. 
	 * The text node is determined to contain whitespace in element content during the load of the document.
	 * 
	 * @return bool
	 *
	public function isWhitespaceInElementContent();
	 */

	/* TODO:
	 * Breaks this node into two nodes at the specified <i>offset</i>, keeping both in the tree as siblings.
	 * After being split, this node will contain all the content up to the <i>offset</i> . 
	 * If the original node had a parent node, the new node is inserted as the next sibling of 
	 * the original node. When the <i>offset</i> is equal to the length of this node, the new node has no data. 
	 *
	 * @param int $offset The offset at which to split, starting from 0.
	 * 
	 * @return DOMText The new node of the same type, which contains all the content at and after the <i>offset</i>.
	 *
	public function splitText($offset);
	 */
}