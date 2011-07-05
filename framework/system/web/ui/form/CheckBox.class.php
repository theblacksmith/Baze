<?php
/**
 * Arquivo CheckBox.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.form.CheckList');

/**
 * Classe CheckBox
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class CheckBox extends HtmlComponent implements IFormField
{
	/**#@+
	 * CheckBox Properties
	 * @access protected
	 */
	//protected $accept;
	protected $accesskey;
	protected $align;
	//protected $alt;
	protected $checked; // boolean
	//protected $defaultChecked;
	protected $disabled; // boolean
	protected $form;
	protected $name;
	protected $size;
	protected $tabIndex;
	protected $type;
	protected $value;
	protected $text;
	/**#@- */

	/**
	 * Event Properties
	 */
	protected $onFocus;
	protected $onBlur;
	protected $onSelect;
	protected $onChange;

	function __construct()
	{
		parent::__construct();
		$this->type = "checkbox";
	}

	public function initialize(DOMElement $elem)
	{
		$this->checked = false;
		$this->disabled = false;
		parent::initialize($elem);
	}

	/**#@+
	 * Event Methods
	 * @access public
	 */
	public function onFocus($args = null) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args = null) {
		$this->raiseEvent(BLUR,$args);
	}

	public function onSelect($args = null) {
		$this->raiseEvent(SELECT,$args);
	}

	public function onChange($args = null) {
		$this->raiseEvent(CHANGE,$args);
	}
	/**#@- */

	/**
	 * Function getOpenTab()<br>
	 *
	 * @ver 1.0 - criação do método (09/06/06)<br>
	 *
	 * @author Luciano
	 * @return string
	 */
	public function getOpenTag()
	{
		return $this->getEntireElement();
	}

	/**
	 * Function getEntireElemente()<br>
	 *
	 * @ver 1.0 - criação do método (09/06/06)<br>
	 * @ver 1.5 - modificação do método (03/08/06)<br>
	 * 
	 * @author Luciano
	 * @author Saulo
	 * @return XHTML
	 */
	public function getEntireElement()
	{
		$xhtml = '<input '.$this->getPropertiesList().' />';

		if (!empty($this->text))
		{
			$xhtml.= '<label for="'.$this->id.'">'.$this->text.'</label>';
		}
		return $xhtml;
	}

	/**
	 * Function isChecked()<br>
	 *
	 * @ver 1.5 - criação do método (03/08/06)<br>
	 *
	 * @author Saulo
	 * @return string
	 */
	public function isChecked()
	{
		return $this->checked;
	}

}