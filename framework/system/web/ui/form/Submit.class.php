<?php
/**
 * Arquivo Submit.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */

import( 'system.web.ui.Button' );

/**
 * Classe Submit
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class Submit extends Button
{
	/**
	 * Properties
	 */
	//protected $accessKey;	[Propriedade Herdada]
	//protected $class;		[Propriedade Herdada]
	//protected $dir;		[Propriedade Herdada]
	//protected $disabled;	[Propriedade Herdada]
	//protected $form;		[Propriedade Herdada]
	//protected $id;		[Propriedade Herdada]
	//protected $lang;		[Propriedade Herdada]
	//protected $name;		[Propriedade Herdada]
	//protected $style;		[Propriedade Herdada]
	//protected $tabIndex;	[Propriedade Herdada]
	//protected $title;		[Propriedade Herdada]
	//protected $type;		[Propriedade Herdada]
	//protected $value;		[Propriedade Herdada]
	//protected $xmlLang;	[Propriedade Herdada]

	/**
	 * Events
	 */
	//protected $onfocus;	[Propriedade Herdada]
	//protected $onblur;	[Propriedade Herdada]
	//protected $onclick;	[Propriedade Herdada]
	//protected $ondblclick;[Propriedade Herdada]
	//protected $onmousedown;[Propriedade Herdada]
	//protected $onmouseup;	[Propriedade Herdada]
	//protected $onmouseover;[Propriedade Herdada]
	//protected $onmousemove;[Propriedade Herdada]
	//protected $onmouseout;[Propriedade Herdada]
	//protected $onkeypress;[Propriedade Herdada]
	//protected $onkeydown;	[Propriedade Herdada]
	//protected $onkeyup;	[Propriedade Herdada]


	function __construct()
	{
		$this->attributes = array(
			'php:class' => 'Submit'
		);
		
		parent::__construct('submit');
	}

}