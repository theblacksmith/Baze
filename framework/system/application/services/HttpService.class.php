<?php

/**
 * The base class for all http services.
 *
 */
abstract class HttpService extends AppModule
{
	//#RemoveBlock

	/// @cond user

	/**
	 * @magic
	 * @var HttpServiceConfig Config
	 * @desc The service config
	 */
	public $Config;

	/**
	 * @magic
	 * @var Event
	 * @desc Fired after init(ServiceConfig $cfg = null)
	 */
	public $OnInit;

	/// @endcond

	//#endRemoveBlock

	/// @cond internal

	/**
	 * @var PageServiceConfig
	 * @desc The service configuration\n
	 * @magic \b Config
	 */
	protected $_config;

	/**
	 * @var Event
	 * @desc Fired after init(ServiceConfig $cfg = null)\n
	 * @magic \b OnInit
	 */
	protected $_onInit;

	/// @endcond

	/**
	 * Initializes the service
	 *
	 * @param ServiceConfig $cfg The service configuration
	 */
	final public function init(ServiceConfig $cfg = null)
	{
		$this->_config = $cfg;

		$this->_init();

		$this->_onInit->raise($this);
	}

	/**
	 * This function can be overloaded to provide aditional functionality to
	 * HttpService.init(ServiceConfig $cfg) method.
	 *
	 * @todo this overloadable function is inconsistent with the hot spot instantiation strategies in App
	 */
	protected function _init() {}

	/**
	 * Runs the service
	 *
	 * @param HttpRequest $req
	 * @param HttpResponse $resp
	 */
	abstract public function run(HttpRequest $req, HttpResponse $resp);

	/**
	 * Returns the service config
	 *
	 * @return ServiceConfig
	 */
	public function getConfig() {
		return $this->_config;
	}

	/**
	 * Sets a merges the service config to the passed object
	 *
	 * @param ServiceConfig $cfg
	 * @param boolean $merge
	 */
	public function setConfig(ServiceConfig $cfg, $merge = false)
	{
		if($merge)
			$this->_config->merge($cfg);
		else
			$this->_config = $cfg;
	}
}