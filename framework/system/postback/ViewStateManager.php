<?php

import('system.postback.SyncMessage');
import('system.postback.EventMessage');

/**
 * @access public
 * @author svallory
 * @package system.postback
 */
class ViewStateManager
{
	/**
	 * @AttributeType system.web.ui.page.Page
	 * 
	 * A pointer to the page containing this.
	 * 
	 * @var Page $page
	 */
	private $page;
	
	/**
	 * @var boolean
	 */
	private $sincronized;
	
	/**
	 * @var ServerViewState
	 */
	private $serverViewState;
	

	/**
	 * Constructor
	 * 
	 * @access public
	 * @param Page $page
	 */
	function __construct(Page $page)
	{
		$this->page = $page;
		$this->serverViewState = $page->getServerViewState();
	}

	/**
	 * Function LoadViewState
	 *
	 * @param $clientState string xml string
	 */
	public function loadViewState(SyncMessage $syncMsg)
	{		
		// Loads the uploaded files
		// @todo move it to request or somewhere more appropriate
		foreach($_FILES as $k => $f)
		{
			if ($_FILES[$k]["error"] == UPLOAD_ERR_OK && is_uploaded_file($_FILES[$k]["tmp_name"])) {
				$fileUpComp = $this->page->get($k);

				if($fileUpComp instanceof FileUpload)
				{
					foreach($_FILES[$k] as $prop => $val) {
						if($prop != "tmp_name")
							$fileUpComp->set("file". ucfirst(strtolower($prop)), $val);
					}

					$fileUpComp->set("fileTmpPath", System::addUploadedFile($_FILES[$k]));
				}
			}
			else
			{
				trigger_error("Upload error: " . $_FILES[$k]["error"], E_USER_NOTICE);
			}
		}

		$this->createObjects($syncMsg->getNewObjects());
		$this->updateObjects($syncMsg->getModifiedObjects());
		$this->removeObjects($syncMsg->getRemovedObjects());
	}

	protected function removeObjects($objects = array())
	{
		foreach($objects as $objId)
		{
			//pegando o objeto
			$c = $this->page->$objId;

			if($c instanceof Container)
			{
				$this->removeObjects($c->Children);
			}
				
			$parent = $c->getContainer();
			if($parent)
				$parent->removeChild($c);
				
			unset($this->page->$objId);
			
			if(!_IS_POSTBACK)
				$this->addRemovedObject($c);
		}
	}

	protected function updateObjects($objects = array())
	{
		global $sysLogger;

		foreach($objects as $obj)
		{
			$objId = $obj['id'];
			$props = $obj['properties'];

			//pegando o objeto
			$auxiliarObj = $this->page->$objId;

			if(!$auxiliarObj)
			{
				trigger_error("Erro atualizando componentes. Não foi possível encontrar o objeto " . $objId . " na página", E_USER_ERROR);
				exit;
			}

			//aplicando as modificações
			foreach($props as $n => $v)
			{
					$auxiliarObj->setAttribute($n, $v);
			}
		}
	}

	protected function createObjects($objects = array())
	{
		foreach($objects as $obj)
		{
			$id = $obj['id'];
			$klass = $obj['class'];

			try {
				//instanciando o objeto
				$auxiliarObj = new $klass();
				$auxiliarObj->set("id", $id);
			}
			catch (Exception $e) {
				throw $e;
			}

			//atribuindo as propriedades
			foreach($obj['properties'] as $n => $v)
			{
				$auxiliarObj->set($n, $v);
			}

			//inserindo objeto na página
			$this->page->$id = $auxiliarObj;
		}
	}

	public function setSynchronized()
	{
		$this->serverViewState->setSynchronized();
	}

	/**
	 * Removes the page references to removed objects
	 * @access protected
	 * @param object
	 */
	protected function removePageReferences($object) {
		// Not yet implemented
	}
}