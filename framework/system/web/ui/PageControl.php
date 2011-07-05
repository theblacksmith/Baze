<?php
/**
 * Arquivo da classe BazeHtmlComponent
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

require_once(dirname(__FILE__).'\PageComponent.php');

/** 
 * Classe BazeHtmlComponent 
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
class PageControl extends PageComponent {

	/**
	 * @var Set
	 * @access protected
	 */
	protected $cssClass;

	/**
	 * @var InlineStyle 
	 * @access protected
	 */
	protected $style;

	/**
	 * @var string 
	 * @access protected
	 */
	protected $title;

	/**
	 * @var Collection 
	 * @access protected
	 */
	protected $children;

	/**
 	 * @access public
	 * @param Component $component 
	 * @return boolean
	 */
	public function addChild(Component $component) {
		return $this->children->add($component) > -1;
	}

	/**
	 * Remove o filho passado como parâmetro deste componente.
	 * O filho removido não é removido da página.
	 * 
 	 * @access public
	 * @param Component $component 
	 * @return boolean 
	 */
	public function removeChild(Component $component) {
		return $this->children->remove($component);
	}

	/**
	 * Remove todos os filhos deste componente. 
	 * Os filhos removidos não são removidos da página.
	 * 
 	 * @access public
 	 * @return Collection A coleção de filhos removidos
	 */
	public function removeChildren() {
		$childColl = $this->children;
		$this->children = new Collection();
		
		return $childColl;
	}

	/**
	 * Adiciona uma classe css ao componente. As classes css serão impressas
	 * na ordem em que foram adicionadas ao componente.
	 * 
 	 * @access public
 	 * @param $className Nome da classe a ser adicionada
	 */
	public function addCssClass($className) {
		$this->cssClass->add($className);
	}

	/**
	 * Remove uma classe css do componente.
	 * 
 	 * @access public
 	 * @param $className Nome da classe a ser removida
	 */
	public function removeCssClass($className) {
		$this->cssClass->add($className);
	}
}