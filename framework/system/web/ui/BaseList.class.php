<?php
/**
 * Arquivo BazeList.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */


/**
 * Classe BazeList
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
abstract class BaseList extends HtmlComponent
{
	/**#@+
	 * List Properties
	 *
	 * @access protected
	 * @var string
	 */
	//protected $class;		//<div><[select|input] class="$class" ...
	//protected $dir;		//<div><[select|input] dir="$dir" ...
	//protected $disabled;	//<div><[select|input] disabled="$disabled" ...
	//protected $id;		[Propriedade Herdada]
	//protected $lang;		//<div><[select|input] lang="$lang" ...
	//protected $multiline;	//<div><select multiline="$multiline" ...
	//protected $name;		//<div><[select|input] name="$name" ...
	//protected $size;		//<div><[select|input] size="$size" ...
	//protected $style;		[Propriedade Herdada]
	//protected $tabindex;	//<div><[select|input] tabindex="$tabindex" ...
	//protected $type;
	//protected $xmlLang;		//<div><[select|input] xmlLang="$class" ...


	/**
	 * @var enum [ 'ul' | 'ol' | 'dl' ]
	 */
	private $listType;

	const UNORDERED_LIST 	= 'ul';
	const ORDERED_LIST 		= 'ol';
	const DEFINITION_LIST 	= 'dl';

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * @param DOMElement $elem
	 */
	public function initialize(DOMElement $elem)
	{
		$this->disabled = false;
		
		while (count($this->children))
		{
			$i = array_shift($this->children);
			unset($i);
		}
		
		parent::initialize($elem);
	}

	/**
	 * @param ListItem $object
	 */
	public function addChild(/*ListItem*/Component $object, $toFirst = false)
	{
		if(is_object($object) && !$this->acceptsChild($object))
		{
			return false;
		}

		parent::addChild($object, $toFirst);
		
		return true;
	}
	
	/**
	 * @return string
	 */
	public function getOpenTag()
	{
		return '<'.$this->listType.' '.$this->getPropertiesList().' >'._NL;
	}

	/**
	 * @return string
	 */
	public function getAttributes()
	{
		return $this->getPropertiesList();
	}

	/**
	 * @return string
	 */
	public function getTagContent()
	{
		$xhtml = '';
		
		foreach($this->children as $child)
		{
			$xhtml .= $child->getXHTML(XML_PART_ENTIRE_ELEMENT);
		}
		
		return $xhtml;
	}

	/**
	 * @return string
	 */
	public function getCloseTag()
	{
		return '</'.$this->listType.'>'._NL;
	}

	/**
	 * @return string
	 */
	public function getEntireElement()
	{
		$strOpen = $this->getOpenTag();
		$strTags = $this->getTagContent();
		$strClose = $this->getCloseTag();

		return $strOpen.$strTags.$strClose;
	}

	/**
	 * @author Luciano
	 * @since 2007-04-10
	 * 
	 * @param string $listType
	 */
	protected function setListType($listType)
	{
		$this->listType = $listType;
	}
	
	/**
	 * @author Luciano
	 * @since 2007-04-10
	 * 
	 * @return string $listType
	 */
	protected function getListType()
	{
		return $this->listType;
	}
}