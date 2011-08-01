<?php
/**
 * Arquivo da classe IRenderer
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
 * Classe IRenderer
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
interface IRenderer {

	/**
 	 * @access public
	 * @param IRenderable $object
	 * @param IOutputWriter $writer
	 */
	public function render(IRenderable $object, IWriter $writer);
}