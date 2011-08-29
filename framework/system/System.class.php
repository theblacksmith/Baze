<?php

/**
 * Arquivo da classe System
 *
 * This file was not yet documented
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze
 */
/**
 * Classe System
 *
 * Usage example:
 * 	$s = System::getInstance();
 * 	$s->init('aConfigFile.xml');
 * 	$s->run();
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @package Baze
 */
class System
{

	/**
	 * The sole System instance
	 * @var System
	 * @access private
	 * @static
	 */
	private static $instance;

	/**
	 * The configuration object
	 * @var SystemConfig
	 */
	private static $config;

	/**
	 * The apps section of config file
	 * @var AppConfig
	 */
	private $appsConfig;

	/**
	 * The applications in xml indexed by host => port => siteroot
	 * @var array
	 */
	private $appsMap;

	/**
	 * The application to run
	 * @var App
	 */
	private $app;

	/**
	 * @var Zend_Cache_Backend_File
	 */
	private $cacheBackend;

	/**
	 * @var SystemEvent
	 */
	private $_onProcessRequest;

	/**
	 * @var SystemEvent
	 */
	private $_onWakeUp;

	/**
	 * @var SystemEvent
	 */
	private $_onBeforeRun;

	/**
	 * @var SystemEvent
	 */
	private $_onBeginRequest;

	/**
	 * @var SystemEvent
	 */
	private $_onLoadApplication;

	/**
	 * @var SystemEvent
	 */
	private $_onApplicationEnd;

	/**
	 * @var SystemEvent
	 */
	private $_onBeforeShutdown;

	/**
	 * @var SystemEvent
	 */
	private $_onShutdown;

	/**
	 * @var HttpRequest
	 */
	private $_request;

	/**
	 * @var HttpResponse
	 */
	private $_response;

	/*---- Fake Members (here for auto-complete) -----*/
	/**
	 * @var SystemConfig
	 */
	public static $Config;

	/*---- Members not used yet -----*/
	/**
	 * @var MyLog
	 * @access private
	 */
	private $logger;

	/**
	 * Constructor
	 *
	 * @access private
	 */
	private function __construct ()
	{
		define('NB_ROOT', dirname(dirname(__FILE__)));
		set_include_path(get_include_path() . PATH_SEPARATOR . NB_ROOT);
		
		define("XML_PART_OPEN_TAG", 1);
		define("XML_PART_ATTRIBUTES", 2);
		define("XML_PART_TAG_CONTENT", 3);
		define("XML_PART_CLOSE_TAG", 4);
		define("XML_PART_ENTIRE_ELEMENT", 5);

		// TODO: check mac os new line char and _SERVER string
		if (stripos($_SERVER['HTTP_USER_AGENT'], 'windows') !== false)
			define('NL', "\r\n");
		else
			define('NL', "\n");

		// TODO: remove after converting requires to imports
		set_include_path(get_include_path() . PATH_SEPARATOR . NB_ROOT . '/external/');

		// TODO: load plugins from plugin folder
	}

	/**
	 * Initializes the system. Steps described in long description.
	 *
	 * The steps executed by this function were move here from the constructor to allow onWakeUp hooking.
	 * If no path is provided for $configFile parameter, the file used will be NEOBASE_ROOT/conf/base-conf.xml
	 * This function executes the following steps:
	 * 	1. call wakeUp
	 * 	2. call loadConf
	 * 	3. fire onWakeUp
	 *
	 * @callgraph
	 * @param string $configPath Path to an alternate config file
	 */
	public function init ($configFile = null)
	{
		//require_once NB_ROOT . '/external/firelogger.php';
		require_once NB_ROOT . '/external/FirePHPCore/fb.php';
		
		FB::log('System::init');
		
		$this->wakeUp();

		if (isset($this->_onWakeUp))
			$this->_onWakeUp->raise();

		$this->loadConf($configFile);
	}

	/**
	 * 1. Imports the essential classes
	 * 2. Loads the cache (clean old files)
	 *
	 * @access private
	 */
	private function wakeUp ()
	{
		// @todo find better place for this require as it should be internationalized
		require_once NB_ROOT . '/system/exceptions/SystemException.class.php';
		require_once NB_ROOT . '/system/exceptions/NotImplementedException.class.php';
		require_once NB_ROOT . '/system/Msg.class.php';
		require_once NB_ROOT . '/system/lang/Enumeration.class.php';
		require_once NB_ROOT . '/system/lang/PhpType.class.php';
		require_once NB_ROOT . '/system/lang/BazeClassLoader.class.php';

		BazeClassLoader::addNamespace('system', NB_ROOT . '/system');
		BazeClassLoader::addNamespace('external', NB_ROOT . '/external');

		import('system.lang.*');
		//import('system.caching.*');
		import('system.diagnostics.ErrorHandler');
		import('system.Component');
		import('system.Module');

		// @todo Include only in debug mode
		import('system.diagnostics.BackTrace');

		ErrorHandler::init();
	}

	/**
	 * Loads the configuration xml file into an object and puts it on self::$config
	 *
	 * @param string $configFile The configuration fil 	e
	 */
	private function loadConf ($configFile = null)
	{
		import('external.Zend.Cache');
		import('external.Zend.Cache.Backend.File');
		import('external.Zend.Cache.Frontend.File');
		import('external.Zend.Config.Xml');
		import('system.application.AppConfig');
		import('system.SystemConfig');

		$this->cacheBackend = new Zend_Cache_Backend_File(array('cache_dir' => NB_ROOT . '/temp/cache/'));

		if ($configFile === null)
			$configFile = NB_ROOT . '/conf/baseconf_browser.xml';

		$cache = new Zend_Cache_Frontend_File(array(
				'master_file' => $configFile,
				'ignore_user_abort' => true,
				'automatic_serialization' => true));
		$cache->setBackend($this->cacheBackend);
		self::$config = $cache->load('system_conf');

		if (! self::$config) {
			// @think: validate xml using XSD ?
			self::$config = new SystemConfig($configFile, 'system');
			$cache->save(self::$config, 'system_conf');
		}

		// @todo replace this by magic function
		self::$Config = self::$config;

		// Loading Apps config
		$this->appsConfig = $cache->load('apps_conf');
		if (! $this->appsConfig) {
			$confs = new Zend_Config_Xml($configFile, 'apps');
			$this->appsConfig = array();
			
			foreach ($confs->app as $appConf) {
				if($appConf instanceof Zend_Config)
					$this->appsConfig[$appConf->url] = $appConf;
				else
				{
					$this->appsConfig[$confs->app->url] = $confs->app;
					break;
				}
			}
			$cache->save($this->appsConfig, 'apps_conf');
		}
	}

	/**
	 * Runs the system
	 * @callergraph
	 * @callgraph
	 */
	public function run ()
	{
		if (isset($this->_onBeforeRun))
			$this->_onBeforeRun->raise();

		import('system.net.HttpRequest');
		import('system.net.HttpResponse');

		$this->_request = HttpRequest::factory();
		$this->_response = new HttpResponse();

		// TODO: change the name of this events
		if (isset($this->_onBeginRequest))
			$this->_onBeginRequest->raise();

		$this->loadApplication();

		if (isset($this->_onLoadApplication))
			$this->_onLoadApplication->raise();

		$this->app->run($this->_request, $this->_response);

		if (isset($this->_onApplicationEnd))
			$this->_onApplicationEnd->raise();
	}

	/**
	 * Identifies, instantiates and initializes the application to handle the request.
	 */
	private function loadApplication ()
	{
		import('system.application.App');
		import('system.application.WebApp');

		$appConfig = null;

		foreach ($this->appsConfig as $appUrl => $app) {
			$k = array_keys($this->appsConfig);
			if (strpos($this->_request->Url->getUri(), $appUrl) !== false) {
				$appConfig = $app;
				break;
			}
		}

		if ($appConfig == null) {
			import('system.exceptions.ApplicationNotFoundException');
			throw new ApplicationNotFoundException(Msg::System_application_not_found);
		}

		self::$config = $appConfig;
		define('NB_LIB_URL', $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] ? $_SERVER['SERVER_PORT'] : '') . self::$config->siteRoot);
		
		$this->app = WebApp::getInstance();

		// converting the Zend_Config_Xml to AppConfig
		$cfg = new AppConfig($appConfig);
		
		$this->app->init($cfg);
	}

	/**
	 * @access private
	 */
	private function shutdown ()
	{
		if (isset($this->_onBeforeShutdown))
			$this->_onBeforeShutdown->raise();

		// nothing to do here. YET.


		if (isset($this->_onShutdown))
			$this->_onShutdown->raise();
	}

	/**
	 * Function getInstance
	 * This function implements the Singleton pattern.
	 *
	 * @return System A reference to the unique System class object
	 *
	 * @access public
	 * @static
	 */
	public static function getInstance()
	{
		if (self::$instance == null)
			self::$instance = new System();

		return self::$instance;
	}

	/**
	 * Returns the currently running application.
	 * This method causes the creation of the singleton instance
	 *
	 * @return App
	 */
	public static function getApp()
	{
		return self::getInstance()->app;
	}

	public static function getConfig()
	{
		return self::$config;
	}
	
	/**
	 * Returns default cache backend
	 * This method causes the creation of the singleton instance
	 * 
	 * @return Zend_Cache_Backend_File
	 */
	public static function getCacheBackend()
	{
		return self::getInstance()->cacheBackend;
	}
}