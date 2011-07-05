<?php
/**
 * Arquivo Script.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

/**
 * Import
 */
import('system.web.ui.Component');

/**
 * Classe Script
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
abstract class Script extends Component
{

	public $charset;

	public $defer;

	public $language;

	public $src;

	/**
	 * @var string
	 */
	public $type;

	public $xmlSpace;
}

class ScriptBlock extends Script
{

	//@removeBlock
	/**
	 * @var string
	 */
	public $Code;

	//@endRemoveBlock
	

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code)
	{
		$this->code = $code;
	}
}

class ScriptFile extends Script
{

	//@removeBlock
	/**
	 * @var string
	 */
	public $Src;

	//@endRemoveBlock
	

	/**
	 * @return string
	 */
	public function getSrc()
	{
		return $this->src;
	}

	/**
	 * @param string $src
	 */
	public function setSrc($src)
	{
		$this->src = $src;
	}
}

class ScriptType extends Enumeration
{
	/**
	 * @var ScriptType
	 */
	public static $TEXT_ECMA_SCRIPT = 'text/ecmascript';
	
	/**
	 * @var ScriptType
	 */
	public static $TEXT_JAVASCRIPT = 'text/javascript';
	
	/**
	 * @var ScriptType
	 */
	public static $APP_ECMA_SCRIPT = 'app/ecmascript';
	
	/**
	 * @var ScriptType
	 */
	public static $APP_JAVASCRIPT = 'app/javascript';
	
	/**
	 * @var ScriptType
	 */
	public static $TEXT_VBSCRIPT = 'text/vbscript';
}

ScriptType::init('ScriptType');