<?php
/**
 * Arquivo ServerViewState.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

require_once 'system/collections/Map.class.php';

/**
 * Classe ServerViewState
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class ServerViewState
{
	/**
	 * URL to redirect browser to, if needed.
	 *
	 * @var string
	 */
	private $redirectURL;


	/**
	 *  A pointer to the page containing this.
	 *
	 * @var Page $page
	 */
	public $page;
	
	/**
	 * @AttributeType system.collections.Map
	 * 
	 * @var Map
	 */
	private $modObjects;

	function __construct(Page $page)
	{
		$this->page = $page;

		$this->commands = new Collection();

		$this->modObjects = new Map();
	}
	
	public function addModifiedObject(Component $obj)
	{
		$this->modObjects->add($obj, $obj->getId());
	}
	
	public function removeModifiedObject(Component $obj)
	{
		$this->modObjects->remove($obj->getId());
	}
	
	private function formatProperty($key, $value)
	{
		$key = strtolower($key);
		
		if ($value instanceof Event)
			$value = (string)$value;
		
		$value = json_encode($value);
			
		if($key == 'class')
			$key = 'className';

		return array($key, $value);
	}

	public function setRedirectURL($url)
	{
		if (_IS_POSTBACK)
		{
			$this->request->setRedirectURL($url);
		}
		else
		{
			header("Location: $url");
			exit;
		}
	}

	public function getRedirectURL()
	{
		return $this->redirectURL;
	}

	/**
	 * Enter description here...
	 *
	 * @param CommandCall $comm
	 * @param boolean $unique - if true, only one instance of this action will be allowed. Other additions of this action will always overwrite the same command
	 * @return int - index of the created command
	 */
	public function addCommand(CommandCall $comm, $unique = false)
	{
		$tab = array(1 => "\t", 2 => "\t\t", 3 => "\t\t\t", 4 => "\t\t\t\t", 5 => "\t\t\t\t\t");

		$cmdTag = 	$tab[2].'<command name="' . $comm->name . '" executeon="'.$comm->executeOn.'">'. NL .
					$tab[3].'<params>'. NL;

		foreach ($comm->arguments as $param => $value)
		{
			$cmdTag .=	$tab[4].'<param name="'.$param.'">' . $value . '</param>'. NL;
		}

		$cmdTag .= $tab[3].'</params>'.NL.$tab[2].'</command>'. NL;

		$this->commands->add($cmdTag, $comm->id);

		return $this->commands->indexOf($comm->id);
	}

	/**
	 * Enter description here...
	 *
	 * @param mixed $id
	 * @return boolean
	 */
	public function removeCommand($id)
	{
		return	$this->commands->remove($id);
	}

	/**
	 * Enter description here...
	 *
	 * @return Collection
	 */
	public function getCommands()
	{
		return $this->commands;
	}

	/**
	 * Function addJSFunction
	 * adiciona uma chamada JavaScript que deve ser feita pelo cliente.
	 *
	 * @param string $func (o nome da função)
	 * @param array $parms (os parâmetros, EXATAMENTE como vão ser
	 * chamados no JavaScript - você deve explicitamente colocar aspas
	 * caso queira passar uma string para o JavaScript (ex: '\'teste\''),
	 * e arrays devem ser sempre strings da forma
	 * [elem1, elem2, elem3, ...])
	 */
	public function addJSFunction($func, $parms)
	{
		$parms = array_map('trim', $parms);

		$cmd = new CommandCall(array( "id" => "callFunction_".$func,
									"name" => JSAPICommand::CallFunction,
									"arguments" => array( "func" => $func, "args" => implode(',', $parms)),
									"executeOn" => MessageParsePhase::OnMessageEnd));

		$this->addCommand($cmd);
	}
	
	/**
	 * Clear all modifications
	 */
	public function setSynchronized()
	{
		$objs = $this->modObjects->toArray();
		
		foreach($objs as $o) {
			$o->getState()->setSynchronized();
		}
		
		$this->modObjects->removeAll();
		$this->commands->clear();
	}


	public function getGUIStateUpdate()
	{
		global $sysLogger;

		header("Content-Type: text/json; charset=UTF-8", true);

		$xml = '{'.NL;

		$syncMsg = $this->getSyncMessage();
		$cmdMsg = $this->getCommandMessage();

		if($syncMsg) {
			$xml .= '  "SyncMsg": ' . $syncMsg . ',' . NL;
		}

		if($cmdMsg) {
			$xml .= '  "CmdMsg": ' . $cmdMsg . NL;
		}

		$xml = trim($xml, ','.NL).NL;
		
		$xml .= '}';

		return $xml;
	}

	/**
	 * Function GetViewState
	 *
	 */
	public function getSyncMessage()
	{
		FB::group("Building SyncMessage");
		if($this->modObjects->count() == 0)
			return false;
			
		$tab = array(1 => "  ", 2 => "    ", 3 => "      ", 4 => "        ", 5 => "          ");
		
		$modObjs = $this->modObjects->toArray();
		$newObjs = array();
		$delObjs = array();
		
		foreach($modObjs as $m) {
			$newObjs = array_merge($newObjs, $m->getState()->getNewChildren());
			$delObjs = array_merge($delObjs, $m->getState()->getRemovedChildren());
		}
		
		FB::info("Stats: ".count($modObjs)." modified, ".count($newObjs)." new and " . count($delObjs) . " removed.");
		
		$msg = '{' . NL;

		/*********************
		 * NEW objects
		 */

		$msg .= $tab[2].'"n": ['. NL;
		
		foreach($newObjs as $newObj) if(array_search($newObj, $delObjs, true) === false)
		{
			if(($phpClass = $newObj->getAttribute('php:class')) == null)
				$phpClass = get_class($newObj);

			$msg .= "{$tab[3]}{".NL;
			$msg .= "{$tab[4]}\"c\": \"{$phpClass}\"," . NL . 
							"{$tab[4]}\"id\": \"".$newObj->getId()."\",". NL .
							"{$tab[4]}\"p\": {".NL;

			$props = $newObj->getAttributesToRender();

			foreach($props as $key => $value)
			{
				list($key, $value) = $this->formatProperty($key, $value);
				$msg .= "$tab[5]\"{$key}\": {$value}," . NL;
			}

			$msg = trim($msg, ', '.NL).NL;
			$msg .= $tab[4].'}'. NL . // properties
							$tab[3].'},'. NL; // object
		}

		$msg = trim($msg, ', '.NL).NL;
		$msg .= $tab[2].'],'. NL; // newObjects
			
		/*********************
		 * MODIFIED objects
		 */

		$msg .= $tab[2].'"m": ['. NL;
		
		foreach($modObjs as $obj)
		{
			$atts = $obj->getAttributesToRender();
			$children = $obj->getState()->getNewChildren();
			
			if(count($atts) + count($children) < 1)
				continue;
				
			$msg .= "{$tab[3]}{" . NL .
							"{$tab[4]}\"id\": \"" . $obj->getId() . "\"";
			
			if(count($atts))
			{
				$msg .= ','.NL;
				$msg .= "{$tab[4]}\"p\": {" . NL;
				
				foreach($atts as $key => $value)
				{
					list($key, $value) = $this->formatProperty($key, $value);
					$msg .= "$tab[5]\"{$key}\": {$value}," . NL;
				}
				
				$msg = trim($msg, ', '.NL).NL;
				$msg .= $tab[4].'}'.NL; // properties
			}
			
			if(count($children))
			{
				$msg .= ','.NL;
				// new children
				$msg .= "{$tab[4]}\"nc\": [" . NL . $tab[5];
				
				foreach ($children as $c)
					$msg .= '"'.$c->getId().'",';
					
				$msg = trim($msg, ',').NL;
				$msg .= $tab[4].']'.NL; // new children
			}
			
			$msg .= $tab[3].'},'.NL; // object
		}
		
		$msg = trim($msg, ', '.NL).NL;
		$msg .= $tab[2].'],'. NL;

		/*********************
		 * DELETED objects
		 */

		$msg .= "{$tab[2]}\"r\": [". NL . $tab[3];
			
		foreach ($delObjs as $del) if(array_search($del, $newObjs, true) === false)
			$msg .= '"'.$del->getId().'",';
			
		$msg = trim($msg, ',').NL;
		
		$msg .= "{$tab[2]}]". NL .
			"{$tab[1]}}";
		
		$this->setSynchronized();

		FB::groupEnd();
		return $msg;
	}

	public function getCommandMessage()
	{
		$tab = array(1 => "\t", 2 => "\t\t", 3 => "\t\t\t", 4 => "\t\t\t\t", 5 => "\t\t\t\t\t");

		if($this->commands->size() > 0)
		{
			$msg = '<serverMessage type="command">' . NL .
					$tab[1].'<commands>'. NL;

			$count = $this->commands->size();

			for($i=0; $i < $count; $i++) {
				$msg .= $this->commands->item($i) . NL;
			}

			$msg .= $tab[1].'</commands>'. NL .
					'</serverMessage>'. NL;

			return $msg;
		}

		return false;
	}
}