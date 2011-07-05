<?php
/**
 * Arquivo FolderView.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
import( 'system.web.ui.Icon' );
import( 'base.system.Collection' );
import( 'system.web.ui.fileManager.*' );
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.data.*' );

/**
 * Classe FolderView
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
class FolderView extends HtmlComponent
{
	private $mode;
	private $folder;
	private $fileMatrix;
	private $fileIcons;
	private $dataTableFiles;
	private $extensions;
	public  $width;
	public	$height;

	/** FolderView's constructor method
	 *
	 * @param Folder $folder - folder to be shown
	 * @param String $mode - show mode [ large | small ]
	 */
	function __construct ( $folder = null, $mode = 'small' )
	{
		parent::__construct();

		$this->folder = new Folder ( './' );

		if( $mode!='details' && $mode!='large' && $mode!='small')
		{
			trigger_error( 'Invalid view mode! [ large | small | details ]', E_USER_ERROR );
			return;
		}
		$this->mode = $mode;

		$this->extensions = array ( "exe", "txt", "doc", "xls", "zip", "ppt" );

		$this->fileMatrix = $this->folder->getFileMatrix();
		$arrayAux = array ( );
		$matrix = array ( );

		$xis = 0;
		foreach( $this->fileMatrix as $title => $value )
		{
			switch( $title )
			{
				case 'name':

					$icone = new Icon ( );
					$icone->setType( "small" );

					$icone->set( "text", $value[ 0 ] );
					$icone->set( "id", str_replace( '.', '_', $value[ 0 ] ) );
					$icone->style->set( "left", "3px" );

					switch( $this->fileMatrix[ 'type' ][ 0 ] )
					{
						case 'folderFile':
							$icone->set ( "image", "FMImages/folder_small.gif" );
							break;
						default:
							if( array_search( $this->fileMatrix[ 'type' ][ 0 ], $this->extensions ) != NULL )
							{
								$icone->set ( "image", "FMImages/" . $this->fileMatrix[ 'type' ][ 0 ] . "_small" . ".gif" );
							}
							else
							{
								$icone->set ( "image", "FMImages/file_small" . ".gif" );
							}
					}

					$arrayAux[ 0 ] = 'Nome do Arquivo';

					$matrixAux[ 0 ][ 0 ] = $icone;

					$matrixCount = 0;
					foreach( $value as $fileProperty )
					{
						if( $matrixCount == 0 )
						{
							$matrixCount++;
							continue;
						}

						$matrixAux[ $matrixCount ] = array ( );
						$icone = new Icon ( );
						$icone->setType( "small" );
						$icone->style->set( "left", "3px" );
						if( $this->fileMatrix[ 'type' ][ $matrixCount /*+ 1*/ ] == 'folderFile')
						{
							$icone->set ( "image", "FMImages/folder_small.gif" );
						}
						elseif( is_numeric ( array_search( $this->fileMatrix[ 'type' ][ $matrixCount /*+ 1*/ ], $this->extensions ) ) )
						{
							$icone->set ( "image", "FMImages/" . $this->fileMatrix[ 'type' ][ $matrixCount /*+ 1*/ ] . "_small.gif" );
						}
						else
						{
							$icone->set ( "image", "FMImages/file_small.gif" );
						}

						$icone->set( "text", $fileProperty );
						$icone->set( "id", str_replace( ".", "_", $fileProperty ) );

						array_push ( $matrixAux[ $matrixCount ], $icone );
						$matrixCount++;

					}
					break;

				case 'size':
					$arrayAux[ 1 ] = 'Tamanho';
					$matrixAux[ 0 ][ 1 ] = $value[ 0 ];
					$matrixCount = 0;
					foreach( $value as $fileProperty )
					{
						if( $matrixCount == 0 )
						{
							$matrixCount++;
							continue;
						}

						array_push ( $matrixAux[ $matrixCount ], $fileProperty );
						$matrixCount++;
					}
					break;

				case 'date':
					$arrayAux[ 2 ] = 'Modificado em:';
					$matrixAux[ 0 ][ 2 ] = $value[ 0 ];
					$matrixCount = 0;
					foreach( $value as $fileProperty )
					{
						if( $matrixCount == 0 )
						{
							$matrixCount++;
							continue;
						}

						array_push ( $matrixAux[ $matrixCount ], $fileProperty );
						$matrixCount++;
					}
					break;

				case 'type':
					$arrayAux[ 3 ] = 'Tipo do Arquivo';
					$matrixAux[ 0 ][ 3 ] = $value[ 0 ];
					$matrixCount = 0;
					foreach( $value as $fileProperty )
					{
						if( $matrixCount == 0 )
						{
							$matrixCount++;
							continue;
						}

						array_push ( $matrixAux[ $matrixCount ], $fileProperty );
						$matrixCount++;
					}
					break;
			}
		}

		$matrixCount = 0;
		foreach( $matrixAux as $arrayFileProperties )
		{
			if( $matrixCount == 0 )
			{
				$matrix[ $matrixCount ] = $arrayAux;
				$matrixCount++;
				continue;
			}

			$matrix[ $matrixCount ] = $arrayFileProperties;
			$matrixCount++;
		}

		$this->dataTableFiles = new DataTable ( $matrix );

		$this->fileIcons = new Collection ( );

		for( $xis = 0; $xis < count( $this->fileMatrix['name'] ); $xis++ )
		{
			$newIcon = new Icon ( );

			if($mode == 'details')
			{
				$newIcon->setType ( 'small' );
				switch( $this->fileMatrix[ 'type' ][ $xis ] )
				{
					case 'folderFile':
						$newIcon->set ( "image", "FMImages/folder_small.gif" );
						break;
					default:
						if( is_numeric ( array_search( $this->fileMatrix[ 'type' ][ $xis ], $this->extensions ) ) )
						{
							$newIcon->set ( "image", "FMImages/" . $this->fileMatrix[ 'type' ][ $xis ] . "_small" . ".gif" );
						}
						else
						{
							$newIcon->set ( "image", "FMImages/file_small" . ".gif" );
						}
				}

			}
			else
			{
				$newIcon->setType ( $mode );
				switch( $this->fileMatrix[ 'type' ][ $xis ] )
				{
					case 'folderFile':
						$newIcon->set ( "image", "FMImages/folder_" . $mode . ".gif" );
						break;
					default:
						if( is_numeric( array_search( $this->fileMatrix[ 'type' ][ $xis ], $this->extensions ) ) )
						{
							$newIcon->set ( "image", "FMImages/" . $this->fileMatrix[ 'type' ][ $xis ] . "_" . $mode . ".gif" );
						}
						else
						{
							$newIcon->set ( "image", "FMImages/file_" . $mode . ".gif" );
						}
				}
			}

			$newIcon->set ( "text", $this->fileMatrix['name'][ $xis ] );

			$idIcon = str_replace( ".", "", $this->fileMatrix['name'][$xis] );
			$newIcon->set( "id", "fv_".$idIcon );
			$newIcon->style->set( "left", "0px" );
			$newIcon->style->set( "right", "0px" );
		    $this->fileIcons->add( $newIcon );
		}

		$this->width = 400;
		$this->height= 300;
	}

	/** it modifies the folder's view mode
	 *
	 * @param String $newMode - [ small | large | details ]
	 */
	function setViewMode ( $newMode )
	{
		$this->mode = $newMode;
	}

	/** returns the xhtml of the FolderView
	 *
	 * @return String
	 */
	function getXHTML ( )
	{
		$xhtml = "\n\n".'<div style="float:left; overflow:auto; width:' . $this->width .'px; height:' . $this->height . 'px; position:absolute; left:125px; top:4px;">';
		switch( $this->mode )
		{
			case 'large':
				$lineSpace = 4;
				$iconSpace = 4;
				for( $iconIndex = 0; $iconIndex < $this->fileIcons->size(); $iconIndex++ )
				{
					$this->fileIcons->get( $iconIndex )->style->set( "top", $lineSpace."px" );
					$this->fileIcons->get( $iconIndex )->style->set( "left", $iconSpace."px" );
					if( $this->width < ( $iconSpace + 200 ) )
					{
						$lineSpace += 80;
						$iconSpace = 4;
					}
					else
					{
						$iconSpace += 100;
					}

					$xhtml .= $this->fileIcons->get( $iconIndex )->getXHTML( );
				}
				break;

			case 'small':
				$lineSpace = 4;
				$iconSpace = 4;
				for( $iconIndex = 0; $iconIndex < $this->fileIcons->size(); $iconIndex++ )
				{
					$this->fileIcons->get( $iconIndex )->style->set( "top", $lineSpace."px" );
					$this->fileIcons->get( $iconIndex )->style->set( "left", $iconSpace."px" );
					if( $this->height < ( $lineSpace + 44 ) )
					{
						$lineSpace = 4;
						$iconSpace += 124;
					}
					else
					{
						$lineSpace += 22;
					}

					$xhtml .= $this->fileIcons->get( $iconIndex )->getXHTML( );
				}
				break;

			case 'details':
				$xhtml .= $this->dataTableFiles->getXHTML();
				break;/*
				$xhtml .= "\n\t" . '<table id="tpanel" width="100%" cellpadding="3" border="0" border-color="#FFFFFF">'
						  . "\n\t\t" . '<tbody>';

				for( $iconIndex = 0; $iconIndex < $this->fileIcons->size(); $iconIndex++ )
				{
					$xhtml .= "\n\t\t\t" . '<tr>' .
							  "\n\t\t\t\t" . '<td width="125" valign="top" style="border-right-color:#000000; border-bottom-color:#000000;">';

					$xhtml .= $this->fileIcons->get( $iconIndex )->getXHTML( );

					$xhtml .= "\n\t\t\t\t" . '</td>';

					$xhtml .= "\n\t\t\t\t" . '<td style="border-right-color:#000000; border-bottom-color:#000000;">';

					$xhtml .= "\n\t\t\t\t\t" . '<div><font style="font:10px MS Sans Serif;">' . $this->fileMatrix['size'][$iconIndex] . '</font></div>';

					$xhtml .= "\n\t\t\t\t" . '</td>';

					$xhtml .= "\n\t\t\t\t" . '<td style="border-right-color:#000000; border-bottom-color:#000000;">';

					$xhtml .= "\n\t\t\t\t\t" . '<div><font style="font:10px MS Sans Serif;">' . $this->fileMatrix['type'][$iconIndex] . '</font></div>';

					$xhtml .= "\n\t\t\t\t" . '</td>';

					$xhtml .= "\n\t\t\t\t" . '<td style="border-right-color:#000000; border-bottom-color:#000000;">';

					$xhtml .= "\n\t\t\t\t\t" . '<div><font style="font:10px MS Sans Serif;">' . $this->fileMatrix['date'][$iconIndex] . '</font></div>';

					$xhtml .= "\n\t\t\t\t" . '</td>';

					$xhtml .= "\n\t\t\t" . '</tr>';
				}

				$xhtml .= "\n\t\t" . '</tbody>' .
						  "\n\t" . '</table>' ;
				break;*/
		}
		$xhtml .= "\n</div>";
		return $xhtml;
	}

	/** modifies the folderView's size
	 *
	 * @param integer $width
	 * @param integer $height
	 */
	function setSize( $newWidth, $newHeight )
	{
		$this->width = $newWidth ;
		$this->height = $newHeight;
	}
}