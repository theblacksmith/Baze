<?php

require_once 'system/lang/BazeObject.class.php';

class ComponentState extends BazeObject
{
	/**
	 * @var array
	 */
	private $properties;
	
	/**
	 * @var Set
	 */
	private $newChildren;
	
	/**
	 * @var Set
	 */
	private $removedChildren;
	
	/**
	 * @var ViewStateManager
	 */
	private $manager;
	
	/**
	 * @var Component
	 */
	private $component;
	
	/**
	 * Keep tracking of current number of changes so we don't need to count all the arrays
	 * @var int
	 */
	private $changesCount;
	
	public function __construct(ServerViewState $vsm, Component $comp)
	{
		$this->manager = $vsm;
		$this->component = $comp;
		$this->changesCount = 0;
		$this->properties = array();
	}
	
	public function setSynchronized()
	{
		$this->properties = array();
		
		if($this->newChildren instanceof Set)
			$this->newChildren->removeAll();
			
		if($this->removedChildren instanceof Set)
			$this->removedChildren->removeAll();
	}
	
	public function hasProperty($name)
	{
		return isset($this->properties[$name]);	
	}
	
	public function getProperty($name)
	{
		return (isset($this->properties[$name]) ? $this->properties[$name] : null);
	}
	
	public function addProperty($name, $value, $defaultValue = null)
	{
		if($value === $defaultValue) {
			unset($this->properties[$name]);
			$this->updateCount(-1);
		}
		else {
			$this->properties[$name] = $value;
			$this->updateCount(+1);
		}
	}
	
	public function removeProperty($name)
	{
			unset($this->properties[$name]);
			$this->updateCount(-1);
	}
	
	public function addNewChild($component)
	{
		if(!($this->removedChildren instanceof Set))
			$this->removedChildren = new Set(gettype(new Component()));
			
		if(!($this->newChildren instanceof Set))
			$this->newChildren = new Set(gettype(new Component()));
			
		if($this->removedChildren->contains($component)) {
			$this->removedChildren->remove($component);
			$this->updateCount(-1);
		}
		else {
			$this->newChildren->add($component);
			$this->updateCount(+1);
		}
	}
	
	public function getNewChildren()
	{
		if(!$this->newChildren)
			return array();
			
		return $this->newChildren->toArray();
	}
	
	public function addRemovedChild($component)
	{
		if(!($this->removedChildren instanceof Set))
			$this->removedChildren = new Set(gettype(new Component()));
			
		if(!($this->newChildren instanceof Set))
			$this->newChildren = new Set(gettype(new Component()));
			
		if($this->newChildren->contains($component)) {
			$this->newChildren->remove($component);
			$this->updateCount(-1);
		}
		else {
			$this->removedChildren->add($component);
			$this->updateCount(+1);
		}
	}
	
	public function getRemovedChildren()
	{
		if(!$this->removedChildren)
			return array();
			
		return $this->removedChildren->toArray();
	}
	
	/**
	 * Updates $changesCount adding it to $operation and adds or removes the component to ServerViewState
	 * modified objects list accordingly.
	 * 
	 * @param int $operation +1 or -1
	 */
	private function updateCount($operation)
	{
		$this->changesCount += $operation;
		
		if($this->changesCount > 0)
			$this->manager->addModifiedObject($this->component);
		else
			$this->manager->removeModifiedObject($this->component);
	}
	
	public function getProperties()
	{
		return $this->properties;
	}
}