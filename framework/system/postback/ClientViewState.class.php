<?php

import('system.postback.ViewState');

/** 
 * @author svallory
 * 
 * 
 */
class ClientViewState extends ViewState
{	
	/**
	 * @param string $clientMessage
	 */
	function __construct($clientMessage) 
	{		
		if($clientMessage == "")
			return;

		$clientMessage = stripslashes($clientMessage);
		$clientMessage = json_decode($clientMessage, true); // FastJSON::decode($newValue);

		if(isset($clientMessage['SyncMsg'])) {
			$this->syncMsg = new SyncMessage($clientMessage['SyncMsg']);
		}
		
		if(isset($clientMessage['EvtMsg'])) {
			$this->evtMsg = new EventMessage($clientMessage['EvtMsg']);
		}
		
		if(isset($clientMessage['CmdMsg'])) {
			$this->cmdMsg = new CommandMessage($clientMessage['CmdMsg']);
		}
	}
}