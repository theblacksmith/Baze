<?php
/**
 * Arquivo Label.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */


/**
 * Classe Label
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Label extends InteractiveContainer
{
	/**
	 * @todo: Restringir label para só aceitar os filhos que a W3C permite, são eles:
	 *
	 * <!ENTITY % fontstyle
		 "TT | I | B | BIG | SMALL">
		<!ENTITY % phrase "EM | STRONG | DFN | CODE |
                   SAMP | KBD | VAR | CITE | ABBR | ACRONYM" >

<!ENTITY % special
   "A | IMG | OBJECT | BR | SCRIPT | MAP | Q | SUB | SUP | SPAN | BDO">

<!ENTITY % formctrl "INPUT | SELECT | TEXTAREA | LABEL | BUTTON">

<!-- %inline; covers inline or "text-level" elements -->
<!ENTITY % inline "#PCDATA | %fontstyle; | %phrase; | %special; | %formctrl;">
	*/


	/**
	* Label Tag Properties
	*/
	//protected $class;
	//protected $dir;
	protected $for;
	//protected $id;	[Propriedade Herdada]
	//protected $lang;
	//protected $style;	[Propriedade Herdada]
	private $text;
	protected $title;
	//protected $xmlLang;
	protected $accesskey;

	/**
	* Event
	*/
	protected $onFocus;
	protected $onBlur;
	//protected $onclick;
	//protected $ondblclick;
	//protected $onmousedown;
	//protected $onmouseup;
	//protected $onmouseover;
	//protected $onmousemove;
	//protected $onmouseout;
	//protected $onkeypress;
	//protected $onkeydown;
	//protected $onkeyup;

	/**
	 * @author Luciano (12/06/06)
	 */
	public function __construct($text = null)
	{
		$this->text = $text;
		$this->for = null;
		parent::__construct();
		$this->noPrintArr[] = 'text';
	}
	
	public function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
	}
	

	/**
	 * @return string
	 */
	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	/**
	 * Function getEntireElement()
	 *
	 * @return string
	 */
	protected function getEntireElement()
	{
		return $this->getOpenTag() . $this->getChildrenXHTML() . $this->getCloseTag();
	}

	/**
	 * Function getOpenTab()
	 *
	 * @return string
	 */
	protected function getOpenTag()
	{
		return '<label '.$this->getPropertiesList() . ' >';
	}

	/**
	 * Function getCloseTag()
	 *
	 * @return string
	 */
	protected function getCloseTag()
	{
		return '</label>';
	}
	
	/**
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @param string $for
	 */
	public function getFor()
	{
		return $this->for;
	}
	
	/**
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @param string $for
	 */
	public function setFor($for)
	{
		$this->for = $for;
	}

	/**
	 * @author Luciano
	 * @since 2007-04-20
	 */
	public function setText($text)
	{
		if (count($this->children) > 0)
		{
			$this->removeChildren();
		}
		
		$this->addChild($text);
	}

	public function getText()
	{
		return $this->text;
		/**
		 * @todo Imprimir o conteúdo texto do nó e dos filhos concatenados.
		 */
	}

	public function onFocus($args) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args) {
		$this->raiseEvent(BLUR,$args);
	}
}