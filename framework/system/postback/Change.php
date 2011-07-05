<?php
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/ChangeType.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/ViewStateManager.php');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
abstract class Change {
	/**
	 * @AttributeType system.postback.ChangeType
	 */
	private $type;
	/**
	 * @AssociationType system.postback.ViewStateManager
	 */
	private $unnamed_ViewStateManager_;
	/**
	 * @AssociationType system.postback.ChangeType
	 */
	private $unnamed_ChangeType_;

	/**
	 * @access public
	 * @return string
	 * @ReturnType string
	 */
	public function getXML() {
		// Not yet implemented
	}

	/**
	 * 
	 * O id foi criado dessa forma para que se possa perceber quando uma alteração se chocar com outra.
	 * @access public
	 * @return string
	 * @ReturnType string
	 */
	public function getId() {
		// Not yet implemented
	}

	/**
	 * @access public
	 * @return system.postback.ChangeType
	 * @ReturnType system.postback.ChangeType
	 */
	public function getType() {
		// Not yet implemented
	}

	/**
	 * 
	 * Function isMirror
	 * Checa se a alteração desta instância é anulada pela alteração do objeto passado como parâmetro.
	 * 
	 * @param {Change} chg
	 * @access public
	 * @param system.postback.Change chg
	 * @ParamType chg system.postback.Change
	 */
	public function isMirror(Change $chg) {
		// Not yet implemented
	}

	/**
	 * 
	 * Function mergeWith
	 * Soma duas alterções transformando-as em uma só.
	 * 
	 * @param {Change} chg
	 * @access public
	 * @param system.postback.Change chg
	 * @ParamType chg system.postback.Change
	 */
	public function mergeWith(Change $chg) {
		// Not yet implemented
	}
}
?>