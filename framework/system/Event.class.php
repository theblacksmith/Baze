<?php
/**
 * Arquivo Event.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
define("CLICK","onClick");				// a user clicks on an object
define("DOUBLE_CLICK","onDblClick");	// a user double-clicks on an object

define("MOUSE_DOWN","onMouseDown");		// a user presses a mouse-button
define("MOUSE_UP","onMouseUp");			// a user releases a mouse-button
define("MOUSE_OVER","onMouseOver");		// a cursor moves over an object
define("MOUSE_MOVE","onMouseMove");		// a cursor moves on an object
define("MOUSE_OUT","onMouseOut");		// a cursor moves off an object

define("FOCUS","onFocus");				// a user makes an object active
define("BLUR","onBlur");				// a user leaves an object

define("KEY_PRESS","onKeyPress");		// a keyboard key is pressed
define("KEY_DOWN","onKeyDown");			// a keyboard key is on its way down
define("KEY_UP","onKeyUp");				// a keyboard key is released

define("CHANGE","onChange");			// a user changes the value of an object
define("RESET","onReset");				// a user resets a form
define("SELECT","onSelect");			// a user selects content on a page
define("SUBMIT","onSubmit");			// a user submits a form

define("ABORT","onAbort");				// a user aborts page loading
define("LOAD","onLoad");				// a page is finished loading. Note: In Netscape (qual Netscape?) this event occurs during the loading of a page!
define("UNLOAD","onUnload");			// a user closes a page

define("VALIDATE","onValidate");
define("VALIDATION_FAIL","onValidationFail");
define("VALIDATION_SUCCESS","onValidationSuccess");

define("CONTAINER_CHANGED","onContainerChange");	// an object has its containing object changed
define("PROPERTY_CHANGE","onPropertyChange");

/**
 * Classe Event
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class Event
{
	private $enlistedFunctions = array();
	private $eventName;
	private $preventDefault;
	private $args;

	public function __construct($eventName, $preventDefault = true)
	{
		$this->eventName = $eventName;
		$this->preventDefault = $preventDefault;
	}

	public function dismiss($e)
	{
		if( ($key = array_search($e, $this->enlistedFunctions)) !== false)
		{
			unset($this->enlistedFunctions[$key]);
			return true;
		}

		return false;
	}

	public function dismissAll()
	{
		foreach($this->enlistedFunctions as $f)
			unset($f);

		$this->enlistedFunctions = array();
	}

	public function enlist($e, $runAtServer = true, $args = null)
	{
		if($runAtServer && !($e instanceof EventHandler))
			return false;

		$key = ($e instanceof EventHandler ? $e->getSignature() : $e);

		if(!array_key_exists($key, $this->enlistedFunctions))
		{
			$this->enlistedFunctions[$key] = $e;
			$this->args = $args;
			return true;
		}

		return false;
	}

	public function getEnlistedFunctions()
	{
		return $this->enlistedFunctions;
	}

	public function raise($sender, $args = array())
	{
		if(!$args) {
			$args = $this->args;
		}
		else if(is_array($this->args) && is_array($args))
			$args = array_merge($args, $this->args);

		foreach($this->enlistedFunctions as $f)
		{
			if ($f instanceof EventHandler)
			{
				FB::log("Calling " . $f->getSignature() . NL . "sender: ".$sender->getId());
									//NL . "with args " . var_export($args,true),__FILE__,__LINE__); gera erro de nesting level to deep
				$f->call(array($sender, $args));
			}
		}
	}

	public function setPreventDefault($bool)
	{
		$this->preventDefault = $bool;
	}

	public function __toString()
	{
		$funcs = '';
		$args = "";

		// defining javascript arguments
		switch($this->eventName)
		{
			case SUBMIT : $args = "targetForm: this";
		}

		if($this->preventDefault)
			$args = ',{' . ($args != '' ? $args.',' : '' ) . ' preventDefault: true}';
		else
			$args = ',{' . ($args != '' ? $args.',' : '' ) . ' preventDefault: false}';

		$addedPostBackCall = false;
		$fCount = 0;
		foreach ($this->enlistedFunctions as $f)
		{
			if (!$addedPostBackCall && $f instanceof EventHandler)
			{
				$fCount++;
				$addedPostBackCall = true;
				if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),"msie") > -1)
					$funcs .= "Baze.doPostBack(window.event" . $args . ");";
				else
					$funcs .= "Baze.doPostBack(event" . $args . ");";
			}
			else if (is_string($f)) {
				$fCount++;
				$funcs .= trim($f, ';').';';
			}
		}

		$funcs = str_replace('"', "'", $funcs);
		if($fCount > 1)
			return 'function(){' . $funcs . '}';
			
		return trim($funcs, ';');
	}
}