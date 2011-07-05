<?php
/**
 * Arquivo da classe BazeApplication
 *
 * Esse arquivo ainda não foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */

/**
 * Classe BazeApplication
 *
 * Essa classe ainda não foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */
abstract class BazeApplication {

	/**
	 * The only instance
	 * @var BazeApplication
	 */
	protected $instance;

	/**
	 * The configuration defined in baseconf file
	 *
	 * @var AppConfig
	 */
	protected $config;

	/**
	 * implements the Singleton pattern.
	 *
	 * @static
	 * @return BazeApplication A reference to the unique BazeApplication object
	 */
	public static function getInstance() {
		if(isset($this->instance))
			return $this->instance;

		$this->instance = new BazeApplication();

		return $this->instance;
	}

	/**
	 * Private constructor declaration to implement Singleton pattern
 	 * @access private
	 */
	private function __construct() {}

	public function init(AppConfig $cfg)
	{
		$this->config = $cfg;

		foreach($cfg->Namespaces as $name => $folder) {
			BazeClassLoader::addNamespace($name, $folder);
		}

		$this->loadState();
		$this->onLoadState();
	}

	/**
 	 * @access public
	 */
	public function loadState() {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access public
	 */
	public function onLoadState() {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access public
	 */
	public function authorizeClient() {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access public
	 * @param HttpRequest $req
	 */
	public function run(HttpRequest $req) {
		throw new NotImplementedException(__method__);
	}

	/**
	 * 1. Processa a requisição
2. Carrega a página
3. Executa a página

Dispara OnProcessRequest
	 *
 	 * @access private
	 */
	private function processRequest() {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access private
	 */
	private function onProcessRequest() {
		throw new NotImplementedException(__method__);
	}

	/**
	 * 1. Renderiza a saída (html ou xml)

Dispara OnBeforeRender

2. Responde pro browser

Dispara onProcessResponse

	 *
 	 * @access private
	 */
	private function processResponse() {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access private
	 */
	private function onProcessResponse() {
		throw new NotImplementedException(__method__);
	}

	/**
 	 * @access private
	 */
	private function onBeforeRender() {
		throw new NotImplementedException(__method__);
	}
}