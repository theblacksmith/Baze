<?php
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/Change.php');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class ChangeType {
	const PROPERTY_CHANGED = 1;
	const CHILD_ADDED = 2;
	const CHILD_REMOVED = 3;
	/**
	 * @AssociationType system.postback.Change
	 */
	private $unnamed_Change_;
}
?>