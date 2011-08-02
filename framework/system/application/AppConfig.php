<?php

class AppMode extends Enumeration
{
	public static $DEBUG;
	public static $DEVELOPMENT;
	public static $PRODUCTION;
}

class AppConfig extends BazeObject {

	//#removeBlock
	/// @cond user

	/**
	 * @var string
	 * @desc Application name
	*/
	//public $Name;

	/**
	 * @var string
	 * @desc Application folder on server
	*/
	//public $DocRoot;

	/**
	 * @var array
	 * @desc List of namespaces where the name is the index and the root dir is the value
	*/
	//public $Namespaces;

	/**
	 * @var Zend_Uri_Http
	 * @desc The url to Baze library folder
	*/
	//public $BazeLibUrl;

	/**
	 * @var array
	 * @desc List of classes to load on application start
	*/
	//public $AutoLoad;

	/**
	 * @var AppMode
	 * @desc The running mode. One of the values in AppMode enum.
	*/
	//public $Mode;

	/**
	 * @var string
	 * @desc The default charset for pages that do not specify it
	*/
	//public $DefaultCharset;

	/**
	 * @var string
	 * @desc The default content type for pages that do not specify it
	*/
	//public $DefaultContentType;

	/**
	 * @var Zend_Uri_Http
	 * @desc The base url for the application
	*/
	//public $Url;

	/**
	 * @var array
	 * @desc An array of ServiceConfig objects. The services configured for this application
	 */
	//public $Services;

	/// @endcond
	//@endRemoveBlock

	/**
	 * The object containing the confs
	 * @var Zend_Config
	 */
	private $zConf;

	public function __construct(Zend_Config $zConf)
	{
		$this->zConf = $zConf;
	}

	/**
	 * @return array
	 */
	public function getAutoLoad() {
		return isset($this->zConf->autoLoad) ? $this->zConf->autoLoad : null;
	}

	/**
	 * @return array
	 */
	public function getDocRoot() {
		return isset($this->zConf->docRoot) ? $this->zConf->docRoot : null;
	}

	/**
	 * @return string
	 */
	public function getDefaultCharset() {
		return isset($this->zConf->defaultCharset) ? $this->zConf->defaultCharset : null;
	}

	/**
	 * @return string
	 */
	public function getDefaultContentType() {
		return isset($this->zConf->defaultContentType) ? $this->zConf->defaultContentType : null;
	}

	/**
	 * @return string
	 */
	public function getMode() {
		return isset($this->zConf->mode) ? $this->zConf->mode : null;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return isset($this->zConf->name) ? $this->zConf->name : null;
	}

	/**
	 * @return array
	 */
	public function getNamespaces() {
		return isset($this->zConf->namespaces) ? $this->zConf->namespaces : null;
	}

	/**
	 * @return array
	 */
	public function getServices()
	{
		if (!isset($this->zConf->services))
			return array();
			
		return $this->zConf->services;
	}

	/**
	 * @return Zend_Uri_Http
	 */
	public function getUrl() {
		return isset($this->zConf->url) ? $this->zConf->url : null;
	}

	public function getBazeLibUrl()
	{
		return isset($this->zConf->bazeLibUrl) ? $this->zConf->bazeLibUrl : null;
	}

	public function toArray()
	{
		return $this->zConf->toArray();
	}
}