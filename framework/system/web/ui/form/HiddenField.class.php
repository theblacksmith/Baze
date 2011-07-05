<?php
/**
 * Arquivo HiddenField.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
import( 'system.web.ui.HtmlComponent' );

/**
 * Classe HiddenField
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class HiddenField extends HtmlComponent implements IFormField
{
	protected $value;

	/**
	 * @param string $part
	 * @return string
	 */
	public function getXHTML()
	{
		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       $this->closeTag();
	}

	private function getDefaultXHTML()
	{
		$xhtml = "";

		$xhtml .= "\n<input " . 'type="hidden"';

		return $xhtml;
	}

	private function closeTag()
	{
		return "/>";
	}

}