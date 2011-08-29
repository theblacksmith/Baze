<?php
/**
 * Arquivo Page.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
require_once  'system/web/ui/HtmlComponent.class.php';
require_once  'system/web/ui/Literal.class.php';
require_once  'system/web/ui/page/Head.class.php';
require_once  'system/web/ui/page/Body.class.php';
require_once  'system/postback/ServerViewState.class.php';

/**
 * Classe Page
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
class Page extends Component implements IRenderable, IContainer
{
	/**
	 * @var ServerViewState
	 */
	protected $viewState;

	/**
	 * @var Head
	 * @desc The page head component
	 */
	protected $head;

	/**
	 * @var Body
	 * @desc The page body component
	 */
	protected $body;

	/**
	 * @var array
	 * @desc an array with pointers to all page components
	 */
	protected $_c;

	/**
	 * @var Event
	 * @desc Occurs before the page is rendered
	 */
	protected $_onPreRender;

	/**
	 * @var Event
	 * @desc Occurs after the page is rendered
	 */
	protected $_onRender;

	/**
	 * The children components
	 *
	 * @var Collection
	 */
	protected $children;

	/**
	 * @var DOMDocument
	 * @desc The DOM document for this page
	 */
	public $document;

	protected $htmlTagAtts = array( 
		'version' => '1.0',
		'xmlns:php' => 'http://www.bazephp.com/namespaces/php',
		'xmlns' => 'http://www.w3.org/1999/xhtml',
		'xml:lang' => 'en',
		'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
		'xsi:schemaLocation' => 'http://www.w3.org/1999/xhtml http://www.w3.org/MarkUp/SCHEMA/xhtml11.xsd'
	);

	public function __construct()
	{
		// page needs a more complex id
		$this->id = $this->uid = uniqid();
		
		$this->_c = array('id' => array(), 'o' => array());
		$this->viewState = new ServerViewState($this);
	}

	public function init(){}					//	| 1a vez
	public function load(){}					//	| | PostBack	| última vez
	public function unload(){}				//	|	|						|

	// @todo implement close handler
	//protected function close(){}		//								|

	/**
	 * Adds a component to the page
	 * @deprecated Use Page::registerComponent()
	 * @param Component $c
	 */
	public function addComponent(Component $c, $replace = false)
	{
		$this->registerComponent($c, $replace = false);
	}
	
	public function getComponent($id)
	{
		if(($pos = array_search($id, $this->_c['id'])) !== false)
			return $this->_c['o'][$pos];

		return null;
	}
	
	/**
	 * Returns the view state manager
	 *
	 * @return ServerViewState
	 */
	public function getServerViewState()
	{
		return $this->viewState;
	}

	/**
	 * Adds a javascript to the page
	 *
	 * @param string $script
	 * @param position $position
	 * @return unknown
	 */
	public function addScript($script, $position = null)
	{
		return $this->head->addScript($script, $position);
	}

	public function addCSS($css, $position = null)
	{
		return $this->head->addCss($css, $position);
	}

	public function setAttribute($name, $value)
	{
		$this->htmlTagAtts[strtolower($name)] = $value;
	}

	/**
 	 * @access public
	 * @return array
	 */
	public function getAttributesToRender()
	{
		return $this->htmlTagAtts;
	}

	/**
 	 * @access public
	 * @return string
	 */
	public function getObjectName()
	{
		return 'html';
	}

	/**
 	 * @access public
	 * @return CustomRender
	 */
	public function getCustomRenderer()
	{
		return null;
	}

	/**
 	 * @access public
	 * @return boolean
	 */
	public function hasCustomRenderer()
	{
		return false;
	}

	/**
 	 * @access public
	 */
	public function onPreRender()
	{
		$this->_onPreRender->raise($this);
	}

	/**
 	 * @access public
	 */
	public function onRender()
	{
		$this->_onRender->raise($this);
	}

	/**
 	 * @access public
	 * @param IRender $render
	 */
	public function renderChildren(IRenderer $render, IWriter $writer)
	{
		foreach ($this->children as $child)
			$render->render($child, $writer);
	}

	public function addChild(PageComponent $component)
	{
		if(!($this->children instanceof Collection))
			$this->children = new Collection();

		$index = $this->children->add($component);

		if($index >= 0)
		{
			$component->setPage($this);
			$component->setContainer($this);
			
			if($component instanceof Body) {
				$this->body = $component;
			}
			else if($component instanceof Head)
				$this->head = $component;
			else
				$this->addComponent($component);

			return true;
		}

		return false;
	}

	public function getParsesOwnChildren()
	{
		return false;
	}
	
	/**
	 * Adds a direct reference from the page to the passed component
	 * @param Component $component
	 */
	public function registerComponent(Component $c, $replace = false)
	{
		$pos = array_search($c->getId(), $this->_c['id']);
		
		if(!$replace && $pos !== false)
		{
			if($this->_c['o'][$pos] === $c) // if the component is already on the page, just return
				return false;

			throw new BazeRuntimeException(Msg::DuplicatedComponentId, array(get_class($this), $c->getId()));
		}

		if($pos !== false)
		{
			$this->_c['id'][$pos] = &$c->_getId();
			$this->_c['o'][$pos] = $c;
		}
		else
		{
			$this->_c['id'][] = &$c->_getId();
			$this->_c['o'][] = $c;
		}
		
		$id = $c->getId();
		$this->$id = $c;
		
		if($c->getPage() !== $this)
			$c->setPage($this);
			
		
		return true;
	}
	
	/**
	 * Removes the direct reference from the page to the passed component
	 * @param Component $component
	 */
	public function unregisterComponent(Component $c)
	{
		$id = $c->getId();
		unset($this->$id);
	}
}