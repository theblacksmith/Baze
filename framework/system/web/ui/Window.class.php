<?php
/**
 * Arquivo Window.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */



/**
 * Classe Window
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Window extends InteractiveContainer
{
	/**#@+
	 * Window Tag Properties
	 * @tag 	<div phpClass="window">
	 * 				<div id="title"></div>
	 * 				<div id="body"></div>
	 * 			</div>
	 * @access protected
	 * @var string
	 */
	//protected $class;
	//protected $id;
	protected $imgClass;	//<div...>...<img class="$imgClass"...
	protected $imgId;		//<div...>...<img id="$imgId"...
	protected $objdragable;	//<div...>...<img objdragable="$objdragable"...
	protected $src;			//<div...>...<img src="$src"...
	protected $caption;		//<div phpClass="window"><div...>$titleText</div>

	public $style;

	/**#@+
	 * Window Object
	 *
	 * @access protected
	 * @var string
	 */
	protected $clientInformation;
	protected $clipboardData;
	protected $document;
	protected $event;
	protected $external;
	protected $history;
	protected $location;
	protected $navigator;
	protected $screen;

	/**#@+
	 * Window HTML DOM Properties
	 *
	 * @access protected
	 * @var string
	 */
	protected $closed;
	protected $defaultStatus;
	//protected $dialogArguments;	(somente no IE)
	//protected $dialogHeight;		(somente no IE)
	//protected $dialogLeft;		(somente no IE)
	//protected $dialogTop;			(somente no IE)
	//protected $dialogWidth;		(somente no IE)
	//protected $frameElement;		(somente no IE)
	protected $length;
	protected $name;
	//protected $offscreenBuffering;(somente no IE)
	protected $opener;
	protected $parent;
	//protected $returnValue;		(somente no IE)
	//protected $screenLeft;		(somente no IE)
	//protected $screenTop;			(somente no IE)
	protected $self;
	protected $status;
	protected $top;

	/**#@+
	 * Window Properties
	 *
	 * @access protected
	 * @var string
	 */
	protected $border;
	protected $frame;
	protected $rules;
	protected $summary;
	protected $width;
	protected $pageObj;
	/**#@-*/

	function __construct()
	{
		// Parent constructor
		parent::__construct();

		// Default external style
		//System::$page->addCSS(SYSTEM_DOC_ROOT . '/library/css/window.css');

		// Load default Attributes of a window
		$this->set("caption","Window");
		$this->style->set("position","absolute");
		$this->style->set("border","1px outset #ccc");
		$this->style->set("padding","2px");
		$this->style->set("background","#ccc");
		$this->style->set("height","200px");
		$this->style->set("width","200px");

		$this->noPrintArr[] = 'buffer';
		$this->noPrintArr[] = 'location';
	}

	function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
		$this->addHTMLClass('window');
	}

	public function setLocation( $page )
	{
		if (!file_exists( "$page.php" ) || $this->location == $page)
			return false;

		/* Carrega $page e coloca dentro do próprio objeto */

		if (file_exists( "$page.code.php" ))
			@include_once( "$page.code.php" );
		else @include_once( "$page.php" );

		$page = explode('/', $page);
		$class = $page[count($page)-1];
		$page = implode('/', $page);

		$src = @file_get_contents("$page.php");
		if ($src === false)
			return false;

		$src = PhpUtils::stripPhpCode($src);

		unset($this->pageObj);
		$this->removeChildren();
		$this->pageObj = new $class($src, true);
		$this->children = $this->pageObj->get('body')->get('children');
		//unset($pageObj);

		$this->location = $page;
		return true;
	}

	public function addChild($obj)
	{
		if (empty($this->location))
			return parent::addChild($obj);

		return false;
	}

	protected function getOpenTag()
	{
		$xhtml = '';
		// window
		$xhtml .= "\n".'<div ';					// Mapping a window object
		$xhtml .= $this->getPropertiesList(); 						// Attributes passed by the user
		//$xhtml .= ' style="'.$this->style->getPropertiesList().'"';	// Style Attributes passed by the user
		$xhtml .= '>';
		// head
		$xhtml .= "\n".'<div id="'.$this->id.'_window_title" style="background:#007;color:white;font:11px Tahoma,sans-serif;padding:3px;height:11px;font-weight:bold;" class="window_title">'.$this->caption.'</div>';
		// body
		$width = (((int)$this->style->get("width"))-2)."px";
		$height = (((int)$this->style->get("height"))-21)."px";
		$xhtml .= "\n".'<div id="'.$this->id.'_window_body" style="background:white;margin-top:2px;height:'.$height.';border:1px inset #ccc;overflow:auto;" class="window_body">';
		return $xhtml;
	}

	protected function getCloseTag()
	{
		$xhtml = '';
		$xhtml .= "\n".'</div>';
		// Img dragable
		// $xhtml .= "\n".'<img id="'.$this->id.'_window_image" container="'.$this->id.'" src="http://lulamolusco:8080/development/quental/vsf/library/images/pixel.gif" style="width:'.$this->style->get("width").';" class="dragable" />';
		$xhtml .= "\n".'</div>';
		return $xhtml;
	}

	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	protected function getTagContent()
	{
		return $this->getChildrenXHTML();
	}

	protected function getEntireElement()
	{
		$xhtml = '';
		$xhtml .= $this->getOpenTag();
		$xhtml .= $this->getTagContent();
		$xhtml .= $this->getCloseTag();
		return $xhtml;
	}
}