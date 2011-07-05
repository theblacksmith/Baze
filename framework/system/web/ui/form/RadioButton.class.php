<?php
/**
 * Arquivo RadioButton.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
import( 'system.web.ui.form.CheckBox' );
import( 'system.web.ui.form.RadioGroup' );

/**
 * Classe RadioButton
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class RadioButton extends CheckBox  implements IFormField
{

	/**
	* Properties
	*/
	/* herdadas
	protected $accept;
	protected $accesskey;
	protected $align;
	protected $alt;
	protected $checked;
	protected $defaultChecked;
	protected $disabled;
	protected $form;
	protected $name;
	protected $size;
	protected $tabIndex;
	protected $type;
	protected $value;
	protected $text;

	//Eventos
	protected $onBlur;
	protected $onChange;
	protected $onFocus;
	protected $onSelect;
	*/

	public function __construct($text = null)
	{
		parent::__construct();
		$this->set("text", $text);
		$this->type = "radio";
	}
	
	public function initialize(DOMElement $elem)
	{
		parent::initialize($elem);
	}
	

	/* Comentado por saulo, armando, por favor me pergunte porque.
	private function unmarkRadios($elems)
	{
		
		foreach ($elems as $elem)
		{
			if ($elem instanceof RadioButton && $elem->get('name') == $this->name)
				$elem->setChecked(false);

			if (array_key_exists('children', $elem->getPropertiesArray()))
				$this->unmarkRadios($elem->get('children'));
		}
		
	}*/
	
	/**
	 * Function setChecked()<br>
	 *
	 *	Essa função será chamada pelo método Initialize caso 
	 * um atributo "checked" seja encontrado.
	 *
	 * @ver 1.5 - modificação do método (03/08/06)<br>
	 * @author Saulo
	 * @return string
	 */
	public function setChecked($checked)
	{
		/* Comentado por saulo, me pergunta que eu explico porque.
		if ($checked)
		{
			if ($this->container instanceof RadioGroup)
				$this->container->clearAll();
			else
			{
				/* todos os outros radio buttons com o mesmo nome devem ser desmarcados * /
				$body = $this->container;
				while (!($body instanceof Body))
					$body = $body->get('container');

				$this->unmarkRadios($body->get('children'));
			}
		}
		*/
		
		/** COMENTADO POR LUCK EM 28/01/2007 - me pergunta depois porque ******
		if(($checked === true || $checked == "checked") && !$this->checked)
		{
			$this->checked = true;
			
			if ($this->container instanceof RadioGroup)
				$this->container->setChecked($this);
		}
		else if(($checked === false || $checked == "false") && $this->checked)
			$this->checked = false;
      /****************************************************************************/
		
		if ( ($checked === TRUE || $checked == 'checked') && !$this->checked)
		{
			$this->check();
		}
		if ( $checked === FALSE && $this->checked)
		{
			$this->uncheck();
		}
	}

	public function check()
	{
		if(!$this->checked)
		{
			$this->checked = true;
		
			if($this->container instanceof RadioGroup)
				$this->container->setChecked($this);
		}
	}

	public function uncheck()
	{
		if($this->checked)
		{
			$this->checked = false;
		
			if($this->container instanceof RadioGroup)
				$this->container->setUnchecked($this);
		}
	}
}