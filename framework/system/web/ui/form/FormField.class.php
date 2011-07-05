<?php

import ( 'system.web.ui.HtmlComponent' );
import ( 'system.web.ui.form.IFormField' );

class TextDirection extends Enumeration
{
	public static $ltr = 'ltr';
	public static $rtl = 'rtl';
}

Enumeration::init('TextDirection');

/**
 * This is the base class for all form fields
 *
 */
class FormField extends HtmlComponent implements IFormField
{
	/**
	 * @var char
	 * @desc the keyboard key to access the field
	 */
	protected $accessKey;

	/**
	 * @var string
	 * @desc the class attribute of an element
	 */
	protected $class;

	/**
	 * @var TextDirection
	 * @desc the direction of text
	 */
	protected $dir;

	/**
	 * @var string
	 * @desc the language code for an element
	 */
	protected $lang;

	/**
	 * @var string
	 * @desc the name of a text field
	 */
	protected $name;

	/**
	 * @var string
	 * @desc the value of the field
	 */
	protected $value;

	/**
	 * @var int
	 * @desc the tab order for the field
	 */
	protected $tabindex;

	/**
	 * @var Event
	 * @desc Occurs when the element loses focus
	 */
	protected $_onBlur;

	/**
	 * @var Event
	 * @desc Occurs when the content of a field changes
	 */
	protected $_onChange;

	/**
	 * @var Event
	 * @desc Occurs when mouse clicks an object
	 */
	protected $onClick;

	/**
	 * @var Event
	 * @desc Occurs when mouse double-clicks an object
	 */
	protected $_onDblClick;

	/**
	 * @var Event
	 * @desc Occurs when an element gets focus
	 */
	protected $_onFocus;

	/**
	 * @var Event
	 * @desc Occurs when a keyboard key is pressed
	 */
	protected $_onKeyDown;

	/**
	 * @var Event
	 * @desc Occurs when a keyboard key is pressed or held down
	 */
	protected $_onKeyPress;

	/**
	 * @var Event
	 * @desc Occurs when a keyboard key is released
	 */
	protected $_onKeyUp;

	/**
	 * @var Event
	 * @desc Occurs when a mouse button is pressed
	 */
	protected $_onMouseDown;

	/**
	 * @var Event
	 * @desc Occurs when the mouse is moved
	 */
	protected $_onMouseMove;

	/**
	 * @var Event
	 * @desc Occurs when the mouse is moved off an element
	 */
	protected $_onMouseOut;

	/**
	 * @var Event
	 * @desc Occurs when the mouse is moved over an element
	 */
	protected $_onMouseOver;

	/**
	 * @var Event
	 * @desc Occurs when a mouse button is released
	 */
	protected $_onMouseUp;

	/**
	 * @return char
	 */
	public function getAccesskey() {
		return $this->getAttribute('accessKey');
	}

	/**
	 * @param char $accesskey
	 */
	public function setAccesskey($accesskey) {
		$this->setInViewState('accessKey', $accesskey);
	}

	/**
	 * @return string
	 */
	public function getClass() {
		return $this->getAttribute('class');
	}

	/**
	 * @param string $class
	 */
	public function setClass($class) {
		$this->setInViewState('class', $class);
	}

	/**
	 * @return TextDirection
	 */
	public function getDir() {
		return $this->getAttribute('dir');
	}

	/**
	 * @param TextDirection $dir
	 */
	public function setDir(TextDirection $dir) {
		$this->setInViewState('dir', $dir);
	}

	/**
	 * @return string
	 */
	public function getLang() {
		return $this->getAttribute('lang');
	}

	/**
	 * @param string $lang
	 */
	public function setLang($lang) {
		$this->setInViewState('lang', $lang);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->getAttribute('name');
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->setInViewState('name', $name);
	}

	/**
	 * @return int
	 */
	public function getTabindex() {
		return $this->getAttribute('tabIndex');
	}

	/**
	 * @param int $tabindex
	 */
	public function setTabindex($tabindex) {
		$this->setInViewState('tabIndex', $tabindex);
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return $this->getFromViewState('value', '');
	}

	/**
	 * @param string $value
	 */
	public function setValue($value) {
		$this->setInViewState('value', $value);
	}
}