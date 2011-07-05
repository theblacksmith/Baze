<?php
/**
 * Arquivo ListItem.class.php
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
 * Classe ListItem
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class ListItem extends InteractiveContainer
{
	/**
	* ListItem Properties
	* @tags: <LI></LI> | <DT></DT> | <DD></DD>
	*/
	//protected $id;			[Propriedade Herdada]
	//protected $class;
	//protected $style;			[Propriedade Herdada]
	//protected $accesskey;
	private $type;
	//protected $content;
	//protected $dir;
	//protected $lang;
	//protected $tabindex;
	//protected $title;

	/**
	 * Events
	 */
	//protected $onclick;
	//protected $ondblclick;
	//protected $onfocus;
	//protected $onkeypress;
	//protected $onkeydown;
	//protected $onkeyup;
	//protected $onmousedown;
	//protected $onmouseup;
	//protected $onmouseover;
	//protected $onmousemove;
	//protected $onmouseout;

	const _LI = "li";
	const _DT = "dt";
	const _DD = "dd";

	/**
	 * Construct Method()<br>
	 *
	 * @param Vocabulary $type
	 * @param Html $content
	 */
	function __construct($type = 'li')
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
	 * @access public
	 * @return string
	 */
	protected function getOpenTag()
	{
		return _NL.'<'.$this->type.$this->getPropertiesList().' >';
	}

	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	protected function getTagContent()
	{
		return $this->getChildrenXHTML();
	}

	protected function getCloseTag()
	{
		return _NL.'</'.$this->type.'>';
	}

	protected function getEntireElement()
	{
		$strOpen = $this->getOpenTag();
		$strTags = $this->getTagContent();
		$strClose = $this->getCloseTag();

		return $strOpen.$strTags.$strClose;
	}
	
	/**
	 * Function getType()<br><br>
	 * 
	 * @author Luciano (03/01/2007)
	 */
	public function getType()
	{
		return $this->type;
	}
	
	
	/**
	 * Function setType()<br><br>
	 * 
	 * @author Luciano (03/01/2007)
	 * @param enum $type ['li','dt','dd']
	 */
	public function setType($type)
	{
		$type = strtolower($type);
		
		if ($type == 'li')
		{
			$this->type = $type;
			return true;
		}
		if ($type == 'dt')
		{
			$this->type = $type;
			return true;
		}
		if ($type == 'dd')
		{
			$this->type = $type;
			return true;
		}
		return false;
	}
	
	/**
	 * Método que retorna o conteúdo (XHTML ou String) do ListItem. Ou seja, o filho do mesmo.
	 * 
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @return string
	 */
	public function getText()
	{
		return $this->getChildrenXHTML();
	}
}