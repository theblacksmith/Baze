<?php
/**
 * Arquivo UList.class.php
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

import( 'system.web.ui.ListItem' );
import( 'system.web.ui.BaseList' );


/**
 * Classe UList<br />
 * This is a Unordered List Class
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class UList extends BaseList
{
	/**
	 * @desc Construct Function
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->setListType(BaseList::UNORDERED_LIST);
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
	 * @author Luciano
	 * @since 2007-04-10 (Y-mm-dd)
	 * 
	 * @return boolean
	 */
	protected function acceptsChild(/*ListItem*/ $object)
	{
		if ($object instanceof ListItem && $object->getType() == 'li')
		{
			return true;
		} 
		
		return false;
	}

	
	public function addChildAsFirst($object)
	{
		parent::addChild($object, true);
	}
	
	public function addChildAsLast($object)
	{
		parent::addChild($object, false); 
	}
}