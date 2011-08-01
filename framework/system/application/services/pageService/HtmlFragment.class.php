<?php

require_once 'system/web/ui/PageComponent.php';
require_once 'system/rendering/IRenderable.php';
require_once 'system/rendering/IRenderer.class.php';

class HtmlFragment extends PageComponent implements IRenderable, IRenderer
{
	/**
	 * @var string
	 * @desc The source of this fragment
	 */
	public $source;
	
	/**
	 * This component doesn't need to have it's changes tracked
	 * @var boolean
	 */
	public $trackViewState = false;

	public function __construct($source)
	{
		parent::__construct();
		$this->source = $source;
	}

	public function __toString()
	{
		return $this->source;
	}

	public function render(IRenderable $frag, IWriter $writer)
	{
		$writer->write($this->source);
	}

	/**
 	 * @access public
	 * @return array
	 */
	public function getAttributesToRender() {
		return array();
	}

	/**
 	 * @access public
	 * @return string
	 */
	public function getObjectName() {
		return 'php:HtmlFragment';
	}

	/**
 	 * @access public
	 * @return IRenderer
	 */
	public function getCustomRenderer() {
		return $this;
	}

	/**
 	 * @access public
	 */
	public function onPreRender()
	{
		$this->_onPreRender->raise($this);
	}

	/**
 	 * @access public
	 */
	public function onRender()
	{
		$this->_onRender->raise($this);
	}

	/**
 	 * @access public
	 * @return boolean
	 */
	public function hasCustomRenderer() {
		return true;
	}


	/**
 	 * @access public
	 * @param IRender $render
	 */
	public function renderChildren(IRenderer $render, IWriter $writer) {}
}