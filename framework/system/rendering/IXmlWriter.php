<?php
/**
 * Arquivo da classe IXmlWriter
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
 * Classe IXmlWriter 
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
interface IXmlWriter {

	/**
	 * @param string $name 
	 * 
	 * @param string $name The tag name
	 * @param array $attributes
	 * 
	 * @return SimpleXmlTag
	 */
	public function writeTag($name, $attributes);

	/**
	 * @param string $text
	 * 
 	 * @access public
	 * @param mixed $text 
	 */
	public function writeText($text);

	/**
	 * 
@param string $name o nome da tag
@param Map Map $attributes
@param SimpleXmlTag $parent A tag onde a nova tag deve ser inserida
@return SimpleXmlTag A tag que acabou de ser inserida
	 * 
 	 * @access public
	 * @param mixed $name 
	 * @param mixed $attributes 
	 * @param mixed $parent_ 
	 */
	public function writeChildTag($name, $attributes, $parent_);

	/**
	 * @param string $text O texto a ser impresso dentro da seção CDATA
	 * 
 	 * @access public
	 * @param mixed $text 
	 */
	public function writeCdata($text);

	/**
	 * @return string
	 * 
 	 * @access public
	 */
	public function flush();
}