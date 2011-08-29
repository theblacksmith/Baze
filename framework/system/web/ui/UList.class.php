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
class UList extends HtmlComponent
{
	protected $tagName = 'ul';
	
	/**
	 * @desc Construct Function
	 */
	function __construct()
	{
		$this->attributes = array(
			'php:class' => 'UList'
		);
		
		parent::__construct();
	}
	
	/**
	 * @param ListItem $component
	 * @return boolean
	 */
	public function addChild(Component $component, $toFirst = false, $replace = false)
	{
		if(!($component instanceof ListItem) && !($component instanceof HtmlFragment))
			throw new BazeException("Invalid child ".get_class($component)." ULists only accept ListItems");
			
		parent::addChild($component, $toFirst, $replace);
	}
}