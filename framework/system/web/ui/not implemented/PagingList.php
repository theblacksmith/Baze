<?php
/**
 * Arquivo PagingList.php
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
import( 'system.web.ui.Span');
import( 'system.web.ui.Component' );

/**
 * Classe PagingList
 * 
 * @author Luciano AJ
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class PagingList extends InteractiveContainer
{
	/**
	 * @var string Style
	 */
	private $listStyle;
	
	/**
	 * @var string Style
	 */
	private $itemStyle;
	
	/**
	 * @var string Style
	 */
	private $linkStyle;
	
	
	/**
	 * @var URL
	 */
	private $refPage;
	
	/**
	 * @var string 
	 */
	private $varName;
	
	/**
	 * @var string
	 */
	private $auxVarName;
	
	/**
	 * @var mixed
	 */
	private $auxVarValue;
	
	/**
	 * @var int
	 */
	private $numItens;
	
	/**
	 * @var int
	 */
	private $numShowItens;
	
	/**
	 * @var mixed
	 */
	private $placeComponent;
	
	/**
	 * @var Delegate
	 */
	private $delegatedFunction;

	/**
	 * @var EventHandler
	 */
	private $clickHandler;
	
	
	/**
	 * Function contruct()<br><br>
	 * 
	 * @author Luciano
	 */
	public function __construct()
	{
		$this->numItens = 0;
		$this->numShowItens = 1;
		$this->elements = null;
		
		$this->clickHandler = new EventHandler(array($this,'handleClick'));
		
		$this->delegatedFunction = new Delegate(/*int*/null,/*int*/null);
		
		parent::__construct();
	}
	
	public function handleClick(Component $sender, $args)
	{
		$start = $sender->get('index');
		$end = $this->numShowItens;
		
		$collection = $this->delegatedFunction->call(array($start,$end)); 
		$sender->style->set('background-color','#EEEEEE');
		$this->setElements($collection);
	}
	
	/**
	 * Function setUpdateFunction()<br><br>
	 * 
	 * @author Luciano (05/01/2007)
	 */
	public function setUpdateFunction(/*funcao q retorna a coleção de elementos */$func)
	{
		$this->delegatedFunction->setFunction($func);
		
		$col = $this->delegatedFunction->call(array(null,null));
		
		$this->numItens = $col->size();
		
		$col->removeRange($this->numShowItens, $this->numItens);
				
		$this->setElements($col);
	}
	
	/**
	 * Function getEntireElement()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @return string
	 */
	protected function getEntireElement()
	{
		$listStyle = null;
		$itemStyle = null;
		$linkStyle = null;
		
		if ($this->listStyle)
		{ $listStyle = 'style="'.$this->listStyle.'"'; }
		
		if ($this->itemStyle)
		{ $itemStyle = 'style="'.$this->itemStyle.'"'; }
		
		if ($this->linkStyle)
		{ $linkStyle = 'style="'.$this->linkStyle.'"'; }
		
		$xhtml = '<ul '.$listStyle.'>';
		$index = 0;
		
		for ($i=1; $i < ($this->numItens/$this->numShowItens) + 1; $i++)
		{
			$blockGoTo = new Span();
			$id = 'BLOCK_GOTO_'.$i;
			$blockGoTo->set('id',$id);
			$blockGoTo->set('style',$this->linkStyle);
			$blockGoTo->set('index',$index);
			$blockGoTo->addChild($i);
			$index = $index + $this->numShowItens;
			$blockGoTo->addEventListener(CLICK, $this->clickHandler);
						
			//TODO: Remover essa referêcia quando o Framework for inteligente o suficiente para não depender do objeto diretamente na página
			System::$page->$id = $blockGoTo;
			
			$xhtml.= '<li '.$itemStyle.' >'.$blockGoTo->getEntireElement().'</li>';	
		}
		
		$xhtml.= '</ul>';
		
		return $xhtml;
	}
	
	/**
	 * Function setLinkStyle()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param $string $linkStyle
	 */
	public function setLinkStyle($linkStyle)
	{
		$this->linkStyle = $linkStyle;
	}
	
	
	/**
	 * Function setNumShowItens()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param int $numShowItens
	 * @return boolean true or false
	 */
	public function setNumShowItens($numShowItens)
	{
		if (is_int($numShowItens) && $numShowItens>0)
		{
			$this->numShowItens = $numShowItens;
			return true;
		}
		return false;
	}
	
	/**
	 * Function setListStyle()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param string $style
	 */
	public function setListStyle($style)
	{
		$this->listStyle = $style;
	}
	
	/**
	 * Function setItemStyle()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param string $style
	 */
	public function setItemStyle($style)
	{
		$this->itemStyle = $style;
	}
	
	/**
	 * Function setRefPage()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param URL $refPage 
	 */
	public function setRefPage($refPage)
	{
		$this->refPage = $refPage;
	}
	
	/**
	 * Function setVarName()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param string $varName 
	 */
	public function setVarName($varName)
	{
		$this->varName = $varName;
	}
	
	/**
	 * Function setAuxVarName()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param string $au/VarName 
	 */
	public function setAuxVarName($auxVarName)
	{
		$this->auxVarName = $auxVarName;
	}
	
	/**
	 * Function setAuxVarValue()<br><br>
	 * 
	 * @author Luciano (20/12/2006)
	 * @param mixed $auxVarValue 
	 */
	public function setAuxVarValue($auxVarValue)
	{
		$this->auxVarValue = $auxVarValue;
	}
	
	/**
	 * Function setElements()<br><br>
	 * 
	 * @author Luciano (05/01/2007)
	 * @param Collection $elements
	 * @return boolean - true if sucessfull or false if failure 
	 */
	public function setElements($elements)
	{
		if (is_object($this->placeComponent) && get_class($elements) == 'Collection')
		{
			$this->placeComponent->removeChildren();

			if (get_class($this->placeComponent) == 'Lista')
			{
				if ($this->placeComponent->getTemplate() !== null)
				{
					$this->placeComponent->populateList($elements);
				}
			}
			else
			{
				$numElements = $elements->size();
					
				for ($i=0; $i<$numElements; $i++)
				{
					$this->placeComponent->addChild($elements->getByPosition($i));
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Function setPlaceComponent()<br><br>
	 * 
	 * @author Luciano (05/01/2007)
	 * @param Object $component
	 * 
	 * @return boolean true if sucessful or false if failure
	 */
	public function setPlaceComponent($component)
	{
		if (is_object($component))
		{
			$this->placeComponent = $component;
			return true;
		}
		return false;
	}
}