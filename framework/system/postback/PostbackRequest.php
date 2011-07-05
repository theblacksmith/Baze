<?php
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/SyncMessage.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/EventMessage.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/CommandMessage.php');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class PostbackRequest {
	/**
	 * @AttributeType system.postback.SyncMessage
	 */
	private $syncMsg;
	/**
	 * @AttributeType system.postback.EventMessage
	 */
	private $eventMsg;
	/**
	 * @AttributeType array
	 */
	private $cmdMsg;
	/**
	 * @AttributeType string
	 */
	private $pageId;
	/**
	 * @AssociationType system.postback.EventMessage
	 */
	private $unnamed_EventMessage_;
	/**
	 * @AssociationType system.postback.CommandMessage
	 * @AssociationMultiplicity 0..*
	 * @AssociationKind Aggregation
	 */
	private $unnamed_CommandMessage_ = array();
	/**
	 * @AssociationType system.postback.SyncMessage
	 * @AssociationMultiplicity 1
	 * @AssociationKind Composition
	 */
	private $unnamed_SyncMessage_;

	/**
	 * 
	 * Function GetViewState
	 * 
	 * @access public
	 * @return system.postback.SyncMessage
	 * @ReturnType system.postback.SyncMessage
	 */
	public function getSyncMessage() {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @return array_1
	 * @ReturnType array
	 */
	public function getCommandMessages() {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @return system.postback.EventMessage
	 * @ReturnType system.postback.EventMessage
	 */
	public function getEventMessage() {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @return string
	 * @ReturnType string
	 */
	public function getPageId() {
		// Not yet implemented
	}
}
?>