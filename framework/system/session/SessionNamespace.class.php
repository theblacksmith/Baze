<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'C:\projects\ZendFramework102\library\\');

require_once 'Zend/Session/Namespace.php';

require_once 'BazeSessionException.class.php';

class SessionNamespace
{
	/**
	 * @var Zend_Session_Namespace
	 */
	private $namespaceInstance;
	
	/**
     * __construct() - Returns an instance object bound to a particular, isolated section
     * of the session, identified by $namespace name (defaulting to 'Default').
     * The optional argument $singleInstance will prevent construction of additional
     * instance objects acting as accessors to this $namespace.
     *
     * @param string $namespace       - programmatic name of the requested namespace
     * @param bool $singleInstance    - prevent creation of additional accessor instance objects for this namespace
     * @return void
     */
    public function __construct($namespace = 'Default', $singleInstance = false)
    {
    	$this->namespaceInstance = new Zend_Session_Namespace($namespace, $singleInstance);
    }
    
    /**
     * __get() - method to get a variable in this object's current namespace
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return mixed
     */
    protected function __get($name)
    {
    	try{
    		return $this->namespaceInstance->$name;
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * __set() - method to set a variable/value in this object's namespace
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @param mixed $value - value in the <key,value> pair to assign to the $name key
     * @throws Zend_Session_Exception
     * @return true
     */
    protected function __set($name, $value)
    {
    	try{
    		$this->namespaceInstance->$name = $value;
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * __isset() - determine if a variable in this object's namespace is set
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return bool
     */
    protected function __isset($name)
    {
    	try{
    		return isset($this->namespaceInstance->$name);
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * __unset() - unset a variable in this object's namespace.
     *
     * @param string $name - programmatic name of a key, in a <key,value> pair in the current namespace
     * @return true
     */
    protected function __unset($name)
    {
    	try{
    		unset($this->namespaceInstance->$name);
    		return true;
    	}
    	catch(Zend_Session_Exception $zse)
    	{
    		throw new BazeSessionException($zse->getMessage());
    	}
    }
    
    /**
     * Retorna o objeto namespace que está sendo manipulado pela classe
     * 
     * @return Zend_Session_Namespace
     */
    public function getNamespace()
    {
    	return $this->namespaceInstance;
    }
    
    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject - iteratable container of the namespace contents
     */
    public function getIterator()
    {
    	return $this->namespaceInstance->getIterator();
    }
    
    /**
     * lock() - mark a session/namespace as readonly
     *
     * @return void
     */
    public function lock()
    {
    	$this->namespaceInstance->lock();
    }
    
    /**
     * unlock() - unmark a session/namespace to enable read & write
     *
     * @return void
     */
    public function unlock()
    {
    	$this->namespaceInstance->unlock();
    }
    
    /**
     * unlockAll() - unmark all session/namespaces to enable read & write
     *
     * @return void
     */
    public static function unlockAll()
    {
    	$this->namespaceInstance->unlockAll();
    }
    
    /**
     * isLocked() - return lock status, true if, and only if, read-only
     *
     * @return bool
     */
    public function isLocked()
    {
    	return $this->namespaceInstance->isLocked();
    }
    
    /**
     * unsetAll() - unset all variables in this namespace
     *
     * @return true
     */
    public function unsetAll()
    {
    	return $this->namespaceInstance->unsetAll();
    }
    
    /**
     * apply() - enables applying user-selected function, such as array_merge() to the namespace
     * Caveat: ignores members expiring now.
     *
     * Example:
     *   $namespace->apply('array_merge', array('tree' => 'apple', 'fruit' => 'peach'), array('flower' => 'rose'));
     *   $namespace->apply('count');
     *
     * @param string $callback - callback function
     * @param mixed  OPTIONAL arguments passed to the callback function
     */
    public function apply($callback)
    {
    	$this->namespaceInstance->apply($callback);
    }
    
    /**
     * applySet() - enables applying user-selected function, and sets entire namespace to the result
     * Result of $callback must be an array. Caveat: ignores members expiring now.
     *
     * Example:
     *   $namespace->applySet('array_merge', array('tree' => 'apple', 'fruit' => 'peach'), array('flower' => 'rose'));
     *
     * @param string $callback - callback function
     * @param mixed  OPTIONAL arguments passed to the callback function
     */
    public function applySet($callback)
    {
    	$this->namespaceInstance->applySet($callback);
    }
    
    /**
     * setExpirationSeconds() - expire the namespace, or specific variables after a specified
     * number of seconds
     *
     * @param int $seconds     - expires in this many seconds
     * @param mixed $variables - OPTIONAL list of variables to expire (defaults to all)
     * @throws Zend_Session_Exception
     * @return void
     */
    public function setExpirationSeconds($seconds, $variables = null)
    {
    	try{
    		$this->namespaceInstance->setExpirationSeconds($seconds, $variables);
    	}
    	catch(Zend_Session_Exception $e)
    	{
    		throw new BazeSessionException($e->getMessage());
    	}
    }

}