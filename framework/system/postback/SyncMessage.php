<?php
require_once(realpath(dirname(__FILE__)) . '/../../Overview/Component.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/Change.php');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class SyncMessage {
	/**
	 * @AttributeType array
	 */
	private $changes;

	/**
	 * @access public
	 * @param Overview.Component obj
	 * @param system.postback.Change chg
	 * @ParamType obj Overview.Component
	 * @ParamType chg system.postback.Change
	 */
	public function addChange(Component $obj, Change $chg) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @param Overview.Component c
	 * @ParamType c Overview.Component
	 */
	public function addNewObject(Component $c) {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @param Overview.Component c
	 * @ParamType c Overview.Component
	 */
	public function addRemovedObject(Component $c) {
		// Not yet implemented
	}
}
?>