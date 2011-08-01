<?php

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class EventMessage {
	
	/**
	 * @AttributeType string
	 * 
	 * The event.
	 * 
	 * @var string
	 */
	private $eventName;
	/**
	 * @AttributeType string
	 * 
	 * The target object.
	 * 
	 * @var string
	 */
	private $sender;
	/**
	 * @AttributeType array
	 * 
	 * The argument via postback.
	 * 
	 * @var array
	 */
	private $args;

	
	public function __construct(array $msg = null) {
		
			$this->eventName = "on" . ucfirst($msg["type"]);
			$this->sender  = $msg["target"];

			foreach($msg['args'] as $k => $v)
			{
				$this->args[$k] = $v;
			}
	}
	
	/**
	 * @return string $event The name of the event
	 */
	public function getEvent() {
		return $this->eventName;
	}

	/**
	 * @return string The id of the event sender
	 */
	public function getSender() {
		return $this->sender;
	}

	/**
	 * @return array The arguments passed by the target the
	 */
	public function getArguments() {
		return $this->args;
	}
}