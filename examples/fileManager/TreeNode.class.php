<?php
/**
 * Arquivo TreeNode.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
import( 'base.system.Collection' );
import( 'system.web.ui.HtmlComponent' );

/**
 * Classe TreeNode
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
class TreeNode extends HtmlComponent
{
	/**#@+
	 * node properties:<br>
	 * - Collection childNodesCollection: collection with the node's children<br>
	 * - TreeNode   parent: pointer to its parent node<br>
	 * - Integer    value: node's mixed value
	 *
	 * @access protected
	 */
	protected $childNodesCollection;
	protected $parent;
	protected $value;
	/**#@-*/

	/**
	 * Contructor method of TreeNode class.
	 *
	 * @param $value - the value of the node<br>
	 * 		default value: null
	 * @param $parent - the node's parent node<br>
	 * 		default value: null
	 */
	function __construct($value = null, $parent = null)
	{
		$this->value = $value;

		$this->parent = $parent;

		$this->childNodesCollection = new Collection();
	}

	/**
	 * It includes a new child node in the node.
	 *
	 * @param TreeNode $node
	 *
	 * @return boolean
	 *
	 * @access public
	 */
	function addChild(TreeNode $node)
	{
		/* código extra para testagem da árvore de diretórios */
		if($this->expan == -1)
		{
			$this->expan = 0;
		}
		/* fim do código extra de teste */

		$index = $this->childNodesCollection->indexOf($node);
		if($index == -1)
		{
			$node->setParent($this);
			return $this->childNodesCollection->add($node);
		}

		return false;
	}

	/**
	 * It returns the quantity of children of a TreeNode
	 *
	 * @return int
	 *
	 * @access public
	 */
	function countChildren()
	{
		return $this->childNodesCollection->size();
	}

	/**
	 * It returns the child node stored in the collection slot pointed to index value
	 * passed to the function.
	 *
	 * @param int $index
	 *
	 * @return TreeNode
	 *
	 * @access public
	 */
	function getChild($index)
	{
		return $this->childNodesCollection->get($index);
	}

	/**
	 * It returns the index of slot wich child node is stored, or -1 case
	 * the child not exists in the collection.
	 *
	 * @param TreeNode $node
	 *
	 * @access public
	 */
	function getChildIndex(TreeNode $node)
	{
		return $this->childNodesCollection->indexOf($node);
	}

	/**
	 * It returns an array list of nodes that will be descendant
	 * to this node and contains the same value passed to the function.
	 *
	 * @param mixed $value
	 *
	 * @return Array
	 *
	 * @access public
	 */
	function getDescendantsByValue($value)
	{
		$arrayDesc = array();
		for($currIndex = 0; $currIndex < $this->childNodesCollection->size(); $currIndex++)
		{
			$currChild = $this->childNodesCollection->get($currIndex);
			$currValue = $currChild->getValue();

			if($currValue == $value)
			{
				array_push($arrayDesc, $currChild);
			}

			$currDesc = $currChild->getDescendantsByValue($value);

			$arrayDesc = array_merge($arrayDesc, $currDesc);
		}

		return $arrayDesc;
	}

	/**
	 * It returns the parent node of this node.
	 *
	 * @return TreeNode
	 *
	 * @access public
	 */
	function getParent()
	{
		return $this->parent;
	}

	/**
	 * It returns the node's value.
	 *
	 * @return mixed
	 *
	 * @access public
	 */
	function getValue()
	{
		return $this->value;
	}

	/**
	 * It informs if the node is descendant or not of the node
	 * passed to the function.
	 *
	 * @param TreeNode $node
	 *
	 * @return boolean
	 *
	 * @access public
	 */
	function isDescendant(TreeNode $node)
	{
		$index = $node->getChildIndex($this);

		return ($index==-1) ? false : true;
	}

	/**
	 * It informs if the node is leaf or not, based on the number of children.
	 *
	 * @return boolean
	 *
	 * @access public
	 */
	function isLeaf()
	{
		return ($this->childNodesCollection->size()==0) ? true : false;
	}

	/**
	 * It removes a child of the node. it receives a param
	 * that must be an integer value, representing the index
	 * of the node that will be removed or its instance object.
	 *
	 * @param [ integer | TreeNode ] $node
	 *
	 * @return boolean
	 *
	 * @access public
	 */
	function removeChild($node)
	{
		if(!is_integer($node))
		{
			$index = $this->childNodesCollection->indexOf($node);
		}
		else
		{
			$index = $node;
		}

		/* código extra para testagem: árvore de diretórios */
		if($this->countChildren() == 0)
		{
			$this->expan = -1;
		}
		/* fim do código extra de teste */

		return $this->childNodesCollection->remove($index);
	}

	/**
	 * Disannexes it into the parent node.
	 *
	 * @return boolean
	 *
	 * @access public
	 */
	function removeFromParent()
	{
		return $this->parent->removeChild($this);
	}

	/**
	 * It modifies the parent node of node in the tree.
	 *
	 * @param TreeNode $newParent
	 *
	 * @access public
	 */
	function setParent (TreeNode $newParent)
	{
		$this->parent = $newParent;
	}

	/**
	 * It modifies the value of the node.
	 *
	 * @param $mixed newValue
	 *
	 * @access public
	 */
	function setValue ($newValue)
	{
		$this->value = $newValue;
	}
}