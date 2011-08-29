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
	protected $onGoLeft;
	
	/**
	 * @var Event
	 */
	protected $onGoRight;
	
	public function __construct(){
		
		parent::__construct("Player");
		
		$this->setAttribute('class', 'player');
		$this->setAttribute('php:runat', 'server');
		
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
	
	public function setId($id)
	{
		$this->goLeftDiv->setId("{$id}_goLeft");
		$this->nameDiv->setId("{$id}_name");
		$this->goRightDiv->setId("{$id}_goRight");
		parent::setId($id);
	}
	public function addChild(Component $component, $toFirst = false, $replace = false)
	{
		$this->nameDiv->addChild($component, $toFirst, $replace);
	}
	
	public function fireGoLeft(Component $sender, $args)
	{
		FB::log("Player::fireGoLeft");
		if($this->onGoLeft) {
			$this->onGoLeft->raise($this, $args);
		}
	}
	
	public function fireGoRight(Component $sender, $args)
	{
		FB::log("Player::fireGoRight");
		if($this->onGoRight) {
			$this->onGoRight->raise($this, $args);
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
	
	public function showGoLeft()
	{
		$this->goLeftDiv->setAttribute('style', 'display: block');
	}
	
	public function showGoRight()
	{
		$this->goRightDiv->setAttribute('style', 'display: block');
	}
}