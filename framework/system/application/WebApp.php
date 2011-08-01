<?php
/**
 * Arquivo da classe WebApplication
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
 * Classe WebApplication
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
class WebApp extends App {

	/**
	 * @property Client
	 */
	public $user;

	/**
	 * @var string
	 */
	private $defaultEncode = 'UTF-8';

	/**
	 * @var Event
	 * @desc Fired after App.processRequest()
	 */
	protected $_onProcessRequest;

	/**
	 * Fired after ::processResponse()
	 * @var Event
	 */
	protected $_onProcessResponse;

	/**
	 * @var HttpRequest
	 * @desc The request object
	 */
	protected $_request;

	/**
	 * @var HttpResponse
	 * @desc The response object
	 */
	protected $_response;
	
	/**
	 * @var WebApp
	 */
	protected static $instance;
	
	/**
	 * @var PageService
	 */
	protected $service;
	
	protected function __construct()
	{
		
	}
	
	/**
	 * implements the Singleton pattern.
	 *
	 * @static
	 * @return WebApp A reference to the unique WebApp object
	 */
	public static function getInstance()
	{
		if(isset(self::$instance))
			return self::$instance;

		self::$instance = new WebApp();

		return self::$instance;
	}

	/**
 	 * @access public
	 * @param mixed $file
	 */
	public function addUploadedFile($file) {
		throw new NotImplementedException(__method__);
	}
	
	public function init(AppConfig $cfg)
	{
		$this->_config = $cfg;
		
		try {
			$nss = $cfg->Namespaces->namespace->toArray();
			if(is_array($nss)) {
				foreach($nss as $ns) {
					set_include_path(get_include_path().PATH_SEPARATOR.$ns['folder']);
					BazeClassLoader::addNamespace($ns['name'], $ns['folder']);
				}
			}
		}
		catch (InvalidArgumentValueException $ex) {
			if($ex->getArgumentName() == 'folder')
			{
				$cfgEx = new ConfigException(Msg::InvalidNamespacePath, array(
					'app' => $cfg->Name,
					'ns' => $ns->name,
					'path' => $ns->folder));

				$cfgEx->setGuiltyFile(NB_ROOT.'/conf/baseconf_browser.xml');

				throw $cfgEx;
			}
		}

		$this->loadState();

		if(isset($this->_onInit))
			$this->_onInit->raise($this, array());
	}

	/**
	 * Runs the application to handle the resquest
	 *
	 * @param HttpRequest $req
	 */
	public function run(HttpRequest $req, HttpResponse $resp)
	{
		$this->_response = $resp;
		$this->_request = $req;

		$this->processRequest();

		if(isset($this->_onProcessRequest))
			$this->_onProcessRequest->raise($this);

		$this->service->run($req, $resp);

		$this->processResponse();

		if(isset($this->_onProcessResponse))
			$this->_onProcessResponse->raise($this);
	}

	/**
 	 * Loads the application state from cache
	 */
	protected function loadState()
	{

	}

	/**
	 * This function finds the service that will respond for this request
	 * and initializes the service.
	 */
	private function processRequest()
	{
		if(is_array($this->_config->Services) || $this->_config->Services instanceof Zend_Config)
		{
			foreach($this->_config->Services as $srv)
			{
				if (preg_match($srv->urlPattern, $this->request->Url) == 1) {
					import($srv->class);
	
					$cls = split('.', $srv->class);
					$cls = array_pop($cls);
	
					$this->service = new $cls($this);
					$this->service->init($srv);
					break;
				}
	
			}
		}

		if($this->service == null) {
			import('system.application.services.PageService');
			$this->service = new PageService($this);
		}
	}

	/**
	 * If output buffer is ON, this method will flush the output.
	 * Otherwise it will do nothing
	 */
	private function processResponse() {
		$this->_response->flush();
	}

	/**
	 * Returns the user request
	 *
	 * @return HttpRequest
	 */
	public static function getRequest()
	{
		return self::$instance->_request;
	}

	/**
	 * Returns the user response
	 *
	 * @return HttpResponse
	 */
	public static function getResponse()
	{
		return self::$instance->_response;
	}
	
	public static function getConfig()
	{
		return self::$instance->_config;
	}
}