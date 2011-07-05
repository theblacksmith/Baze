<?php

interface IXMLAttr extends IXMLNode
{	
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getValue();

	/**
	 * @param string
	 */
	public function setValue($value);
	
	/**
	 * @return DOMElement
	 */
	public function getOwnerElement();
}