<?php
/**
 * Arquivo da classe ViewStateRender
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
 * Classe ViewStateRenderer
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
class ViewStateRenderer implements IRenderer {
	/* (non-PHPdoc)
	 * @see IRender::render()
	 */
	public function render(IRenderable $page, IWriter $writer) {
		$writer->write($page->getServerViewState()->getGUIStateUpdate());
	}

}