<?php

import('system.application.services.HttpService');
import('system.application.services.pageService.SimpleXmlPageParser');
import('system.web.ui.Page');
import('system.io.HttpResponseWriter');

import('system.rendering.XhtmlRenderer');
import('system.rendering.XmlRenderer');
import('system.rendering.ViewStateRenderer');
import('system.postback.ViewStateManager');
import('system.postback.PostbackRequest');
import('system.postback.PostbackResponse');

class PlaceHolder extends PageComponent {}
class Content extends PageComponent {}

class PageService extends HttpService
{
	/**
	 * @magic
	 * @var PageServiceConfig Config
	 * @desc The service config
	 */
	public $Config;

	/**
	 * @var HttpRequest
	 * @desc The request object
	 */
	protected $request;

	/**
	 * @var HttpResponse
	 * @desc The response object
	 */
	protected $response;

	/**
	 * @var Page
	 * @desc The current page
	 */
	protected $page;

	/**
	 * @var IPageParser
	 */
	protected $pageParser;

	/**
	 * Page cache for the default page and viewing pages states
	 * @var Zend_Cache_Frontend_File
	 */
	private $cache;
	
	/**
	 * @var ViewStateManager
	 */
	private $viewStateManager;
	
	public function __construct(App $app)
	{
		$this->app = $app;
		// array('preserveWhiteSpace' => false)
		$this->pageParser = new SimpleXmlPageParser();
	}

	/**
	 * @copydoc HttpService.run()
	 *
	 * @param HttpRequest $req
	 * @param HttpResponse $resp
	 */
	public function run(HttpRequest $req, HttpResponse $resp)
	{
		$this->request = $req;
		$this->response = $resp;
		
		list($klass, $designFile) = $this->findPage();
		
		$layoutFile = $this->findLayout($klass);
		
		/// @todo: test caching vs object creation
		$this->cache = new Zend_Cache_Frontend_File(array(
				'master_file' => $designFile,
				'ignore_user_abort' => true,
				'automatic_serialization' => true));
		$this->cache->setBackend(System::getCacheBackend());
		
		$this->page = $this->loadPage($klass, $designFile, $layoutFile);

		if(_IS_POSTBACK)
		{
			$this->request = new PostbackRequest($req);
			//$this->response = new PostbackResponse($resp);
			
			$this->viewStateManager = new ViewStateManager($this->page);
			$this->viewStateManager->loadViewState($this->request->SyncMessage);
			
			$this->page->load();
		
			if(($msg = $this->request->getEventMessage()) != null)
			{
				$obj = $msg->getSender();
				$event = $msg->getEvent();
				$args = $msg->getArguments();
	
				$this->page->$obj->$event->raise($this->page->$obj, $args);
			}
			
			$render = new ViewStateRenderer();
		}
		else
		{
			$this->page->init();
			$this->page->load();
			$this->page->unload();
			$render = new XhtmlRenderer();
		}

		$writer = new HttpResponseWriter($this->response);
		$render->render($this->page, $writer);
		
		if(_IS_POSTBACK)
			$this->viewStateManager->setSynchronized();
		
		$this->cache->save($this->page, $this->page->getId());
	}

	private function findPage()
	{
		$designFile = $_SERVER['SCRIPT_FILENAME'];
		
		if(!file_exists($designFile))	{
			$this->response->setStatus(404);
			return $this->getErrorPage(404, HttpStatusCode::_404);
		}
		
		$pos = strrpos($designFile, '.');
		$klass = basename($designFile, System::$Config->DesignFileExt);
		$codeFile = $klass . System::$Config->CodeFileExt;

		if(file_exists($codeFile)) {
			// @think maybe we should use import here
			require_once($codeFile);
		}
		else
			$klass = 'Page';
			
		return array($klass, $designFile);
	}
	
	private function findLayout($klass)
	{
		$c = new ReflectionClass($klass);
		$parent = $c->getParentClass();
		
		if($parent->getName() == 'Page')
			return false;
			
		$codeFile = $parent->getFileName();
		// @think maybe we should use import here
		require_once($codeFile);
		
		return str_replace(System::$Config->CodeFileExt, System::$Config->DesignFileExt, $codeFile);
	}
	
	/**
	 * Loads the page class for the requested page
	 *
	 * @return Page The page class in the file $code_file
	 */
	private function loadPage($klass, $designFile, $layoutFile = false)
	{			
		$pageId = $this->request->getParam('PageId', false);
		
		$p = false;
		if(!$this->request->getParam('baze-clear-cache', false) || _IS_POSTBACK)
		{
			if($pageId)
				$p = $this->cache->load($pageId);
			
			if(!$p)
				$p = $this->cache->load($this->getPageFileId($designFile));
		}
		
		if(!$p) {
			$p = new $klass();
			
			if($layoutFile) {
				$this->pageParser->parsePageFile($layoutFile, $p);
				$this->pageParser->parseComponents(file_get_contents($designFile), $p, true);
			}
			else
				$this->pageParser->parsePageFile($designFile, $p);
			
			// the head component must have been added
			$this->addScripts($p);
			
			$this->cache->save($p, $this->getPageFileId($designFile));
		}
			
		return $p;
	}
	
	private function addScripts(Page $p) {
		
		$appConf = $this->app->getConfig();
		
		if($appConf->Mode == AppMode::$PRODUCTION)
			$p->addScript($appConf->BazeLibUrl.'/js/BazeAPI.min.js', 0);
		else
			$p->addScript($appConf->BazeLibUrl.'/js/BazeAPI.js', 0);
			
		$p->addCSS($appConf->BazeLibUrl.'/css/default.css', 0);
		$p->addScript(
'	<script type="text/javascript">
		APP_ROOT = "'. $appConf->Url . '";
		LIB_ROOT = "'. $appConf->BazeLibUrl  . '";
		PAGE_ID = "' . $p->getId() . '";
	</script>
');
	}

	/**
	 * Generates an unique file id for caching purposes
	 * 
	 * @param string $file The complete file path and name
	 */
	private function getPageFileId($file)
	{
		return trim(str_replace(array('/','\\','.'), '_', $file), '/\\');
	}
	
	/**
	 * Returns the error page set by user in configuration or the default error page if none is specified
	 *
	 * @param int $code The status code
	 * @param string $reason Optional status reason
	 * @return Page
	 */
	protected function getErrorPage($code, $reason=null)
	{
		if(isset($this->_config->errorPages->{'_'.$code}))
		{
			$qName = $this->_config->errorPages->{'_'.$code};

			import($qName);
			$class = basename($qName);

			return new $class;
		}
		else
		{
			$path = NB_ROOT.'/exception/templates/';

			if(file_exists($path."$code.html"))
			{
				$file = $path."$code.html";
			}
			else if(file_exists(NB_ROOT.'/exception/templates/'.$code.'.html'))
			{
				$file = $path.$code.'.html';
			}

			return new Page(file_get_contents($file));
		}
	}
}