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

	function __construct(Page $page)
	{
		$this->page = $page;

		$this->commands = new Collection();

		$this->oldObjects = new Map();
		$this->newObjects = new Map();
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
	
	private function propValueToString($key, $value, &$eval)
	{
		$eval = "0";
		
		if ($value instanceof Event)
		{
			$value = 'function(event){' . $value->getXHTML(false) . '}';
			$eval = "1";
		}
		else if (is_bool($value))
		{
			$value == true ? $value = $key : $value = false;
		}
		else if (($value instanceof Component))
		{
			if($value instanceof Page || $value->get("id") == "")
				$value = false;
			else
				$value = $value->get("id");
		}
		else if($key == 'class')
		{
			$key = 'className';

			if(is_array($value))
				$value = join(' ', $value);
			else
				echo "wrong class: " . $key . "\n";
		}
		
		if(is_array($value))
			$value = FastJSON::encode($value);
			
		return $value;
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
			$o->_setSynchronized();
		}
		
		$this->oldObjects->addAll($this->newObjects);
		$this->newObjects->removeAll();
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
			$xml .= '"SyncMsg": ' . $syncMsg;
		}

		if($cmdMsg) {
			$xml .= '"CmdMsg": ' . $cmdMsg . NL;
		}

		$xml = trim($xml, ',');
		
		$xml .= '}';

		return $xml;
	}

	/**
	 * Function GetViewState
	 *
	 */
	public function getSyncMessage()
	{
		if($this->newObjects->count() == 0 && $this->modObjects->count() == 0 && $this->oldObjects->count() == 0)
			return false;
			
		$tab = array(1 => "  ", 2 => "    ", 3 => "      ", 4 => "        ", 5 => "          ");

		$msg = '{' . NL;

		/*********************
		 * NEW objects
		 */

		$msg .= $tab[2].'"newObjects": ['. NL;
		$objs = $this->newObjects->toArray();
		
		foreach($objs as $newObj)
		{
			if(($phpClass = $newObj->get('phpclass')) == null)
				$phpClass = get_class($newObj);

			$msg .= "{$tab[3]}\"klass\": '{$phpClass}'," . NL . 
							"{$tab[3]}\"id\": '".$newObj->get("id")."',". NL .
							"\"properties\": [".NL;

			$props = $newObj->getAttributes();

			foreach($props as $key => $value)
			{
				$key = strtolower($key);

				$value = $this->propValueToString($key, $value, $eval);
				$msg .= "$tab[5]{\"name\": '{$key}', \"eval\": {$eval}, \"value\": '{$value}'}," . NL;
			}

			$msg = trim($msg, ', '.NL);
			$msg .= $tab[4].']'. NL . // properties
							$tab[3].'}'. NL; // object
		}

		$msg .= $tab[2].'],'. NL; // newObjects

		$this->newObjects->removeAll();
			
		/*********************
		 * MODIFIED objects
		 */

		$msg .= $tab[2].'"modifiedObjects": ['. NL;
		
		$objs = $this->modObjects->toArray();
		foreach($objs as $obj)
		{
			$msg .= "{$tab[3]}{" . NL .
							"{$tab[4]}\"id\": \"" . $obj->getId() . "\"," . NL .
							"{$tab[4]}\"properties\": [" . NL;
			
			foreach($obj->getAttributesToRender() as $key => $value)
			{
				$key = strtolower($key);

				$value = $this->propValueToString($key, $value, $eval);
				$msg .= "$tab[5]{\"name\": \"{$key}\", \"eval\": {$eval}, \"value\": \"{$value}\"}," . NL;
			}
			
			$msg = trim($msg, ', '.NL);
			$msg .= $tab[4].']'.NL. // properties
							$tab[3].'},'.NL; // object
		}
		
		$msg = trim($msg, ', '.NL);
		$msg .= $tab[2].'],'. NL;

		/*********************
		 * DELETED objects
		 */

		$msg .= "{$tab[2]}\"removedObjects\": [". NL;
			
		$msg = trim($msg, ',');
		$msg .= "{$tab[2]}]". NL .
			'}';

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