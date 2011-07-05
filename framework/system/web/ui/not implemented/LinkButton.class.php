<?php
/**
 * Arquivo LinkButton.class.php
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
import( 'system.web.ui.HyperLink' );
import( 'system.web.ui.IButton' );

/**
 * Classe LinkButton
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class LinkButton extends HyperLink// implements IButton
{
	/**
	 * LinkButton Properties
	 *
	 * @access protected
	 * @var string
	 */
	//protected $accesskey; [Propriedade Herdada]
	//protected $title;		//<a href=... title="$title" ...
	//protected $charset;	[Propriedade Herdada]
	//protected $class;		[Propriedade Herdada]
	//protected $coords;	[Propriedade Herdada]
	//protected $dir;		[Propriedade Herdada]
	//protected $href;		[Propriedade Herdada]
	//protected $hreflang;	[Propriedade Herdada]
	//protected $id;		[Propriedade Herdada]
	protected $imgBorder;	//<a...><img border="$imgBorder" ...
	protected $imgClass;	//<a...><img class="$imgClass" ...
	protected $imgId;		//<a...><img id="$imgId" ...
	protected $imgStyle;	//<a...><img style="$imgStyle" ...
	//protected $lang;		[Propriedade Herdada]
	//protected $name;		[Propriedade Herdada]
	//protected $rel;		[Propriedade Herdada]
	//protected $rev;		[Propriedade Herdada]
	//protected $shape;		[Propriedade Herdada]
	//protected $style;		[Propriedade Herdada]
	//protected $tabindex;	[Propriedade Herdada]
	//protected $target;	[Propriedade Herdada]
	private $value;
	//protected $title;		[Propriedade Herdada]
	//protected $type;		[Propriedade Herdada]
	//protected $xmlLang;	[Propriedade Herdada]
	protected $src;			//<a...><img src="$src" ...

	function __construct()
	{
		parent::__construct();
		$this->noPrintArr[] = 'imgBorder';
		$this->noPrintArr[] = 'imgClass';
		$this->noPrintArr[] = 'imgId';
		$this->noPrintArr[] = 'imgStyle';
		$this->noPrintArr[] = 'value';
		$this->noPrintArr[] = 'src';
	}

	/**
	 * Function getEntireElement()
	 *
	 * @author Luciano (23/06/06)
	 * @return string
	 */
	public function getEntireElement()
	{
		$strOpen = $this->getOpenTag();
		$value = empty($this->value) ? '' : '<br />' . $this->value;
		$strClose = $this->getCloseTag();
		return $strOpen.NL.$this->getImage().NL.$value.NL.$strClose;
	}

	/**
	 * Function getImage()<br>
	 *
	 * @author Luciano (23/06/06)
	 * @return string
	 */
	public function getImage()
	{
		$style = null;
		$border = null;
		$class = null;
		$id = null;
		/*
		if ($this->imgBorder != null)
		{
			$border = 'border="'.$this->imgBorder.'"';
		}
		*/
		if($this->imgClass != null)
		{
			$class = 'class="'.$this->imgClass.'"';
		}

		if ($this->imgId != null)
		{
			$id = 'id="'.$this->imgId.'"';
		}

		if ($this->imgStyle != null)
		{
			$style = 'style="'.$this->imgStyle.'"';
		}

		if ($this->src != null)
		{
			return '<img src="'.$this->src.'" '.$border.' '.$class.' '.$id.' '.$style.' />';
		}

		return null;
	}

	/**
	 * Function setValue()<br>
	 *
	 * @author Luciano (28/06/06)
	 * @param string $value
	 */
	public function setValue($value)
	{
		$value = trim($value);
		if (! empty($value) && $this->value !== $value)
		{
			$this->value = $value;
			return true;
		}
		return false;
	}
}