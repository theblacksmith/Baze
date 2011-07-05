<?php

class ComponentState
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
	
	public function __construct(ViewStateManager $vsm)
	{
		$this->manager = $vsm;
	}
	
	public function addProperty($name, $value, $defaultValue = null)
	{
		if($value === $defaultValue) {
			unset($this->properties[$name]);
			if(count($this->properties) == 0) {
				$this->manager->removeModifiedObject($this);
			}
		}
		else {
			if(count($this->viewState) == 0) {
				$this->manager->addModifiedObject($this);
			}
			
			$this->viewState[$name] = $value;
		}
	}
	
	public function addNewChild($component)
	{
		if($this->removedChildren->contains($component))
			$this->removedChildren->remove($component);
		else
			$this->newChildren->add($component);
	}
	
	public function addRemovedChild($component)
	{
		if($this->newChildren->contains($component))
			$this->newChildren->remove($component);
		else
			$this->removedChildren->add($component);
	}
}