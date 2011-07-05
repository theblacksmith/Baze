<?php

abstract class Singleton
{
	protected $_i;
	
	protected function __constructor()
	{
		
	}
	
	public static function getInstance()
	{
		if($this->_i == null)
		{
			$this->_i = new ${__CLASS__}();
		}
	}
}