<?php

import('external.Zend.Config.*');

/**
 * This class is only a proxy for a Zend_Config object
 *
 */
class Config implements Countable, Iterator
{
	/**
	 * Zend config object
	 *
	 * @var Zend_Config
	 */
	protected $configObject;

	/**
	 * Config provides a property based interface to
	 * an array. The data are read-only unless $allowModifications
	 * is set to true on construction.
	 *
	 * Config also implements Countable and Iterator to
	 * facilitate easy access to the data.
	 *
	 * @param array $array
	 * @param boolean $allowModifications
	 * @throws ConfigException
	 */
	public function __construct($array, $allowModifications = false)
	{
		try {
			$this->configObject = new Zend_Config($array, $allowModifications);
		}
		catch(Zend_Config_Exception $ex)
		{
			throw new ConfigException($ex->getMessage(),$ex->getCode());
		}
	}

	/**
	 * Retrieve a value and return $default if there is no element set.
	 *
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		return $this->configObject->get($name, $default);
	}

	/**
	 * Magic function so that $obj->value will work.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->configObject->get($name);
	}

	/**
	 * Only allow setting of a property if $allowModifications
	 * was set to true on construction. Otherwise, throw an exception.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @throws ConfigException
	 */
	public function __set($name, $value)
	{
		try {
			$this->configObject->__set($name, $value);
		} catch(Zend_Config_Exception $ex) {
			throw new ConfigException($ex->getMessage(),$ex->getCode());
		}
	}

	/**
	 * Return an associative array of the stored data.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->configObject->toArray();
	}

	/**
	 * Support isset() overloading on PHP 5.1
	 *
	 * @param string $name
	 * @return boolean
	 */
	protected function __isset($name)
	{
		return $this->configObject->__isset($name);
	}

	/**
	 * Support unset() overloading on PHP 5.1
	 *
	 * @param string $name
	 * @throws ConfigException when the instance doesn't allows modification
	 */
	protected function __unset($name)
	{
		try {
			$this->configObject->__unset($name);
		} catch(Zend_Config_Exception $ex) {
			throw new ConfigException($ex->getMessage(), $ex->getCode());
		}
	}

	/**
	 * Defined by Countable interface
	 *
	 * @return int
	 */
	public function count()
	{
		return $this->configObject->count();
	}

	/**
	 * Defined by Iterator interface
	 *
	 * @return mixed
	 */
	public function current()
	{
		return $this->configObject->current();
	}

	/**
	 * Defined by Iterator interface
	 *
	 * @return mixed
	 */
	public function key()
	{
		return $this->configObject->key();
	}

	/**
	 * Defined by Iterator interface
	 *
	 */
	public function next()
	{
		$this->configObject->next();
	}

	/**
	 * Defined by Iterator interface
	 *
	 */
	public function rewind()
	{
		$this->configObject->rewind();
	}

	/**
	 * Defined by Iterator interface
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return $this->configObject->valid();
	}

	/**
	 * Returns the section name(s) loaded.
	 *
	 * @return mixed
	 */
	public function getSectionName()
	{
		return $this->configObject->getSectionName();
	}

	/**
	 * Returns true if all sections were loaded
	 *
	 * @return boolean
	 */
	public function areAllSectionsLoaded()
	{
		return $this->configObject->areAllSectionsLoaded();
	}


	/**
	 * Merge another Config with this one. The items
	 * in $merge will override the same named items in
	 * the current config.
	 *
	 * @param Config $merge
	 * @return Config
	 */
	public function merge(Config $merge)
	{
		foreach($merge as $key => $item) {
			if(isset($this->$key)) {
				if($item instanceof Config && $this->$key instanceof Config) {
					$this->$key = $this->$key->merge($item);
				} else {
					$this->$key = $item;
				}
			} else {
				$this->$key = $item;
			}
		}

		return $this;
	}
}