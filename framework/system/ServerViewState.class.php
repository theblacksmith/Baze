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

import("system.collections.Map");


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
	 * Pointer to DOMDocument class.
	 *
	 * @var DOMDocument class
	 */
	public $clientMessage;

	/**
	 * Pointer to DOMDocument class.
	 *
	 * @var DOMDocument class
	 */
	public $serverMessage;

	/**
	 * Client Message Attributes
	 */

	/**
	 * The event.
	 *
	 * @var string
	 */
	private $event;

	/**
	 * URL to redirect browser to, if needed.
	 *
	 * @var string
	 */
	private $redirectURL;

	/**
	 * Array of commands for the client.
	 *
	 * @var Collection
	 */
	private $commands;

	/**
	 * The target object.
	 *
	 * @var string
	 */
	private $eventTarget;

	/**
	 * The argument via postback.
	 *
	 * @var array
	 */
	private $eventArguments;

	/**
	 *  A pointer to the page containing this.
	 *
	 * @var Page $page
	 */
	public $page;

	/**
	 * Array with the result of a call of serialize function
	 * for each object in the page in the last call of SaveState().
	 * The elements are indexed by the id of the variables.
	 *
	 * @var array
	 */
	private $cachedObjs = array();

	/**
	 * @var Map
	 */
	private $oldObjects;

	/**
	 * @var Map
	 */
	private $newObjects;

	/**
	 * @var Map
	 */
	private $modObjects;

	/**
	 * @var Map
	 */
	private $delObjects;

	private $SyncMessage;
	private $EventMessage;

	/**
	 * Constructor
	 *
	 * @param Page $page
	 */
	function __construct(Page $page)
	{
		$this->page = $page;

		$this->commands = new Collection();

		$this->eventArguments = array();

		$this->oldObjects = new Map();
		$this->newObjects = new Map();
		$this->modObjects = new Map();
		$this->delObjects = new Map();
	}
	
	public function addModifiedObject(Component $obj)
	{
		$this->modObjects->add($obj, $obj->getId());
	}
	
	public function removeModifiedObject(Component $obj)
	{
		$this->modObjects->remove($obj->getId());
	}

	public function addChange(Component $obj, $data)
	{
		$chg = null;

		if(strtolower($obj->get("runat")) != "server")
			return;

		if(!array_key_exists("changeType", $data))
			throw new Exception("Change type not defined");

		switch($data["changeType"])
		{
			case ChangeType::PROPERTY_CHANGED :
				$chg = new Change(ChangeType::PROPERTY_CHANGED, array("propertyName" => $data["propertyName"],
																 "oldValue" => $data["oldValue"],
																 "newValue" => $obj->get($data["propertyName"])));
				break;

			case ChangeType::CHILD_ADDED :
				$chg = new Change(ChangeType::CHILD_ADDED, array("child" => $data["child"]));
				break;

			case ChangeType::CHILD_REMOVED :
				$chg = new Change(ChangeType::CHILD_REMOVED, array("child" => $data["child"]));
				break;

			default :
				throw new Exception("Change type '" . $data["changeType"] . "' is not supported");
		}

		$chgObj = null;

		if(!$this->modObjects->get($obj->get("id"))) {
			$this->modObjects->add(array("realObject" => $obj, "changes" => new Collection()), $obj->get("id"));
			$chgObj = $this->modObjects->get($obj->get("id"));
		}
		else {
			$chgObj = $this->modObjects->get($obj->get("id"));
		}

		// verifica se existe um conflito de alterações
		$_change = $chgObj["changes"]->get($chg->getId());

		if($_change) {

			//if($chg->getType() != ChangeType::PROPERTY_CHANGED)
			//	throw new Exception("Trying to merge change (".$chg->getId().") of type " . $chg->getType() . " with " . $_change->getId() . " of type " .$_change->getType());

			// verifica se alteração atual anula uma alteração anterior
			if($_change->isMirror($chg)) {
				// neste caso deleta a alteração anterior
				$chgObj["changes"]->remove($chg->getId());
			}
			else {
				// senão faz um merge das alterações
				$_change->mergeWith($chg);
			}
		}
		else {
			// adiciona a alteração
			$chgObj["changes"]->add($chg, $chg->getId());
		}
	}

	public function addNewObject(Component $c)
	{
		if($this->oldObjects->get($c->get("id")))
			return;

		$this->newObjects->add($c, $c->get("id"));
	}

	public function addRemovedObject(Component $c)
	{
		$this->delObjects->add($c, $c->get("id"));
	}

	/**
	 * Function LoadViewState
	 *
	 * @param $clientState string xml string
	 */
	public function loadViewState($clientState)
	{
		$this->event = $this->eventArguments = $this->eventTarget = null;
		/*
		 Loading uploaded files in components
		*/
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

		if($clientState == "")
			return;

		$clientState = stripslashes($clientState);
		$this->clientMessage = json_decode($clientState, true); // FastJSON::decode($newValue);

		$this->SyncMessage = isset($this->clientMessage['SyncMsg']) ? $this->clientMessage['SyncMsg'] : null;
		$this->EventMessage = isset($this->clientMessage['EvtMsg']) ? $this->clientMessage['EvtMsg'] : null;

		if($this->SyncMessage != null)
		{
			if(isset($this->SyncMessage['n']))
				$this->createObjects($this->SyncMessage['n']);
				
			if(isset($this->SyncMessage['m']))
				$this->updateObjects($this->SyncMessage['m']);
				
			if(isset($this->SyncMessage['r']))
				$this->removeObjects($this->SyncMessage['r']);
		}

		// @TODO NEXT check why calling this makes only the first postback work
		$this->setSynchronized();
	
		if($this->EventMessage != null)
		{
			/*
			* Getting event
			*/
			$this->event = "on" . ucfirst($this->EventMessage["type"]);

			/*
			* Getting event target
			*/
			$this->eventTarget  = $this->EventMessage["target"];

			/*
			* Getting event argument
			*/
			foreach($this->EventMessage['args'] as $k => $v)
			{
				$this->eventArguments[$k] = $v;
			}
		}
		
		// A página está na sessão
		$this->saveState($this->page);
	}

	protected function removeObjects($objects)
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

	protected function updateObjects($objects)
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
/*
				switch($cType)
				{
					case 'propertyChanged':
						break;

					case 'childAdded':
						$childId = $change->getAttribute('childId');
						$childObj = $this->page->$childId;

						$auxiliarObj->addChild($childObj);
						break;

					case 'childRemoved':
						$childId = $change->getAttribute('childId');
						$childObj = $this->page->$childId;

						$auxiliarObj->removeChild($childObj);
						break;
				}
				*/
			}
		}
	}

	protected function createObjects($objects)
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

	/**
	 * Function SaveState
	 *
	 * Itera, por reflexão as propriedades públicas definidas na página q está sendo carregada
	 * e adiciona no array cachedObjs
	 */
	public function saveState(Page $page)
	{
		/*
		$reflete = new ReflectionClass(get_class($page));
		$vars = $reflete->getProperties();

		for($i = 0; $i < count($vars); $i++)
		{
			if($vars[$i]->isPublic() && $vars[$i]->getDeclaringClass() == $reflete)
			{
				$id = $vars[$i]->getName();
				if( is_object($page->get($id)) && $this->delObjects->contains($id))
				{
					$this->cachedObjs[$id] = clone $page->get($id);
				}
			}
		}
*/
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
		
		$objs = $this->delObjects->toArray();
		foreach ($objs as $obj)
			$msg .= '"'.$obj->getId().'",';
			
		$msg = trim($msg, ',');
		$msg .= "{$tab[2]}]". NL .
			'}';

		return $msg;
	}


	public function setSynchronized()
	{
		$objs = $this->modObjects->toArray();
		foreach($objs as $o) {
			$o->_setSynchronized();
		}
		
		$this->oldObjects->addAll($this->newObjects);
		$this->newObjects->removeAll();
		$this->modObjects->removeAll();
		$this->delObjects->removeAll();

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

		$sysLogger->debug("ServerViewState::getGUIStateUpdate - Trying to generate XML:", __FILE__, __LINE__+4);
		// carrega o xml
		try
		{
			$xmldoc = new DOMDocument();
			$xmldoc->preserveWhiteSpace = false;

			if($xml != "")
			{
				$xmldoc->loadXML($xml);
				$xml = $xmldoc->saveXML();
			}
		}
		catch(DOMException $e)
		{
			trigger_error($e->getMessage(), $e->getCode());
			trigger_error("Malformed XML:<BR>\n" . $xml, E_USER_ERROR);
		}

		return $xml;
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

	/**
	 * Function getEvent
	 *
	 * @return string $event The name of the last event happened
	 */
	public function getEvent()
	{
		return $this->event;
	}

	/**
	 * Function getEventTarget
	 *
	 * @return string The id of the target of the last event happened
	 */
	public function getEventTarget()
	{
		return $this->eventTarget;
	}

	/**
	 * Function getEventArgument
	 *
	 * @return string $event The arguments passed by the target the
	 */
	public function getEventArguments()
	{
		return $this->eventArguments;
	}

	public function setRedirectURL($url)
	{
		if (_IS_POSTBACK)
		{
			$this->redirectURL = $url;

			$this->addCommand(new CommandCall(array("name" => JSAPICommand::Redirect,
													"arguments" => array("url" => $url),
													"executeOn" => MessageParsePhase::BeforeCreateObjects)), true);
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
}

/**
 * Classe Change
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class Change
{
	protected $type = null;
	protected $data = null;

	function __construct($type, $data)
	{
		$this->type = $type;
		$this->data = $data;
	}

	public function getXML()
	{
		$str = "";

		switch($this->type)
		{
			case ChangeType::CHILD_ADDED :
				$str .= '<change type="'.ChangeType::CHILD_ADDED.'" childId="' . $this->data["child"]->get("id") . '" />';
				break;

			case ChangeType::CHILD_REMOVED :
				$str = '<change type="'.ChangeType::CHILD_REMOVED.'" childId="' . $this->data["child"]->get("id") . '" />';
				break;

			case ChangeType::PROPERTY_CHANGED :
				$propType = "";

				//TODO: remover quando o atributo 'class' for modificado para 'className' no component
				if($this->data["propertyName"] == 'class')
				{
					$this->data["propertyName"] = 'className';

					if(is_array($this->data["newValue"]))
					{
						$this->data["newValue"] = join(' ',$this->data["newValue"]);
					}
				}

				if(is_array($this->data["newValue"])) {
					$propType = "array"; }
				else {
					$propType = gettype($this->data["newValue"]); }

				$str = 	'<change type="'.ChangeType::PROPERTY_CHANGED.'"' .
								' propertyName="' . $this->data["propertyName"] . '"' .
								' propertyType="'. $propType .'">';

				if(is_object($this->data["newValue"]))
				{
					if(method_exists($this->data["newValue"], "getPropertiesList"))
						$str .= $this->data["newValue"]->getPropertiesList();
					else if(method_exists($this->data["newValue"], "get"))
						$str .= $this->data["newValue"]->get("id");
					else
					{
						$str .= "Couldn't convert object to string";
					}
				}
				else
				{
					if(is_array($this->data["newValue"]))
						$str .= FastJSON::encode($this->data["newValue"]);
					else if($this->data["newValue"] != "" && $this->data["newValue"] != null)
					{
						$str .= $this->data["newValue"];
					}
				}

				$str .= '</change>';
		}

		return $str;
	}

	/**
	 * O id foi criado dessa forma para que se possa perceber quando uma alteração se chocar com outra.
	 */
	public function getId()
	{
		switch($this->type)
		{
			case ChangeType::PROPERTY_CHANGED :
				return "PropChange_" . $this->data["propertyName"];

			case ChangeType::CHILD_ADDED :
				return "ChildAddOrRemove_" . $this->data["child"]->get("id");

			case ChangeType::CHILD_REMOVED :
				return "ChildAddOrRemove_" . $this->data["child"]->get("id");
		}
	}

	public function getType()
	{
		return $this->type;
	}

	/**
	 * Function isMirror
 	 * 		Checa se a alteração desta instância é anulada pela alteração do objeto passado como parâmetro.
	 *
	 * @param {Change} chg
	 */
	public function isMirror($chg)
	{
		switch($this->type)
		{
			case ChangeType::PROPERTY_CHANGED :
				if($chg->type == ChangeType::PROPERTY_CHANGED && $this->data["newValue"] == $chg->data["oldValue"] && $this->data["oldValue"] == $chg->data["newValue"]) {
					return true; }
				break;

			case ChangeType::CHILD_ADDED :
				if($chg->type == ChangeType::CHILD_REMOVED && $chg->data["child"]->get("id") == $this->data["child"]->get("id")) {
					return true; }
				break;

			case ChangeType::CHILD_REMOVED :
				if($chg->type == ChangeType::CHILD_ADDED && $chg->data["child"]->get("id") == $this->data["child"]->get("id")) {
					return true; }
				break;
		}

		return false;
	}

	/**
	 * Function mergeWith
	 * 		Soma duas alterções transformando-as em uma só.
	 *
	 * @param {Change} chg
	 */
	function mergeWith($chg)
	{
		//if($this->type !== ChangeType::PROPERTY_CHANGED) {
		//	throw new Exception("Only property changes can be merged"); }

		if($this->type !== $chg->type) {
			throw new Exception("Different types of changes can not be merged"); }

		switch($this->type)
		{
			case ChangeType::PROPERTY_CHANGED :
					$this->data["newValue"] = $chg->data["newValue"];
					break;

			case ChangeType::CHILD_ADDED :
					//$this->data["child"] = $chg->data["child"];
					break;

			case ChangeType::CHILD_REMOVED :
					//$this->data["child"] = $chg->data["child"];
					break;
		}
	}

};

/**
 * Classe ChangeType
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class ChangeType extends Enumeration {
	const PROPERTY_CHANGED = 1;
	const CHILD_ADDED = 2;
	const CHILD_REMOVED = 3;
}

/**
 * Classe MessageParsePhase
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class MessageParsePhase
{
	const BeforeCreateObjects = 701;
	const BeforeModifyObjects = 702;
	const BeforeDeleteObjects = 703;
	const OnMessageEnd = 704;
};