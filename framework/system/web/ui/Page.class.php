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
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.Literal' );
import( 'system.web.ui.page.Head' );
import( 'system.web.ui.page.Body' );
import( 'system.ServerViewState' );

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
	 *
	 * @param Component $c
	 */
	public function addComponent(Component $c)
	{
		if(($pos = array_search($c->getId(), $this->_c['id'])) !== false)
		{
			if($this->_c['o'][$pos] === $c) // if the component is already on the page, just return
				return;

			throw new BazeRuntimeException(Msg::DuplicatedComponentId, array(get_class($this), $c->getId()));
		}

		$this->_c['id'][] = &$c->_getId();
		$this->_c['o'][] = $c;

		$id = $c->getId();
		$this->$id = $c;
		
		if($c->getPage() !== $this)
			$c->setPage($this);
	}
	
	
	/// @cond internal

	/**
	 * Handles the postback event
	 *
	 * @param Object $obj The target object
	 * @param string $event The name of the event that happenedbreaks
	 */
	public function _handleEvent()
	{
		if($this->viewState->getEvent() != null)
		{
			global $sysLogger;
			$obj = $this->viewState->getEventTarget();
			$event = $this->viewState->getEvent();
			$args = $this->viewState->getEventArguments();

			if($sysLogger)
				$sysLogger->debug("Handling Event - " . $obj ." " . $event,__FILE__,__LINE__);

			$this->$obj->$event->raise($this->$obj, $args);
		}
	}

	
	/// @endcond
	
	/**
	 * Returns the view state manager
	 *
	 * @return ServerViewState
	 */
	public function getViewStateManager()
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
	public function getCustomRender()
	{
		return null;
	}

	/**
 	 * @access public
	 * @return boolean
	 */
	public function hasCustomRender()
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
	public function renderChildren(IRender $render, IWriter $writer)
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
}