<?php

import('system.web.ui.Page');
import('system.web.ui.form.TextBox');
import('system.web.ui.form.Submit');

import('examples.TeamPicker.Player');

class TeamPicker extends Page {
	
	/**
	 * @var Panel
	 */
	public $players;
	
	/**
	 * @var Panel
	 */
	public $team;
	
	/**
	 * @var Player
	 */
	public $mack;
	
	/**
	 * @var Player
	 */
	public $zack;
	
	/**
	 * @var Player
	 */
	public $jonh;
	
	/**
	 * @var Player
	 */
	public $bart;
	
	/**
	 * @var Player
	 */
	public $black;
	 
	public function init() {
		$players = array('Jack', 'Smith', 'Morris', 'Becker', 'Muller');
		
		$glEh = new EventHandler(array($this, 'moveLeft'));
		$grEh = new EventHandler(array($this, 'moveRight'));
		
		foreach($players as $pName)
		{
			$p = new Player();
			$p->Name = $pName;
			$p->setId(strtolower($pName));
			$p->hideGoLeft();
			
			$p->onGoLeft = $glEh;
			$p->onGoRight = $grEh;
			
			$this->players->addChild($p);
		}
		
		$children = $this->team->getChildNodes();
		foreach ($children as $p) if ($p instanceof Player) {
			$p->hideGoRight();
			$p->onGoLeft = $glEh;
			$p->onGoRight = $grEh;
		}
	}

	public function moveLeft(Component $sender, $args)
	{
		echo 'move left';
		$this->players->addChild($sender);
	}

	public function moveRight(Component $sender, $args)
	{
		echo 'move right';
		$this->team->addChild($sender);
	}
}
