<?php
/**
 * Arquivo DocumentFragment.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */

/**
 * Classe DocumentFragment
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
class DocumentFragment
{
	private $fragment;

	public function __construct(DOMElement $elem)
	{
		$this->fragment = new DOMDocumentFragment();

	}

	public function appendXML()
	{
		return $this->fragment->appendXML();
	}

	public function addNode(DOMNode $node)
	{
		$dummyDoc = new DOMDocument();

		$dummyDoc->importNode($node, true);

		//$this->fragment->  // $dummyDoc->saveXML($node);
	}
}