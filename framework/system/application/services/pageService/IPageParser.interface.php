<?php

/**
 *
 */
interface IPageParser
{
	/**
	 * Parses a file and sets the Head and Body components in $page
	 *
	 * @param string $file
	 * @return string
	 */
	public function parsePageFile($file, Page $page);

	/**
	 * Parses a string and sets the Head and Body components in $page
	 *
	 * @param string $file
	 * @return string
	 */
	public function parsePageString($source, Page $page);

	/**
	 * Parses a string and adds the components in it to $comp, replacing existing ones if $replace is true.
	 *
	 * @param string $source
	 */
	public function parseComponents($source, Page $comp, $replace = false);
}

?>