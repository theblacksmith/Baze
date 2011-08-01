<?php
/**
 * Arquivo Page.class.php
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
import( 'system.web.ui.HtmlComponent' );
import( 'system.web.ui.Literal' );
import( 'system.web.ui.page.Head' );
import( 'system.web.ui.page.Body' );
import( 'system.ServerViewState' );

/**
 * Classe Page
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web.page
 */
class Page extends Component implements IRenderable
{
	/**
	 * @var ServerViewState
	 */
	protected $viewState;

	/**
	 * @var Head
	 * @desc The page head component
	 */
	protected $head;

	/**
	 * @var Collection
	 * @desc The page javascripts
	 */
	protected $scripts;

	/**
	 * @var Collection
	 * @desc The page css tags or links
	 */
	protected $css;

	/**
	 * @var Body
	 * @desc The page body component
	 */
	protected $body;

	/**
	 * @var array
	 * @desc an array with pointers to all page components
	 */
	protected $_c;

	/**
	 * @var Event
	 * @desc Occurs before the page is rendered
	 */
	protected $_onPreRender;

	/**
	 * @var Event
	 * @desc Occurs after the page is rendered
	 */
	protected $_onRender;

	/**
	 * @var string
	 * @desc The design file source code
	 */
	protected $source;

	/**
	 * @var DOMDocument
	 * @desc The DOM document for this page
	 */
	public $document;

	public function __construct($source = "")
	{
		$this->_c = array('id' => array(), 'o' => array());
		$this->viewState = new ServerViewState($this);

		$this->body = new Body($this);
		$this->head = new Head($this);
		$this->scripts = array();
		$this->css = array();

		$this->parse($source);

		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$protocol = 'https://';
		else
			$protocol = 'http://';

		// Adicionando os scripts necessários
		$this->addScript(NL .
		'<script type="text/javascript">' . NL .
		'	NB_LIB_URL = "' . NB_LIB_URL . '";' . NL .
		'</script>', 0);

		$appMode = System::getApp()->getConfig()->getMode();

		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']),"msie") > -1)
		{
			$this->addScript(NB_LIB_URL . '/js/hacks/IEHacks.js',1);

			if($appMode == AppMode::$DEVELOPMENT || isset($_GET['devTest']))
				$this->addScript(NB_LIB_URL . '/js/util/firebug/firebug.js',2);
		}
		else
			$this->addScript(NB_LIB_URL . '/js/hacks/MozHacks.js',2);

		if($appMode == AppMode::$DEVELOPMENT || isset($_GET['devTest']))
		{
			$this->addScript(NB_LIB_URL . '/js/system/package-src.js',10);
			$this->addScript(NB_LIB_URL . '/js/web/package-src.js',20);
		}
		else
		{
			$this->addScript(NB_LIB_URL . '/js/BazeJSAPI.js');
		}

		$this->addCSS(NB_LIB_URL . '/css/loading.css');
	}

	public function init(){}		//	| 1ª vez
	public function load(){}		//	|	| PostBack	| Última vez
	public function unload(){}		//	|	|			|
	//protected function close(){}		//					|

	protected function parse($source)
	{
		// lê o buffer
		$xml = '<?xml version="1.0" encoding="utf-8" ?>' . $source;

		// carrega o xml
		try
		{
			$this->document = new DOMDocument();
			$this->document->preserveWhiteSpace = false;

			if($xml != "")
				$this->document->loadXML($xml);
			else
				throw new ParserException(Msg::EmptyInterfaceXml, get_class($this));
		}
		catch(DOMException $e)
		{
			throw ParserException::fromException($e);
		}

		// html element
		$pageElem = $this->document->documentElement;

		$nl = new DOMNodeList();

		// getting page attributes
		$nl = $pageElem->attributes;

		foreach($nl as $attr)
		{
			$this->{$attr->nodeName} = $attr->nodeValue;
		}

		// html child nodes

		$nl = $pageElem->childNodes;

		for($i=0; $i < $nl->length; $i++)
		{
			$elem = $nl->item($i);

			// baze components
			if($elem->prefix == 'php')
			{
				$id = $elem->getAttribute('id');

				if($id != '' && isset($this->$id))
					throw new ParserException(Msg::DuplicatedComponentId, array(get_class($this), $id));

				$comp = new $elem->localName($this);
				$comp->parse($elem);

				$this->addComponent($prop);
			}
			// html tags
			else if($elem->nodeType == XML_ELEMENT_NODE)
			{
				if(strcasecmp($elem->tagName, 'head'))
				{
					$this->head = new Head($this);
					$this->head->parse($elem);
				}
				else if(strcasecmp($elem->tagName, 'body'))
				{
					$this->body = new Body($this);
					$this->body->parse($elem);
				}
			}
		}
	}

	/**
	 * Adds a component to the page
	 *
	 * @param Component $c
	 */
	public function addComponent(Component $c)
	{
		if(($pos = array_search($c->getId(), $this->_c['id'])) !== false)
		{
			if($this->_c['o'] === $pos) // if the component is already on the page, just return
				return;

			throw new BazeRuntimeException(Msg::DuplicatedComponentId, array(get_class($this), $c->getId()));
		}

		if($c->getPage() !== $this)
			$c->setPage($this);

		$this->_c['id'][] = &$c->_getId();
		$this->_c['o'][] = $c;
	}

	/**
	 * Function handle
	 *
	 * @param Object $obj The target object
	 * @param string $event The name of the event that happenedbreaks
	 */
	protected function handleEvent()
	{
		if($this->viewState->getEvent() != null)
		{
			global $sysLogger;
			$obj = $this->viewState->getEventTarget();
			$event = $this->viewState->getEvent();
			$args = $this->viewState->getEventArguments();

			$sysLogger->debug("Handling Event - " . $obj ." " . $event,__FILE__,__LINE__);

			$this->$obj->$event($args);
		}
	}

	/**
	 *	Build Graphical User Interface
	 *
	 *	Constrói a interface a partir do xhtml da página para não perder tags
	 *	que não são do e dos objetos
	 *
	 *	IDEA: chamar recursivamente o print de cada nó, como num percurso em nível de uma árvore n-ária
	 */
	private function buildGUI()
	{
		global $sysLogger;

		$tempDoc = new DOMDocument();		// documento temporário, criado para gerar nós
		$tempDoc->preserveWhiteSpace = true;

		/*
		 *	Replacing Head
		 */
		if (!$this->isInclusion)
		{
			$oldNode = $this->document->getElementsByTagName("head")->item(0);
			$newNodeXhtml = $this->head->getXHTML();
			$sysLogger->debug("XHTML of Head:".NL.$newNodeXhtml, __FILE__, __LINE__);

			if(!$tempDoc->loadXML('<?xml version="1.0" encoding="utf-8" ?>' . $newNodeXhtml))
			{
				echo "Malformed XML: " . $newNodeXhtml;
			}

			$newNode = $this->document->importNode($tempDoc->documentElement, true); // true para importar os filhos
			$oldNode->parentNode->replaceChild($newNode, $oldNode);
			$sysLogger->debug("Head Replaced", __FILE__, __LINE__);
		}

		/*
		 *	Replacing Body
		 */

		// pega a tag body
		$oldNode = $this->document->getElementsByTagName("body")->item(0);
		// pega o xhtml da tag
		$newNodeXhtml = $this->body->getXHTML();

		$sysLogger->debug("Replacing body" . NL . $newNodeXhtml, __FILE__, __LINE__);
		// carrega o xhtml da tag

		$tempDoc->loadXML('<?xml version="1.0" encoding="utf-8" ?>' . $newNodeXhtml);
		//
		$newNode = $this->document->importNode($tempDoc->documentElement, true); // true para importar os filhos
		$oldNode->parentNode->replaceChild($newNode, $oldNode);

		$headNode = $this->document->getElementsByTagName("head")->item(0);

		/*
		 * Adding the scripts
		 */
		if (!$this->isInclusion) foreach($this->scripts as $scriptNodeXHTML)
		{
			$tempDoc->loadXML('<?xml version="1.0" encoding="utf-8" ?>' . $scriptNodeXHTML);

			$newNode = $this->document->importNode($tempDoc->documentElement, true); // true para importar os filhos
			$headNode->appendChild($newNode);
		}

		/*
		 * Adding the Styles
		 */
		if (!$this->isInclusion) foreach($this->css as $styleNodeXHTML)
		{
			$tempDoc->loadXML('<?xml version="1.0" encoding="utf-8" ?>' . $styleNodeXHTML);

			$newNode = $this->document->importNode($tempDoc->documentElement, true); // true para importar os filhos
			$headNode->appendChild($newNode);
		}

		// Evitando erro de javascript
		$this->htmlBuffer = str_replace("<![CDATA[","<!-- // --><![CDATA[",$this->document->saveXML());
		$this->htmlBuffer = str_replace("]]>","<!-- // -->]]>", $this->htmlBuffer);

		preg_match_all('%<script[\d\s\w_\-."\'=:/\\\\]*/>%', $this->htmlBuffer, $match);

		foreach($match[0] as $result)
		{
			$newScr = str_replace("/>","></script>",$result);
			$this->htmlBuffer = str_replace($result,$newScr,$this->htmlBuffer);
		}

		return $this->htmlBuffer;
	}

	/**
	 * Returns the view state manager
	 *
	 * @return ServerViewState
	 */
	public function getViewStateManager()
	{
		return $this->viewState;
	}

	/**
	 * Adds a javascript to the page
	 *
	 * @param string $script
	 * @param position $position
	 * @return unknown
	 */
	public function addScript($script, $position = null)
	{
		return $this->head->addScript($script, $position);
	}

	public function addCSS($css, $position = null)
	{
		return $this->head->addCss($css, $position);
	}

	/**
 	 * @access public
	 * @return array
	 */
	public function getAttributesToRender()
	{
		return array( 'xmlns:php' => 'http://www.neoconn.com/namespaces/php', 'version' => '1.0');
	}

	/**
 	 * @access public
	 * @return string
	 */
	public function getObjectName()
	{
		return 'html';
	}

	/**
 	 * @access public
	 * @return CustomRender
	 */
	public function getCustomRenderer()
	{
		return null;
	}

	/**
 	 * @access public
	 * @return boolean
	 */
	public function hasCustomRenderer()
	{
		return false;
	}

	/**
 	 * @access public
	 */
	public function onPreRender()
	{
		$this->_onPreRender->raise($this);
	}

	/**
 	 * @access public
	 */
	public function onRender()
	{
		$this->_onRender->raise($this);
	}

	/**
 	 * @access public
	 * @param IRender $render
	 */
	public function renderChildren(IRenderer $render, IOutputWriter $writer)
	{
		$render->render($this->head, $writer);
		$render->render($this->body, $writer);
	}
}