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

require_once 'system/web/ui/PageComponent.php';
require_once 'system/collections/Set.class.php';

/**
 * Classe HtmlComponent
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
class HtmlComponent extends PageComponent {

	/**
	 * @var Set
	 * @desc The css class
	 */
	protected $cssClass;

	/**
	 * @var InlineStyle
	 * @desc The inline style
	 */
	protected $style;

	/**
	 * @var string
	 * @desc an advisory title
	 */
	protected $title;

	/**
	 * @var string
	 * @desc The tag name
	 */
	protected $tagName;

	public function __construct()
	{
		$this->cssClass = new Set(gettype(''));
		parent::__construct();
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