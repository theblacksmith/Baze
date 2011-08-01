<?php

/**
 * Classe MessageParsePhase
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class MessageParsePhase
{
	const BeforeCreateObjects = 701;
	const BeforeModifyObjects = 702;
	const BeforeDeleteObjects = 703;
	const OnMessageEnd = 704;
}

class ViewState extends BazeObject
{
	/**
	 * @var SyncMessage
	 */
	protected $syncMsg;
	
	/**
	 * @var EventMessage
	 */
	protected $evtMsg;
	
	/**
	 * @var CommandMessage
	 */
	protected $cmdMsg;

	/**
	 * @return SyncMessage The sync message received from the client
	 */
	public function getSyncMessage()
	{
		return $this->syncMsg;
	}

	/**
	 * @return EventMessage The event message received from the client
	 */
	public function getEventMessage()
	{
		return $this->evtMsg;
	}

	/**
	 * @return CommandMessage The command message received from the client
	 */
	public function getCommandMessage()
	{
		return $this->cmdMsg;
	}
}