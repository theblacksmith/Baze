<?php
/**
 * Arquivo Menu.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */


/**
 * Classe Menu
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Menu extends InteractiveContainer
{
	protected $menuType;
	protected $disabled;
	protected $text;
	protected $link;
	protected $icon;

	/**
	 * Function addChild()<br>
	 *
	 * @param ListItem $object
	 */

	public function __construct()
	{
		parent::__construct();
		$this->menuType = 'horz';
		$this->noPrintArr[] = 'menuType';
		$this->noPrintArr[] = 'disabled';
		$this->noPrintArr[] = 'text';
		$this->noPrintArr[] = 'link';
		$this->noPrintArr[] = 'icon';
	}

	public function initialize(DOMelement $elem)
	{
		parent::initialize($elem);

		$this->addHTMLClass('phpmenu');
		$page = system::$page;
		$page->addCSS('/base/library/css/menu.css');
		$page->addScript('/base/library/js/web/components/menu.js');
	}

	public function addChild(Menu $object)
	{
		if (isset($this->container) && $this->container->get('container') instanceof Menu)
			$this->addHTMLClass('phpsubmenu');
		parent::addChild($object);
	}

	protected function getEntireElement()
	{
		return $this->getChildrenXHTML();
	}

	protected function getChildrenXHTML()
	{
		$children = '';
		foreach ($this->children as $child)
		{
			$children .= $child->getXHTML();
		}

		$open = $close = '';
		if (!empty($children))
		{
			$open = '<ul '.$this->getPropertiesList().'>';
			$close = '</ul>';
		}

		if ($this->container instanceof Menu)
		{
			$txt = (empty($this->icon)?'':'<img src="'.$this->icon.'" alt=" " />').$this->text;
			if (!($this->disabled || empty($this->link)))
				$txt = '<a href="'.$this->link.'">' . $txt . '</a>';
			else
				$txt = '<span class="menudisabled">' . $txt . '</span>';

			$open = '<li'.$this->getPropertiesList().'>'.$txt;
			if (!empty($children)) $open .= '<ul>';

			$close .= '</li>';
		}

		return $open.$children.$close;
	}

	function setMenuType($menuType)
	{
		if ($menuType != 'horz' || $menuType != 'vert')
			return;

		if ($menuType == 'horz')
			$this->addHTMLClass('horimenu');
		else
			$this->removeHTMLClass('horimenu');

		$this->menuType = $menuType;
	}

	function setDisabled($disabled)
	{
		if ($disabled)
			$this->addHTMLClass('disabled');
		else
			$this->removeHTMLClass('disabled');

		$this->disabled = $disabled;
	}
}