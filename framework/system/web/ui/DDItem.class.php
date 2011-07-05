<?php
/**
 * Arquivo DDItem.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

/**
 * Import
 */


/**
 * Classe DDItem<br />
 * This is a Definition Description Item Class
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class DDItem extends InteractiveContainer
{
	/**
	 * @desc Construct Method
	 */
	function __construct()
	{
		$this->type = $type;
		
		parent::__construct();
	}
	
	/**
	 * @param DOMElement $elem
	 */
	public function initialize(DOMElement $elem)
	{
		$this->disabled = false;
				
		parent::initialize($elem);	
	}
	

	/**
	 * @return string
	 */
	protected function getOpenTag()
	{
		return _NL.'<dd ' . $this->getPropertiesList().' >';
	}

	/**
	 * @return string
	 */
	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	/**
	 * @return string
	 */
	protected function getTagContent()
	{
		return $this->getChildrenXHTML();
	}

	/**
	 * @return string
	 */
	protected function getCloseTag()
	{
		return _NL.'</dd>';
	}

	protected function getEntireElement()
	{
		$strOpen = $this->getOpenTag();
		$strAtt = $this->getAttributes();
		$strTags = $this->getTagContent();
		$strClose = $this->getCloseTag();

		return $strOpen.$strTags.$strClose;
	}		
}