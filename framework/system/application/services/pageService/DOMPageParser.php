<?php

import('system.application.services.pageService.IPageParser');
import('system.parsing.DOMParser');

class DOMPageParser extends DomParser implements IPageParser
{
	/**
	 * @var handler
	 * @desc
	 */
	private $parser;

	/**
	 * @var HtmlComponent
	 * @desc The current component being processed
	 */
	private $curComp;

	/**
	 * @var string
	 * @desc The current html snippet being parsed
	 */
	private $curSnippet;

	/**
	 * The page being parsed
	 *
	 * @var Page
	 */
	private $page = null;

	public function parseComponents($source)
	{
		throw new NotImplementedException();
	}

	public function parsePageFile($file, Page $page)
	{
		$this->page = $page;

		if(is_file($file))
		{
			if(is_readable($file)) {
				$this->parsePageString(file_get_contents($file), $page);
			}
			else
				throw new FileNotReadableException($file);
		}
		else
			throw new FileNotFoundException($file);
	}

	public function parsePageString($source, Page $page)
	{
		$this->page = $page;
		$this->curComp = $page;
		$this->parse($source);
	}

	protected function startElementHandler(DOMParser $parser, $name, DOMNodeList $attribs)
	{
		$this->debug->htmlMsg("Current Component: ".get_class($this->curComp));

		$nsName = split(':',strtolower($name));

		if($nsName[0] == 'php')
			$name = $nsName[1];

		if($nsName[0] == 'php' || $name == 'head' || $name == 'body')
		{
			if($this->curSnippet != '')
			{
				$this->curComp->addChild(new HtmlFragment($this->curSnippet));
				$this->curSnippet = '';
			}

			$c = $this->createComponent($name, $attribs);

			$this->curComp->addChild($c);
			$this->curComp = $c;
		}
		else
		{
			$str = "<$name";

			while (list($k, $v) = each($attribs))
			{
				if(strpos($v, '"') === false)
					$str .= " $k=\"$v\"";
				else
					$str .= " $k='$v'";
        	}

        	$str .= '>';

        	$this->curSnippet .= $str;
		}
	}

	protected function createComponent($class, DOMNamedNodeMap $attributes)
	{
		$c = new $class($this->page);

		foreach ($attributes as $name => $attNode)
		{
			$c->setAttribute($name, $attNode->nodeValue);
		}

		return $c;
	}

	protected function endElementHandler(DOMParser $parser, $name)
	{
		$nsName = split(':',$name);
		if($nsName[0] == 'php')
		{
			if($this->curSnippet != '')
			{
				$this->curComp->addChild(new HtmlFragment($this->curSnippet));
				$this->curSnippet = '';
			}

			$this->curComp = $this->curComp->getContainer();
		}
		else
		{
			$this->curSnippet .= "</$name>";
		}
	}

	protected function defaultHandler(DOMParser $parser, $data)
	{
		$this->curSnippet .= $data;
	}
}

?>