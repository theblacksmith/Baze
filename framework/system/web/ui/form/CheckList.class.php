<?php
/**
 * Arquivo CheckList.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
import( 'system.web.ui.Lista' );

/**
 * Classe CheckList
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class CheckList extends Lista
{
	/**
	 * CheckList Properties <input type="check" />
	 *
	 * @access protected
	 */
	//protected $checked;
	//protected $class;		[Propriedade Herdada (List_)]
	//protected $dir;		[Propriedade Herdada (List_)]
	//protected $disabled;	[Propriedade Herdada (List_)]
	//protected $id;		[Propriedade Herdada (List_)]
	//protected $lang;
	//protected $name;		[Propriedade Herdada (List_)]
	//protected $size;		[Propriedade Herdada (List_)]
	//protected $tabindex;
	//protected $title;
	protected $type;
	protected $value;
	//protected $style;		[Propriedade Herdada (List_)]
	//protected $accesskey;
	//protected $tabindex;


	/**
	 * Event Attributes
	 *
	 * @access protected
	 *
	 */
	protected $onFocus;
	protected $onBlur;
	protected $onSelect;
	protected $onChange;
	//protected $onclick;
	//protected $ondblclick;
	//protected $onmousedown;
	//protected $onmouseup;
	//protected $onmouseover;
	//protected $onmousemove;
	//protected $onmouseout;
	//protected $onkeypress;
	//protected $onkeydown;
	//protected $onkeyup;

	public function __construct()
	{
		parent::__construct();
	}

	public function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
		if (get_class($this) == __CLASS__)
		{
			$page = System::$page;
			$page->addCSS('/base/library/css/checklist.css');
			$this->addHTMLClass("checklist");
		}
	}

	function getChildrenXHTML()
	{
		$xhtml = '';
		foreach ($this->children as $child)
		{
			$xhtml .= '<li>' . $child->getXHTML() . "</li>\n";
		}
		return $xhtml;
	}

	function addChild(CheckBox $object)
	{
		InteractiveContainer::addChild($object);
	}

	function markXor()
	{
		foreach ($this->children as $elem)
			$elem->set('checked', !$elem->get('checked'));
	}

	function markAll()
	{
		foreach ($this->children as $elem)
			$elem->set('checked', true);
	}

	function clearAll()
	{
		foreach ($this->children as $elem)
			$elem->set('checked', false);
	}

	public function getValue()
	{
		$ret = array();
		
		for($i=0; $i< count($this->children); $i++)
		{
			if ($this->children[$i]->isChecked())
				$ret[$i] = $this->children[$i]->get('value');
		}

		return $ret;
	}

	public function onFocus($args) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args) {
		$this->raiseEvent(BLUR,$args);
	}

	public function onSelect($args) {
		$this->raiseEvent(SELECT,$args);
	}

	public function onChange($args) {
		$this->raiseEvent(CHANGE,$args);
	}
}