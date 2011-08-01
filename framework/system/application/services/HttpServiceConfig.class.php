<?php

class HttpServiceConfig extends Zend_Config_Xml
{
	/**
	 * @var string
	 * @desc the id of the service
	 */
	public $Id;

	/**
	 * @var string
	 * @desc the qualified class name of the service
	 */
	public $Class;

	/**
	 * @var string
	 * @desc a regular expression defining the url's that should be handled by this service
	 */
	public $UrlPattern;
}