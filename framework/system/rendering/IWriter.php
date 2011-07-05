<?php
/**
 * Arquivo da classe IWriter
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
 * Classe IWriter
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
interface IWriter {
	/**
 	 * @access public
	 * @return mixed
	 */
	public function flush();

	/**
 	 * @access public
	 * @param string $text
	 */
	public function write($text);

	/**
 	 * @access public
	 * @return boolean
	 */
	public function getBufferOutput();

	/**
	 * A buferização de saída não pode ser ativada se algo já foi escrito. Se algo já foi escrito, a chamada a essa função irá disparar uma exceção de operação inválida. O contrário (desativar a buferização depois que algo já foi "escrito") é válido. Nesse caso, toda a saída no buffer será liberada no momento em que a buferização é desativada.
	 *
 	 * @access public
	 * @param boolean $b
	 */
	public function setBufferOutput($b);
}