<?php
/**
 * Arquivo OptionItem.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */

/**
 * Import
 */
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.ListItem' );

/**
 * Classe OptionItem
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class OptionItem extends ListItem implements IFormField
{

	/**
	 * OptionItem Properties
	 */
	//protected $class;			[Propriedade Herdada (List_)]
	//protected $dir;			[Propriedade Herdada (List_)]
	protected $disabled;
	//protected $id;			[Propriedade Herdada (Object)]
	//protected $lang;			[Propriedade Herdada (List_)]
	protected $label;
	//protected $name;			[Propriedade Herdada (List_)]

	protected $selected;
	//protected $size;			[Propriedade Herdada (List_)]
	//protected $style;			[Propriedade Herdada (Object)]
	//protected $tabindex;		[Propriedade Herdada (List_)]
	private $text;
	//protected $xmlLang;		[Propriedade Herdada (List_)]
	protected $value;

	/**#@+
	 * @access public
	 * @return string
	 */

	/**
	 * Construct Method()<br>
	 */
	public function __construct( $text = null )
	{
		$this->text = $text;

		parent::__construct();
		$this->type = "option";
	}

	public function initialize(DOMElement $elem)
	{
		// false é o valor default
		$this->selected = false;
		parent::initialize($elem);
	}

	/**
	 * Function getOpenTab()
	 *
	 * @return string
	 */
	public function getOpenTag()
	{
		return '<option '.$this->getPropertiesList().' >';
	}

	/**
	 * Function getAttributes()
	 *
	 * @return string
	 */
	public function getAttributes()
	{
		return $this->getPropertiesList();
	}

	/**
	 * Function getTagContent()
	 *
	 * @return string
	 */
	public function getTagContent()
	{
		return $this->getChildrenXHTML();
	}

	/**
	 * Function getCloseTag()
	 *
	 * @return string
	 */
	public function getCloseTag()
	{
		return "</option>\n";
	}

	/**
	 * Function getEntireElement()
	 *
	 * @return string
	 */
	public function getEntireElement()
	{
		$strOpen = $this->getOpenTag();
		$strClose = $this->getCloseTag();

		return $strOpen . $this->getChildrenXHTML() . $strClose;
	}
	/**#@- */

	/**
	 * Function setSelected()
	 *
	 * @param boolean $isSelected
	 * @return void
	 */
	public function setSelected($isSelected = true)
	{
		$isSelected = (boolean)$isSelected;
		if ($this->selected !== $isSelected)
		{
			$this->selected = $isSelected;
			if($this->container instanceof DropDownList)
			{
				if($isSelected)
					$this->container->setSelectedItem($this);
				else
					$this->container->setSelectedIndex(-1);
			}
		}
	}

	/**
	 * Function getText()<br>
	 *
	 * @author Luciano (04/07/06)
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * Function setText()<br>
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text)
	{
		if (count($this->children) > 0)
		{
			$this->removeChildren();
		}
		
		$this->addChild($text);
	}
	
	
	/**
	 * Método que retorna o valor da propriedade value. 
	 * ATENÇÃO: Precisa exitir esse método, pois OptionItem extende ListItem e a função "getValue" de ListItem retorna outro valor.
	 * 
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * Método que seta o valor da propriedade value. 
	 * ATENÇÃO: Precisa exitir esse método, pois OptionItem extende ListItem e a função "setValue" de ListItem retorna outro valor.
	 * 
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @return string
	 */
	public function setValue($value)
	{
		//printCallStack(false);
		//echo $value;
		//unset($this->value);
		$this->value = $value;
	}
	

	/**
	 * Function isSelected()
	 *
	 * @return void
	 */
	public function isSelected()
	{
		return $this->selected;
	}
}