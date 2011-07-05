<?php
/**
 * Arquivo Form.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */


/**
 * Classe Form
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.form
 */
class Form extends InteractiveContainer
{

	/**
	 * Form Properties
	 *
	 * @access protected
	 * @tab <form></form>
	 */
	protected $accept;
	protected $accept_charset;
	protected $action;
	// protected $class;
	// protected $dir; 
	protected $enctype;
	// protected $id;	[Propriedade Herdada (Object)]
	// protected $lang;
	protected $method;
	protected $name;
	// protected $style;	[Propriedade Herdada (Object)]
	// protected $target;
	// protected $title;
	// protected $xmlLang;

	/**
	 * Event Attributes
	 *
	 * @access protected
	 */
	protected $onReset;
	protected $onSubmit;
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

	public $fieldsChildren;

	function __construct()
	{
		// parent constructor
		parent::__construct();

		// default Attributes
		$this->set("method","post"); // TODO: saber qual é o padrão no html quando não colocamos nada
		
		$this->fieldsChildren = array();
	}
	
	
	/**
	 * Método auxiliar que verifica se o componente adicionado ao formulário é um compontente do tipo FormField.
	 * Caso verdadeiro, adiciona o objeto na estrutura auxiliar. Por fim, chama a função de mesmo nome de seu 'parent'.
	 * 
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @param Component $object
	 * @param bool $toFirst
	 * 
	 * @return bool
	 */
	public function addField(Component $obj)
	{
		if ($obj instanceof IFormField)
		{
			array_push($this->fieldsChildren, $obj);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Método que retorna um array dos campos do formulário que implementam FormField.
	 * 
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @return array
	 */
	public function getFields()
	{
		return $this->fieldsChildren;
	}
	

	protected function getEntireElement()
	{
		$xhtml = '';

		$xhtml .= $this->getOpenTag();
		$xhtml .= $this->getChildrenXHTML();
		$xhtml .= $this->getCloseTag();

		return $xhtml;
	}

	protected function getOpenTag()
	{
		$xhtml = "";
		$xhtml .= "\n".'<form ';					// Mapping the object
		$xhtml .= $this->getPropertiesList(); 						// Attributes passed by the user
		//$xhtml .= ' style="'.$this->style->getPropertiesList().'"';	// Style Attributes passed by the user
		$xhtml .= ' >';
		return $xhtml;
	}

	protected function getCloseTag()
	{
		return "\n" . '</form>';
	}

	public function onSubmit($args) {
		$this->raiseEvent(SUBMIT,$args);
	}

	public function onReset($args) {
		$this->raiseEvent(RESET,$args);
	}
}