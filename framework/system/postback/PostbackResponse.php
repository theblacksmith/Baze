<?php

import('system.postback.SyncMessage');
import('system.postback.EventMessage');
import('system.postback.CommandMessage');
import('system.postback.ClientViewState');
import('system.net.HttpRequest');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class PostbackResponse extends HttpResponse {
	/**
	 * @AttributeType string
	 * 
	 * URL to redirect browser to, if needed.
	 * 
	 * @var string
	 */
	private $redirectURL;
	
	/**
	 * @var SyncMessage
	 */
	private $syncMsg;
	
	/**
	 * @var EventMessage
	 */
	private $evtMsg;
	
	/**
	 * @var CommandMessage
	 */
	private $cmdMsg;

	/**
	 * @var HttpResponse
	 */
	private $resp;
	
	public function __construct(HttpResponse $resp)
	{
		$this->resp = $resp;
		$this->bufferOutput = $resp->bufferOutput;
		$this->status = $resp->status;
		$this->reason = $resp->reason;
		$this->charset = $resp->charset;
		$this->contentType = $resp->contentType;
		$this->_cookies = $resp->_cookies;
		$this->content = $resp->content;
		$this->outputStarted = $resp->outputStarted; // = false;
	}
	
	public function setRedirectURL($url)
	{
		$this->redirectURL = $url;

		$comm = new CommandCall(array(
				"name" => JSAPICommand::Redirect,
				"arguments" => array("url" => $url),
				"executeOn" => MessageParsePhase::BeforeCreateObjects));

		$this->cmdMsg->addCommand($comm, true);
	}
	
	/**
	 * 
	 * @param CommandCall $comm
	 * @param boolean $unique - if true, only one instance of this action will be allowed. Other additions of this action will always overwrite the same command
	 * @return int - index of the created command
	 */
	public function addCommand(CommandCall $comm, $unique = false) {
		$this->cmdMsg->addCommand($comm, $unique);
	}

	/**
	 * Adds a javascript function call to be executed on the client
	 * 
	 * The parameters specified on $params must be EXACTLY as you would write them on javascript.
	 * For instance, if you want to pass a string you have to put the quotes as part of the value 
	 * of the parameter you are passing here.
	 * 
	 * Examples:
	 * ->addJSFunction('alert', array('"a message!"'));  							# alert("a message!");
	 * ->addJSFunction('myFunc', array('1', 'aVar', '"a string"'));		# myFunc(1, aVar, "a string");
	 * 
	 * @param string $func The function name
	 * @param array $params The parameters, exactly as they should be writen on the javascript
	 */
	public function addJSFunction($func, $parms) {
		$this->addJSFunction($func, $parms);
	}
}