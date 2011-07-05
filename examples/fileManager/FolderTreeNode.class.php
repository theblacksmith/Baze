<?php
/**
 * Arquivo FolderTreeNode.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
import( 'system.web.ui.fileManager.TreeNode' );
import( 'system.web.ui.fileManager.Folder' );
import( 'system.web.ui.Icon' );
import( 'system.web.ui.image.*' );

/**
 * Classe FolderTreeNode
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
class FolderTreeNode extends TreeNode
{
	public $isExpanded;
	public $ctrlFilled;

	private $folderIcon;
	private $plus;
	private $minus;
	private $join;
	private $plusBottom;
	private $minusBottom;
	private $joinBottom;

	/**
	 * constructor method of the FolderTreeNode class
	 */
	function __construct( $folderPath, $parentFolder = null )
	{
		$folder = new Folder ( $folderPath );

		parent::__construct( $folder, $parentFolder );
		//$this->set('id', 'teste');
		$this->set('id', str_replace( '\\', '_', str_replace( '/', '_', ( ( $parentFolder != null )?str_replace( ".", '_', $parentFolder->get('id') ) : '' ) ) ). str_replace( '\\', '_', str_replace ( '/', '_', str_replace( ".", '_', $folderPath) ) ) );
		$expan = -1;

		$folderHandle = opendir( $this->value->getPath( ) );

		if( $folderHandle )
		{
			while( $file = readdir( $folderHandle ) )
			{
				if( !is_file( $this->value->getPath( ) . '/' . $file ) && $file != '.' && $file != '..' )
				{
					$expan = 0;
					break;
				}
			}
			closedir($folderHandle);
		}

		$this->isExpanded = $expan;

		$this->ctrlFilled = 0;

		$this->folderIcon = new Icon ( );
		$this->folderIcon->set( 'id', $this->get( 'id' ) );

		$this->folderIcon->setType( 'small' );
		$this->folderIcon->set( "image", "FMImages/folder_small.gif" );

		$iconText = basename( $this->value->getPath( ) );
		$this->folderIcon->set( "text", $iconText );

		$this->plus = new Image ( );
		$this->plus->set( 'id', 'p_'.$this->get( 'id' ) );
		$this->plus->set( "src", "FMImages/plus.gif" );

		$this->minus = new Image ( );
		$this->minus->set( 'id', 'm_'.$this->get( 'id' ) );
		$this->minus->set ( "src", "FMImages/minus.gif" );

		$this->join = new Image ( );
		$this->join->set( 'id', 'j_'.$this->get( 'id' ) );
		$this->join->set( "src", "FMImages/join.gif" );

		$this->plusBottom = new Image ( );
		$this->plusBottom->set( 'id', 'pb_'.$this->get( 'id' ) );
		$this->plusBottom->set( "src", "FMImages/plusBottom.gif" );

		/****************** image's event handler *********************/
		$this->plusBottom->onClick->enlist( new EventHandler( array ( $this, "expand" ) ) );
		/**************************************************************/

		$this->minusBottom = new Image ( );
		$this->minusBottom->set( 'id', 'mb_'.$this->get( 'id' ) );
		$this->minusBottom->set( "src", "FMImages/minusbottom.gif" );

		$this->joinBottom = new Image ( );
		$this->joinBottom->set( 'id', 'jb_'.$this->get( 'id' ) );
		$this->joinBottom->set( "src", "FMImages/joinbottom.gif" );
	}

	/**
	 * expands the tree node, if it's possible. This function fulls the subfolders
	 * collection of the folder tree node
	 */
	function expand( Component $component = null, $args = null )
	{
		if( $this->ctrlFilled )
		{
			$this->isExpanded = 1;
			return;
		}

		if( $this->isExpanded == -1 )
		{
			trigger_error( "Folder \"".$this->value->getPath()."\" don't support this action!") ;
			return;
		}

		$folderHandle = opendir( $this->value->getPath( ) );
		if( $folderHandle )
		{
			while( $file = readdir( $folderHandle ) )
			{
				if( !is_file( $this->getValue( )->getPath( ).'/'.$file ) && $file!='.' && $file!='..' )
				{
					if( $this->isExpanded == 0 )
					{
						$this->isExpanded=1;
					}

					$folderTreeNode = new FolderTreeNode ( $this->value->getPath().'/'.$file, $this );
					$this->childNodesCollection->add($folderTreeNode);
				}
			}

			closedir($folderHandle);

			$this->ctrlFilled = 1;
		}
	}

	/**
	 * only sets the tree status to collapsed
	 */
	function collapse( )
	{
		$this->isExpanded = 0;
	}

	/**
	 * print the xml structure representing the folder and generate recursive calls
	 * for your subfolders.
	 */
	function getChildrenXhtml( $nivel )
	{
		$ret = '';
		for( $xr=0; $xr < ( $this->countChildren() - 1 ); $xr++ )
		{
			$filho = $this->getChild($xr);

			if($filho == null)
				continue;

			$ret .= "<tr><td>";
			$ret .= $this->printNivel( $nivel );

			if( $filho->isExpanded > -1 )
			{
				if( $filho->isExpanded == 1 )
				{
					$ret .= $filho->minusBottom->getXHTML( );
					$ret .= $filho->folderIcon->getXHTML( );
					$ret .= $filho->getXhtmlChildren( $nivel + 1 );
				}
				else
				{
					$ret .= $filho->plusBottom->getXHTML( );
					$ret .= $filho->folderIcon->getXHTML( );
				}
			}
			else
			{
				$ret .= $filho->joinBottom->getXHTML( );
				$ret .= $filho->folderIcon->getXHTML( );
			}

			$ret .= "</td></tr>";
		}

		if( ( $this->countChildren( )-1 ) >= 0 )
		{
			$filho = $this->getChild( ( $this->countChildren( )-1 ) );

			if($filho == null)
				return $ret;

			$ret .= "<tr><td>";
			$ret .= $this->printNivel($nivel);

			if( $filho->isExpanded > -1 )
			{
				if($filho->isExpanded == 1)
				{
					$ret .= $filho->minus->getXHTML();
					$ret .= $filho->folderIcon->getXHTML( );
					$ret .= $filho->getXhtmlChildren( $nivel + 1 );
				}
				else
				{
					$ret .= $filho->plus->getXHTML( );
					$ret .= $filho->folderIcon->getXHTML( );
				}
			}
			else
			{
				$ret .= $filho->join->getXHTML();
				$ret .= $filho->folderIcon->getXHTML( );
			}
			$ret .= "</td></tr>";
		}
		return $ret;
	}

	/**
	 * print the images for the tree node level view
	 */
	private function printNivel( $nivel )
	{
		$ret = '';
		for($x = 0; $x < $nivel; $x++)
		{
			$aux = $nivel;
			$noAux = $this;

			while($aux > $x+1)
			{
				$noAux = $noAux->getParent( );
				$aux--;
			}

			$paiAux = $noAux->getParent( );

			$indAux = $paiAux->getChildIndex( $noAux );

			if($indAux < ($paiAux->countChildren()-1))
				$ret .= "<img src=\"FMImages/line.gif\" hspace=\"0\" vspace=\"0\" align=\"absmiddle\" style=\"border: 0px;\" />";
			else
				$ret .= "<img src=\"FMImages/empty.gif\" hspace=\"0\" vspace=\"0\" align=\"absmiddle\" style=\"border: 0px;\" />";
		}
		return $ret;
	}
}