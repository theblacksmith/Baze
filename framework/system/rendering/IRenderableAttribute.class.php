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
interface IRenderableAttribute {

	/**
 	 * @access public
	 * @return array
	 */
	public function getAttributeName();

	/**
 	 * @access public
	 * @return CustomRender
	 */
	public function getCustomRenderer();

	/**
 	 * @access public
	 * @return boolean
	 */
	public function hasCustomRenderer();

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
	public function renderChildren(IRenderer $render, IWriter $writer);
}