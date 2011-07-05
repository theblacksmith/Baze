<?php
/**
 * Arquivo OList.class.php
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

import( 'system.web.ui.ListItem' );
import( 'system.web.ui.BazeList' );

/**
 * Classe OList<br />
 * This is a Ordered List Class
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class OList extends BaseList
{
	/**
	 * @desc Construct Function
	 */
	function __construct()
	{
		parent::__construct();
		
		$this->setListType(BaseList::ORDERED_LIST);
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
	protected function acceptsChild(ListItem $object)
	{
		if ($object->getType() == 'li')
		{
			return true;
		} 
		
		return false;
	}	
}
?>