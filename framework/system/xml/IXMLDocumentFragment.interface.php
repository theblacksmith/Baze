<?php

interface IXMLDocumentFragment extends IXMLNode
{
	/**
	 * Appends raw XML data to a DOMDocumentFragment.
	 * This method is not part of the DOM standard. 
	 * It was created as a simplier approach for appending an XML DocumentFragment in a DOMDocument.
	 * If you want to stick to the standards, you will have to create a temporary DOMDocument with a 
	 * dummy root and then loop through the child nodes of the root of your XML data to append them. 
	 * 
	 * @param string $data XML to append.
	 * 
	 * @return bool Returns TRUE on success or FALSE on failure.
	 */
	public function appendXML($data);
}