<?php

set_include_path(get_include_path() . PATH_SEPARATOR . 'C:\projects\ZendFramework102\library\\');

require_once 'Zend/Session.php';

require_once 'ZendSaveHandlerProxy.php';

require_once 'ZendValidatorProxy.php';

require_once 'BazeSessionException.class.php';

class Session
{
	/**
     * setOptions - set both the class specified
     *
     * @param  array $userOptions - pass-by-keyword style array of <option name, option value> pairs
     * @throws Zend_Session_Exception
     * @return void
     */
    public static function setOptions(array $userOptions = array())
    {
    	try
    	{
    		Zend_Session::setOptions($userOptions);
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * setSaveHandler() - Session Save Handler assignment
     *
     * @param Zend_Session_SaveHandler_Interface $interface
     * @return void
     */
    public static function setSaveHandler(ISessionSaveHandler $saveHandler)
    {
    	Zend_Session::setSaveHandler(new ZendSaveHandlerProxy($saveHandler));
    }
    
    /**
     * regenerateId() - Regenerate the session id.  Best practice is to call this after
     * session is started.  If called prior to session starting, session id will be regenerated
     * at start time.
     *
     * @throws Zend_Session_Exception
     * @return void
     */
    public static function regenerateId()
    {
    	try{
    		Zend_Session::regenerateId();
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * rememberMe() - Write a persistent cookie that expires after a number of seconds in the future. If no number of
     * seconds is specified, then this defaults to self::$_rememberMeSeconds.  Due to clock errors on end users' systems,
     * large values are recommended to avoid undesirable expiration of session cookies.
     *
     * @param $seconds integer - OPTIONAL specifies TTL for cookie in seconds from present time
     * @return void
     */
    public static function rememberMe($seconds = null)
    {
    	Zend_Session::rememberMe($seconds);
    }
    
    /**
     * forgetMe() - Write a volatile session cookie, removing any persistent cookie that may have existed. The session
     * would end upon, for example, termination of a web browser program.
     *
     * @return void
     */
    public static function forgetMe()
    {
    	Zend_Session::forgetMe();
    }
    
    /**
     * rememberUntil() - This method does the work of changing the state of the session cookie and making
     * sure that it gets resent to the browser via regenerateId()
     *
     * @param int $seconds
     * @return void
     */
    public static function rememberUntil($seconds = 0)
    {
    	Zend_Session::rememberUntil($seconds);
    }
    
    /**
     * sessionExists() - whether or not a session exists for the current request
     *
     * @return bool
     */
    public static function sessionExists()
    {
    	return Zend_Session::sessionExists();
    }
    
    /**
     * start() - Start the session.
     *
     * @param bool|array $options  OPTIONAL Either user supplied options, or flag indicating if start initiated automatically
     * @throws Zend_Session_Exception
     * @return void
     */
    public static function start($options = false)
    {
    	try{
    		Zend_Session::start($options);
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * isStarted() - convenience method to determine if the session is already started.
     *
     * @return bool
     */
    public static function isStarted()
    {
    	return Zend_Session::isStarted();
    }
    
    /**
     * isRegenerated() - convenience method to determine if session_regenerate_id()
     * has been called during this request by Zend_Session.
     *
     * @return bool
     */
    public static function isRegenerated()
    {
    	return Zend_Session::isRegenerated();
    }
    
    /**
     * getId() - get the current session id
     *
     * @return string
     */
    public static function getId()
    {
    	return Zend_Session::getId();
    }
    
    /**
     * setId() - set an id to a user specified id
     *
     * @throws Zend_Session_Exception
     * @param string $id
     * @return void
     */
    public static function setId($id)
    {
    	try{
    		Zend_Session::setId($id);
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * registerValidator() - register a validator that will attempt to validate this session for
     * every future request
     *
     * @param Zend_Session_Validator_Interface $validator
     * @return void
     */
    public static function registerValidator(ISessionValidator $validator)
    {
    	Zend_Session::registerValidator(new ZendValidatorProxy($validator));
    }
    
    /**
     * stop() - Disable write access.  Optionally disable read (not implemented).
     *
     * @return void
     */
    public static function stop()
    {
    	Zend_Session::stop();
    }
    
    /**
     * writeClose() - Shutdown the sesssion, close writing and detach $_SESSION from the back-end storage mechanism.
     * This will complete the internal data transformation on this request.
     *
     * @param bool $readonly - OPTIONAL remove write access (i.e. throw error if Zend_Session's attempt writes)
     * @return void
     */
    public static function writeClose($readonly = true)
    {
    	Zend_Session::writeClose($readonly);
    }
    
    /**
     * destroy() - This is used to destroy session data, and optionally, the session cookie itself
     *
     * @param bool $remove_cookie - OPTIONAL remove session id cookie, defaults to true (remove cookie)
     * @param bool $readonly - OPTIONAL remove write access (i.e. throw error if Zend_Session's attempt writes)
     * @return void
     */
    public static function destroy($remove_cookie = true, $readonly = true)
    {
    	Zend_Session::destroy($remove_cookie, $readonly);
    }
    
    /**
     * expireSessionCookie() - Sends an expired session id cookie, causing the client to delete the session cookie
     *
     * @return void
     */
    public static function expireSessionCookie()
    {
    	Zend_Session::expireSessionCookie();
    }
    
    /**
     * namespaceIsset() - check to see if a namespace is set
     *
     * @param string $namespace
     * @return bool
     */
    public static function namespaceIsset($namespace)
    {
    	return Zend_Session::namespaceIsset($namespace);
    }
    
    /**
     * namespaceUnset() - unset a namespace or a variable within a namespace
     *
     * @param string $namespace
     * @throws Zend_Session_Exception
     * @return void
     */
    public static function namespaceUnset($namespace)
    {
    	try{
    		Zend_Session::namespaceUnset($namespace);
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * namespaceGet() - get all variables in a namespace
     * Deprecated: Use getIterator() in Zend_Session_Namespace.
     *
     * @param string $namespace
     * @return array
     */
    public static function namespaceGet($namespace)
    {
    	return Zend_Session::namespaceGet($namespace);
    }
    
    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject
     */
    public static function getIterator()
    {
    	return Zend_Session::getIterator();
    }
    
    /**
     * isWritable() - returns a boolean indicating if namespaces can write (use setters)
     *
     * @return bool
     */
    public static function isWritable()
    {
    	return Zend_Session::isWritable();
    }
    
    /**
     * isReadable() - returns a boolean indicating if namespaces can write (use setters)
     *
     * @return bool
     */
    public static function isReadable()
    {
    	return Zend_Session::isReadable();
    }
    
}