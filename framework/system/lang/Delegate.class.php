<?php
/**
 * Arquivo Delegate.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

/**
 * Classe Delegate
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class Delegate
{
	/**
	 * Constants
	 *
	 */
	 const _INSTANTIATED_ACCESS = 1;
	 const _STATIC_ACCESS = 2;
	 const _GLOBAL_ACCESS = 3;

	/**
	 * Object reference.
	 *
	 * @var Object
	 */
	protected $object;

	/**
	 * Classe Name
	 *
	 * @var string $classe The name of the class for static access.
	 */
	protected $class;

	/**
	 * Access Method
	 *
	 * 	The way in wich the method subscribed to the event will be called.
	 *
	 * @var int $accessMethod One of the following classe internal constants _INSTANTIATED_ACCESS | _STATIC_ACCESS | _GLOBAL_ACCESS
	 */
	protected $accessMethod;

	/**
	 * Method name
	 *
	 * 	The name of the mehtod that will be called when this event is raised.
	 *
	 * @var string $method The method that will be called
	 */
	protected $method;

	protected $paramCount;

	protected $paramType;

	/**
	 * Recebe ReflectionParameter[] ou lista de tipos (Ex: null, "DataTable", "Event")
	 */
	public function __construct(/* list of types */)
	{
		$args = func_get_args();

		if($args && is_array($args[0]) && gettype($args[0][0]) == "RefelctionParameter")
		{
			$this->paramCount = count($args[0]);

			foreach($args[0] as $param)
			{
				if($param->getClass())
					$this->paramType[] = $param->getClass()->getName();
				else
					$this->paramType[] = null;
			}
		}
		else
		{
			$this->paramCount = func_num_args();

			foreach($args as $param)
				$this->paramType[] = $param;
		}
	}

	public function factory($func)
	{
		if(is_array($func))
		{
			$reflect = new ReflectionMethod($func[0],$func[1]);
		}
		else
			$reflect = new ReflectionFunction($func);

		if(isset($this) && get_class($this) == __CLASS__)
		{
			// Comparar os parametros da função passada com os parametros da classes
			if($this->paramCompare($reflect->getParameters()))
			{
				// cria o novo objeto
				$newDel = clone($this);

				// seta a funcao
				$newDel->setFunctionInternal($func);

				return $newDel;
			}

			trigger_error("Function type does not match the delegate type.", E_USER_ERROR);

			return false;
		}

		return self::staticFactory($func);
	}

	private static function staticFactory($func)
	{
		if(is_array($func))
			$reflect = new ReflectionMethod($func[0],$func[1]);
		else
			$reflect = new ReflectionFunction($func);

		// Criar um novo delegate baseado na funcao passada
		$newDel = new Delegate($reflect->getParameters());

		$newDel->setFunctionInternal($func);

		return $newDel;
	}

	/**
	 * @param ReflectionParameter[] $params
	 */
	protected function paramCompare($params)
	{
		if(count($params) != $this->paramCount)
			return false;

		for($i=0; $i < $this->paramCount; $i++)
		{
			if($this->paramType[$i])
			{

				if(!($params[$i]->getClass()) || strcmp($this->paramType[$i], $params[$i]->getClass()->getName()) != 0)
					return false;
			}
		}

		return true;
	}

	private function setFunctionInternal($func)
	{
		if(is_array($func))
		{
			if(is_object($func[0]))
			{
				$this->accessMethod = Delegate::_INSTANTIATED_ACCESS;
				$this->object = $func[0];
			}
			else
			{
				$this->accessMethod = Delegate::_STATIC_ACCESS;
				$this->classe = $func[0];
			}

			$this->method = $func[1];
		}
		else
		{
			$this->accessMethod = Delegate::_GLOBAL_ACCESS;
			$this->method = $func;
		}
	}

	public function __assign_add($var)
	{
		echo "__assignAdd($var)<br>";
		$this->setFunction($var);
	}

	public function setFunction($func)
	{
		if(!$func)
			return false;

		if(is_array($func))
		{
			//echo "Delegate::ObjectClasse: " . get_class($func[0]);
			//echo "\nDelegate::ObjectMethod: $func[1]";
			$reflect = new ReflectionMethod($func[0],$func[1]);
		}
		else
			$reflect = new ReflectionFunction($func);

		if($this->paramCompare($reflect->getParameters()))
		{
			$this->setFunctionInternal($func);

			return true;
		}

		trigger_error("Function type does not match the delegate type.", E_USER_ERROR);

		return false;
	}

	public function call($params)
	{
		if($this->accessMethod == Delegate::_GLOBAL_ACCESS)
		{
			return call_user_func_array($this->method, $params);
		}
		else if($this->accessMethod == Delegate::_STATIC_ACCESS)
		{
			return call_user_func_array(array($this->class,$this->method),$params);
		}
		else
		{
			return $this->object->{$this->method}($params[0],$params[1]);
		}
	}

	public function getSignature()
	{
		if($this->accessMethod == Delegate::_GLOBAL_ACCESS)
			return $this->method . "(...)";

		if($this->accessMethod == Delegate::_STATIC_ACCESS)
			return $this->class . "::" . $this->method . "(...)";

		return get_class($this->object) . "->" . $this->method . "(...)";
	}
}