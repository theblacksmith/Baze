<?php
require_once(realpath(dirname(__FILE__)) . '/../../system/web/ui/page/Page.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/collections/Map.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/Change.php');
require_once(realpath(dirname(__FILE__)) . '/../../Overview/Page.php');
require_once(realpath(dirname(__FILE__)) . '/../../system/postback/SyncMessage.php');
require_once(realpath(dirname(__FILE__)) . '/../../Overview/Component.php');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class ViewStateManager {
	/**
	 * @AttributeType system.web.ui.page.Page
	 * 
	 * A pointer to the page containing this.
	 * 
	 * @var Page $page
	 */
	private $page;
	/**
	 * @AttributeType system.collections.Map
	 * 
	 * Array with the result of a call of serialize function
	 * for each object in the page in the last call of SaveState().
	 * The elements are indexed by the id of the variables.
	 * 
	 * @var array
	 */
	private $cachedObjs;
	/**
	 * @AttributeType system.collections.Map
	 * 
	 * @var Map
	 */
	private $oldObjects;
	/**
	 * @AttributeType system.collections.Map
	 * 
	 * @var Map
	 */
	private $newObjects;
	/**
	 * @AttributeType system.collections.Map
	 * 
	 * @var Map
	 */
	private $modObjects;
	/**
	 * @AttributeType system.collections.Map
	 * 
	 * @var Map
	 */
	private $delObjects;
	/**
	 * @AttributeType boolean
	 */
	private $sincronized;
	/**
	 * @AssociationType system.postback.Change
	 * @AssociationKind Aggregation
	 */
	private $unnamed_Change_;

	/**
	 * 
	 * Constructor
	 * 
	 * @param Page $page
	 * @access public
	 * @param Overview.Page page
	 * @ParamType page Overview.Page
	 */
	public function __construct(Page $page) {
		// Not yet implemented
	}

	/**
	 * 
	 * Function LoadViewState
	 * 
	 * @param $clientState string xml string
	 * @access public
	 * @param system.postback.SyncMessage syncMsg
	 * @ParamType syncMsg system.postback.SyncMessage
	 */
	public function loadViewState(SyncMessage $syncMsg) {
		// Not yet implemented
	}

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

	/**
	 * @access protected
	 * @param objects
	 */
	protected function createObjects($objects) {
		// Not yet implemented
	}

	/**
	 * @access protected
	 * @param objects
	 */
	protected function updateObjects($objects) {
		// Not yet implemented
	}

	/**
	 * @access protected
	 * @param objects
	 */
	protected function removeObjects($objects) {
		// Not yet implemented
	}

	/**
	 * Removes the page references to removed objects
	 * @access protected
	 * @param object
	 */
	protected function removePageReferences($object) {
		// Not yet implemented
	}

	/**
	 * Itera, por reflexão as propriedades públicas definidas na página q está sendo carregada
	 * e adiciona no array cachedObjs
	 * @access public
	 */
	public function saveState() {
		// Not yet implemented
	}

	/**
	 * Returns the current sync message based on the changes that have happened until the time the function is called
	 * @access public
	 * @return system.postback.SyncMessage
	 * @ReturnType system.postback.SyncMessage
	 */
	public function getSyncMessage() {
		// Not yet implemented
	}

	/**
	 * Removes all changes and sets page state to sincronized
	 * @access public
	 */
	public function setSynchronized() {
		// Not yet implemented
	}
}
?>