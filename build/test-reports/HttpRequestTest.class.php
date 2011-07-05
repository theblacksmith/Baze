<?php

ini_set('include_path', ini_get('include_path').PATH_SEPARATOR
	. dirname(dirname(__FILE__)).PATH_SEPARATOR
	. dirname(dirname(__FILE__)).'/external');

require_once 'PHPUnit/Framework.php';

class HttpRequestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var HttpRequest
	 */
	private $r;

	public function setUp()
	{
		$_SERVER['HTTP_HOST'] = 'labamba';
		$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

		require_once 'system/net/HttpRequest.php';
		$this->r = new HttpRequest();
	}

	public function testAlias()
	{
		$this->r->setAlias('test_alias', 'SERVER_PROTOCOL');
		$this->assertEquals($this->r->SERVER_PROTOCOL, $this->r->test_alias);
	}

	public function tearDown()
	{
		$this->r = null;
	}
}