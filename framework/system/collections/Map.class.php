<?php
/**
 * Arquivo Map.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system.collections
 */

import("system.collections.Collection");

/**
 * Classe Map
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system.collections
 */
class Map implements Countable {
	
	private $map;
	
	private $count;
	
	public function __construct()
	{
		$this->map = array();
		$this->count = 0;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param mixed $obj
	 * @param mixed $key
	 */
	public function add($obj, $key)
	{
		if($this->contains($key))
			return;

		if(!is_scalar($key))
			throw new Exception("Map only accepts scalar values for keys");
			
		$this->map[$key] = $obj;
		$this->count = count($this->map);
	}

	/**
	 * Enter description here...
	 *
	 * @param Map $map
	 */
	public function addAll(Map $map)
	{
		$objects = $map->toArray();
		
		foreach($objects as $key => $value) {
			$this->add($value, $key);
		}
		
		$this->count = count($this->map);
	}

	/**
	 * Enter description here...
	 *
	 * @param mixed $key
	 * @return boolean
	 */
	public function contains($key)
	{
		return array_key_exists($key, $this->map);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return $this->count;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function get($key)
	{
		if(array_key_exists($key, $this->map))
			return $this->map[$key];
		
		return null;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getValues()
	{
		return array_values($this->map);
	}

	/**
	 * Enter description here...
	 *
	 * @return array
	 */
	public function getKeys()
	{
		return array_keys($this->map);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param mixed $key
	 * @return boolean
	 */
	public function remove($key)
	{			
		if(array_key_exists($key,$this->map))
		{
			unset($this->map[$key]);
			$this->count--;
			return true; 
		}
		
		return false;
	}
	
	public function removeAll()
	{
		$this->count = 0;
		$this->map = array();
	}
	
	public function toArray()
	{
		return $this->map;
	}
	
	public function toString()
	{
		echo json_encode($this->map) . "<br /><br />";
	}
}