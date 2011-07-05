<?php
/**
 * Arquivo Lista.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

import( 'system.web.ui.ListItem' );

/**
 * Classe Lista
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Lista extends InteractiveContainer
{
	/**#@+
	 * List Properties
	 *
	 * @access protected
	 * @var string
	 */
	//protected $class;		//<div><[select|input] class="$class" ...
	//protected $dir;		//<div><[select|input] dir="$dir" ...
	//protected $disabled;	//<div><[select|input] disabled="$disabled" ...
	//protected $id;		[Propriedade Herdada]
	//protected $lang;		//<div><[select|input] lang="$lang" ...
	//protected $multiline;	//<div><select multiline="$multiline" ...
	//protected $name;		//<div><[select|input] name="$name" ...
	//protected $size;		//<div><[select|input] size="$size" ...
	//protected $style;		[Propriedade Herdada]
	//protected $tabindex;	//<div><[select|input] tabindex="$tabindex" ...
	//protected $type;
	//protected $xmlLang;		//<div><[select|input] xmlLang="$class" ...

	/**#@+
	 * Event Attributes
	 *
	 * @access protected
	 * @var string
	 */
	private $itemStyle;
	private $listType;
	private $template;

	const UNORDERED_LIST 	= "ul";
	const ORDERED_LIST 		= "ol";
	const DEFINITION_LIST 	= "dl";

	function __construct()
	{
		$this->listType = "ul";
		$this->template = null;

		parent::__construct();
	}

	public function initialize(DOMElement $elem)
	{
		$this->disabled = false;
		while (count($this->children))
		{
			$i = array_shift($this->children);
			unset($i);
		}
		parent::initialize($elem);
	}

	/**
	 * Function addChild()<br>
	 *
	 * @param ListItem $object
	 */
	public function addChild(ListItem $object)
	{
		if(is_object($object) && !$this->acceptsChild($object))
		{
			return;
		}

		parent::addChild($object);
	}

	protected function acceptsChild($object)
	{
		if ($object->getType() == 'li' && ($this->listType == 'ul' || $this->listType == 'ol'))
		{
			return true;
		} 
		
		if (($object->get("type") == 'dt' || $object->get("type") == 'dd') && $this->listType == 'dl')
		{
			return true;
		}
		
		return false;
	}

	protected function getOpenTag()
	{
		return '<'.$this->listType.' '.$this->getPropertiesList()." >\n";
	}

	protected function getAttributes()
	{
		return $this->getPropertiesList();
	}

	protected function getTagContent()
	{
		$xhtml = '';
		foreach($this->children as $child)
		{
			$xhtml .= $child->getXHTML(XML_PART_ENTIRE_ELEMENT);
		}
		return $xhtml;
		
//		return $this->getChildrenXHTML();panel
	}

	protected function getCloseTag()
	{
		return '</'.$this->listType.">\n";
	}

	protected function getEntireElement()
	{
		$strOpen = $this->getOpenTag();
		$strTags = $this->getTagContent();
		$strClose = $this->getCloseTag();

		return $strOpen.$strTags.$strClose;
	}
	
	/**
	 * Function setItemStyle()<br><br>
	 * 
	 * @author Luciano (04/01/2007)
	 * @param mixed $style
	 */
	public function setItemStyle($style)
	{
		if (is_object($style) && get_class($style) == 'Style')
		{
			$this->itemStyle = $style;
		}
		else
		{
			$st = new Style($style, $this);
			$this->itemStyle = $st;
		}
	}
	
	public function setListType ($type)
	{
		if ($type == self::UNORDERED_LIST || $type == self::ORDERED_LIST || $type == self::DEFINITION_LIST)
		{
			$this->listType = $type;
			return true;
		}
		return false;
	}
	
	/**
	 * Function setTemplate()<br><br>
	 * 
	 * @author Luciano (04/01/2007)
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		// - Um template é um script (html/xml) ou um texto comum
		// - Um template possui blocos (ou variáveis) aos quais serão substituídos depois pelas informações corretas.
		// - Cada bloco de um template deve ser no formato %<número_do_bloco>%, ou seja, se há um template com 3 entradas
		//então haverá os blocos %1%, %2% e %3%.
		// - A numeração do bloco deve ser da seguinte forma: %n% sendo que n>0 e n é inteiro.
		// - É necessário que o número de blocos seja igual ao número de itens nas funções que obtém as informações, para
		//maires detalhes sobre como popular um template, ver "populateList".
		$this->template = $template;
		
		//Exemplo de Template:
		//
		//<div style="border:1px solid #646464>
		//Olá %1%, seja bem vindo!<br />
		//<a href="minhapágina.html?id=%2% style="color:#30FF00" >%3%</a>
		//</div> 
		//
	}
	
	/**
	 * Function populateList()<br><br>
	 * This function constructs a list with itens received by the collection. 
	 * The information of itens are gotten through the array of functions $funcs parameter.
	 * Each gotten information is set in the blocks of template.
	 * 
	 * @author Luciano (04/01/2007)
	 * @param Collection $itens
	 * @param array $funcs
	 * 
	 * @return boolean - true if sucessful or false if failure
	 */
	public function populateList($itens, $funcs)
	{
		//Parametro $itens precisa ser uma coleção para popular a lista
		if (get_class($itens) != 'Collection')
		{
			return false;
		}
		
		//O template NÃO pode ser nulo. É através dele que os itens serão construídos.
		if ($this->template == null)
		{
			return false;
		}
		
		//Se o parâmetro $func tiver apenas uma função (string simples com o nome da função)
		//então crar um array com um único elemento.
		if (! is_array($funcs))
		{
			$funcs = array ($funcs);			
		}
		
		$numItens = $itens->size();
		//Pra cada item da coleção, setar no template as informações obtidas pelas funções recebidas. 
		//No template há os blocos %n% (n>0), em que as informações serão sobrescritas nesses blocos.
		for ($i=0; $i<$numItens; $i++)
		{
			$item = $itens->getByPosition($i);
			
			$template = $this->template;
			
			for ($j=0; $j<count($funcs); $j++)
			{
				//Momentaneamente a informalão ($result) é o próprio item da coleção.
				$result = $item;
				
				//Uma única informação pode ser adquirida chamando mais de uma função
				//Parseando a string "nome_função;nome_outra_função" temos duas funções que provavelmente a 
				//primeira é um "get" de algum objeto e a segunda uma função "get" da própria informação.
				$func = $funcs[$j];
				$func = explode(';',$func);
				foreach ($func as $f)
				{
					if (is_object($result))
					{
						$result = call_user_func(array ($result,$f));
					}
				}
				//O bloco do tipo %n% (n>0) será substituído pelo resultado final, pela informação em si.
				//Maiores detalhes sobre o template ver "setTemplate"
				$block = '%'. ($j+1) .'%';
				$template = str_replace($block,$result,$template);
			}
		
			//Criando o Item de Lista e adcionando na Lista
			$it = new ListItem();
			$id = 'LI_ELEMENT_'.$i;
			$it->set('id',$id);
			$it->set('style',$this->itemStyle->getPropertiesList());
			$it->addChild($template);
			$this->addChild($it);
		}
		return true;
	}
}