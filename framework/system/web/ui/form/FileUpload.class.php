<?php
/**
 * Arquivo FileUpload.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
import( 'system.web.ui.HtmlComponent' );

/**
 * Classe FileUpload
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class FileUpload extends HtmlComponent
{
	/**
	 * Tag Properties
	 *
	 * @access protected
	 */
	protected $accept;
	//protected $class;
	//protected $dir;
	protected $disabled;
	//protected $id;	[Propriedade Herdada]
	//protected $lang;
	protected $name;
	protected $size;
	//protected $style;	[Propriedade Herdada]
	//protected $title;
	protected $type;
	//protected $xmlLang;

	/**
	 * Event Attributes
	 *
	 * @access protected
	 */
	protected $onFocus;
	protected $onBlur;
	protected $onSelect;
	protected $onChange;
	//protected $onclick;
	//protected $ondblclick;
	//protected $onmousedown;
	//protected $onmouseup;
	//protected $onmouseover;
	//protected $onmousemove;
	//protected $onmouseout;
	//protected $onkeypress;
	//protected $onkeydown;
	//protected $onkeyup;
	public $fileName;
	public $fileType;
	public $fileTmpPath;
	public $fileSize;

	function __construct()
	{
		parent::__construct();
	}

	function initialize(DOMElement $elem)
	{
		$this->disabled = false;
		parent::initialize($elem);
	}

	private function findForm()
	{
		$cont = $this->container;
		
		while($cont !== null && !($cont instanceof Body) && !($cont instanceof Form))
			$cont = $cont->get("container");
		
		if(!($cont instanceof Form))
			return null;

		return $cont;
	}
	
	private function findPage()
	{
		$cont = $this->container;
		
		while($cont !== null && !($cont instanceof Body))
			$cont = $cont->get("container");
		
		if($cont instanceof Body)
			return $cont->get("page");

		return null;
	}
	
	protected function getEntireElement()
	{
		if(!($this->findForm() instanceof Form))
			trigger_error("The FileUpload component requires a form to work properly.", E_USER_WARNING);
		
		if(($page = $this->findPage()) !== null)
			$page->addScript(SYSTEM_DOC_ROOT . "library/js/web/components/FileUploader.js");
		
		$xhtml = _NL . '<input type="file" ' . $this->getPropertiesList() . ' />';

		return $xhtml;
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