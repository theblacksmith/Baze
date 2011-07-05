<?php

import('external.Zend.Uri');

/**
 * This class encapsulates all the request data avaible in the superglobals _GET, _POST, _REQUEST, _FILES, _SERVER and _ENV
 *
 * You can add custom parameters to the request
 * Get and Post params can be got just by accessing the property with the parameter name.
 *
 * note: Get has precedence, if two parameters, one in _GET and the other in _POST, have the same name
 * (eg: action) doing $req->action will return the _GET param. To get the _POST one you can use {@link getPostParam()}
 *
 * @todo Allow setting parameters by $req->ParameterName = value
 * @package system.net
 */
class HttpRequest
{

	//@removeBlock
	///@cond user


	/**
	 * @var string
	 * @desc Contents of the Accept: header from the current request, if there is one.
	 */
	public $Accept;

	/**
	 * @var string
	 * @desc Contents of the Accept-Charset: header from the current request, if there is one. Example: 'iso-8859-1,*,utf-8'.
	 */
	public $AcceptCharset;

	/**
	 * @var string
	 * @desc Contents of the Accept-Encoding: header from the current request, if there is one. Example: 'gzip'.
	 */
	public $AcceptEncoding;

	/**
	 * @var array
	 * @desc A list of user preferred languages sorted by the most preferred.
	 */
	public $AcceptLanguages;

	/**
	 * @var string
	 * @desc This variable is set to the 'Authorization' header sent by the client, when running under Apache as module doing Digest HTTP authentication (which you should then use to make the appropriate validation).
	 */
	public $AuthDigest;

	/**
	 * @var string
	 * @desc This variable is set to the authentication type, when running under Apache as module doing HTTP authenticated.
	 */
	public $AuthType;

	/**
	 * @var string
	 * @desc The request content's type. Example: 'application/x-www-form-urlencoded'.
	 */
	public $ContentType;

	/**
	 * @var array
	 * @desc An associative array of variables passed via HTTP Cookies.
	 */
	public $Cookies;

	/**
	 * @var array
	 * @desc An associative array of items uploaded to the current script via the HTTP POST method.
	 */
	public $Files;

	/**
	 * @var string
	 * @desc What revision of the CGI specification the server is using; i.e. 'CGI/1.1'.
	 */
	public $GatewayInterface;

	/**
	 * @var array
	 * @desc An associative array of variables passed via the HTTP GET method.
	 */
	public $GetVars;

	/**
	 * @var array
	 * @desc The request headers
	 */
	public $Headers;

	/**
	 * @var string
	 * @desc Contents of the Host: header from the current request, if there is one.
	 */
	public $Host;

	/**
	 * @var string
	 * @desc Which request method was used to access the page; i.e. 'GET', 'HEAD', 'POST', 'PUT'.
	 */
	public $Method;

	/**
	 * @var string
	 * @desc he URI which was given in order to access this page; for instance, '/index.php'.
	 */
	public $Path;

	/**
	 * @var array
	 * @desc An associative array of variables passed via the HTTP POST method.
	 */
	public $PostVars;

	/**
	 * @var string
	 * @desc Name and revision of the information protocol via which the page was requested; i.e. 'HTTP/1.1';
	 */
	public $Protocol;

	/**
	 * @var string
	 * @desc The query string, if any, via which the page was accessed.
	 */
	public $QueryString;

	/**
	 * @var string
	 * @desc The address of the page (if any) which referred the user agent to the current page.  This is set by the user agent. Not all user agents will set this, and some provide  the ability to modify HTTP_REFERER as a feature. In short, it cannot really be trusted.
	 */
	public $Referrer;

	/**
	 * @var array
	 * @desc The IP address from which the user is viewing the current page.
	 */
	public $RemoteAddress;

	/**
	 * @var int
	 * @desc The port being used on the user's machine to communicate with the web server.
	 */
	public $RemotePort;

	/**
	 * @var string
	 * @desc TODO: describe this property
	 */
	public $RequestUri;

	/**
	 * @var string
	 * @desc TODO: describe this property
	 */
	//public $ScriptName;


	/**
	 * @var string
	 * @desc The name of the server host under which the current script is executing.  If the script is running on a virtual host, this will be the value defined  for that virtual host.
	 */
	public $ServerName;

	/**
	 * @var int
	 * @desc The port on the server machine being used by the web server for communication.  For default setups, this will be '80'; using SSL, for instance, will change  this to whatever your defined secure HTTP port is.
	 */
	public $ServerPort;

	/**
	 * @var string
	 * @desc The IP address of the server under which the current script is executing.
	 */
	public $ServerAddress;

	/**
	 * @var int
	 * @desc The timestamp of the start of the request. Available since PHP 5.1.0.
	 */
	public $Time;

	/**
	 * @var string
	 * @desc The identification sent by the browser
	 */
	public $UserAgent;

	/**
	 * @var string
	 * @desc This variable is set to the username provided by the user,  when running under Apache or IIS (ISAPI on PHP 5) as module doing HTTP authentication
	 */
	public $Username;

	/**
	 * @var string
	 * @desc This variable is set to the password provided by the user,  when running under Apache or IIS (ISAPI on PHP 5) as module doing HTTP authentication.
	 */
	public $Password;

	/**
	 * @var Zend_Uri_Http
	 * @desc The request url
	 */
	//public $Url;


	///@endcond
	//@endRemoveBlock


	///----------------------------- Baze specifc properties -------------------------------------


	/// @cond internal
	/**
	 * @var Zend_Uri_Http
	 * @desc An object representing the URL
	 */
	private $_url;

	/**
	 * @var string
	 * @desc Baze URL of request
	 */
	protected $_baseUrl = null;

	/**
	 * @var string
	 * @desc Baze path of request
	 */
	protected $_basePath = null;

	/**
	 * @var string
	 * @desc PATH_INFO
	 */
	protected $_pathInfo = '';

	/**
	 * @var boolean
	 * @desc Wether a request is a postback or not
	 */
	private $_isPostback = false;

	/**
	 * @var array
	 * @desc Instance parameters
	 */
	protected $_params = array();

	/**
	 * @var boolean
	 * @desc Should use _GET params?
	 */
	protected $_useGetParams = true;

	/**
	 * @var boolean
	 * @desc Should use _POST params?
	 */
	protected $_usePostParams = true;

	/**
	 * @var array
	 * @desc The default aliases to items of the global $_REQUEST array
	 */
	protected $_aliases = array('Accept' => 'HTTP_ACCEPT' , 'AcceptCharset' => 'HTTP_ACCEPT_CHARSET' , 'AcceptEncoding' => 'HTTP_ACCEPT_ENCODING' , //'AcceptLanguage' => '', availble trough getAcceptLanguage
'AuthDigest' => '' , // TODO: check name on php.net
'AuthType' => '' , // TODO: check name on php.net
'ContentType' => 'CONTENT_TYPE' , //'Cookies' => '',
//'UploadedFiles' => '',
	'GatewayInterface' => 'GATEWAY_INTERFACE' , //'GetVars' => '', // TODO: create method getGetVars
//'Headers' => '', // TODO: create method getHeaders
	'Host' => 'HTTP_HOST' , 'Method' => 'REQUEST_METHOD' , 'Path' => 'PATH_INFO' , //'PostVars' => '', // TODO: create method getPostVars
'Protocol' => 'SERVER_PROTOCOL' , 'QueryString' => 'QUERY_STRING' , 'Referer' => 'HTTP_REFERER' , 'RemoteAddress' => 'REMOTE_ADDR' , 'RemotePort' => 'REMOTE_PORT' , 'RequestUri' => 'REQUEST_URI' , 'ScriptName' => 'SCRIPT_NAME' , 'ServerAddress' => 'SERVER_ADDR' , 'ServerName' => 'SERVER_NAME' , 'ServerPort' => 'SERVER_PORT' , 'Time' => 'REQUEST_TIME' , 'UserAgent' => 'HTTP_USER_AGENT' , 'Username' => '' , // TODO: check name on php.net
'Password' => '',
'ViewState' => '__clientMessage', 'PageId' => 'pageId'); // TODO: check name on php.net


	/// @endcond


	private function __construct(){
		define('_IS_POSTBACK', $this->isPostback());
	}

	/**
	 * Implements the Factory Method Pattern
	 *
	 * @return HttpRequest
	 */
	public static function factory()
	{
		$o = new HttpRequest();
		$o->getRequestUri();
		return $o;
	}

	/**
	 * @return Zend_Uri_Http
	 */
	public function getRequestUri()
	{
		if (! isset($this->_url)) {
			if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
				$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
			}
			elseif (isset($_SERVER['REQUEST_URI'])) {
				$requestUri = $_SERVER['REQUEST_URI'];
			}
			elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
				$requestUri = $_SERVER['ORIG_PATH_INFO'];
				if (! empty($_SERVER['QUERY_STRING'])) {
					$requestUri .= '?' . $_SERVER['QUERY_STRING'];
				}
			}
			else {
				$requestUri = '';
			}

			if ($this->isSecure())
				$s = 's';
			else
				$s = '';

			if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80')
				$port = ':' . $_SERVER['SERVER_PORT'];
			else
				$port = '';

			$this->_url = Zend_Uri::factory("http{$s}://{$_SERVER['HTTP_HOST']}{$port}{$requestUri}");
		}

		return $this->_url;
	}

	/**
	 * Returns true if the request was made through https
	 *
	 * @return unknown
	 */
	public function isSecure()
	{
		return isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off');
	}

	/**
	 * Access values contained in the superglobals as public members
	 * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV
	 *
	 * @see http://msdn.microsoft.com/en-us/library/system.web.ui.httprequest.item.aspx
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		$getter = 'get' . $key;
		if (method_exists($this, $getter))
			return $this->$getter();

		$key = (null !== ($alias = $this->getAlias($key))) ? $alias : $key;

		switch (true)
		{
			case isset($this->_params[$key]):
				return $this->_params[$key];

			case isset($_GET[$key]) && $this->_useGetParams:
				return $_GET[$key];

			case isset($_POST[$key]) && $this->_usePostParams:
				return $_POST[$key];

			case isset($_SERVER[$key]):
				return $_SERVER[$key];

			default:
				return null;
		}
	}

	/**
	 * Set values
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 * @throws Exception
	 */
	public function __set($key, $value)
	{
		$setter = 'set' . $key;
		if (method_exists($this, $setter)) {
			return $this->$setter($value);
		}

		if (method_exists($this, 'get' . $key)) {
			// TODO: review exception
			throw new Exception('read_only');
		}

		// TODO: review exception
		throw new Exception('undefined_property');
	}

	/**
	 * Check to see if a property is set
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		switch (true)
		{
			case isset($this->_params[$key]):
				return true;
			case isset($_REQUEST[$key]):
				return true;
			default:
				return false;
		}
	}

	/**
	 * Retrieve a member of the $_GET superglobal
	 *
	 * If no $key is passed, returns the entire $_GET array.
	 *
	 * @todo How to retrieve from nested arrays
	 * @param string $key
	 * @param mixed $default Default value to use if key not found
	 * @return mixed Returns null if key does not exist
	 */
	public function getQueryParam($key = null, $default = null)
	{
		if (null === $key) {
			return $_GET;
		}

		return (isset($_GET[$key])) ? $_GET[$key] : $default;
	}

	/**
	 * Retrieve a member of the $_POST superglobal
	 *
	 * If no $key is passed, returns the entire $_POST array.
	 *
	 * @todo How to retrieve from nested arrays
	 * @param string $key
	 * @param mixed $default Default value to use if key not found
	 * @return mixed Returns null if key does not exist
	 */
	public function getPostParam($key = null, $default = null)
	{
		if (null === $key) {
			return $_POST;
		}

		return (isset($_POST[$key])) ? $_POST[$key] : $default;
	}

	/**
	 * Retrieve a member of the $_COOKIE superglobal
	 *
	 * If no $key is passed, returns the entire $_COOKIE array.
	 *
	 * @todo How to retrieve from nested arrays
	 * @param string $key
	 * @param mixed $default Default value to use if key not found
	 * @return mixed Returns null if key does not exist
	 */
	public function getCookie($key = null, $default = null)
	{
		if (null === $key) {
			return $_COOKIE;
		}

		return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
	}

	/**
	 * Set the base URL of the request; i.e., the segment leading to the script name
	 *
	 * E.g.: /myapp/index.php in /myapp/index.php/module/page
	 *
	 * Do not use the full URI when providing the base. The following are
	 * examples of what not to use:
	 * - http://example.com/admin (should be just /admin)
	 * - http://example.com/subdir/index.php (should be just /subdir/index.php)
	 *
	 * If no $baseUrl is provided, attempts to determine the base URL from the
	 * environment, using SCRIPT_FILENAME, SCRIPT_NAME, PHP_SELF, and
	 * ORIG_SCRIPT_NAME in its determination.
	 *
	 * @param mixed $baseUrl
	 * @return Zend_Controller_Request_Http
	 */
	protected function setBazeUrl($baseUrl = null)
	{
		if ((null !== $baseUrl) && ! is_string($baseUrl)) {
			return $this;
		}

		if ($baseUrl === null) {
			$filename = basename($_SERVER['SCRIPT_FILENAME']);

			if (basename($_SERVER['SCRIPT_NAME']) === $filename) {
				$baseUrl = $_SERVER['SCRIPT_NAME'];
			}
			elseif (basename($_SERVER['PHP_SELF']) === $filename) {
				$baseUrl = $_SERVER['PHP_SELF'];
			}
			elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
				$baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
			}
			else {
				// Backtrack up the script_filename to find the portion matching
				// php_self
				$path = $_SERVER['PHP_SELF'];
				$segs = explode('/', trim($_SERVER['SCRIPT_FILENAME'], '/'));
				$segs = array_reverse($segs);
				$index = 0;
				$last = count($segs);
				$baseUrl = '';
				do {
					$seg = $segs[$index];
					$baseUrl = '/' . $seg . $baseUrl;
					++ $index;
				}
				while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
			}

			// Does the baseUrl have anything in common with the request_uri?
			$requestUri = $this->getRequestUri();

			if (0 === strpos($requestUri, $baseUrl)) {
				// full $baseUrl matches
				$this->_baseUrl = $baseUrl;
				return $this;
			}

			if (0 === strpos($requestUri, dirname($baseUrl))) {
				// directory portion of $baseUrl matches
				$this->_baseUrl = rtrim(dirname($baseUrl), '/');
				return $this;
			}

			if (! strpos($requestUri, basename($baseUrl))) {
				// no match whatsoever; set it blank
				$this->_baseUrl = '';
				return $this;
			}

			// If using mod_rewrite or ISAPI_Rewrite strip the script filename
			// out of baseUrl. $pos !== 0 makes sure it is not matching a value
			// from PATH_INFO or QUERY_STRING
			if ((strlen($requestUri) >= strlen($baseUrl)) && ((false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0))) {
				$baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
			}
		}

		$this->_baseUrl = rtrim($baseUrl, '/');
		return $this;
	}

	/**
	 * Everything in REQUEST_URI before PATH_INFO
	 * E.g.: /myapp/index.php in /myapp/index.php/module/page
	 *
	 * @return string
	 */
	public function getBazeUrl()
	{
		if (null === $this->_baseUrl) {
			$this->setBazeUrl();
		}

		return $this->_baseUrl;
	}

	/**
	 * Set the base path for the URL
	 *
	 * @param string|null $basePath
	 * @return Zend_Controller_Request_Http
	 */
	protected function setBazePath($basePath = null)
	{
		if ($basePath === null) {
			$filename = basename($_SERVER['SCRIPT_FILENAME']);

			$baseUrl = $this->getBazeUrl();
			if (empty($baseUrl)) {
				$this->_basePath = '';
				return $this;
			}

			if (basename($baseUrl) === $filename) {
				$basePath = dirname($baseUrl);
			}
			else {
				$basePath = $baseUrl;
			}
		}

		$this->_basePath = rtrim($basePath, '/');
		return $this;
	}

	/**
	 * Everything in REQUEST_URI before PATH_INFO not including the filename
	 * E.g.: /myapp in /myapp/index.php/module/page (base url will be /myapp/index.php)
	 *
	 * @return string
	 */
	public function getBazePath()
	{
		if (null === $this->_basePath) {
			$this->setBazePath();
		}

		return $this->_basePath;
	}

	/**
	 * Set the PATH_INFO string
	 *
	 * @param string|null $pathInfo
	 * @return Zend_Controller_Request_Http
	 */
	protected function setPathInfo($pathInfo = null)
	{
		if ($pathInfo === null) {
			$baseUrl = $this->getBazeUrl();

			if (null === ($requestUri = $this->getRequestUri())) {
				return $this;
			}

			// Remove the query string from REQUEST_URI
			if ($pos = strpos($requestUri, '?')) {
				$requestUri = substr($requestUri, 0, $pos);
			}

			if ((null !== $baseUrl) && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
				// If substr() returns false then PATH_INFO is set to an empty string
				$pathInfo = '';
			}
			elseif (null === $baseUrl) {
				$pathInfo = $requestUri;
			}
		}

		$this->_pathInfo = (string) $pathInfo;
		return $this;
	}

	/**
	 * Returns everything between the BazeUrl and QueryString.
	 * This value is calculated instead of reading PATH_INFO
	 * directly from $_SERVER due to cross-platform differences.
	 *
	 * @return string
	 */
	public function getPathInfo()
	{
		if (empty($this->_pathInfo)) {
			$this->setPathInfo();
		}

		return $this->_pathInfo;
	}

	/**
	 * Set allowed parameter sources
	 *
	 * Can be empty array, or contain one or more of '_GET' or '_POST'.
	 *
	 * @param  array $paramSoures
	 * @return void
	 */
	public function setParamSources(array $paramSources = array())
	{
		$this->_useGetParams = in_array('_GET', $paramSources);
		$this->_usePostParams = in_array('_POST', $paramSources);
	}

	/**
	 * Get list of allowed parameter sources
	 *
	 * @return array
	 */
	public function getParamSources()
	{
		$arr = array();

		if ($this->_useGetParams)
			$arr[] = '_GET';

		if ($this->_usePostParams)
			$arr[] = '_POST';

		return $arr;
	}

	/**
	 * Retrieve an alias
	 *
	 * Retrieve the actual key represented by the alias $name.
	 *
	 * @param string $name
	 * @return string|null Returns null when no alias exists
	 */
	public function getAlias($name)
	{
		if (isset($this->_aliases[$name])) {
			return $this->_aliases[$name];
		}

		return null;
	}

	/**
	 * Set an alias
	 *
	 * Set the actual key represented by the alias $aliasName.
	 * Setting null remove the alias.
	 *
	 * @param string $aliasName
	 * @param string $keyName
	 * @return void
	 */
	public function setAlias($aliasName, $keyName)
	{
		if ($keyName === null) {
			unset($this->_aliases[$aliasName]);
		}

		$this->_aliases[$aliasName] = $keyName;
	}

	/**
	 * Retrieve the list of all aliases
	 *
	 * @return array
	 */
	public function getAliases()
	{
		return $this->_aliases;
	}

	/**
	 * Retrieve a parameter
	 *
	 * Retrieves a parameter from the instance. Priority is in the order of
	 * userland parameters (see {@link setParam()}), $_GET, $_POST. If a
	 * parameter matching the $key is not found, null is returned.
	 *
	 * If the $key is an alias, the actual key aliased will be used.
	 *
	 * @param mixed $key
	 * @param mixed $default Default value to use if key not found
	 * @return mixed
	 */
	public function getParam($key, $default = null)
	{
		$keyName = (null !== ($alias = $this->getAlias($key))) ? $alias : $key;

		if (isset($this->_params[$keyName])) {
			return $this->_params[$keyName];
		}
		elseif ($this->_useGetParams && (isset($_GET[$keyName]))) {
			return $_GET[$keyName];
		}
		elseif ($this->_usePostParams && (isset($_POST[$keyName]))) {
			return $_POST[$keyName];
		}

		return $default;
	}

	/**
	 * Retrieve an array of parameters
	 *
	 * Retrieves a merged array of parameters, with precedence of userland
	 * params (see {@link setParam()}), $_GET, $POST (i.e., values in the
	 * userland params will take precedence over all others).
	 *
	 * @return array
	 */
	public function getParams()
	{
		$return = $this->_params;
		if (isset($_GET) && is_array($_GET)) {
			$return += $_GET;
		}
		if (isset($_POST) && is_array($_POST)) {
			$return += $_POST;
		}
		return $return;
	}

	/**
	 * Set a userland parameter
	 *
	 * Uses $key to set a userland parameter. A $value of null will unset the $key if it exists
	 *
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function setParam($key, $value)
	{
		$key = (null !== ($alias = $this->getAlias($key))) ? $alias : $key;

		if ((null === $value) && isset($this->_params[$key])) {
			unset($this->_params[$key]);
		}
		elseif (null !== $value) {
			$this->_params[$key] = $value;
		}
	}

	/**
	 * Set parameters
	 *
	 * Set one or more parameters. Parameters are set as userland parameters,
	 * using the keys specified in the array.
	 *
	 * @param array $params
	 */
	public function setParams(array $params)
	{
		$this->_params = $this->_params + (array) $params;

		foreach ($this->_params as $key => $value) {
			if (null === $value) {
				unset($this->_params[$key]);
			}
		}
	}
	
	/**
	 * Was the request made by POST?
	 *
	 * @return boolean
	 */
	public function isPost()
	{
		return ('POST' == $this->getMethod());
	}

	/**
	 * Is the request a postback?
	 *
	 * @return boolean
	 */
	public function isPostback()
	{
		static $isPB = null;
		
		if($isPB === null)
			$isPB = $this->getPostParam('__clientMessage', false) !== false;
			
		return $isPB;
	}

	/**
	 * Was the request made by GET?
	 *
	 * @return boolean
	 */
	public function isGet()
	{
		return ('GET' == $this->getMethod());
	}

	/**
	 * Was the request made by PUT?
	 *
	 * @return boolean
	 */
	public function isPut()
	{
		if ('PUT' == $this->getMethod()) {
			return true;
		}

		return false;
	}

	/**
	 * Was the request made by DELETE?
	 *
	 * @return boolean
	 */
	public function isDelete()
	{
		if ('DELETE' == $this->getMethod()) {
			return true;
		}

		return false;
	}

	/**
	 * Was the request made by HEAD?
	 *
	 * @return boolean
	 */
	public function isHead()
	{
		if ('HEAD' == $this->getMethod()) {
			return true;
		}

		return false;
	}

	/**
	 * Was the request made by OPTIONS?
	 *
	 * @return boolean
	 */
	public function isOptions()
	{
		if ('OPTIONS' == $this->getMethod()) {
			return true;
		}

		return false;
	}

	/**
	 * Return the raw body of the request, if present
	 *
	 * @return string|false Raw body, or false if not present
	 */
	public function getRawBody()
	{
		$body = file_get_contents('php://input');

		if (strlen(trim($body)) > 0) {
			return $body;
		}

		return false;
	}

	/**
	 * Return the value of the given HTTP header. Pass the header name as the
	 * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
	 * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
	 *
	 * @param string $header HTTP header name
	 * @return string|false HTTP header value, or false if not found
	 * @throws ArgumentException
	 */
	public function getHeader($header)
	{
		if (empty($header)) {
			// TODO: review exception
			throw new ArgumentException('An HTTP header name is required');
		}

		// Try to get it from the $_SERVER array first
		$temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
		if (! empty($_SERVER[$temp])) {
			return $_SERVER[$temp];
		}

		// This seems to be the only way to get the Authorization header on
		// Apache
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if (! empty($headers[$header])) {
				return $headers[$header];
			}
		}

		return false;
	}

	/**
	 * Is the request a Javascript XMLHttpRequest?
	 *
	 * Should work with Prototype/Script.aculo.us, possibly others.
	 *
	 * @return boolean
	 */
	public function isXmlHttpRequest()
	{
		return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
	}

	/**
	 * Is this a Flash request?
	 *
	 * @return bool
	 */
	public function isFlashRequest()
	{
		return ($this->getHeader('USER_AGENT') == 'Shockwave Flash');
	}

	/**
	 * Gets the information about the browser by User Agent using external.Browscap class.
	 *
	 * @see Browscap::getBrowser()
	 *
	 * @param string $user_agent   the user agent string
	 * @param bool   $return_array whether return an array or an object
	 * @return mixed stdClass An object containing the browsers details. Array if $return_array is set to true.
	 * @throws Browscap_Exception
	 */
	public function getBrowser()
	{
		static $userAgent = null;

		if (! isset($this->_browser) || $this->getUserAgent() !== $userAgent) {
			$bscap = new Browscap(_SYSTEM_CACHE_DIR . '/browscap');
			$this->_browser = $bscap->getBrowser();
		}

		return $this->_browser;
	}

	/**
	 * Returns a list of user preferred languages.
	 * The languages are returned as an array. Each array element
	 * represents a single language preference. The languages are ordered
	 * according to user preferences. The first language is the most preferred.
	 *
	 * @return array list of user preferred languages.
	 */
	public function getAcceptLanguages()
	{
		if ($this->_languages === null) {
			if (! isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
				$this->_languages[0] = 'en';
			else {
				$this->_languages = array();
				foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $language) {
					$array = split(';q=', trim($language));
					$this->_languages[trim($array[0])] = isset($array[1]) ? (float) $array[1] : 1.0;
				}

				arsort($this->_languages);
				$this->_languages = array_keys($this->_languages);

				if (empty($this->_languages))
					$this->_languages[0] = 'en';
			}
		}

		return $this->_languages;
	}

	/**
	 * @return array list of cookies to be sent
	 */
	public function getCookies()
	{
		if (isset($this->_cookies))
			return $this->_cookies;
		else
			return array();
	}

	/**
	 * @return array list of uploaded files.
	 */
	public function getUploadedFiles()
	{
		return $_FILES;
	}

	/**
	 * @return Zend_Uri_Http The request url
	 */
	public function getUrl()
	{
		return $this->_url;
	}

	/**
	 * @return array list of server variables.
	 */
	public function getServerVariables()
	{
		return $_SERVER;
	}

	/**
	 * @return array list of environment variables.
	 */
	public function getEnvironmentVariables()
	{
		return $_ENV;
	}
}