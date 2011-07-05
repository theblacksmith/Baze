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
class Form extends PageComponent
{
	protected $tagName = 'Form';

	/**
	 * Form Properties
	 *
	 * @access protected
	 * @tab <form></form>
	 */
	//protected $accept;
	//protected $accept_charset;
	//protected $action;
	// protected $class;
	// protected $dir; 
	//protected $enctype;
	// protected $id;	[Propriedade Herdada (Object)]
	// protected $lang;
	//protected $method;
	//protected $name;
	// protected $style;	[Propriedade Herdada (Object)]
	// protected $target;
	// protected $title;
	// protected $xmlLang;

	/**
	 * Event Attributes
	 *
	 * @access protected
	 * @var Event
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
		$this->attributes = array(
			'php:class' => 'Form'
		);
		
		parent::__construct();
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
}