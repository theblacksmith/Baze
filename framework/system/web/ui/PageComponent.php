<?php
/**
 * Arquivo da classe PageComponent
 *
 * Esse arquivo ainda não foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.web
 */

require_once 'system/rendering/IRenderable.php';
require_once 'system/xml/IXMLElement.interface.php';
require_once 'system/IContainer.interface.php';
require_once 'system/postback/ComponentState.class.php';
require_once 'system/Component.class.php';

/**
 * Classe PageComponent
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.web
 */
class PageComponent extends Component implements IRenderable, IContainer
{
	/**
	 * @var Collection
	 * @desc Collection of the child components
	 */
	protected $children;

	/**
	 * @var DOMElement
	 * @access protected
	 */
	protected $sourceElement;

	/**
	 * @var boolean Define se o componente será sincronizado com o servidor
	 * @access protected
	 */
	protected $runAtServer;

	/**
	 * @var string Nome da classe javascript desse componente
	 * @access protected
	 */
	protected $jsClass = "";

	/**
	 * @var Page Página a qual esse componente pertence
	 * @access protected
	 */
	protected $page;

	/**
	 * @var string Nome da tag no html correspondente a esse componente
	 * @access protected
	 */
	protected $tagName;

	/**
	 * @var PageComponent Componente que é o pai desse componente na árvore do documento
	 * @access protected
	 */
	protected $parentElement;

	/**
	 * {@internal Matriz de atributos, <i>default</i> e <i>custom</i>, definidos no componente.
	 * A matriz possui duas linhas ['c'], com os valores dos atributos no cliente), e ['s']
	 * com os valores dos atributos no servidor.
	 * {@link http://intranet.neoconn.com:8080/wiki/index.php/Baze#ViewState:_armazenando_altera.C3.A7.C3.B5es Mais detalhes sobre o ViewState} }}
	 *
	 * @var array Lista de todos os atributos definidos desse componente
	 * @access protected
	 */
	protected $attributes = array();

	/**
	 * {@internal Matriz de ponteiros para atributos na matriz attributes que não
	 * pertencem ao componente. A matriz possui duas linhas ['c'], com ponteiros para
	 * <i>custom attributes</i> no cliente ($this->attributes['c']), e ['s'], com ponteiros
	 * para <i>custom attributes</i> no servidor ($this->attributes['s']).
	 * {@link http://intranet.neoconn.com:8080/wiki/index.php/Baze#ViewState:_armazenando_altera.C3.A7.C3.B5es Mais detalhes sobre o ViewState} }}
	 *
	 * @var array Lista de atributos definidos desse componente que <strong>não</strong>
	 * fazem parte da especificação W3 do HTML
	 * @access protected
	 */
	protected $customAttributes = array();

	/**
	 * @var array Lista de atributos definidos desse componente que precisam ser sincronizados com a interface
	 * @access protected
	 */
	protected $viewState;

	/**
	 * @var CompositePageComponent
	 * @access private
	 */
	protected $container;

	/**
	 * @var CustomRender
	 * @access protected
	 */
	protected $customRenderer;

	/**
	 * Whether the state should be tracked or not
	 * @var boolean
	 */
	protected $trackViewState = true;
	
	/**
	 * Array of ids of new child components
	 * @var array
	 */
	protected $newChildren;
	
	/**
	 * Array of ids of removed child components
	 * @var array
	 */
	protected $delChildren;

	public function __construct()
	{
		parent::__construct();
		
		if(!is_array($this->attributes))
			$this->attributes = array();
			
		$this->attributes = array_merge($this->attributes, array('id' => &$this->id));
	}
	/**
	 * Sets the component id
	 *
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
		$this->setInViewState('id', $id);
	}

	/**
	 * Gets an attribute or a custom attribute from this component.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getAttribute($name)
	{
		PhpType::ensureArgumentType('name', $name, PhpType::String);

		$getter = 'get'.$name;

		// search for a getter
		if(method_exists($this, $getter)) {
			return $this->$getter();
		} // try to get from VS
		if(_IS_POSTBACK && $this->viewState->hasProperty($name)) {
			return $this->viewState->getProperty($name);
		} // check if it exists and return it
		else if(isset($this->attributes[$name]))
			return $this->attributes[$name];

		// no way, nothing found
		return null;
	}

	/**
	 * Sets an attribute in this component. If a setter method can't be found,
	 * setCustomAttribute will be called to set the attribute.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function setAttribute($name, $value)
	{
		PhpType::ensureArgumentType('name', $name, PhpType::Scalar);

		$setter = 'set'.$name;

		// check for a setter method
		if(method_exists($this, $setter)) {
			$this->$setter($value);
		} // every attr has a setter, so it will be treated as a custom attr
		else
			$this->setCustomAttribute($name, $value);
	}

	/**
	 * Gets a custom attribute from this component.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getCustomAttribute($name)
	{
		PhpType::ensureArgumentType('name', $name, PhpType::String);

		// check if the attr exists
		if(isset($this->customAttributes[$name]))
		{
			// try to get it from VS
			if(isset($this->viewState[$name]))
				return $this->viewState[$name];

			// not in VS, return what we have...
			return $this->customAttributes[$name];
		}

		// no way, nothing found
		return null;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws InvalidArgumentTypeException
	 */
	public function setCustomAttribute($name, $value)
	{
		PhpType::ensureArgumentType('name', $name, PhpType::String);

		if($name == '')
			throw new InvalidArgumentValueException(ErrorMessages::InvalidArgument_VoidString, 'name');

		$this->setInViewState($name, $value, null);

		// @todo: check why we do this
		if(isset($this->attributes[$name]))
			$this->customAttributes[$name] = &$this->attributes[$name];
		else
			unset($this->customAttributes[$name]);
	}

	/**
	 * Removes a custom attribute
	 *
	 * @param string $name The name of the attribute to remove
	 * @return mixed The former attribute value or null if the attribute doesn't exists
	 */
	public function removeCustomAttribute($name)
	{
		if(isset($this->customAttributes[$name]))
		{
			$value = $this->customAttributes[$name];

			unset($this->attributes[$name]);
			unset($this->customAttributes[$name]);

			return $value;
		}

		return null;
	}

	/**
	 * Gets an attribute or a custom attribute from this component.
	 * This function can be used by component creators to manipulate attributes that should
	 * be synced with the client interface, that is, should be stored on viewState.
	 *
 	 * @access protected
	 * @param string $name
	 * @param string $defaultValue
	 * @return mixed
	 */
	protected function getFromViewState($name, $defaultValue = null)
	{
		if($this->viewState && $this->viewState->hasProperty($name)) {
			return $this->viewState->getProperty($name);
		}
		else if(isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}

		return $defaultValue;
	}

	/**
	 * Sets a value to a component's attribute. This function can be used by component
	 * creators to manipulate attributes that should be synced with the client interface,
	 * that is, should be stored on viewState.
	 *
 	 * @access protected
	 * @param string $name The name of the attribute to be set
	 * @param string $value The value of this attribute
	 */
	protected function setInViewState($name, $value, $defaultValue = null)
	{
		if(_IS_POSTBACK)
		{
			if(isset($this->attributes[$name]))
				$defaultValue = $this->attributes[$name];
				
			$this->viewState->addProperty($name, $value, $defaultValue);
		}
		else
		{
			if($value === $defaultValue)
			{
				unset($this->attributes[$name]);
			}
			else
			{
				$this->attributes[$name] = $value;
			}
		}
	}

	/**
	 * @internal
	 * Copies all property values in viewState to the actual properties
	 */
	public function _setSynchronized()
	{
		foreach ($this->viewState as $prop => $val) {
			$this->attributes[$prop] = $val;
			unset($this->viewState[$prop]);
		}
	}
	
	/**
 	 * @access public
	 * @param string $name
	 * @return boolean
	 */
	public function hasAttribute($name)
	{
		if(method_exists($this, 'get'.$name) || method_exists($this, 'set'.$name) || isset($this->attributes[$name]))
			return true;

		return false;
	}

	/**
	 * Essa função é chamada pelo framework após a construção do componente
	 * quando essa construção ocorre devido o parseamento de uma página.
	 * É responsável por parsear a tag e adicionar os atributos ao componente.
	 * Esta função pode ser sobrescrita para adicionar funcionalidades,
	 * mas a função da classe pai deve ser chamada.
	 *
 	 * @access public
	 * @param ArrayAccess $attributes
	 */
	public function parse($attributes)
	{
		foreach ($attributes as $name => $val) {
			$this->setAttribute($name, $val);
		}
	}

	/**
	 * Parses a child element from source.
	 * This function must to return the child component, if it creates one. Or null if it doesn't.
	 *
	 * @return PageComponent
	 */
	public function parseChild($tagName, $attributes)
	{
		throw new NotImplementedException();
	}

	/**
	 * Returns whether this component parses its own children or not
	 *
	 * @return bool
	 */
	public function getParsesOwnChildren() {
		return false;
	}

	/**
	 * Define a página à qual este componente está ligado.
	 * <strong>Este método só deve ser usado por desenvolvedores do framework</strong>
	 *
 	 * @access public
	 * @param Page $p
	 * @return boolean
	 */
	public function setPage(Page $p = null, $replace = false) {

		if($this->page === $p || $p === null)
			return;

		$this->page = $p;
		if($this->page->addComponent($this, $replace))
		{		
			if ($this->children) foreach ($this->children as $c)
				$c->setPage($this->page);
			
			if($this->trackViewState)
				$this->viewState = new ComponentState($this->page->getServerViewState(), $this);
		}
	}

	/**
	 * Retorna a página à qual este componente está ligado.
	 *
 	 * @access public
	 * @return Page
	 */
	public function getPage() {
		return $this->page;
	}

	/**
 	 * @access public
	 * @return array
	 */
	public function getAttributesToRender() {
		if(_IS_POSTBACK)
			return $this->viewState->Properties;
		else
			return $this->attributes;
	}

	/**
	 * Este método retorna o nome do componente.
	 * Em componentes que mapeiam tags do HTML esse método
	 * deve ser sobrescrito para retornar o nome da tag mapeada.
	 *
 	 * @access public
	 * @return string
	 */
	public function getObjectName() {
		if(isset($this->tagName))
			return $this->tagName;

		return 'php:'.get_class($this);
	}

	/**
 	 * @access public
	 * @return CustomRender
	 */
	public function getCustomRenderer() {
		return $this->customRenderer;
	}

	/**
 	 * @access public
	 * @param CustomRender
	 */
	public function setCustomRender($cRender) {
		$this->customRenderer = $cRender;
	}

	/**
 	 * @access public
	 * @return boolean
	 */
	public function hasCustomRenderer() {
		return isset($this->customRenderer);
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
	public function renderChildren(IRenderer $render, IWriter $writer) {
		if($this->children instanceof Collection)
			foreach ($this->children as $child) {
				$render->render($child, $writer);
			}
	}

	/**
 	 * @access public
	 * @return string
	 */
	public function getJsClass() {
		return $this->jsClass;
	}

	public function getTagName()
	{
		return $this->tagName;
	}

	public function getElementsByTagName($name)
	{
		throw new NotImplementedException();
	}

	public function removeAttribute($name)
	{
		throw new NotImplementedException();
	}

	public function getNodeType()
	{
		return XML_ELEMENT_NODE;
	}

	public function getNodeName()
	{
		return $this->tagName;
	}

	/**
	 * Returns an array containing the child nodes. 
	 * Changes made to the returned array will NOT change the collection.
	 *
	 * @return array
	 */
	public function getChildNodes()
	{
		return $this->children->toArray();
	}

	/**
	 * Returns all defined attributes
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		if(!_IS_POSTBACK)
			return $this->attributes;
		else
		{
			$atts = $this->attributes;

			foreach($this->viewState as $n => $v)
				$atts[$n] = $v;

			return $atts;
		}
	}

	/**
	 * @param Component $component
	 * @return boolean
	 */
	public function addChild(Component $component, $toFirst = false, $replace = false)
	{
		if(!($this->children instanceof Collection))
			$this->children = new Collection();

		$found = false;
		for($i = 0, $count = $this->children->count(); $i < $count; $i++) { 
			if($this->children[$i]->getId() == $component->getId()) {
				$found = true;
				break;
			}
		}
		
		$index = -1;
		
		if(!$found) {
			$index = $this->children->add($component);
		}
		else if($replace) {
			$this->children->replace($i, $component);
			$index = $i; 
		}
		else {
			throw new BazeRuntimeException(Msg::DuplicatedComponentId, array(get_class($this), $component->getId()));
		}

		if($index >= 0)
		{
			if(isset($this->page))
				$this->page->addComponent($component, $replace);
				
			if($component->container instanceof PageComponent)
				$component->container->removeChild($component);
				
			$component->setContainer($this);
			
			if($this->trackViewState && _IS_POSTBACK)
				$this->viewState->addNewChild($component);
			
			return true;
		}

		return false;
	}

	/**
	 * Remove o filho passado como parâmetro deste componente.
	 * O filho removido não é removido da página.
	 *
	 * @param Component $component
	 * @return boolean
	 */
	public function removeChild(Component $component)
	{
		if(!($this->children instanceof Collection))
			return null;

		$c = $this->children->remove($component);
		$c->setContainer(null);
		
		if($c) {
			if(isset($this->page)) {
				$id = $c->getId();
				unset($this->page->$id);
			}
			
			if(($pos = array_search($c->getId(), $this->newChildren)) !== false)
				unset($this->newChildren[$pos]);
			else {
				$this->delChildren[] = &$c->_getId();
			}
		}
		
		return $c; 
	}

	/**
	 * Remove todos os filhos deste componente.
	 * Os filhos removidos não são removidos da página.
	 *
 	 * @access public
 	 * @return Collection A coleção de filhos removidos
	 */
	public function removeChildren()
	{
		if(!($this->children instanceof Collection))
			return;

		$childColl = new Collection();

		foreach ($this->children as $c)
			$childColl->add($c->remove($c));
			
		$this->children = new Collection();

		return $childColl;
	}

	
	/**
 	 * @access public
	 * @param string $event
	 * @param callback $callback
	 * @param boolean $runatServer
	 * @param array $args
	 * @return boolean
	 */
	public function addEventListener($event, $callback, $runatServer = true, array $args = null, $preventDefault = true)
	{
		$ret = parent::addEventListener($event, $callback, $runatServer, $args, $preventDefault);
		
		$this->setAttribute($event, array($this->$event, 'getXHTML'));
		
		return $ret;
	}
}