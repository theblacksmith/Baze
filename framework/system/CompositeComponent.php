<?php
/**
 * Arquivo da classe CompositeComponent
 * 
 * Esse arquivo ainda não foi documentado
 * 
 * @author Saulo Vallory 
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package Baze.web
 */

/** 
 * Classe CompositeComponent 
 * 
 * Essa classe ainda não foi documentada
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package Baze.web
 */
class CompositeComponent extends Component {

	/**
	 * @var Component 
	 * @access private
	 */
	private $parts;

	/**
 	 * @access protected
	 * @param Component $comp 
	 */
	protected function addPart(Component $comp) {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access public
	 * @return array 
	 */
	public function getParts() {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access protected
	 * @param Component $comp 
	 */
	protected function removePart(Component $comp) {
		throw new NotImplementedException(__method__);
	}
}