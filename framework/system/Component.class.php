<?php
/**
 * Arquivo da classe Component
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

require_once NB_ROOT . '/system/exceptions/UndefinedPropertyException.class.php';
require_once NB_ROOT . '/system/Event.class.php';
require_once NB_ROOT . '/system/EventHandler.class.php';
require_once NB_ROOT . '/system/lang/BazeObject.class.php';

/**
 * Classe Component
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
class Component extends BazeObject {

	/**
	 * @var string User defined identifier
	 * @access protected
	 */
	protected $id;

	/**
	 * @var string Unique identifier automatticaly generated
	 * @access protected
	 */
	protected $uid;

	/**
	 * The number of components created so far. Used to give uid's.
	 *
	 * @var unknown_type
	 */
	protected static $objCount = 0;

	/**
	 * @var Component Container desse objeto
	 * @access protected
	 */
	protected $container;

	public function __construct()
	{
		$this->id  = $this->uid = self::getUid();
	}

	/**
	 * Returns a unique id in the form _9 (where 9 is the current object count)
	 *
	 * @return string
	 */
	private static function getUid()
	{
		self::$objCount++;
		return '_'.time().'_'.self::$objCount;
	}

	/**
	 * função utilizada para permitir o uso da sintaxe do c# ao invés do tradicional método get___, entretanto, é a existência de um método get que define se uma propriedade pode ser obtida.
	 *
 	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		$getter='get'.$name;

		if(method_exists($this,$getter))
		{
			return $this->$getter();
		}
		else if(strncasecmp($name,'on',2)===0)
		{
			$name[0] = 'o';
			if($this->supportsEvent($name))
			{
				if(!isset($this->$name))
					$this->$name = new Event($name);

				return $this->$name;
			}
		}

		throw new UndefinedPropertyException(Msg::UndefinedProperty, get_class($this).'::'.$name, 0);
	}

	/**
	 * função utilizada para permitir o uso da sintaxe do c# ao invés do tradicional método set___,
	 * entretanto, é a existência de um método set que define se uma propriedade pode ser setada.
	 *
 	 * @access public
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		$setter = 'set'.$name;

		if(method_exists($this, $setter))
		{
			$this->$setter($value);
		}
		else if(strncasecmp($name,'on',2) === 0)
		{
			$name[0] = 'o';
			if($this->supportsEvent($name))
			{
				$this->addEventListener($name, $value, is_callable($value));
			}
		}
		else if(method_exists($this,'get'.$name))
		{
			throw new IllegalAccessException(Msg::ReadOnlyProperty, array(get_class($this).'::'.$name));
		}
		else
		{
			// @think what of below should be done?
			$this->$name = $value; // this is the default php behaviour for undefined properties setting
			//throw new UndefinedPropertyException(Msg::UndefinedProperty, array(get_class($this).'::'.$name), 1);
		}
	}

	/**
	 * Returns the component id.
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @internal
	 * This function returns a pointer to the component id.
	 * <b>Never change the component id directly through the pointer</b>.
	 * The pointer should only be used for indexing purposes like in {@see Page::$_c }}
	 * <code>
	 *   $pointer_to_id = &$comp->getId();
	 * </code>)
	 *
	 * @return &string
	 */
	public function &_getId()
	{
		return $this->id;
	}

	/**
	 * Sets the component id
	 *
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Retorna o container desse objeto
	 *
 	 * @access public
	 * @return IContainer
	 */
	public function getContainer() {
		return $this->container;
	}

	/**
	 * Define o container desse objeto.
	 *
 	 * @access public
	 * @param IContainer $comp
	 */
	public function setContainer(IContainer $comp) {
		$this->container = $comp;
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
		if(!$this->supportsEvent($event))
			return false;

		if(!isset($this->$event))
			$this->$event = new Event($event, $preventDefault);
		
		if(!$runatServer || ($runatServer && $callback instanceof EventHandler))
			return $this->$event->enlist($callback, $runatServer);

		if( ($eh = new EventHandler($callback)) == true)
			return $this->$event->enlist($eh, true, $args);

		return false;
	}

	/**
 	 * @access public
	 * @param string $event
	 * @param callback $callback
	 * @return boolean
	 */
	public function removeEventListener($event, $callback)
	{
		if(!$this->supportsEvent($event) || !isset($this->$event))
			return false;

		return $this->$event->dismiss($callback);
	}

	/**
	 * Retorna se o componente suporta ou não o evento
	 *
	 * @param string $eventName Nome do evento. Ex: onClick
	 */
	public function supportsEvent($eventName)
	{
		return property_exists($this, $eventName);
	}

	/**
	 * Dispara o evento passado, caso ele exista. Se o evento não
	 * existir, uma exceção será lançada
	 *
	 * @param string $name Nome do evento
	 * @param array $args
	 * @throws InvalidOperationException Quando o evento não é suportado
	 * @throws InvalidArgumentTypeException Quando o parâmetro $name não é uma string
	 */
	protected function raiseEvent($name, array $args)
	{
		if(!is_string($name)) {
			throw new InvalidArgumentTypeException(Msg::ArgumentTypeMismatch,
												   array('name', 'string', gettype($name)));
		}

		if(!$this->supportsEvent($name))
			throw new InvalidOperationException(Msg::Component_EventNotSupported,
												array($name, get_class($this)));

		if(isset($this->$name))	{
			$this->$name->raise($this,$args);
		}
	}
}