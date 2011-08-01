<?php
/**
 * Arquivo TextBox.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
require_once 'system/web/ui/form/FormField.class.php';

/**
 * Classe TextBox
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class TextBox extends FormField
{
	protected $tagName = 'input';

	/**
	 * Event Attributes
	 * @access protected
	 * @var Event
	 */
	protected $onSelect;

	public function __construct()
	{
		$this->attributes = array(
			'type' => 'text',
			'php:class' => 'TextBox'
		);
		
		parent::__construct();
	}

	/**
	 * @return boolean
	 */
	public function getDisabled() {
		return $this->getAttribute('disabled');
	}

	/**
	 * @return int
	 */
	public function getMaxlength() {
		return $this->getAttribute('maxlength');
	}

	/**
	 * @return boolean
	 */
	public function getReadonly() {
		return $this->getAttribute('readonly');
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->getAttribute('size');
	}

	/**
	 * @param boolean $disabled
	 */
	public function setDisabled($disabled) {
		$this->setInViewState('disabled', $disabled);
	}

	/**
	 * @param int $maxlength
	 */
	public function setMaxlength($maxlength) {
		$this->setInViewState('maxlength', $maxlength);
	}

	/**
	 * @param boolean $readonly
	 */
	public function setReadonly($readonly) {
		$this->setInViewState('readonly', $readonly);
	}

	/**
	 * @param int $size
	 */
	public function setSize($size) {
		$this->setInViewState('size', $size);
	}

	public function setText($value)
	{
		$this->setInViewState('value', $value);
	}

	public function setValue($value)
	{
		$this->setInViewState('value', $value);
	}
}