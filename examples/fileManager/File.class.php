<?php
/**
 * Arquivo File.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */

/**
 * Classe File
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Neobase.classes.web.fileManager
 */
class File {
	/**#@+
	 * it contains the properties of the File like the folder parent,
	 * the name, the size, the extension type file and the date of the
	 * last modification of the file
	 *
	 * @access private
	 */
	private $name;
	private $folder;
	private $size;
	private $type;
	private $date;
	/**#@-*/

	/**
	 * constructor method of the File class.
	 *
	 * @param $name - name of the file
	 * @param $folder - folder object that contains the file
	 */
	function __construct($name, Folder $folder)
	{
		$this->name = $name;
		$this->folder = $folder;

		if(file_exists($this->folder->getPath() . '/'. $this->name))
		{
			$this->date = filemtime($this->folder->getPath() . '/'. $this->name);
			$this->size = filesize ($this->folder->getPath() . '/'. $this->name);
		}
		else
		{
			$this->date = null;
			$this->size = 0;
		}

		if(is_file($this->folder->getPath() . '/'. $this->name))
		{

			$type = explode(".", $this->name);
			$ext = array_pop($type);

			if($ext != $this->name)
			{
				$this->type = $ext;
			}
			else
			{
				$this->type = 'file';
			}
		}
		else
		{
			$this->type = 'folderFile';
		}
	}

	/**
	 * it returns the name of the file object
	 *
	 * @return String
	 *
	 * @access public
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 * it returns the last modification date of the file object
	 *
	 * @return Date
	 *
	 * @access public
	 */
	function getDate()
	{
		return $this->date;
	}

	/**
	 * it returns the folder object that contains the file object
	 *
	 * @return Folder
	 *
	 * @access public
	 */
	function getFolder()
	{
		return $this->folder;
	}

	/**
	 * it returns the size of the file object
	 *
	 * @return float
	 *
	 * @access public
	 */
	function getSize()
	{
		return $this->size;
	}

	/**
	 * it returns the extension type to the file object
	 *
	 * @return String
	 *
	 * @access public
	 */
	function getType()
	{
		return $this->type;
	}

	/**
	 * it attributes a new extension type to the file object
	 *
	 * @param $newType - the new extension type of the file
	 *
	 * @access public
	 */
	function setType($newType)
	{
		$type = $this->name;
		$extension = strstr($type, '.');

		while($extension)
		{
			$type = substr($extension, 1);
			$extension = strstr($type, '.');
			if($extension!=null)
				$type = substr($extension, 1);
		}

		if($type == $this->name)
		{
			$newName = $this->name.$newType;
		}
		else
		{
			$newName = str_replace($type, $newType, $this->name);
		}

		if(rename($this->folder->getPath() . $this->folder->getName() . $this->name, $this->folder->getPath() . $this->folder->getName() . $newName))
		{
			$this->name = $newName;
			$this->type = $newType;
		}
		else
		{
			trigger_error("não foi possível modificar a extensão do arquivo " . $this->name);
		}
	}

	/**
	 * it attributes a new name of the file object
	 *
	 * @param $newName - the new name of the file
	 *
	 * @access public
	 */
	function setName($newName)
	{
		$extension = strstr($newName, '.');

		while($extension)
		{
			$type = substr($extension, 1);
			$extension = strstr($type, '.');
			if($extension!=null)
				$type = substr($extension, 1);
		}

		if(rename($this->folder->getPath() . $this->folder->getName() . $this->name, $this->folder->getPath() . $this->folder->getName() . $newName))
		{
			$this->name = $newName;
			$this->type = $type;
		}
		else
		{
			trigger_error("não foi possível renomear o arquivo " . $this->name);
		}
	}
}