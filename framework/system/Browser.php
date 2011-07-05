<?php
class Browser extends Component // TODO: SURE????
{
	/**
	 * @var string
	 */
	private $name;
	
	/**
	 * @var string
	 */
	private $version;
	
	/**
	 * @var string
	 */
	private $majorVersion;
	
	/**
	 * @var string
	 */
	private $minorVersion;
	
	/**
	 * @var string
	 */
	private $platform;
	
	/**
	 * @var boolean
	 */
	private $hasDotNetCLR;
	
	/**
	 * @var string
	 */
	private $platformVersion;
	
	/**
	 * @var string
	 */
	private $userAgent;
	
	//@removeBlock Just for auto-complete
	///@cond user
	
	/**
	 * @var string
	 */
	public $Name;
	/**
	 * @var string
	 */
	private $Version;
	
	/**
	 * @var string
	 */
	private $MajorVersion;
	
	/**
	 * @var string
	 */
	private $MinorVersion;
	
	/**
	 * @var string
	 */
	private $Platform;
	
	/**
	 * @var boolean
	 */
	private $HasDotNetCLR;
	
	/**
	 * @var string
	 */
	private $PlatformVersion;
	/**
	 * @var string
	 */
	private $UserAgent;

	///@endcond
	//@endRemoveBlock

	/**
	 * Return true if the browser is Internet Explorer
	 * @return boolean
	 */
	public function isIE()
	{
		if($this->name == "Internet Explorer")
			return true;
			
		return false;
	}
	
	/**
	 * Return true if the browser is Internet Explorer
	 * @return boolean
	 */
	public function isFirefox()
	{
		if($this->name == "Firefox")
			return true;
			
		return false;
	}
}