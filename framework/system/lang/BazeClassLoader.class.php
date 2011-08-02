<?php
/**
 * File of the class BazeClassLoader
 *
 * This file contains the BazeClassLoader class and the shortcut function
 * {@see import}.
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package system.lang
 */

require_once NB_ROOT.'/system/exceptions/runtime/InvalidArgumentValueException.class.php';
require_once NB_ROOT.'/system/exceptions/io/ClassNotFoundException.class.php';

/**
 * The BazeClassLoader class manage the class importing feature
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package system.lang
 */
class BazeClassLoader
{
	/**
	 * The only instance of this class
	 * @var BazeClassLoader
	 */
	private static $instance;

	/**
	 * The possible extensions of the files to be imported
	 * @var Set
	 */
	private static $extensions;

	private static $namespaces = array();

	private static $requiredFiles = array();

	private static $initialized = false;

	private function __construct(){}

	private static function init()
	{
		require_once NB_ROOT . '/system/collections/Collection.class.php';
		require_once NB_ROOT . '/system/collections/Set.class.php';
		// @todo add these classes to the list of required classes

		self::$extensions = new Set('string');
		self::$extensions->addThese(array(".class.php",".interface.php",".code.php",".php"));

		self::$initialized = true;
	}

	public static function import($path)
	{
		if(!self::$initialized)
			self::init();

		// validating import path
		if(trim($path) == '' || preg_match('%[|\\ \n\t\s/?"<>]|\A\.|\.\z|[\.]{2}|(?<=[^\.\A])[\*]|[\*](?=[^\z])%', $path))
		{
			$ex = new InvalidArgumentValueException(Msg::Import_InvalidPath, $path, 2);
		}

		self::_import($path);
	}

	/**
	 * This function should only be used by framework deelopers.
	 * If you want to import a class use the global import function
	 * or the import method.
	 *
	 * @access private
	 * @param string $path
	 */
	private static function _import($path)
	{
		// checking if the class isn't already loaded
		$pathArr = split('\.',$path);
		$currNS = $pathArr[0];
		$className = array_pop($pathArr);
	
		// special treatment for zend framework
		if(isset($pathArr[1]) && strtolower($pathArr[1]) == 'zend')
		{
			$zClassName = join('_', array_slice($pathArr, 1)) . "_$className";
			if(class_exists($zClassName))
				return;			
		}
		else if(class_exists($className))
			return;
		
		if(isset(self::$namespaces[$currNS]))
		{
			$classesRoot = self::$namespaces[$currNS];
			array_shift($pathArr);
		}
		else {
			$ex =  new NamespaceNotFoundException($currNS, Msg::Import_NamespaceNotFound, array(), 1);
			throw $ex;
		}

		$realPath = $classesRoot . '/' . join("/",$pathArr);

		if($className == '*')
		{
			if(is_dir($realPath))
			{
				$filesArray = array();

				$d = dir($realPath);

				while (false !== ($file = $d->read()))
				{
					if ($file != "." && $file != "..")
					{
						if(is_file($d->path."/".$file))
						{
							array_push($filesArray,$d->path."/".$file);
						}
					}
				}

				$d->close();

				self::requireFile($filesArray);
			}
			else
			{
				throw new PackageNotFoundException(Msg::Import_PackageNotFound, str_replace('.*','',$path), 3);
			}
		}
		else
		{
			$realPath .= '/'.$className;

			$bool_fileExists = false;

			foreach(self::$extensions as $ext)
			{
				$file = $realPath . $ext;

				if(file_exists($file))
				{
					$bool_fileExists = true;
					break;
				}
			}

			if($bool_fileExists) {
				self::requireFile($file);
			}
			else {
				$btrace = debug_backtrace();
				$ex = new ClassNotFoundException($path, Msg::Import_ClassNotFound . " Required by {$btrace[2]['file']} at {$btrace[2]['line']}");
				$ex->setGuiltyFile($btrace[2]['file']);
				$ex->setGuiltyLine($btrace[2]['line']);
				throw $ex;
			}
		}
	}

	private static function requireFile($path)
	{
		if(is_array($path))
		{
			foreach($path as $file)
			{
				$lower_fname = strtolower($file);
				if(!isset(self::$requiredFiles[$lower_fname]))
				{
					try {
						require_once(realpath($file));
						self::$requiredFiles[$lower_fname] = true;
					}
					catch(PhpErrorException $ex)
					{
						// TODO: SEM PERMISSAO PARA LER O ARQUIVO
						throw new IOException(realpath($file));
					}
				}
			}
		}
		else
		{
			$lower_fname = strtolower($path);

			if(!isset(self::$requiredFiles[$lower_fname]))
			{
				try {
					require_once(realpath($path));
					self::$requiredFiles[$lower_fname] = true;
				}
				catch(PhpErrorException $ex)
				{
					// TODO: SEM PERMISSAO PARA LER O ARQUIVO
					throw new IOException(realpath($path));
				}
			}
		}

		return true;
	}

	/**
	 * Adds a possible file extension for the imported files
	 *
	 * @param string $ext Not empty string containing the file extension
	 * @throws InvalidArgumentValueException
	 */
	public static function addClassFileExtension($ext)
	{
		if(!self::$initialized)
			self::init();

		$ext = trim($ext);

		if(empty($ext))
		{
			$ex = new InvalidArgumentValueException('ext', $ext);
			throw $ex;
		}

		self::$extensions->add($ext);
	}

	public function removeClassFileExtension($ext)
	{
		if(!self::$initialized)
			self::init();

		self::$extensions->remove($ext);
	}

	/**
	 * Adds a namespace prefix to the internal namespace-path mapping
	 *
	 * @param string $prefix
	 * @param string $folder
	 * @throws EmptyNamespacePrefixException
	 * @throws InvalidArgumentValueException
	 */
	public static function addNamespace($name, $folder)
	{
		if(!self::$initialized)
			self::init();

		$name = trim($name);

		if(empty($name)) {
			throw new EmptyNamespacePrefixException(msg::InvalidEmptyArgument, array('namespace prefix'));
		}

		if(!is_dir($folder))
		{
			$ex = new InvalidArgumentValueException(Msg::InvalidFolderPath, array($folder));
			$ex->setArgumentName('folder');
			$ex->setArgumentValue($folder);
			throw $ex;
		}

		self::$namespaces[$name] = $folder;
	}
}

/**
 * Function Import
 *
 * @param path
 *
 * Warning: the .* syntax will not import classes in subpackages.
 */
function import($path) {
	BazeClassLoader::import($path);
}