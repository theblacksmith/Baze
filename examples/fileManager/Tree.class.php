<?php
/**
 * Arquivo Tree.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
import( 'system.web.ui.fileManager.TreeNode' );


/**
 * Classe Tree
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
class Tree extends InteractiveContainer
{
	/**
	 * The root of Tree. - instance of TreeNode.
	 *
	 * @access protected
	 */
	protected $root;

	/**
	 * The constructor method of Tree class.
	 *
	 * @param $params
	 */
	function __construct()
	{
		parent::__construct();

		$root = new TreeNode( );

		$this->addChild( $root );
	}

	/**
	 * It searches and returns the nodes of the tree that contains a specific value.
	 *
	 * @param mixed $value - the value of the nodes to search
	 *
	 * @return Array - it contains the nodes's pointers required
	 *
	 * @access public
	 */
	function getNodesByValue($value)
	{
		$nodes = array();

		if ($this->root->getValue() == $value)
			array_push($nodes, $this->root);

		$others = $this->root->getDescendantsByValue($value);

		$nodes = array_merge($nodes, $others);

		return $nodes;
	}

	/**
	 * Returns the root of Tree.
	 *
	 * @return TreeNode
	 *
	 * @access public
	 */
	function getRoot()
	{
		return $this->root;
	}

	/**
	 * It modifies the root of Tree.
	 *
	 * @param TreeNode $root - the new root of Tree
	 *
	 * @access public
	 */
	function setRoot ( $root )
	{
		if(isset($root))
			$this->root = $root;
	}

	/**
	 * It removes a node of tree.
	 *
	 * @param TreeNode $node - the node that will be removed
	 *
	 * @access public
	 */
	function removeNode( TreeNode $node )
	{
		if(($parent = $node->getParent())!=null)
		{
			$parent->removeChild($node);
		}
		else
		{
			$this->root = new TreeNode();
		}
	}
}