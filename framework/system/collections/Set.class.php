<?php
/**
 * Arquivo Set.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system.collections
 */

require_once NB_ROOT . '/system/collections/Collection.class.php';

/**
 * Classe Set
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system.collections
 */
class Set extends Collection
{
	/**
	 * The contructor
	 *
	 * @param string $type The type of element this set can contain
	 */
	public function __construct($type)
	{
		$this->items = array();
		$this->count = 0;

		parent::__construct($type);
	}

	public function add($obj)
	{
		if($this->contains($obj))
			return;

		$this->items[] = $obj;
		$this->count = count($this->items);
	}

	public function addThese($objects)
	{
		if(!is_array($objects))
			throw new Exception("Set::addThese expects first argument to be an array or IIterable");

		if($objects instanceof IIterable)
		{
			for($i=0; $i < $objects->count(); $i++) {
				$this->add($objects[$i]);
			}
		}
		else if(is_array($objects))
		{
			for($i=0; $i < count($objects); $i++) {
				$this->add($objects[$i]);
			}
		}

		$this->count = count($this->items);
	}

	/**
	 * Enter description here...
	 *
	 * @param any $o
	 * @return boolean
	 */
	public function contains($o)
	{
		for($i=0; $i < $this->count; $i++)
		{
			if($this->items[$i] === $o)
				return true;
		}

		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param mixed $o
	 */
	public function remove($o)
	{
		$i = array_search($o,$this->items,true);

		if($i >= 0 && $i < $this->count)
		{
			$delObj = $this->items[$i];
			array_splice($this->items, $i, 1);

			$this->count = count($this->items);
			return $delObj;
		}

		$this->count = count($this->items);
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @param array | IIterable $arr
	 */
	public function removeThese($arr)
	{
		if(!is_array($arr))
			throw new Exception("Set::reomoveThese expects first argument to be an array or IIterable");

		$n = count($arr);

		for($i=0; $i < $n; $i++)
		{
			$j = array_search($arr[$i],$this->items,true);

			if($j >= 0 && $j < $this->count)
			{
				$delObj = $this->items[$j];
				array_splice($this->items, $j, 1);
			}
		}

		$this->count = count($this->items);
	}

	public function removeAll()
	{
		$this->count = 0;
		$this->items = array();
	}

	public function toArray()
	{
		return $this->items;
	}

	public function toString()
	{
		echo json_encode($this->items) . "<br /><br />";
	}
}