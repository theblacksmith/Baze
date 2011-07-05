<?php
	/**
	 * Arquivo DatePicker.class.php
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
	import( 'system.web.ui.form.TextBox' );
	import( 'system.web.ui.page.Page' );

	/**
	 * Classe DatePicker
	 * 
	 * @author Saulo Vallory
	 * @copyright 2007 Neoconn Networks
	 * @license http://baze.saulovallory.com/license
	 * @version SVN: $Id$
	 * @since 0.9
	 * @package Baze.classes.web.form
	 */
	class DatePicker extends TextBox
	{
		/**
		 * DatePicker Properties
		 * @access protected
		 */
		//protected $class;
		//protected $dir;
		//protected $id;	[Propriedade Herdada de Object]
		//protected $lang;
		//protected $maxlength;
		//protected $name;
		//protected $readonly;
		//protected $size;
		//protected $style;	[Propriedade Herdada de Object]
		//protected $title;
		//protected $type;
		//protected $value;
		//protected $xmlLang;

		/**
		 * Event Attributes
		 * @access protected
		 */
		//protected $accesskey;
		//protected $onfocus;
		//protected $onblur;
		//protected $onselect;
		//protected $onchange;
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
		//protected $tabindex;

		/**
		 * Methods
		 *
		 * @access public
		 */
		//public function blur(){}
		//public function click(){}
		//public function focus(){}
		//public function select(){}

		/**
		 * Private Properties
		 *
		 * @access private
		 */
		private $day;
		private $month;
		private $year;
		private $script;
		/**
		 * @param string $part
		 * @return string
		 */
		public function __construct()
		{
			parent::__construct();
			$this->title = "DD/MM/AAAA";
		}
/*		public function getXHTML($part = XML_PART_ENTIRE_ELEMENT)
		{
			switch ($part)
			{
				case XML_PART_OPEN_TAG :
					if($this->renderXSL != null)
					{
						$xslProc = new XSLTProcessor();
						$xslProc->importStylesheet(DOMDocument::loadXML($this->renderXSL));
						return $xslProc->transformToXml($this->getDefaultXHTML());
					}
					return $this->getDefaultXHTML();

				case XML_PART_ATTRIBUTES :
					return $this->getPropertiesList();

			/**
			 *	Notice: This element doesn't have children
			 * /
				case XML_PART_TAG_CONTENT :
					break;

				case XML_PART_CLOSE_TAG :
					return "/>";

				case XML_PART_ENTIRE_ELEMENT :
					break;

				default :
					break;

			}

			return /*$this->getDefaultXHTML() .
			       $this->getPropertiesList() .
			       $this->closeTag().* /'';
		}
*/
		/**
		 * @access private
		 * @return string
		 */
		protected function getDefaultXHTML()
		{
			$xhtml = null;

			$page = System::$page;
			$page->addCSS('/base/library/css/datepicker.css');

			$xhtml .= "\n<input ";

			return $xhtml;
		}
	}