<?php

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class SyncMessage {

	/**
	 * @var array
	 */
	protected $newObjs;
	
	/**
	 * @var array
	 */
	protected $modObjs;
	
	/**
	 * @var array
	 */
	protected $delObjs;
	
	/**
	 * Constructor.
	 * Parses the text and extract all the info in it
	 * @param string $message [optional]
	 */
	public function __construct($message = array())
	{
		$this->newObjs = array();
		$this->modObjs = array();
		$this->delObjs = array();
		
		if(isset($message['n']))
			$this->newObjs = $message['n'];
		
		if(isset($message['m']))
			$this->modObjs = $message['m'];
		
		if(isset($message['r']))
			$this->delObjs = $message['r'];
	}
	
	public function getNewObjects()
	{
		return $this->newObjs;
	}
	
	public function getModifiedObjects()
	{
		return $this->modObjs;
	}
	
	public function getRemovedObjects()
	{
		return $this->delObjs;
	}
}