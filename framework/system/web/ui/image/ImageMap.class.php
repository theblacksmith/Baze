<?php
/**
 * Arquivo ImageMap.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.image
 */

/**
 * Import
 */


/**
 * Classe ImageMap
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.image
 */
class ImageMap extends InteractiveContainer
{

	/**
	 * Tag Properties <map>
	 *
	 * @access private
	 * @var string
	 */
	//protected $class;
	//protected $dir;
	//protected $id; [propriedade herdada]
	//protected $lang;
	protected $name;
	//protected $title;
	//protected $style; [propriedade herdada]
	//protected $xmlLang;

	/**
	 * Constructor
	 *
	 * @param string $mapName
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getXHTML()
	{

		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       ">" .
		       $this->getChildrenXHTML() .
		       $this->closeTag();
	}

	/**
	 * @access private
	 * @return string
	 */
	private function getDefaultXHTML()
	{
		$xhtml = "";

		$style = null;

		if(isset($this->style))
		{
			$style = $this->style->getPropertiesList();

			if($style == " style=\"\"")
			{
				$style = "";
			}

		}

		$xhtml .= "\n<map" . $style;

		return $xhtml;
	}

	/**
	 * @access private
	 * @return string
	 */
	private function closeTag()
	{
		return "\n</map>";
	}

	/**
	 * @access public
	 * @param Object $object
	 */
	function addChild(MapArea $object)
	{
		parent::addChild($object);
	}
}