<?php

require_once ('system/web/services/HttpServiceConfig.class.php');


class PageServiceConfig extends HttpServiceConfig
{
	/**
	 * @var array
	 * @desc The error handler pages in configuration. {@wiki ErrorHandlerPages}}
	 */
	public $errorPages;

	/**
	 * @var IPageParser
	 */
	public $PageParser;
}