<?php
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/Change.php');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class PropertyChange extends Change {

	/**
	 * @access public
	 * @param string propertyName
	 * @param mixed newValue
	 * @ParamType propertyName string
	 * @ParamType newValue mixed
	 */
	public function __construct($propertyName, $newValue) {
		// Not yet implemented
	}
}
?>