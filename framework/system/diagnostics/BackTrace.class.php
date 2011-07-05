<?php
/**
 * Arquivo BackTrace.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

/**
 * Classe BackTrace
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class BackTrace
{
	private $btrace;

	private $truncateArgs = false;

	private $maxArgLength;

	/**
	 * The constructor
	 *
	 * If $btrace is null, the function debug_backtrace() will be called to get the current backtrace.
	 * If $argLength is null, the arguments will not be trucated.
	 *
	 *
	 * @param array $btrace The backtrace to be manipulated
	 * @param int $argLength The maximal length of the arguments values
	 */
	public function __construct($btrace=null, $argLength=null)
	{
		if(!$btrace)
		{
			$btrace = debug_backtrace();
			array_shift($btrace);
		}
		else
			$this->btrace = $btrace;

		if($argLength !== null)
		{
			$this->truncateArgs = true;
			$this->argLength = $argLength;
		}
	}

	public function getStep($i)
	{
		self::step($i, $this->btrace, $this->maxArgLength);
	}

	public function getStepString($i)
	{
		self::stepString($i, $this->btrace, $this->maxArgLength);
	}

	public static function step($i, $btrace = null, $argLength = null)
	{
		if($btrace !== null)
			PhpType::ensureArgumentType('btrace', $btrace, PhpType::ArrayType);

		if($argLength !== null)
			PhpType::ensureArgumentType('argLenth', $argLength, PhpType::Integer);

		if(!$btrace)
		{
			$btrace = debug_backtrace();
			array_shift($btrace);
		}

		if(count($btrace) == 0)
			return null;

		if($i < 0)
			$i = count($btrace) + $i;

		if(isset($btrace[$i]))
			return $btrace[$i];

		return null;
	}

	public static function stepString($i, $btrace = null, $argLength = null)
	{
		$step = self::step($i, $btrace, $argLength);

		$str = 'function ' . $step['function'] . '(';

		if(is_int($argLength))
			foreach ($step['args'] as $arg)
				$step['args'] = substr($arg, 0, $argLength);

		$str .= implode(' , ',$step['args']) . ') in file ' . $step['file'] . ' at line ' . $step['line'];

		return $str;
	}

	public static function stepLocation($i, $btrace = null)
	{
		if($this && !$btrace)
			$btrace = $this->btrace;

		$i = (($i+count($btrace))%count($btrace));

		$str .= 'file ' . $btrace['file'] . ' at line ' . $btrace['line'];

		return $str;
	}
}