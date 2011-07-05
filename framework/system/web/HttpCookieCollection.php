<?php

import('system.collections.Collection');


/**
 * HttpCookieCollection class.
 *
 * THttpCookieCollection implements a collection class to store cookies.
 * Besides using all functionalities from {@link TList}, you can also
 * retrieve a cookie by its name using either {@link findCookieByName} or
 * simply:
 * <code>
 *   $cookie=$collection[$cookieName];
 * </code>
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: THttpRequest.php 2541 2008-10-21 15:05:13Z qiang.xue $
 * @package System.Web
 * @since 3.0
 */
class HttpCookieCollection extends Set
{
	/**
	 * Inserts an item at the specified position.
	 * This overrides the parent implementation by performing additional
	 * operations for each newly added THttpCookie object.
	 *
	 * @param HttpCookie new item
	 * @param integer the specified position.
	 */
	public function insertAt(HttpCookie $item, $index = null)
	{
		parent::insertAt($item, $index);
	}

	/**
	 * @param integer|string index of the cookie in the collection or the cookie's name
	 * @return THttpCookie the cookie found
	 */
	public function itemAt($index)
	{
		// @todo review
		if(is_integer($index))
			return parent::g($index);
		else
			return $this->findCookieByName($index);
	}

	/**
	 * Finds the cookie with the specified name.
	 * @param string the name of the cookie to be looked for
	 * @return THttpCookie the cookie, null if not found
	 */
	public function findCookieByName($name)
	{
		// @todo review
		foreach($this as $cookie)
			if($cookie->getName()===$name)
				return $cookie;
		return null;
	}
}

/**
 * HttpCookie class.
 *
 * A THttpCookie instance stores a single cookie, including the cookie name, value,
 * domain, path, expire, and secure.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id: THttpRequest.php 2541 2008-10-21 15:05:13Z qiang.xue $
 * @package System.Web
 * @since 3.0
 */
class HttpCookie extends Component
{
	/**
	 * @var string domain of the cookie
	 */
	private $_domain = '';

	/**
	 * @var string name of the cookie
	 */
	private $_name;

	/**
	 * @var string value of the cookie
	 */
	private $_value = '';

	/**
	 * @var integer expire of the cookie
	 */
	private $_expire = 0;

	/**
	 * @var string path of the cookie
	 */
	private $_path = '/';

	/**
	 * @var boolean whether cookie should be sent via secure connection
	 */
	private $_secure = false;

	/**
	 * Constructor.
	 * @param string name of this cookie
	 * @param string value of this cookie
	 */
	public function __construct($name, $value)
	{
		$this->_name = $name;
		$this->_value = $value;
	}

	/**
	 * @return string the domain to associate the cookie with
	 */
	public function getDomain()
	{
		return $this->_domain;
	}

	/**
	 * @param string the domain to associate the cookie with
	 */
	public function setDomain($value)
	{
		$this->_domain=$value;
	}

	/**
	 * @return integer the time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
	 */
	public function getExpire()
	{
		return $this->_expire;
	}

	/**
	 * @param integer the time the cookie expires. This is a Unix timestamp so is in number of seconds since the epoch.
	 */
	public function setExpire($value)
	{
		$this->_expire = (int)$value;
	}

	/**
	 * @return string the name of the cookie
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * @param string the name of the cookie
	 */
	public function setName($value)
	{
		$this->_name = $value;
	}

	/**
	 * @return string the value of the cookie
	 */
	public function getValue()
	{
		return $this->_value;
	}

	/**
	 * @param string the value of the cookie
	 */
	public function setValue($value)
	{
		$this->_value = $value;
	}

	/**
	 * @return string the path on the server in which the cookie will be available on, default is '/'
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * @param string the path on the server in which the cookie will be available on
	 */
	public function setPath($value)
	{
		$this->_path = $value;
	}

	/**
	 * @return boolean whether the cookie should only be transmitted over a secure HTTPS connection
	 */
	public function getSecure()
	{
		return $this->_secure;
	}

	/**
	 * @param boolean ether the cookie should only be transmitted over a secure HTTPS connection
	 */
	public function setSecure($value)
	{
		$this->_secure = (int)$value;
	}
}