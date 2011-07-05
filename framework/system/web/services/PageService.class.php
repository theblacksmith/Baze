<?php

import('system.web.services.HttpService');
import('system.web.services.pageService.DOMPageParser');
import('system.web.services.pageService.SimpleXmlPageParser');
import('system.web.ui.Page');
import('system.io.HttpResponseWriter');

import('system.rendering.XhtmlRender');
import('system.rendering.XmlRender');
import('system.rendering.ViewStateRender');

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
		
		/// @todo: test caching vs object creation
		$this->cache = new Zend_Cache_Frontend_File(array(
				'master_file' => $designFile,
				'ignore_user_abort' => true,
				'automatic_serialization' => true));
		$this->cache->setBackend(System::getCacheBackend());
		
		$this->page = $this->loadPage($klass, $designFile);

		if($req->isPostback()) {
			$this->page->getViewStateManager()->loadViewState($req->ViewState);
		}
		else {
			$this->page->init();
		}

		$this->page->load();

		if($req->isPostback()) {
			$this->page->_handleEvent();
		}

		$this->page->unload();

		if($req->isPostback())
			$render = new ViewStateRender();
		else
			$render = new XhtmlRender();

		$writer = new HttpResponseWriter($this->response);

		$render->render($this->page, $writer);
		
		$this->page->getViewStateManager()->setSynchronized();
		
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
	
	/**
	 * Loads the page class for the requested page
	 *
	 * @return Page The page class in the file $code_file
	 */
	private function loadPage($klass, $designFile)
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
			$this->pageParser->parsePageFile($designFile, $p);
			// the head component must have been added
			$this->addScripts($p);
			
			$this->cache->save($p, $this->getPageFileId($designFile));
		}
			
		return $p;
	}
	
	private function addScripts(Page $p) {
		
		$appConf = System::getApp()->getConfig();
		
		if($this->app->getConfig()->Mode == AppMode::$PRODUCTION)
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