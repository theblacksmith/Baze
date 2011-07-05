<?php
/**
 * Arquivo Folder.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
import( 'system.web.ui.fileManager.File' );
import( 'base.system.Collection' );

/**
 * Classe Folder
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
class Folder
{
	/**#@+
	 * it contains the properties of the Folder like the path and the date of the last modification in the folder
	 *
	 * @access private
	 */
	private $path;
	private $date;
	private $size;
	/**#@-*/

	/**
	 * it contains the pointer to this parent folder object
	 *
	 * @access private
	 */
	//private $parentFolder;

	/**#@+
	 * it stores the files and subfolders objects,
	 * children of the folder object
	 *
	 * @access public
	 */
	public $filesCollection;
	public $subfoldersCollection;
	/**#@-*/

	/**
	 * The constructor method of the class Folder
	 *
	 * @param String $path - the string path
	 */
	function __construct ( $path = './' )
	{
		$this->path = $path;

		$this->date = filemtime( $this->path );

		$this->size = 0;

		$this->filesCollection = new Collection( );
		$this->subfoldersCollection = new Collection( );

		if( $handle = opendir( $this->path ) )
		{
			while( $file = readdir( $handle ) )
			{
				if( $file != '.' && $file != '..' )
				{
					$this->filesCollection->add( new File( $file, $this ) );
					$this->size += $this->filesCollection->get( $this->filesCollection->size()-1 )->getSize();
				}
			}
		}
	}

	/**
	 * It fills the subfolders collection at the folder
	 *
	 * @access public
	 */
	function expand()
	{
		$this->subfoldersCollection = new Collection ( );

		if( $handle = opendir( $this->path ) )
		{

			while($file = readdir($handle))
			{
				if( !is_file( $this->path . "/" . $file ) && $file!='.' && $file!='..' )
				{

					$this->subfoldersCollection->add( new Folder( $this->path . '/' . $file ) );
				}
			}
		}
		else
		{
			trigger_error( "houve algum erro na tentativa de abrir a pasta para manipulação" );
		}
	}

	/**
	 * It inserts a new file in the folder
	 *
	 * @param File $newFile - File object
	 *
	 * @access public
	 */
	function addFile(File $newFile)
	{
		if( file_exists( $this->path . '/' . $newFile->getName( ) ) )
		{
			$this->filesCollection->add( $newFile );
		}
		else
		{
			trigger_error( "arquivo inexistente! não foi possível adicionar" );
		}
	}

	/**
	 * It inserts a new subfolder in the folder
	 *
	 * @param Folder $newSubfolder - Folder object
	 *
	 * @acces public
	 */
	function addSubfolder(Folder $newSubfolder)
	{
		if( substr_count( $newSubfolder->getPath( ), $this->getPath( ) ) > 0  )
		{
			$this->subfoldersCollection->add( $newSubfolder );
		}
		else
		{
			trigger_error( "pasta não existente! não foi possível adicionar" );
		}
	}

	/**
	 * It returns the path of the folder.
	 *
	 * @return String
	 *
	 * @access public
	 */
	function getPath( )
	{
		return $this->path;
	}

	/**
	 * It returns the size of the folder.
	 *
	 * @return Integer
	 *
	 * @access public
	 */
	function getSize()
	{
		return $this->size;
	}

	/**
	 * It returns the date of the folder last modification.
	 *
	 * @return String
	 *
	 * @access public
	 */
	function getDate( )
	{
		return $this->date;
	}

	/**
	 * It searches a file in the files collection array,
	 * using a name envoy to the function, if found,
	 * returns the index of the file in the array, and
	 * if not found, returns the integer -1.
	 *
	 * @param String $fileName - the name of the file will be searched
	 *
	 * @return Integer
	 *
	 * @access private
	 */
	private function searchFile( $fileName )
	{
		$index = -1;
		for($fileIndex = 0; $fileIndex < $this->filesCollection->size(); $fileIndex++)
		{
			if( strcasecmp( $fileName, $this->filesCollection->get( $fileIndex )->getName( ) ) == 0 )
			{
				$index = $fileIndex;
				break;
			}
		}
		return $index;
	}

	/**
	 * If exists a file in the folder, that have the name envoy
	 * to the function, it returns the file object.
	 *
	 * @param String $fileName - the file name that will want catch
	 *
	 * @return File
	 *
	 * @access public
	 */
	function getFile($fileName)
	{
		$indexFile = $this->searchFile($fileName);

		if($indexFile >= 0)
		{
			return $this->filesCollection->get( $indexFile );
		}

		trigger_error("File not exists!");
		return null;
	}

	/**
	 * It searches a subfolder in the subfolders collection array,
	 * using a name sent to the function, if found,
	 * returns the index of the subfolder in the array, and
	 * if not found, returns the integer -1.
	 *
	 * @param String $subfolderName - the name of the subfolder will be searched
	 *
	 * @return Integer
	 *
	 * @access private
	 */
	private function searchSubfolder( $subfolderPath )
	{
		$index = -1;
		for( $fileIndex = 0; $fileIndex < $this->subfoldersCollection->size(); $fileIndex++)
		{
			if( strcasecmp( $subfolderPath, $this->subfoldersCollection->get( $fileIndex )->getPath( ) ) )
			{
				$index = $fileIndex;
				break;
			}
		}
		return $index;
	}

	/**
	 * If exists a subfolder in the folder, that have the name envoy
	 * to the function, it returns the folder object.
	 *
	 * @param String $subfolderName - the subfolder name that will want catch
	 *
	 * @return Folder
	 *
	 * @access public
	 */
	function getSubfolder($subfolderPath)
	{
		$indexSubfolder = $this->searchSubfolder( $subfolderPath );

		if( $indexSubfolder >= 0 )
		{
			return $this->subfoldersCollection->get( $indexSubfolder );
		}

		trigger_error( "Subfolder not exists!" );
		return null;
	}

	/**
	 * It removes a file from the folder's files collection
	 *
	 * @param [String | File] $file - can be a name of file or a File object
	 *
	 * @access public
	 */
	function removeFile($file)
	{
		if( is_a( $file, 'File' ) )
		{
			$index = $this->searchFile( $file->getName( ) );
		}
		else
		{
			$index = $this->searchFile( $file );
		}

		if($index >= 0)
			return $this->filesCollection->remove( $index );

		return null;
	}

	/**
	 * It removes a subfolder from the folder's subfolders collection
	 *
	 * @param [String | Folder] $subfolder - can be a name of subfolder or a Folder object
	 *
	 * @access public
	 */
	function removeSubfolder($subfolder)
	{
		if( is_a( $subfolder, 'Folder' ) )
		{
			$index = $this->searchFile( $subfolder->getPath( ) );
		}
		else
		{
			$index = $this->searchFile( $subfolder );
		}

		if($index >= 0)
			return $this->subfoldersCollection->remove( $index );

		return null;
	}

	/**
	 * It attributes a new name to the folder
	 *
	 * @param String $newName - folder's string new name
	 *
	 * @access public
	 */
	function setName( $newName )
	{
		$basePath = dirname( $this->path );
		if(rename( $this->path, $basePath.'/'.$newName ))
		{
			$this->path = $basePath.'/'.$newName;
		}
		else
		{
			trigger_error("Não foi possível renomear a pasta " . $this->path);
		}
	}

	/** returns a matrix with the folder's file information
	 *
	 *	@return Matrix
	 */
	function getFileMatrix()
	{
		$files = array( );
		$files['name'] = array();
		$files['size'] = array();
		$files['type'] = array();
		$files['date'] = array();
		$counFiles = 0;

		for( $x=0; $x < $this->filesCollection->size( ); $x++ )
		{
			if( ( $this->filesCollection->get( $x )->getType( ) ) == 'folderFile' )
			{
				$files['name'][$counFiles] = $this->filesCollection->get( $x )->getName( );
				$files['date'][$counFiles] = date( "d/m/Y", $this->filesCollection->get( $x )->getDate( ) );
				$files['size'][$counFiles] = ' ';
				$files['type'][$counFiles] = $this->filesCollection->get( $x )->getType( );
				$counFiles++;
			}
		}

		for( $x=0; $x < $this->filesCollection->size( ); $x++ )
		{
			if( ( $this->filesCollection->get( $x )->getType( ) ) != 'folderFile' )
			{
				$files['name'][$counFiles] = $this->filesCollection->get( $x )->getName( );
				$files['date'][$counFiles] = date( "d/m/Y", $this->filesCollection->get( $x )->getDate( ) );
				$files['size'][$counFiles] = bcdiv( $this->filesCollection->get( $x )->getSize( ), 1024, 1 ).' Kb';
				$files['type'][$counFiles] = $this->filesCollection->get( $x )->getType( );
				$counFiles++;
			}
		}

		return $files;
	}
}