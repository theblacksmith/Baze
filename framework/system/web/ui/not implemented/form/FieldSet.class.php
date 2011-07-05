<?php
	/**
	 * Arquivo FieldSet.class.php
	 * 
	 * @author Saulo Vallory
	 * @copyright 2007 Neoconn Networks
	 * @license http://baze.saulovallory.com/license
	 * @version SVN: $Id$
	 * @since 0.9
	 * @package Baze.classes.web.form
	 */

	

	/**
	 * Classe FieldSet
	 * 
	 * @author Saulo Vallory
	 * @copyright 2007 Neoconn Networks
	 * @license http://baze.saulovallory.com/license
	 * @version SVN: $Id$
	 * @since 0.9
	 * @package Baze.classes.web.form
	 */
	class FieldSet extends InteractiveContainer
	{
		/**
		 * FieldSet Properties <fieldset>
		 *
		 * @access protected
		 */
		protected $class;
		protected $dir;
		// protected $id;		[Propriedade Herdada]
		protected $lang;
		protected $legend;
		// protected $style;	[Propriedade Herdada]
		protected $title;
		protected $xmlLang;

		/**
		 * Event Attributes
		 *
		 * @access protected
		 */
		protected $accesskey;
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
		public function __construct()
		{
			parent::__construct();
			$this->noPrintArr[] = 'legend';
			$this->noPrintArr[] = 'accesskey';
		}

		protected function getOpenTag()
		{
			$xhtml = '';
			// Fieldset - painel
			$xhtml .= "\n".'<fieldset ';		// Mapping the object to xhtml
			$xhtml .= $this->getPropertiesList(); 						// Attributes passed by the user
			$xhtml .= '>';
			// legend - caption
			$xhtml .= "\n".'<legend id="'.$this->id.'_painel_legend"'.(empty($this->accesskey)?'':(' accesskey="'.$this->accesskey.'"')).'>'.$this->legend."</legend>\n";
			return $xhtml;
		}

		protected function getCloseTag(){
			$xhtml = '';
			$xhtml .= "\n</fieldset>\n";
			return $xhtml;
		}

		protected function getTagContent()
		{
			$xhtml = '';
			foreach($this->children as $child)
			{
				$xhtml .= $child->getXHTML(XML_PART_ENTIRE_ELEMENT);
			}
			return $xhtml;
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