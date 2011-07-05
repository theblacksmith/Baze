<?php
	/**
	 * Arquivo Reset.class.php
	 * 
	 * @author Saulo Vallory
	 * @copyright 2007 Neoconn Networks
	 * @license http://baze.saulovallory.com/license
	 * @version SVN: $Id$
	 * @since 0.9
	 * @package Baze.classes.web.form
	 */

	/**
	 * Import
	 */
	import( 'system.web.ui.Button' );

	/**
	 * Classe Reset
	 * 
	 * @author Saulo Vallory
	 * @copyright 2007 Neoconn Networks
	 * @license http://baze.saulovallory.com/license
	 * @version SVN: $Id$
	 * @since 0.9
	 * @package Baze.classes.web.form
	 */
	class Reset extends Button
	{
		//protected $accessKey;		[Propriedade Herdada]
		//protected $class;			[Propriedade Herdada]
		//protected $dir;			[Propriedade Herdada]
		//protected $disabled;		[Propriedade Herdada]
		//protected $form;			[Propriedade Herdada]
		//protected $id;			[Propriedade Herdada]
		//protected $lang;			[Propriedade Herdada]
		//protected $name;			[Propriedade Herdada]
		//protected $style;			[Propriedade Herdada]
		//protected $tabIndex;		[Propriedade Herdada]
		//protected $title;			[Propriedade Herdada]
		//protected $type;("Reset")	[Propriedade Herdada]
		//protected $value;			[Propriedade Herdada]
		//protected $xmlLang;		[Propriedade Herdada]

		/**
		 * Events
		 */
		//protected $onfocus;		[Propriedade Herdada]
		//protected $onblur;		[Propriedade Herdada]
		//protected $onclick;		[Propriedade Herdada]
		//protected $ondblclick;	[Propriedade Herdada]
		//protected $onmousedown;	[Propriedade Herdada]
		//protected $onmouseup;		[Propriedade Herdada]
		//protected $onmouseover;	[Propriedade Herdada]
		//protected $onmousemove;	[Propriedade Herdada]
		//protected $onmouseout;	[Propriedade Herdada]
		//protected $onkeypress;	[Propriedade Herdada]
		//protected $onkeydown;		[Propriedade Herdada]
		//protected $onkeyup;		[Propriedade Herdada]

		function __construct()
		{
			parent::__construct("reset");
		}
	}