<?php

import('external.Zend.Config.Xml');

class SystemConfig extends Zend_Config_Xml
{
	/*-- FAKE --*/

	/**
	 * @var string
	 * @desc The default Code file extension
	 */
	public $CodeFileExt = '.code.php';

	/**
	 * @var string
	 * @desc The default Design file extension
	 */
	public $DesignFileExt = '.php';
}