<?php
/**
 * Arquivo InteractiveContainer.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

import( 'system.web.ui.HtmlComponent' );
import( 'system.IContainer' );
import( 'system.web.ui.Literal' );

/**
 * Classe InteractiveContainer
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class InteractiveContainer extends HtmlComponent implements IContainer
{
	/**
	 * Container Properties
	 * @access protected
	 */
	//protected $id;			[Propriedade Herdada]
	//protected $style;			[Propriedade Herdada]

	/**
	 * Children array of objects
	 * @access protected
	 * @var array
	 */
	protected $children;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->children = array();
	}

	/**
	 * @access public
	 * @param Component $object
	 */
	function addChild($object, $toFirst = false)
	{
		global $sysLogger;

		if(is_scalar($object) || empty($object))
		{
			$object = new Literal($object);
		}

		if($object instanceof Component)
		{
			if(!$this->children)
				$this->children = array();

			if (array_key_exists($object->get('id'), $this->children))
				return;

			if($toFirst)
			{
				array_unshift($this->children, $object);
			}
			else
			{
				array_push($this->children, $object);
			}

			$object->setContainer($this);

			// TODO: Remove in futre clean up
			$this->noPrintArr[] = $object->get('id');

			if($this->page)
				$object->setPage($this->page);
		}

		//$sysLogger->debug("Adding ".get_class($object).":".$object->get("id")." to ".get_class($this).":".$this->get("id").NL,__FILE__,__LINE__);

		System::$page->getViewStateManager()->addChange($this, array("changeType" => ChangeType::CHILD_ADDED, "child" => $object));
	}

	/**
	 * @access public
	 * @param Object $object
	 */
	 function isChild($object)
	 {
		 foreach($this->children as $obj)
			if($obj->get("id") == $object->get("id"))
				return true;

		return false;
	 }

	/**
	 * @access public
	 * @param Object $object
	 */
	 function getChild($property, $value)
	 {
		 foreach($this->children as $obj)
			if($obj->get($property) == $value)
				return $obj;

		return null;
	 }

	/**
	 * @access public
	 * @param Object $object
	 */
	function removeChild( $object )
	{
		$off = 0;
		foreach( $this->children as $i => $obj )
		{
			if( $obj->get('id') == $object->get('id') )
			{
				$arr = array_splice( $this->children, $off, 1 );
				System::$page->getViewStateManager()->addChange($this, array("changeType" => ChangeType::CHILD_REMOVED, "child" => $object));
				//unset($arr[0]);
				return true;
			}
			$off++;
		}
		return false;
	}

	/**
	 * @access public
	 * @param void
	 */
	function removeChildren( )
	{
		while( count( $this->children ))
		{
			$arr = array_splice( $this->children, 0, 1 );
			System::$page->getViewStateManager()->addChange($this, array("changeType" => ChangeType::CHILD_REMOVED, "child" => $arr[0]));
			//unset($arr[0]);
		}

		$cmd = new CommandCall(array( "id" => ContainerCommand::RemoveChildren . $this->id,
									  "name" => ContainerCommand::RemoveChildren,
									  "arguments" => array("containerId" => $this->id),
									  "executeOn" => MessageParsePhase::BeforeCreateObjects));

		System::$page->getViewStateManager()->addCommand($cmd, true);
	}

	/**
	 * @return array
	 */
	function getChildren()
	{
		return $this->children;
	}
	/**
	 * @access protected
	 * @return string $children The children objects
	 */
	protected function getChildrenXHTML()
	{
		global $sysLogger;

		$msg = "\t";
		foreach($this->children as $c)
			$msg .= get_class($c).":".$c->get("id") . "\n\t";

		$sysLogger->debug("Getting children XHTML of ".get_class($this).":".$this->get("id").NL.$msg);
		$children = "";

		foreach ($this->children as $child)
		{
			$children .= $child->getXHTML();
		}

		return $children;
	}

	public function initialize(DOMElement $element)
	{
		global $sysLogger;

		if(_IS_POSTBACK) return;

		$sysLogger->debug("Initializing " . get_class($this) . ":" . $this->get("id"), __FILE__, __LINE__);
		/*
		 * carrega as propriedades
		 */
		parent::initialize($element);

		/*
		 * remove os filhos caso eles existam
		 * (evita que filhos sejam duplicados no Postback)
		 */
		 $oldChidren = $this->children;
		 $this->removeChildren();

		/*
		 * adiciona os filhos
		 */
		$nl = $element->childNodes;

		for($i=0; $i < $nl->length; $i++)
		{
			$child = $nl->item($i);

			if($child->nodeType == XML_ELEMENT_NODE && $child->prefix == "php")
			{
				$prop = null;

				$id = $child->getAttribute("id");

				//echo "init $id: ";
				$prop = System::$page->get($id);

				if(!$prop)
				{
					//echo "não encontrou\n";
					$class = ucfirst($child->localName);
					$prop = new $class();
				}

				// Carrega os atributos no objeto instanciado
//				foreach($child->attributes as $attr)
//				{
//					$prop->set($attr->localName, $attr->nodeValue);
//				}

				$prop->initialize($child);
//				$this->set($prop->get("id"),$prop);
				$this->addChild( $prop );

				// colocando uma referencia para o objeto dentro do container
				$page = System::$page;
				$page->set($id,$prop);
			}
			else if($child->nodeType == XML_ELEMENT_NODE)
			{
				// add HTMLTag child
				$tag = new HTMLTag($child);
				$this->addChild($tag);
			}
			else if($child->nodeType == XML_COMMENT_NODE)
			{
				$literal = new Literal("<!--" . $child->nodeValue . "-->");
				$literal->set("id", uniqid(get_class(System::$page) . "Comment"));
				$this->addChild($literal);
			}
			else
			{
				if(trim($child->nodeValue) != "")
				{
					// its a text node
					$sysLogger->debug("Text node found, creating Literal: " . $child->nodeValue);
					$literal = new Literal($child->nodeValue);
					$this->addChild($literal);
				}
			}
		}
	}

	/**
	 * Seta a página a qual este componente pertence propaghando a ação a todos os filhos.
	 *
	 * @author saulo
	 * @param Page $page
	 */
	public function setPage(Page $page)
	{
		$this->page = $page;

		for($i=0; $i < count($this->children); $i++)
		{
			if($this->children[$i] instanceof Component)
				$this->children[$i]->setPage($page);
		}

		$page->addChild($this);
	}
}