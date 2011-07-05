<?php
/**
 * Arquivo PanelContainer.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
import( 'system.web.ui.Lista' );
import( 'system.web.ui.Panel' );

/**
 * Classe PanelContainer
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class PanelContainer extends Lista
{
	/**#@+
	 * List Properties
	 *
	 * @access protected
	 * @var string
	 */
	//protected $class;		//<div><[select|input] class="$class" ...
	//protected $dir;			//<div><[select|input] dir="$dir" ...
	//protected $disabled;	//<div><[select|input] disabled="$disabled" ...
	//protected $id;		[Propriedade Herdada]
	//protected $lang;		//<div><[select|input] lang="$lang" ...
	//protected $multiline;	//<div><select multiline="$multiline" ...
	//protected $name;		//<div><[select|input] name="$name" ...
	//protected $size;		//<div><[select|input] size="$size" ...
	//protected $style;		[Propriedade Herdada]
	//protected $tabindex;	//<div><[select|input] tabindex="$tabindex" ...
	//protected $type;
	//protected $xmlLang;		//<div><[select|input] xmlLang="$class" ...
	protected $tabAlign;
	protected $selectedPanel;

	/**#@+
	 * Event Attributes
	 *
	 * @access protected
	 * @var string
	 */
	//protected $onfocus;
	//protected $onblur;
	//protected $onchange;

	public function initialize(DOMElement $elem)
	{
		$this->set('tabAlign', 'top');
		while (count($this->children))
		{
			$i = array_shift($this->children);
			unset($i);
		}
		$this->noPrintArr[] = 'tabAlign';
		$this->noPrintArr[] = 'selectedPanel';
		parent::initialize($elem);
	}

	/**
	 * Function acceptsChild()<br>
	 *
	 * @param Object $object
	 * @author Armando
	 */
	protected function acceptsChild($object)
	{
		return ($object instanceof Panel);
	}

	public function addChild($obj)
	{
		$obj->addHTMLClass('tabs_content');
		parent::addChild($obj);
	}

	public function getXHTML()
	{
		if ($this->selectedPanel == null && !empty($this->children))
			$this->selectedPanel = $this->children[0];
		$page = System::$page;
		$page->addCSS('/base/library/css/panels.css');
		return parent::getXHTML();
	}

	protected function getOpenTag()
	{
		return '<div ' . $this->getPropertiesList() . '>' .
		($this->tabAlign != 'bottom' ? $this->getTabs() : '');//."\n";
	}

	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	protected function getTagContent()
	{
		return $this->getChildrenXHTML();
	}

	protected function getCloseTag()
	{
		return ($this->tabAlign == 'bottom' ? $this->getTabs() : '') . '</div>';//."\n";
	}

	protected function getEntireElement()
	{
		$strOpen = $this->getOpenTag();
		$strTags = $this->getTagContent();
		$strClose = $this->getCloseTag();

		return $strOpen.$strTags.$strClose;
	}

	private function getTabs()
	{
		$tabs = '';
		foreach ($this->children as $child)
		{
			$selected = '';
			if ($this->selectedPanel == $child)
				$selected = ' class="selected"';

			$img = $child->get('captionImg');
			$tabs .= '<li'.$selected.'><a '.(empty($img)?'class="defaulttab" ':'').'href="#'.$child->get('id').'">'.(empty($img)?$child->get('caption'):'<img src="'.$img.'" alt="'.$child->get('caption').'" />').'</a></li>';
		}

		if (!empty($tabs)) $tabs = "<ul class=\"tabs_list\">$tabs</ul>";
		return $tabs;
	}

	public function setTabAlign($tabAlign)
	{
		$this->removeHTMLClass('tabs_'.$this->tabAlign);
		$this->tabAlign = $tabAlign;
		$this->addHTMLClass('tabs_'.$this->tabAlign);
	}
}