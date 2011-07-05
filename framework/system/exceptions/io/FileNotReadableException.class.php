<?php

class FileNotReadableException extends IOException
{
	/**
	 * Path of the required file
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Constructor
	 *
	 * @param string $path Path of the required file
	 * @param string[optional] $message Custom message
	 * @param string $strings Strings to replace in custom message hooks ({0}, {1}, ...)
	 */
	public function __construct($path, $message=null, $strings = array())
	{
		if($message == null)
			$message = Msg::FileCanNotBeRead;

		$this->path = $path;

		parent::__construct($message, $strings);

	}

	/**
	 * Gets the path of the required file
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Defines the path of the required file
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}
}