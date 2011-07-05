<?php

import('system.web.ui.page.*');
import('system.web.ui.Panel');
import('system.web.ui.form.TextBox');
import('system.web.ui.form.Submit');
import('system.web.ui.Button');
import('system.web.ui.Span');
import('system.web.ui.Form');

import('system.web.ui.HyperLink');
import('system.web.ui.Image');

import('zend.Service.Flickr');

class PhotoGallery extends Page {
	
	const PER_PAGE = 12;
	
	/**
	 * @var Form
	 */
	public $searchForm;
	
	/**
	 * @var Panel
	 */
	public $galleryPanel;
	
	/**
	 * @var TextBox
	 */
	public $txtTagValue;
	
	/**
	 * @var Button
	 */
	public $btnSearch;
	
	/**
	 * @var Span
	 */
	public $spnTag;
	
	public function init(){
		$this->searchForm->OnSubmit =  array($this, 'loadGallery');
	}
	
	/**
	 * Cria a galeria baseado numa tag a ser buscada no site Flickr utilizando
	 * a classe Zend_Service_Flickr do Zend Framework
	 */
	public function loadGallery(Component $sender, $args)
	{
		//pega a tag a ser procurada
		$tagValue = $this->txtTagValue->Value;
		
		//se campo estiver vazio, retorna
		if(empty($tagValue))
			return;

		//utilizaremos o serviço flickr do ZendFramework
		$flickr = new Zend_Service_Flickr('54948dc39ab34a793c2b385859a17090');
		//fazendo a busca por fotos que contenham a tag desejada para criar a galeria de fotos
		$results = $flickr->tagSearch($tagValue, array('per_page' => PhotoGallery::PER_PAGE));
		
		//monta a galeria. 
		//recebe o resultset retornado pela classe do Zend Framework e a tag procurada
		//para colocar no título da galeria
		$this->fillGallery($results, $tagValue);
	}
	
	/**
	 * Função que carrega outra página de uma determinada galeria de uma tag anteriormente buscada
	 */
	public function changePage(Component $sender, $args)
	{
		//pega o número da página passada no array de argumentos do evento
		$newPage = $args['page'];
		
		//inicializa a classe do flickr
		$flickr = new Zend_Service_Flickr('54948dc39ab34a793c2b385859a17090');
		
		//pega os resultados da página informada da última tag buscada 
		$results = $flickr->tagSearch($args['tagValue'], array('per_page' => PhotoGallery::PER_PAGE, 'page' => $newPage));
		
		//monta a galeria. 
		//recebe o resultset retornado pela classe do Zend Framework e a tag procurada
		//para colocar no título da galeria
		$this->fillGallery($results, $args['tagValue']);
	}
	
	/**
	 * cria a galeria baseado num resultset recebido
	 */
	private function fillGallery($results, $tagValue)
	{
		//limpa o painel da galeria de fotos
		$this->galleryPanel->removeChildren();
		
		//calcula o número da página atual e o total de páginas baseado nas informações do resultset
		$currentPage = ($results->totalResultsAvailable==0)?0:(intval($results->firstResultPosition/PhotoGallery::PER_PAGE)+1);
		$totalPages = intval($results->totalResultsAvailable/PhotoGallery::PER_PAGE)+((($results->totalResultsAvailable%PhotoGallery::PER_PAGE)>0)?1:0);
		
		//atualiza o título da galeria com a tag busca
		$this->spnTag->removeChildren();
		$this->spnTag->addChild(new HtmlFragment($tagValue . '<br />'));
		
		//testa se precisa de botão para página anterior
		if($currentPage > 1)
		{
			//cria um novo botão que vai servir para ir para a próxima página da galeria
			$previousPage = new Submit();
			
			//gera o id do botão
			//o time() está aí para não se preocupar com a geração de id repetido de componentes
			$previusId = 'PAGINATION_PREVIOUS_'.time();
			
			//seta as propriedades do botão como value (texto do botão) e 
			//runat (informa que é um objeto que é manipulável pelo servidor)
			$previousPage->setProperties(
				array(
					'id' => $previusId,
					'runat' => 'server',
					'value' => 'Anterior'
				)
			);
			
			//inscreve componente na página para que a página conheça sua existência e comece a observá-lo
			$this->$previusId = $previousPage;
		
			//alistando um evento para mudança de página no botão
			$previousPage->addEventListener(CLICK, array($this, 'changePage'), true, true, array('tagValue' => $tagValue, 'page' => ($currentPage-1)));
			
			//insere o botão no elemento span da galeria, antes da informação de paginação
			$this->spnTag->addChild($previousPage);
		}
		
		//insere informações de paginação no span
		$this->spnTag->addChild(new HtmlFragment('[Page ' . $currentPage . ' of ' .$totalPages. ']'));
		
		//testa se não é a última página
		if($currentPage < $totalPages)
		{
			//cria um novo botão que vai servir para ir para a próxima página da galeria
			$nextPage = new Submit();
			
			//inscreve componente na página para que a página conheça sua existência e comece a observá-lo
			$this->addComponent($nextPage);
			
			//gera o id do botão
			//o time() está aí para não se preocupar com a geração de id repetido de componentes
			$nextId = 'PAGINATION_NEXT_'.time();
			
			//seta as propriedades do botão como value (texto do botão) e 
			//runat (informa que é um objeto que é manipulável pelo servidor)
			$nextPage->Id = $nextId;
			$nextPage->Runat = 'server';
			$nextPage->Value = 'Próxima';
			
			
			//alistando um evento para mudança de página no botão
			$eh = new EventHandler(array($this, 'changePage'));
			//$eh->addParameters(array('tagValue' => $tagValue, 'page' => $currentPage+1));
			$nextPage->addEventListener->OnClick = $eh;
			
			//insere o botão no elemento span da galeria, depois da informação de paginação
			$this->spnTag->addChild($nextPage);
		}		
		
		//variavel auxiliar para calcular onde inserir a quebra de linha da galeria
		$j = 1;
		
		//para cada foto retornada no resultset
		foreach ($results as $result) {
			
			//cria novo link para a foto no flickr
			$link = new HyperLink('http://www.flickr.com/photo_zoom.gne?id='.$result->id);
			
			//gera id do link
			//time() está para não preocupar-se em gerar ids iguais de componentes
			$linkId = 'PHOTO_LINK_' . $result->id . '_' . time();
			//seta propriedades do link

			$link->Id = $linkId;
			$link->Runat = 'server';
			$link->Target = '_blank';
			
			//inscreve link na página
			$this->$linkId = $link;
			
			//cria a imagem de thumbnail da galeria
			$squareImg = new Image();
			
			//gera id da imagem
			//time() está para não preocupar-se em gerar ids iguais de componentes
			$photoId = 'PHOTO_' . $result->id . '_' . time();
			//seta propriedades da imagem

			$squareImg->Id = $photoId;
			$squareImg->Runat = 'server';
			$squareImg->Src = $result->Square->uri;
			
			//inscreve imagem na página
			$this->$photoId = $squareImg;
			
			//adiciona a imagem no link
			$link->addChild($squareImg);
			
			//testa se já chegou na metade das fotos...
			if((intval(PhotoGallery::PER_PAGE / 2)+1)==$j)
				$this->galleryPanel->addChild('<br />'); //...insere quebra de linha caso positivo
			
			//insere o link no painel da galeria de fotos
		    $this->galleryPanel->addChild($link);
		    
		    //incrementa a variável auxiliar que informa o número da próxima foto
		    $j++;
		}
	}
}