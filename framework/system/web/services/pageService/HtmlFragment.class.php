<?php

import('system.web.ui.PageComponent');
import('system.rendering.IRenderable');

class HtmlFragment extends PageComponent implements IRenderable
{
	/**
	 * @var string
	 * @desc The source of this fragment
	 */
	public $source;

	/**
	 * A custom render
	 *
	 * @var IRender
	 */
	public $customRender;
	
	/**
	 * This component doesn't need to have it's changes tracked
	 * @var boolean
	 */
	public $trackViewState = false;

	public function __construct($source)
	{
		parent::__construct();
		$this->source = $source;
		$this->customRender = array('HtmlFragment', 'render');
	}

	public function __toString()
	{
		return $this->source;
	}

	public static function render(HtmlFragment $frag, IRender $render, IWriter $writer)
	{
		$writer->write($frag->source);
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
	 * @return CustomRender
	 */
	public function getCustomRender() {
		return $this->customRender;
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
	public function hasCustomRender() {
		return true;
	}


	/**
 	 * @access public
	 * @param IRender $render
	 */
	public function renderChildren(IRender $render, IWriter $writer) {}
}