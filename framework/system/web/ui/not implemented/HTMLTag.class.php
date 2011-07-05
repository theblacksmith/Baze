<?php
/**
 * Arquivo HTMLTag.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

/**
 * Import
 */
import( 'system.web.ui.Container' );

/**
 * Classe HTMLTag
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class HTMLTag extends Container
{
	/**#@+
	 * Properties
	 *
	 * @access protected
	 * @var string
	 */
	//protected $class;
	//protected $id;
	//protected $lang;
	//protected $title;
	//protected $xmlLang;

	public $style;
	public $tagName;

	private $prefix;
	private $localName;
	private $canHaveChildren;

	/**#@+
	 * The tag to print
	 *
	 * @access protected
	 * @var string
	 */
	private $element;

	public function __construct(DOMElement $elem = null)
	{
		if($elem != null)
			$this->initialize($elem);

		unset($this->phpclass);
	}

	public function initialize(DOMElement $elem)
	{
		$this->element = $elem;

		$this->prefix = $elem->prefix;
		$this->localName = $elem->localName;
		$this->tagName = $elem->nodeName;
		if($elem->hasAttribute('id'))
			$this->id = $elem->getAttribute('id');
		$this->canHaveChildren = 1;

		parent::initialize($elem);
	}

	protected function getOpenTag()
	{
		return '<' . $this->tagName . ' ' . $this->getPropertiesList() . ($this->hasChildNodes() ? ' >' : ' />');
	}

	public function getAttributes()
	{
		return $this->getPropertiesList();
	}

	protected function getTagContent()
	{
		return $this->getChildrenXHTML();
	}

	protected function getCloseTag()
	{
		return ($this->hasChildNodes() ? '</'.$this->tagName.'>' : '');
	}

	protected function getEntireElement()
	{
		return $this->getOpenTag() . $this->getTagContent() . $this->getCloseTag();
	}

	protected function hasChildNodes()
	{
		return count($this->children) > 0;
	}

	public function getTagName()
	{
		return $this->tagName;
	}

	public function getXML()
	{

	}

	public function onPropertyChange()
	{
		// this class does not support propertyChange event
	}
}