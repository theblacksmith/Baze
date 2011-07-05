<?php

interface IAppModule extends IModule
{
	/**
	 * @param App $app the application to which this module belongs
	 */
	public function __construct(App $app);

	/**
	 * @return App the currently running application
	 */
	public function getApp();

	/**
	 * @return HttpRequest the user request
	 */
	public function getRequest();

	/**
	 * @return HttpRequest the user response
	 */
	public function getResponse();

	/**
	 * @return HttpService the currently running service
	 */
	public function getService();
}

/**
 * This abstract class defines most of the methods required by
 * IAppModule and thou can be used to implement new application modules.
 */
abstract class AppModule extends Component implements IAppModule
{
	/**
	 * @var App
	 * @desc The application object
	 */
	protected $app;

	/**
	 * @var string
	 * @desc the id of the module
	 */
	protected $id;

	/**
	 * The module constructor sets the owner application
	 *
	 * @param App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}

	/**
	 * Returns the application to which this module belongs
	 *
	 * @return unknown
	 */
	public function getApp()
	{
		return $this->app;
	}

	/**
	 * Returns the module id
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Sets the module id
	 *
	 * @param string $id The module id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Returns the user request
	 *
	 * @return HttpRequest
	 */
	public function getRequest()
	{
		return $this->app->getRequest();
	}

	/**
	 * Returns the user response
	 *
	 * @return HttpResponse
	 */
	public function getResponse()
	{
		return $this->app->getResponse();
	}

	/**
	 * Returns the running service
	 *
	 * @return HttpResponse
	 */
	public function getService()
	{
		return $this->app->getService();
	}
}