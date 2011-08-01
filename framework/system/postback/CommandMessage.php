<?php

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class CommandMessage {
	/**
	 * @AttributeType system.collections.OrderedList
	 */
	private $commands;

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
}
?>