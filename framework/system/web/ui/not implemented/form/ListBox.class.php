<?php
/**
 * Arquivo ListBox.class.php
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
import( 'system.web.ui.form.DropDownList' );

/**
 * Classe ListBox
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class ListBox extends DropDownList
{
	protected $multiple;

	function __construct()
	{
		$this->size = 5;
		$this->multiple = false;
		parent::__construct();
	}

	function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
		$this->multiple = false;
	}

	/**
	 * @param OptionItem $selected
	 */
	public function setSelectedItem(OptionItem $option, $selected = true)
	{
		if (!$this->multiple)
		{
			$this->selectedItem = $option;
			
			parent::setSelectedItem($option);

		}
		else
		{
			if (is_array($this->selectedItem))
				$this->selectedItem = array_shift($this->selectedItem);
				
			if (!is_array($this->selectedItem))
				$this->selectedItem = array($this->selectedItem);					
		}

		$option->setSelected($selected);
	}


	/**
	 * Método que retora um objeto OptionItem, caso o ListBox não for 'multiply', do contrário retorna um array de elementos OptionItens selecionados.
	 * 
	 * @author Luciano AJ
	 * @version 1.2
	 * 
	 * @return mixed
	 */
	public function getSelectedItem()
	{
		return $this->selectedItem;
		
		
		/* Comentado por Luciano (não há necessidade de usar um foreach, pois o componente já foi
		 * inteligente suficente de organizar o 'selectedItem' em um array os elementos selecionados 
		 * caso seja 'multiply' do contrário o 'selectedItem' é o próprio elemento selecionado!)
		  
		$ret = array();
		foreach ($this->children as $optItem)
		{
			if ($optItem->isSelected())
				$ret[] = $optItem;
		}

		$this->selectedItem = $ret;
		return $ret;
		*/
	}

	public function getValue()
	{
		$arr = $this->getSelectedItem();
		foreach ($arr as $i => $item)
			$arr[$i] = $item->get('value');
			
		return $arr;
	}
}