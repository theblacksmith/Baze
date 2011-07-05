<?php
/**
 * Arquivo Select.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */

/**
 * Require
 */
import( 'system.web.ui.Lista' );

/**
 * Classe Select
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class Select extends Lista
{
	protected $onFocus;
	protected $onBlur;
	protected $onChange;
	
	/**
	 * @var OptionItem
	 */
	protected $selectedOption;
	
	function __construct()
	{
		parent::__construct();
		$this->selectedOption = null;
	}

	public function getXHTML()
	{
		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       ">\n" .
		       $this->getChildrenXHTML() .
		       $this->closeTag();
	}

	protected function getDefaultXHTML()
	{
		$xhtml = "";

		if(isset($this->style))
		{
			$style = $this->style->getPropertiesList();

			if($style == " style=\"\"")
			{
				$style = "";
			}

		}

		$xhtml .= "\n<select" . $style;

		return $xhtml;
	}

	protected function closeTag()
	{
		return "</select>";
	}

	/**
	 * Returns the selected option or null if none is selected
	 *
	 * @return OptionItem
	 */
	public function getSelectedOption()
	{
		return $this->selectedOption;
	}
	
	public function getValue()
	{
		if($this->selectedOption !== null)
			return $this->selectedOption->get("value");
		
		return null;
	}
	
	public function addChild(OptionItem $option)
	{
		array_push($this->children, $option);
		parent::addChild($option);
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