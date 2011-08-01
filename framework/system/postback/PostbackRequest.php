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
class PostbackRequest extends HttpRequest {
	
	/**
	 * @var ClientViewState
	 */
	private $clientViewState;
	
	/**
	 * @var string
	 */
	private $pageId;
	
	/**
	 * @var HttpRequest
	 */
	private $req;
	
	public function __construct(HttpRequest $req)
	{
		$this->req = $req;
		
		if(($msg = $req->getParam('__clientMessage', false)) !== false)
			$this->clientViewState = new ClientViewState($msg);
	}
	
	/**
	 * 
	 * Function GetViewState
	 * 
	 * @access public
	 * @return SyncMessage
	 */
	public function getSyncMessage() {
		return $this->clientViewState->SyncMessage;
	}

	/**
	 * @access public
	 * @return CommandMessage
	 */
	public function getCommandMessages() {
		return $this->clientViewState->CommandMessage;
	}

	/**
	 * @access public
	 * @return EventMessage
	 */
	public function getEventMessage() {
		return $this->clientViewState->EventMessage;
	}

	/**
	 * @access public
	 * @return string
	 * @ReturnType string
	 */
	public function getPageId() {
		return $this->pageId;
	}
}