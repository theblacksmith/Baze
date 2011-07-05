<?php
/**
 * Arquivo PasswordField.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
import( 'system.web.ui.form.TextBox' );

/**
 * Classe PasswordField
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class PasswordField extends TextBox implements IFormField
{
		/**
		 * PasswordField Properties
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
	 * @access private
	 * @return string
	 */
	protected function getEntireElement()
	{
		$xhtml = "";

		if(isset($this->style))
		{
			$style = $this->style->getPropertiesList();

			if($style == " style=\"\"")
			{
				$style = "";
			}

		}

		$xhtml .= "\n<input " . 'type="password"' . $style. ' value="' . $this->getValue() .'" />';

		return $xhtml;
	}
	
	/**
	 * @author Luciano
	 * @since 2007-04-16 
	 * 
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	/**
	 * @author Luciano
	 * @since 2007-04-16
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
}