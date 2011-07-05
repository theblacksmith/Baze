<?php
/**
 * Arquivo Icon.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.image.Image' );
import( 'system.web.ui.form.TextBox' );

define("LARGE_ICON", "large");
define("SMALL_ICON", "small");

/**
 * Classe Icon
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Icon extends HtmlComponent
{
	/**#@+
	 * Icon Properties
	 *
	 * @access protected
	 * @var string
	 */
	protected $align;

	public $image; 		// <div><img src="$image"...
	private $imageClass; 	// <div><input class="$textClass" ...
	private $imageStyle;	// <div><input style="$textStyle" ...
	protected $alt;			// <div><img src="$image"...

	public $text; 		// <div><input value="$text" ...
	private $textClass; 	// <div><input class="$textClass" ...
	private $textStyle;	// <div><input style="$textStyle" ...

	protected $maxlength;	// <div><input maxlength="$maxlength" ...

	// Habilita/desabilita o script que marca o ícone como selecionado
	private $isSelectable;
	private $isEditable;

	/**#@+
	 * Events
	 *
	 * @access protected
	 * @var string
	 */
	protected $onfocus;
	protected $onblur;
	protected $onselect;
	protected $onchange;

	public function __construct()
	{
		// Parent constructor
		parent::__construct();

		// Load default Attributes of a icon
		$this->type="large";
		$this->isEditable = true;

		$this->image = new Image();

		$this->text = new TextBox();
		$this->text->set('wrap','off');
		$this->text->set('rows','1');
	}

	function initialize(DOMElement $elem)
	{
		parent::initialize($elem);

		$this->set("class", array_merge(array($this->type.'_icon'), $this->class));

		// loading properties in image
		$this->image->set("id", $this->id.'_icon_img');
		$this->image->set("class", array_merge(array($this->type.'_icon_img'), $this->image->get('class')));
		$this->image->set("alt", $this->alt);

		// só dá pra setar se transformar o ícone em container
		// $this->image->set('container', $this);

		// loading properties in textarea
		$this->text->set("id", $this->id.'_icon_text');
		$this->text->set("class", array_merge(array($this->type.'_icon_text'),$this->text->get('class')));
		$this->text->set("alt", $this->alt);
		$this->text->set("readonly", (boolean)$this->isEditable);

		// só dá pra setar se transformar o ícone em container
		// $this->textArea->set('container', $this);

		if($this->isSelectable)
		{
			$this->addEventListener(MOUSE_DOWN, "iconSelect(this);", false, true);
			$this->addEventListener(MOUSE_UP, "iconSelect(this);", false, true);
		}

		System::$page->addCSS("http://" . SYSTEM_SITE_ROOT . 'library/css/icon.css');
	}

	public function setSelectable($bool)
	{
		$this->isSelectable = (boolean)$bool;
	}

	public function setImage($img) {
		$this->image->set('src', $img);
	}

	public function setImageClass($cls) {
		$this->image->addHTMLClass($cls);
	}

	public function setImageStyle($stl) {
		$this->image->set('style', $stl);
	}

	public function setText($txt) {
		$this->text->set('value', $txt);
	}

	public function setTextClass($txt) {
		$this->text->set('class', $txt);
	}

	public function setTextStyle($txt) {
		$this->text->set('style', $txt);
	}

	protected function getEntireElement($preserveWhiteSpaces)
	{
		$xhtml = "";
		// Div
		$xhtml .= ($preserveWhiteSpaces ? NL.NL : "" ) . '<div ';
		$xhtml .= $this->getPropertiesList() . '>';
		// Img
		$xhtml .= ($preserveWhiteSpaces ? NL : "" ) . $this->image->getXHTML(XML_PART_ENTIRE_ELEMENT, false);
		// Input
		$xhtml .= ($preserveWhiteSpaces ? NL : "" ) . $this->text->getXHTML(XML_PART_ENTIRE_ELEMENT, false);

		$xhtml .= ($preserveWhiteSpaces ? "\n" : "" ) . "</div>";

		return $xhtml;
	}

	public function setType($type)
	{
		$type = strtolower($type);
		switch($type)
		{
			case LARGE_ICON:
			case SMALL_ICON:
				$this->type = $type;
				break;
			default:
				trigger_error( "Trying to define a non existing type to an Icon.",E_USER_ERROR);
		}
	}

	public function onFocus($args) {
		$this->raiseEvent(FOCUS,$args);
	}

	public function onBlur($args) {
		$this->raiseEvent(BLUR,$args);
	}

	public function onSelect($args) {
		$this->raiseEvent(SELECT,$args);
	}

	public function onChange($args) {
		$this->raiseEvent(CHANGE,$args);
	}

}