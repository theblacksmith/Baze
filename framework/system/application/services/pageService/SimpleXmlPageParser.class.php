<?php

require_once 'system/parsing/SimpleXmlParser.class.php';
require_once 'system/application/services/pageService/IPageParser.interface.php';
require_once 'system/application/services/pageService/HtmlFragment.class.php';
require_once 'system/web/ui/HtmlTag.class.php';

class SimpleXmlPageParser extends SimpleXmlParser implements IPageParser
{
	/**
	 * @var HtmlComponent
	 * @desc The last component being processed
	 */
	private $lastComp;

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

	/**
	 * @internal This property is used when a component parses a child element that doesn't generates a child component.
	 * This tell us to ignore anything until the call of the endElementHandler for the child element.
	 *
	 * @var bool
	 */
	private $ignoreUntilEndElement = false;
	
	/**
	 * @internal
	 * Wheter to replace components with conflicting ids or not
	 * @var bool
	 */
	private $replaceComponents = false;

	public function __construct(array $options = null)
	{
		$options[XML_OPTION_CASE_FOLDING] = false;

		parent::__construct($options);
	}

	/**
	 * @see IPageParser::parsePageFile()
	 *
	 * @param string $file
	 * @return string
	 */
	public function parsePageFile($file, Page $page, $replace = false)
	{
		$this->page = $page;

		if(is_file($file))
		{
			if(is_readable($file)) {
				$this->parsePageString(file_get_contents($file), $page, $replace);
			}
			else
				throw new FileNotReadableException($file);
		}
		else
			throw new FileNotFoundException($file);
	}

	/**
	 * @see IPageParser::parsePageString()
	 *
	 * @param string $source
	 * @return string
	 */
	public function parsePageString($source, Page $p, $replace = false)
	{
		$this->page = $p;
		$this->curComp = $p;
		$this->replaceComponents = $replace;
		$this->parse($source);
	}

	public function parseComponents($source, Page $p, $replace = false)
	{
		$this->page = $p;
		$this->curComp = $p;
		$this->replaceComponents = $replace;
		$this->parse($source);
	}

	protected function startElementHandler($parser, $name, $attribs)
	{
		$this->debug->msg(__FUNCTION__ . " $name");
		if($this->ignoreUntilEndElement)
			return;

		$name = strtolower($name);
		$nsName = split(':', $name);

		if($nsName[0] == 'php')
			$name = $nsName[1];

		if($nsName[0] == 'php' || $name == 'head' || $name == 'body')
		{
			if($this->curSnippet != '')
			{
				$frag = new HtmlFragment($this->curSnippet);
				$frag->setPage($this->page, $this->replaceComponents);
				$this->curComp->addChild($frag, false, $this->replaceComponents);
				$this->curSnippet = '';
			}

			if($this->curComp->getParsesOwnChildren())
			{
				$lastComp = $this->curComp;
				$this->curComp = $this->curComp->parseChild(join(':', $nsName), $attribs);

				if($this->curComp === null) {
					$this->curComp = $lastComp;
					$this->ignoreUntilEndElement = true;
				}
				else if(!($this->curComp instanceof PageComponent))
				{
					if(is_object($this->curComp))
						$ret = 'a ' . get_class($this->curComp);
					else
						$ret = 'the ' . gettype($this->curComp) . ' ' . $this->curComp;

					throw new ParserException(Msg::ParseChildInvalidReturn, array('component' => $name, 'return' => $ret), 1);
				}
			}
			else
			{
				$c = $this->createComponent($name, $attribs);
				$old = $this->page->getComponent($c->getId());
				
				if($this->replaceComponents && $old)
				{
					$old->container->addChild($c, false, true); 
				}
				else
				{
					$c->setPage($this->page, $this->replaceComponents);
					$this->curComp->addChild($c, false, $this->replaceComponents);
				}
				
				$this->curComp = $c;
			}
			
			// Done on set Page
			//if(isset($attribs['id']))
			//	$this->page->{$attribs['id']} = $c;
		}
		else if($name == 'html')
		{
			foreach ($attribs as $attName => $attValue) {
				$this->page->setAttribute($attName, $attValue);
			}
		}
		else
		{
			$str = "<$name";

			foreach ($attribs as $k => $v) {
				if(strpos($v, '"') === false)
					$str .= " $k=\"$v\"";
				else
					$str .= " $k='$v'";
        	}

        	if($this->isLeaf($name))
        		$str .= ' /';

        	$str .= '>';

        	$this->curSnippet .= $str;
		}
	}

	/**
	 * Create a component of type $class and set the attributes in $attributes
	 *
	 * @param string $class
	 * @param ArrayAccess $attributes
	 * @return PageComponent
	 */
	protected function createComponent($class, $attributes)
	{
		$c = new $class();

		$c->parse($attributes);

		return $c;
	}

	protected function endElementHandler($parser, $name)
	{
		$this->debug->msg(__FUNCTION__ . " $name");
		if($this->ignoreUntilEndElement) {
			$this->ignoreUntilEndElement = false;
			return;
		}

		$name = strtolower($name);
		$nsName = split(':', $name);

		if($nsName[0] == 'php' || $name == 'html' || $name == 'body' || $name == 'head')
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
        	if(!$this->isLeaf($name))
        		$this->curSnippet .= "</$name>";
		}
	}

	protected function defaultHandler($parser, $data)
	{
		$this->curSnippet .= $data;
	}

	/**
	 * Returns whether an element is a leaf or not
	 *
	 * @return boolean
	 */
	protected function isLeaf($name)
	{
		switch($name)
		{
			case 'br':
			case 'img':
			case 'input':
			case 'meta':
			case 'link':
			case 'hr':
			case 'param':
			case 'base':
			case 'area':
				return true;

			default :
				return false;
		}
	}
}

