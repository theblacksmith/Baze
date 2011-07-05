<?php

/**
 * Arquivo Button.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.table
 */

/**
 * Import
 */
import( 'system.web.ui.IButton' );
import( 'system.web.ui.form.FormField' );

/**
 * Classe Button
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.table
 */
class Button extends FormField
{
	/**
	 * Button Tag Properties
	 * @access protected
	 * @tag <button></button>
	 */
	protected $accessKey;
	protected $disabled;
	protected $name;
	protected $tabindex;
	protected $type;
	protected $value;

	protected $tagName = 'input';

	/**
	 * Events
	 */
	protected $onFocus;
	protected $onBlur;
	protected $onSelect;
	protected $onChange;
	//protected $onClick;
	//protected $onDblClick;
	//protected $onMouseDown;
	//protected $onMouseUp;
	//protected $onMouseOver;
	//protected $onMouseMove;
	//protected $onMouseOut;
	//protected $onKeyPress;
	//protected $onKeyDown;
	//protected $onKeyUp;

	/**
	 * Constructor
	 */
	public function __construct($type = 'button')
	{
		$this->attributes = array(
			'type' => $type,
			'php:class' => 'Button'
		);
	}


}