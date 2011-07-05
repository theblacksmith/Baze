<?php
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/PostbackRequest.php');

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
	/**
	 * @AssociationType system.postback.PostbackRequest
	 */
	private $unnamed_PostbackRequest_;

	/**
	 * 
	 * Function getEvent
	 * 
	 * @return string $event The name of the last event happened
	 * @access public
	 * @return string
	 * @ReturnType string
	 */
	public function getEvent() {
		// Not yet implemented
	}

	/**
	 * 
	 * Function getEventTarget
	 * 
	 * @return string The id of the target of the last event happened
	 * @access public
	 * @return string
	 * @ReturnType string
	 */
	public function getSender() {
		// Not yet implemented
	}

	/**
	 * 
	 * Function getEventArgument
	 * 
	 * @return string $event The arguments passed by the target the
	 * @access public
	 * @return array_1
	 * @ReturnType array
	 */
	public function getArguments() {
		// Not yet implemented
	}
}
?>