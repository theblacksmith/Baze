<?php
/**
 * Arquivo da classe HttpResponseWriter
 *
 * Esse arquivo ainda não foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.io
 */

import('system.io.IOutputWriter');

/**
 * Classe HttpResponseWriter
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.system.io
 */
class HttpResponseWriter implements IOutputWriter
{
	/**
	 * Whether output should be buffered or not
	 *
	 * @var boolean
	 */
	private $bufferOutput = true;

	/**
	 * The buffer
	 *
	 * @var string
	 */
	private $buffer = '';

	/**
	 * The http response
	 *
	 * @var HttpResponse
	 */
	private $response;

	public function __construct(HttpResponse $resp)
	{
		$this->response = $resp;
	}

	/**
 	 * Flushes the buffer.
	 */
	public function flush() {
		echo $this->buffer;
	}

	/**
 	 * @access public
	 * @param string $text
	 */
	public function write($text) {
		$this->response->write($text);
	}

	/**
 	 * @access public
	 * @return boolean
	 */
	public function getBufferOutput() {
		return $this->bufferOutput;
	}

	/**
	 * A buferização de saída não pode ser ativada se algo já foi escrito. Se algo já foi escrito, a chamada a essa função irá disparar uma exceção de operação inválida. O contrário (desativar a buferização depois que algo já foi "escrito") é válido. Nesse caso, toda a saída no buffer será liberada no momento em que a buferização é desativada.
	 *
 	 * @access public
	 * @param boolean $b
	 */
	public function setBufferOutput($b) {
		$this->bufferOutput = $b;
	}
}