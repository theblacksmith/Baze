<?php
/**
 * Arquivo RadioGroup.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */

/**
 * @todo: criar função setName, que altera o nome do grupo
 * 			e de todos os radios dele.
 */

import( 'system.web.ui.form.CheckList' );

/**
 * Classe RadioGroup
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class RadioGroup extends Lista
{
	/**
	 * RadioGroup Properties
	 *
	 * @accesss protected
	 */
	//protected $checked;
	//protected $class;
	//protected $dir;
	//protected $disabled;
	//protected $id; 		[Propriedade Herdada (Object)]
	//protected $lang;
	//protected $name;
	//protected $size;
	//protected $tabindex;
	//protected $title;
	//protected $type;
	//protected $value;
	//protected $style;		[Propriedade Herdada (List_)]
	//protected $accesskey;
	//protected $tabindex;

	/**#@+
	 * Event Attributes
	 *
	 * @access protected
	 * @var string
	 */
	//protected $onFocus;
	//protected $onBlur;
	//protected $onSelect;
	//protected $onChange;
	protected $name;
	
	private $currChecked;

	function __construct()
	{
		parent::__construct();
		$this->noPrintArr[] = 'name';
	}

	public function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
		if (get_class($this) == __CLASS__)
		{
			$page = System::$page;
			$page->addCSS('/base/library/css/radiogroup.css');
			$this->addHTMLClass("radiogroup");
		}
	}

	public function addChild($object)
	{
		if (get_class($object) == 'RadioButton' && $object->isChecked())
		{
			$this->setChecked($object);
			$object->set('name', $this->name);
		}
			
		InteractiveContainer::addChild($object);
	}
	
	/**
	 * Function setChecked()<br>
	 *
	 * @ver 1.5 - modificação do método (03/08/06)<br>
	 * 
	 * @author Saulo
	 * @return string
	 */
	public function setChecked($rad)
	{
		if(!$this->isChild($rad))
			return;
			
		if($this->currChecked)
			$this->currChecked->uncheck();
		
		if(!$rad->isChecked())
			$rad->set('checked',true); // isso eh um erro, nao deveria poder fazer isso
			
		$this->currChecked = $rad;
	}
	
	/**
	 * Function setUnchecked()<br>
	 *
	 * @ver 1.5 - modificação do método (03/08/06)<br>
	 * 
	 * @author Saulo
	 * @return string
	 */
	public function setUnchecked(RadioButton $rad)
	{
		if(!$this->isChild($rad))
			return;
			
		if ($this->currChecked === $rad)
			$this->currChecked = null;
	}
	
	/**
	 * Function getValue()<br>
	 *
	 * @ver 1.5 - modificação do método (03/08/06)<br>
	 * 
	 * @author Saulo
	 * @return string
	 */
	public function getValue()
	{
		return $this->currChecked->get('value');
	}
}