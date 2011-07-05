<?php

import('system.web.ui.Panel');
import('system.web.ui.HtmlFragment');

class Player extends Panel
{
	private $goLeftDiv;
	
	private $nameDiv;
	
	private $goRightDiv;
	
	/**
	 * @var Event
	 */
	public $_onGoLeft;
	
	/**
	 * @var Event
	 */
	public $_onGoRight;
	
	public function __construct(){
		
		parent::__construct("Player");
		
		$this->setAttribute('class', 'player');
		
		$this->goLeftDiv = new Panel();
		$this->goLeftDiv->setAttribute('class', 'arrow goLeft');
		$this->goLeftDiv->addChild(new HtmlFragment("<"));
		$this->goLeftDiv->OnClick = array($this, 'fireGoLeft');
		
		$this->nameDiv = new Panel();
		
		$this->nameDiv->setAttribute('class', 'name');
		
		$this->goRightDiv = new Panel();

		$this->goRightDiv->setAttribute('class', 'arrow goRight');
		$this->goRightDiv->addChild(new HtmlFragment(">"));
		$this->goRightDiv->OnClick = array($this, 'fireGoRight');
		parent::addChild($this->goRightDiv);
		parent::addChild($this->goLeftDiv);
		parent::addChild($this->nameDiv);
		
	}
	
	public function addChild(Component $component, $toFirst = false)
	{
		$this->nameDiv->addChild($component, $toFirst);
	}
	
	public function fireGoLeft(Component $sender, $args)
	{
		if($this->_onGoLeft) {
			$this->_onGoLeft->raise($this, $args);
		}
	}
	
	public function fireGoRight(Component $sender, $args)
	{
		if($this->_onGoRight) {
			$this->_onGoRight->raise($this, $args);
		}
	}
	
	/**
	 * @param string $name The name of the player
	 */
	public function setName($name)
	{
		$this->nameDiv->removeChildren();
		$this->nameDiv->addChild(new HtmlFragment($name));
	}
	
	public function hideGoLeft()
	{
		$this->goLeftDiv->setAttribute('style', 'display: none');
	}
	
	public function hideGoRight()
	{
		$this->goRightDiv->setAttribute('style', 'display: none');
	}
}