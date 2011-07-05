<?php
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/SyncMessage.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/EventMessage.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/CommandMessage.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/collections/OrderedList.php');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class PostbackResponse {
	/**
	 * @AttributeType string
	 * 
	 * URL to redirect browser to, if needed.
	 * 
	 * @var string
	 */
	private $redirectURL;
	/**
	 * @AttributeType system.postback.SyncMessage
	 */
	private $syncMsg;
	/**
	 * @AttributeType system.postback.EventMessage
	 */
	private $evtMsg;
	/**
	 * @AttributeType system.postback.CommandMessage
	 */
	private $cmdMsg;

	/**
	 * 
	 * Enter description here...
	 * 
	 * @param CommandCall $comm
	 * @param boolean $unique - if true, only one instance of this action will be allowed. Other additions of this action will always overwrite the same command
	 * @return int - index of the created command
	 * @access public
	 * @param CommandCall comm
	 * @param unique
	 * @ParamType comm CommandCall
	 */
	public function addCommand(CommandCall $comm, $unique = false) {
		// Not yet implemented
	}

	/**
	 * 
	 * Enter description here...
	 * 
	 * @return Collection
	 * @access public
	 * @return system.collections.OrderedList
	 * @ReturnType system.collections.OrderedList
	 */
	public function getCommands() {
		// Not yet implemented
	}

	/**
	 * 
	 * Enter description here...
	 * 
	 * @param mixed $id
	 * @return boolean
	 * @access public
	 * @param id
	 */
	public function removeCommand($id) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @param string url
	 * @ParamType url string
	 */
	public function setRedirectURL($url) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @return string
	 * @ReturnType string
	 */
	public function getRedirectURL() {
		// Not yet implemented
	}

	/**
	 * 
	 * Function addJSFunction
	 * adiciona uma chamada JavaScript que deve ser feita pelo cliente.
	 * 
	 * @param string $func (o nome da função)
	 * @param array $parms (os parâmetros, EXATAMENTE como vão ser
	 * chamados no JavaScript - você deve explicitamente colocar aspas
	 * caso queira passar uma string para o JavaScript (ex: '\'teste\''),
	 * e arrays devem ser sempre strings da forma
	 * [elem1, elem2, elem3, ...])
	 * @access public
	 * @param func
	 * @param parms
	 */
	public function addJSFunction($func, $parms) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @param system.postback.SyncMessage msg
	 * @ParamType msg system.postback.SyncMessage
	 */
	public function setSyncMessage(SyncMessage $msg) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @param system.postback.EventMessage msg
	 * @ParamType msg system.postback.EventMessage
	 */
	public function setEventMessage(EventMessage $msg) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @param system.postback.CommandMessage msg
	 * @ParamType msg system.postback.CommandMessage
	 */
	public function setCommandMessage(CommandMessage $msg) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @return system.postback.SyncMessage
	 * @ReturnType system.postback.SyncMessage
	 */
	public function getSyncMessage() {
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
	 * @return system.postback.CommandMessage
	 * @ReturnType system.postback.CommandMessage
	 */
	public function getCommandMessage() {
		// Not yet implemented
	}
}
?>