<?php
/**
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */

import('system.application.AppModule');
import('system.configuration.ConfigException');

/**
 * This abstract class implements the basic functionalities of an Application
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze.application
 */
abstract class App extends Component
{

	// #removeBlock
	// This block of code (up to @endRemoveBlock) will be removed within build.
	// It's here just to allow code completion of magic properties.

	/// @cond user

	/**
	 * @var AppConfig
	 * @desc The application config defined in baseconf file
	 */
	//public $Config;

	/// @endcond

	// #endRemoveBlock

	/// @cond internal

	/**
	 * @var AppConfig
	 * @desc The application config defined in baseconf file
	 */
	protected $_config;

	/**
	 * @var Event
	 * @desc Fired after App.init(AppConfig $cfg)
	 */
	protected $_onInit;

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

	/// @endcond

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
	 * @var HttpService
	 * @desc The service currently running
	 */
	protected $service = null;

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

		if($this->service == null) {
			import('system.web.services.PageService');
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
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Returns the user response
	 *
	 * @return HttpResponse
	 */
	public function getResponse()
	{
		return $this->_response;
	}

	/**
	 * Returns the application configuration
	 *
	 * @return AppConfig
	 */
	public function getConfig()
	{
		return $this->_config;
	}
}