<?php

require_once 'IPageParser.interface.php';
require_once 'system/Msg.class.php';
require_once 'system/exceptions/BazeException.class.php';
require_once 'system/exceptions/io/IOException.class.php';
require_once 'system/exceptions/io/FileNotFoundException.class.php';

function debug($s, $tag='')
{
	//echo ($tag!=''?"<$tag>":''), str_replace('<', '&lt;', $s), '<br/>', ($tag!=''?"</$tag>":'');
}

class StringParser //implements IPageParser
{

	/**
	 * @var string
	 * @desc The source of the page to parse
	 */
	private $page;

	/**
	 * @var int
	 * @desc The internal pointer for reading $page
	 */
	private $curPos = 0;

	/**
	 * @see IPageParser::parseFile()
	 *
	 * @param string $file
	 * @return string
	 */
	public function parseFile($file)
	{
		if(is_file($file))
		{
			if(is_readable($file))
				$this->parseString(file_get_contents($file));
			else
				throw new FileNotReadableException($file);
		}
		else
			throw new FileNotFoundException($file);
	}

	/**
	 * @see IPageParser::parseString()
	 *
	 * @param string $source
	 * @return string
	 */
	public function parseString($content)
	{
		$content = trim($content);

		if($content == '')
			return array();

		$arr = $this->parseChildren($content, array());

		//print_r($arr);

		return $arr;
	}

	private function parseChildren($source, array $comp)
	{
		static $curPos = 0;
//debug("parsing from ".substr($source, $curPos, 15));
		$closePos = strpos($source, '</php:', $curPos);

		if($closePos === false)
			$closePos = strlen($source);

		// find next comp
		while (($compPos = strpos($source, '<php:', $curPos)) !== false)
		{
			// are we done here?
			if($compPos > $closePos)
			{
				$html = trim(substr($source, $curPos, $closePos - $curPos));
				// updating the position to the end of this component
				$curPos = strpos($source, '>', $closePos);
				if($html != '') {
					//$comp->addChild(new HtmlFragment($html));
					$comp[] = array('html' => $html);
					debug("<< html snippet >>");
				}

				return $comp;
			}

			// do we have html code before the first component?
			if($compPos > $curPos)
			{
				$html = trim(substr($source, $curPos, $compPos));
				if($html != '') {
					//$comp->addChild(new HtmlFragment($html));
					$comp[] = array('html' => $html);
					debug("<< html snippet >>");
				}
			}

			$tag = $this->getTagLine($source, $compPos);
			debug($tag,'b');

			$child = $this->parseComponent($tag);

			// adding the child
			//$comp->addChild($child);
			$comp[] = $child;

			// updating curPos to the end of the tag
			$curPos = $compPos + strlen($tag);

			// is it a self-closed tag?
			if(substr($tag,-2) != '/>')
			{
				//echo '<blockquote>';
				$this->parseChildren($source, $child);
				//echo '</blockquote>';
				debug('</php>', 'b');
				////echo '<br/>';

				// curPos is now at the end of the component

				// we've found a new child with a closing tag (</php:)
				// so, our $closePos is not really this comp close tag pos
				// it must be after its child nodes, so let's update it
				$closePos = strpos($source, '</php:', $curPos);
				if($closePos === false)
					$closePos = strlen($source);
			}
		}

		if($closePos > $curPos)
		{
			$html = trim(substr($source, $curPos, $closePos - $curPos));
			// updating the position to the end of this component
			$curPos = strpos($source, '>', $closePos);
			if($html != '') {
				//$comp->addChild(new HtmlFragment($html));
				$comp[] = array('html' => $html);
				debug("<< html snippet >>");
			}
		}

		return $comp;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $content
	 * @param int $startPos
	 * @return string
	 * @throws InvalidArgumentValueException
	 * @throws ParserException
	 */
	private function getTagLine($content, $startPos)
	{
		$quote = '';
		$i = $startPos;
		$len = strlen($content);

		if($startPos > strlen($content))
			throw new InvalidArgumentValueException(Msg::IndexOutOfBounds);

		while($i < $len) {
			$char = $content[$i];

			if($char == '"' || $char == "'")
				$quote = ($quote == $char ? '' : $char);
			else
			{
				if($quote == '' && $char == '>')
					return substr($content, $startPos, ++$i - $startPos);
			}

			$i++;
		}

		throw new ParserException(Msg::MalformedTag);
	}

	public function parseComponent($tag)
	{
		if(substr($tag,0,5) != '<php:' || substr($tag, -1) != '>')
			throw new ParserException(Msg::MalformedTag, array('tag' => $tag));

		$cName = substr($tag, 5, strpos($tag, ' ')-5);

		return array($cName => $tag);
	}
}