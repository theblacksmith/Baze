<?php
/**
 * Arquivo HtmlComponent.class.php
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

import( 'system.EventHandler' );
import( 'system.Event' );
import( 'system.web.ui.Component' );

/**
 * Classe HtmlComponent
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class InteractiveComponent extends Component
{

	/**
	 * Common events
	 */
	protected $onClick;
	protected $onDblClick;
	protected $onMouseDown;
	protected $onMouseUp;
	protected $onMouseOver;
	protected $onMouseMove;
	protected $onMouseOut;
	protected $onKeyPress;
	protected $onKeyDown;
	protected $onKeyUp;


	public function onClick($args)
	{
		$this->raiseEvent(CLICK,$args);
	}

	public function onDblClick($args)
	{
		$this->raiseEvent(DOUBLE_CLICK,$args);
	}

	public function onMouseDown($args)
	{
		$this->raiseEvent(MOUSE_DOWN,$args);
	}

	public function onMouseUp($args)
	{
		$this->raiseEvent(MOUSE_UP,$args);
	}

	public function onMouseOver($args)
	{
		$this->raiseEvent(MOUSE_OVER,$args);
	}

	public function onMouseMove($args)
	{
		$this->raiseEvent(MOUSE_MOVE,$args);
	}

	public function onMouseOut($args)
	{
		$this->raiseEvent(MOUSE_OUT,$args);
	}

	public function onKeyPress($args)
	{
		$this->raiseEvent(KEY_PRESS,$args);
	}

	public function onKeyDown($args)
	{
		$this->raiseEvent(KEY_DOWN,$args);
	}

	public function onKeyUp($args)
	{
		$this->raiseEvent(KEY_UP,$args);
	}
}