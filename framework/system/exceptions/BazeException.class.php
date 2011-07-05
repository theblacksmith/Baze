<?php

/**
 * Classe base para todas as exceções
 * @author Saulo Vallory
 * @version 1.0
 * @copyright  2007 Neoconn Networks
 * @since 1.0
 */
/**
 * This is the base exception class for all exceptions in the framework.
 *
 * This class provides automatic replacement of tokens in the exception message.
 * Each subclass may provide aditional tokens by just adding the $key => $value
 * pairs to the $tokens array passed in the constructor. The tokens provided by
 * this class are:
 * {{code}} - The user defined exception code
 * {{file}} - The file where the exception was thrown
 * {{guiltyFile}} - The file guilty for the exception
 * {{line}} - The line where the exception was thrown
 * {{guiltyLine}} - The line guilty for the exception
 * {{function}} - The function or method where the exception was thrown
 * {{guiltyFunction}} - The function or method where the exception was thrown
 * {{class}} - The class, in case the exception occurs inside a method, wich the method belongs to
 * {{guiltyClass}} - The class, in case the exception occurs inside a method, wich the method belongs to
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package system.exception
 */
class BazeException extends Exception
{
	/**
	 * The exception message with the placeholders
	 * replaced by the tokens.
	 *
	 * @var string
	 */
	protected $message = 'Unknown exception';

	/**
	 * The original exception message, with the
	 * placeholders intact.
	 *
	 * @var string
	 */
	protected $origMsg = 'Unknown exception';

	/**
	 * User defined exception code
	 *
	 * @var string
	 */
	protected $code = 0;

	/**
	 * Source filename of exception
	 *
	 * @var string
	 */
	protected $file;

	/**
	 * Source line of exception
	 *
	 * @var string
	 */
	protected $line;

	/**
	 * Source function of exception
	 *
	 * @var string
	 */
	protected $function;

	/**
	 * Source class of exception
	 *
	 * @var string
	 */
	protected $class;

	/**
	 * The file guilty for the exception
	 *
	 * @var string
	 */
	protected $guiltyFile;

	/**
	 * The line guilty for the exception
	 *
	 * @var int
	 */
	protected $guiltyLine;

	/**
	 * The line guilty for the exception
	 *
	 * @var string
	 */
	protected $guiltyFunction;

	/**
	 * The line guilty for the exception
	 *
	 * @var string
	 */
	protected $guiltyClass;

	/**
	 * The trace starting from the guilty step
	 *
	 * @var array
	 */
	protected $guiltyTrace;

	/**
	 * A piece of code around where the error or exception was throwed
	 *
	 * @var string
	 */
	private $source;

	/**
	 * The constructor
	 *
	 * @param string $message The error message
	 * @param mixed $tokens A value or an array of values to replace the placeholders (like {{aToken}}) in the message.
	 */
	public function __construct($message, $tokens = array(), $guiltyStep = 0)
	{
		if(!is_array($tokens))
			$tokens = array($tokens);

		if($guiltyStep !== null)
		{
			$trace = $this->getTrace();

			for($i=0; $i < $guiltyStep; $i++)
				array_shift($trace);

			$this->guiltyTrace = $trace;
			$this->setGuiltyStep($this->guiltyTrace[0]);
		}

		$this->origMsg = $message;

		$this->message = $message;
		$this->updateMessage();

		foreach($tokens as $tok)
			$this->message = preg_replace('|{{([\w _]+)}}|', '<strong>'.((string)$tok).'</strong>', $this->message, 1);

		parent::__construct();
	}

	public function getGuiltyTrace()
	{
		return $this->guiltyTrace;
	}

	/**
	 * This function updates the exception message based on
	 * the original message and the current object property values
	 *
	 */
	protected function updateMessage()
	{
		global $_nb_ex;

		$_nb_ex = $this;
		$this->message = preg_replace_callback('|{{([\w _]+)}}|', array('BazeException', 'replacePlaceholder'), $this->message);
		$_nb_ex = null;
	}

	/**
	 * This function gets an array with two items
	 * the first is the placeholder found and the second is the
	 * placeholder name (ex: {{file}} and file). The purpose of
	 * this function is replace message placeholders with its
	 * values. If you want to extend this functionality you can
	 * override this function, but you must call this function
	 * at the end of yours.
	 *
	 * @param array $match
	 */
	protected static function replacePlaceholder(array $match)
	{
		global $_nb_ex;

		if(!isset($match[1]))
			throw new InvalidArgumentValueException('match', $match, Msg::$BazeException_invalidPlaceholderMatch);

		$getter = 'get'.ucfirst($match[1]);

		if(method_exists($_nb_ex,$getter))
		{
			return '<strong>'.$_nb_ex->$getter().'</strong>';
		}

		return $match[0];
	}

	/**
	 * Get the file guilty for the exception
	 *
	 * @return string
	 */
	public function getGuiltyFile()
	{
		return $this->guiltyFile;
	}

	/**
	 * Get the line guilty for the exception
	 *
	 * @return int
	 */
	public function getGuiltyLine()
	{
		return $this->guiltyLine;
	}
	/**
	 * @return string
	 */
	public function getClass()
	{
		if(isset($this->class))
			return $this->class;

		$this->grabTraceData();

		return $this->class;
	}

	/**
	 * @return string
	 */
	public function getFunction()
	{
		if(isset($this->function))
			return $this->function;

		$this->grabTraceData();

		return $this->function;
	}

	/**
	 * @return string
	 */
	public function getGuiltyClass () {
		return $this->guiltyClass ;
	}

	/**
	 * @return string
	 */
	public function getGuiltyFunction () {
		return $this->guiltyFunction ;
	}

	/**
	 * @return string
	 */
	public function getOrigMsg() {
		return $this->origMsg ;
	}

	/**
	 * This function grabs all the possible useful
	 * data from trace and put it on class properties
	 */
	protected function grabTraceData()
	{
		if(isset($this->trace[0]))
		{
			if(isset($this->trace[0]['class']))
				$this->class = $this->trace[0]['class'];

			if(isset($this->trace[0]['function']))
				$this->function = $this->trace[0]['function'];
		}
		else
		{
			$this->class = '';
			$this->function = '';
		}
	}

	/**
	 * @param string $class
	 */
	public function setClass($class) {
		if($this->class === $class)
			$this->class = $class ;

		if(strpos($this->origMsg,'{{class}}') !== false)
			$this->updateMessage();
	}

	/**
	 * @param string $function
	 */
	public function setFunction($function) {
		$this->function = $function;
	}

	/**
	 * @param string $guiltyClass
	 */
	public function setGuiltyClass ( $guiltyClass ) {
		$this->message = str_replace('{{guiltyClass}}', $guiltyClass, $this->message);
		$this->guiltyClass = $guiltyClass ;
	}

	/**
	 * @param string $guiltyFunction
	 */
	public function setGuiltyFunction ( $guiltyFunction ) {
		$this->message = str_replace('{{guiltyFunction}}', $guiltyFunction, $this->message);
		$this->guiltyFunction = $guiltyFunction ;
	}

	/**
	 * @param string $origMsg
	 */
	public function setOrigMsg ( $origMsg ) {
		$this->origMsg = $origMsg ;
	}

	/**
	 * Set the line file for the exception
	 *
	 * @param string $filePath
	 */
	public function setGuiltyFile($filePath)
	{
		$this->message = str_replace('{{guiltyFile}}', $filePath, $this->message);
		$this->guiltyFile = (string)$filePath;
	}

	/**
	 * Set the line guilty for the exception
	 *
	 * @param int $lineNumber
	 */
	public function setGuiltyLine($lineNumber)
	{
		$this->message = str_replace('{{guiltyLine}}', $lineNumber, $this->message);
		$this->guiltyLine = (int)$lineNumber;
	}

	public function getSourceLines()
	{
		return $this->source;
	}

	public function setSourceLines($source)
	{
		$this->source = $source;
	}

	public function setCode($code)
	{
		$this->code = $code;
	}

	/**
	 * Sets the guilty step.
	 * For each value (among file, line, function and class) in the array the respective
	 * setGuilty* function will be called.
	 *
	 * @param array $step
	 */
	public function setGuiltyStep(array $step = null)
	{
		if($step !== null)
			PhpType::ensureArgumentType('step', $step, PhpType::ArrayType);

		if(isset($step['file']))
			$this->setGuiltyFile($step['file']);

		if(isset($step['line']))
			$this->setGuiltyLine($step['line']);

		if(isset($step['function']))
			$this->setGuiltyFunction($step['function']);

		if(isset($step['class']))
			$this->setGuiltyClass($step['class']);
	}

	public static function fromException(Exception $e)
	{
		$class = get_class();
		$ex = new $class($e->getMessage());

		$ex->setGuiltyFile($e->getFile());
		$ex->setGuiltyLine($e->getLine());
		$ex->setCode($e->getCode());

		return $ex;
	}
}