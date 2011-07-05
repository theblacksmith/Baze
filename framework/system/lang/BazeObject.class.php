<?php

/**
 * Provides default __get and __set magic methods
 *
 */
abstract class BazeObject
{
	public function __get($name)
	{
		$getter='get'.$name;
		if(method_exists($this,$getter))
		{
			return $this->$getter();
		}
		else
		{
			$ex = new BazeException(Msg::UndefinedProperty, array(get_class($this) . '::' . $name));
			$btrace = debug_backtrace();
			$ex->setGuiltyFile($btrace[0]['file']);
			$ex->setGuiltyLine($btrace[0]['line']);
			throw $ex;
		}
	}

	public function __set($name,$value)
	{
		$setter='set'.$name;
		if(method_exists($this,$setter))
		{
			$this->$setter($value);
		}
		else if(method_exists($this,'get'.$name))
		{
			$ex = new BazeException(Msg::ReadOnlyProperty, array(get_class($this) . '::' . $name));
			$btrace = debug_backtrace();
			$ex->setGuiltyFile($btrace[0]['file']);
			$ex->setGuiltyLine($btrace[0]['line']);
			throw $ex;
		}
		else
		{
			$ex = new BazeException(Msg::UndefinedProperty, array(get_class($this) . '::' . $name));
			$btrace = debug_backtrace();
			$ex->setGuiltyFile($btrace[0]['file']);
			$ex->setGuiltyLine($btrace[0]['line']);
			throw $ex;
		}
	}
}