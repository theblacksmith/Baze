<?php

require_once 'Zend/Session/SaveHandler/Interface.php';

require_once 'ISessionSaveHandler.interface.php';

class ZendSaveHandlerProxy implements Zend_Session_SaveHandler_Interface
{
	/**
	 * @var ISessionSaveHandler
	 */
	private $sh;
	
	public function __construct(ISessionSaveHandler $saveHandler)
	{
		$this->sh = $saveHandler;
	}
	
	/**
     * Open Session - retrieve resources
     *
     * @param string $save_path
     * @param string $name
     */
    public function open($save_path, $name)
	{
		$this->sh->open($save_path, $name);
	}

    /**
     * Close Session - free resources
     *
     */
    public function close()
	{
		$this->sh->close();
	}

    /**
     * Read session data
     *
     * @param string $id
     */
    public function read($id)
	{
		$this->sh->read($id);
	}

    /**
     * Write Session - commit data to resource
     *
     * @param string $id
     * @param mixed $data
     */
    public function write($id, $data)
	{
		$this->sh->write();
	}

    /**
     * Destroy Session - remove data from resource for
     * given session id
     *
     * @param string $id
     */
    public function destroy($id)
	{
		$this->sh->destroy($id);
	}

    /**
     * Garbage Collection - remove old session data older
     * than $maxlifetime (in seconds)
     *
     * @param int $maxlifetime
     */
    public function gc($maxlifetime)
	{
		$this->sh->gc($maxlifetime);
	}
}