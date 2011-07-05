<?php
	/**
	 * Arquivo DropDownList.class.php
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
	import( 'system.web.ui.Lista' );

	/**
	 * Classe DropDownList
	 * 
	 * @author Saulo Vallory
	 * @copyright 2007 Neoconn Networks
	 * @license http://baze.saulovallory.com/license
	 * @version SVN: $Id$
	 * @since 0.9
	 * @package Baze.classes.web.form
	 */
	class DropDownList extends InteractiveContainer
	{

		/**
		 * @var OptionItem
		 */
		protected $selectedItem;
		protected $value;		
		protected $tabindex;

		/**
		 * Event Attributes
		 *
		 * @access protected
		 */
		protected $onFocus;
		protected $onBlur;
		protected $onChange;

		protected $selectedIndex;
		
		public function __construct()
		{
			parent::__construct();
			$this->noPrintArr[] = 'selectedItem';
			//$this->children = array ();
		}
		
		public function addChild(OptionItem $opt)
		{
			parent::addChild($opt);
			
			if($opt->isSelected() || !isset($this->children[1]))
			{
				$this->setSelectedItem($opt);
			}
		}
		
		public function removeChildren()
		{
			parent::removeChildren();
			
			$this->selectedIndex = -1;
			$this->selectedItem = null;
		}
		
		public function getEntireElement()
		{
			if (!is_object($this->selectedItem))
			{
				if( count($this->children) > 0 )
				{
					$children = $this->children;
					$children[0]->setSelected(true);
					$this->selectedIndex = 0;	
				}
			}

			return	$this->getDefaultXHTML() . ">\n".
					$this->getChildrenXHTML() . "\n" .
					$this->closeTag();
		}

		protected function getDefaultXHTML()
		{
			$xhtml = "";

			$xhtml .= "<select " . $this->getPropertiesList();

			return $xhtml;
		}

		protected function closeTag()
		{
			return "</select>\n";
		}

		protected function acceptsChild($object)
		{
			return ($object instanceof OptionItem);
		}

		/**
		 * @param OptionItem $selected
		 */
		public function setSelectedItem(OptionItem $option)
		{
			if($option === null)
			{
				$this->selectedItem = null;
				$this->selectedIndex = -1;
				return;
			}
		
			if($this->selectedItem === $option)	{
				return;
			}
			
			if($option->getContainer() !== $this)
				trigger_error('Erro definindo como selecionada uma opção que não pertence ao objeto DropDownList');				
			
			if(is_object($this->selectedItem)) {
				$this->selectedItem->setSelected(false);
			}

			$index = array_search($option, $this->children, true);
			
			$this->selectedItem = $option;
			$this->selectedIndex = $index;
			$option->setSelected(true);
		}
		
		public function setSelectedIndex($index)
		{
			if($index === -1)
			{
				$this->selectedItem = null;
				$this->selectedIndex = -1;
				return;
			}
			
			if($index == $this->selectedIndex)
				return;
			
			if($index > count($this->children))
				trigger_error('O índice ultrapassa o índice da última opção do DropDownList');
				
			if(is_object($this->selectedItem))
			{
				$this->selectedItem->setSelected(false);
			}
				
			$this->selectedItem = $this->children[$index];
			$this->selectedIndex = $index;
			$this->selectedItem->setSelected(true);
		}

		/**
		 * Function getSelectedItem()<br>
		 *
		 * CAUTION: This function return a private element of DropDownList.
		 * Is necessary for get a selected OptionItem object.
		 *
		 * @author Luciano (13/06/06)
		 *
		 * @return OptionItem
		 */
		public function getSelectedItem()
		{
			if($this->selectedIndex < 0 || $this->selectedIndex >= count($this->children))
				return null;
				
			return $this->children[$this->selectedIndex];
		}

		public function getSelectedIndex()
		{
			return $this->selectedIndex;
		}
		
		public function getValue()
		{
			return $this->selectedItem->get('value');
		}

		public function onFocus($args) {
			$this->raiseEvent(FOCUS,$args);
		}

		public function onBlur($args) {
			$this->raiseEvent(BLUR,$args);
		}

		public function onChange($args) {
			$this->raiseEvent(CHANGE,$args);
		}
	}