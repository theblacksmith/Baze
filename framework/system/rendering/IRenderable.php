<?php
/**
 * Arquivo da classe IRenderable
 *
 * Esse arquivo ainda não foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.rendering
 */

/**
 * Classe IRenderable
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.rendering
 */
interface IRenderable {

	/**
 	 * @access public
	 * @return array
	 */
	public function getAttributesToRender();

	/**
 	 * @access public
	 * @return string
	 */
	public function getObjectName();

	/**
 	 * @access public
	 * @return CustomRender
	 */
	public function getCustomRender();

	/**
 	 * @access public
	 * @return boolean
	 */
	public function hasCustomRender();

	/**
 	 * @access public
	 */
	public function onPreRender();

	/**
 	 * @access public
	 */
	public function onRender();

	/**
 	 * @access public
	 * @param IRender $render
	 */
	public function renderChildren(IRender $render, IWriter $writer);
}