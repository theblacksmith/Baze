<?
/**
 * Arquivo Debug.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

/**
 * Classe Debug<br />
 * Prints in the screens debug message. Provides a easy way to turn on/off the debug process.
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class Debug
{
	/**
	 * @var bool $active contains the current status of message printing.
	 * @access public
	 */
	protected $active;

	protected $file;

	/**
	 * Constructor
	 */
	function __construct($active = true)
	{
		$this->active = $active;
	}

	/**
	 * Function getActive
	 *
	 * @return boolean $active
	 */
	function isActive()
	{
		return $this->active;
	}

	/**
	* 	Activate the debug messages.
	*/

	public function activate()
	{
		$this->active = true;
	}

	/**
	* Deactive the debug messages.
	*/
	public function deactivate()
	{
		$this->active = false;
	}

	/**
	* Print a message if debugging is activated.
	*
	* @param string The message that will be printed.
	* @return string The message passed as a parameter. You may use this to filter debug content when using a buffered output.
	*/
	public function msg($msg)
	{
		if(isset($this) && $this->active)
		{
			echo $msg."\n";
			return $msg;
		}
		else if(defined("_DEBUGGING") && _DEBUGGING == true)
		{
			echo $msg;
			return $msg;
		}

		return "";
	}

	public function htmlMsg($msg)
	{
		$msg = $msg . "<br />\n";

		if(isset($this) && $this->active)
		{
			echo $msg;
			return $msg;
		}
		else if(defined("_DEBUGGING") && _DEBUGGING == true)
		{
			echo $msg;
			return $msg;
		}

		return "";
	}

	public function log($msg)
	{
		if($this->active && $this->file)
		{
			// Abre o arquivo e coloca o ponteiro no final ou tenta cria-lo se o mesmo não existir
			if (!($handle = fopen($this->file, 'a')))
			{
				//echo "Arquivo não pôde ser criado.";
				return -1;
			}
			// Escrevendo no arquivo aberto.
			if (!(fwrite($handle, $msg))){
				//print "Erro gravando arquivo.";
				return -2;
			}

			// Fechando o arquivo
			fclose($handle);

			//print "O arquivo de log foi atualizado.";
			return 1;
		}

		return 0;
	}

}