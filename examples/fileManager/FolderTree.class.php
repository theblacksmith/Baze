<?php
/**
 * Arquivo FolderTree.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
import( 'system.web.ui.fileManager.Tree' );
import( 'system.web.ui.fileManager.Folder' );
import( 'system.web.ui.fileManager.FolderTreeNode' );

/**
 * Classe FolderTree
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
class FolderTree extends Tree
{
	private $width;
	private $height;

	/**
	 * constructor method of class FolderTree
	 */
	function __construct()
	{
		parent::__construct();

		$root = new FolderTreeNode ( './');

		//$root->expand( $root, ' ' );

		$root->set( "id", '__root' . $this->get( 'id' ) );

		$this->setRoot( $root );

		$this->width = 120;
		$this->height = 300;
	}

	/**
	 * modifies the root node of the tree.
	 *
	 * @param FolderTreeNode $newRoot
	 */
	function setRoot( FolderTreeNode $newRoot )
	{
		$newRoot->expand( $newRoot, '' );
		$this->root = $newRoot;

		//$this->setChild( 0, $newRoot );
	}

	/**
	 * returns the xml structure that relates to the tree
	 *
	 * @return String - it contains the xml result
	 */
	function getXHTML ()
	{
		$ret = "<div id=\"".str_replace('.', '', str_replace('/', '_', $this->root->getValue()->getPath()))."\" style=\"position:absolute; overflow:auto; width:" . $this->width . "px; height:" . $this->height . "px; top:4px; left:4px; border:0px; padding:0px; margin:0px;\">"
		.'<table width="600" border="0" cellpadding="0" cellspacing="0">'
		.'<tr><td>'
		."<img src=\"FMImages/base.gif\" align=\"absmiddle\"  /> "
		."<font style=\"font-family:Verdana, Geneva, Arial, Helvetica, sans-serif; font-size: 10px; color: #000000; text-decoration: none;\">"
		.$this->getRoot( )->getValue( )->getPath( )
		."</font>"
		.'</td></tr>';

		$ret .= $this->getRoot( )->getChildrenXhtml( 0 );

		$ret .= "</table></div>";
		return $ret;
	}

	/** modifies the FolderTree's size
	 *
	 * @param integer $width
	 * @param integer $height
	 */
	function setSize( $width, $height )
	{
		$this->width = $width;
		$this->height = $height;
	}
}