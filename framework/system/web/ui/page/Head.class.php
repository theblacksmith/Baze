<?php
/**
 * Arquivo Head.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
/**
 * @todo: consertar essa estrutura de armazenamento de scripts e styles
 *  do jeito q tá naum dá pra acessá-los
 */

/**
 * Classe Head
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
class Head extends PageComponent
{
	/**
	 * @var Collection
	 */
	private $childScripts;

	/**
	 * @var Collection
	 */
	private $childCss;

	/**
	 * Constructor
	 *
	 * @param string $title The page title
	 */
	function __construct($page=null)
	{
		parent::__construct();
		$this->childCss = new Collection();
		$this->childScripts = new Collection();

		$this->page = $page;
	}

	function addChild(Component $child, $position = null)
	{
		if(is_scalar($child))
		{
			if(strpos($child,'<link') !== false || strpos($child,'<style') !== false) {
				$this->addCSS($child, $position);
			}
			else if(strpos($child,'<script') !== false) {
				$this->addScript($child, $position);
			}
		}
		else if($child instanceof HTMLTag)
		{
			if($child->get("tagName") == "script") {
				$this->addScript($child->getXHTML(), $position);
			}
			else if($child->get("tagName") == "link" || $child->get("tagName") == "style") {
				$this->addCSS($child->getXHTML(), $position);
			}
		}
		else
			parent::addChild($child);
	}

	public function addScript($script, $position = null)
	{
		// verifica se foi passado um caminho ou uma tag com código dentro
		if(strpos($script,'<script') === false)
			$script = '	<script type="text/javascript" src="' . $script . '"></script>'."\n";

		// verifica se o script já foi inserido
		if ($this->childScripts->contains($script))
			return false;

		$this->childScripts->add($script, $position);

		return true;
	}

	public function addCSS($css, $position = null)
	{
		// verifica se foi passado um caminho
		if(strpos($css,'<link') === false && strpos($css,'<style') === false )
			$css = '	<link rel="stylesheet" type="text/css" href="' . $css . '"/>'."\n";

		// verifica se a css já foi inserida
		if ($this->childScripts->contains($css))
			return false;

		$this->childCss->add($css, $position);

		return true;
	}

	public function getObjectName()
	{
		return 'head';
	}
	
	/**
	 * Overrides default renderChildren to render the scripts in the correct order
 	 * @access public
	 * @param IRender $render
	 */
	public function renderChildren(IRender $render, IWriter $writer) {
		if($this->children instanceof Collection) {
			foreach ($this->children as $child) {
				$render->render($child, $writer);
			}
		}
		
		if($this->childCss instanceof Collection) {
			foreach ($this->childCss as $child) {
				$writer->write($child);
			}
		}
		
		if($this->childScripts instanceof Collection) {
			foreach ($this->childScripts as $child) {
				$writer->write($child);
			}
		}
	}
}