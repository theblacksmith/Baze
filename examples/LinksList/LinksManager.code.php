<?php
import('system.web.ui.page.*');
import('system.web.ui.Panel');
import('system.web.ui.form.TextBox');
import('system.web.ui.form.Submit');
import('system.web.ui.Form');
import('system.web.ui.Button');
import('system.web.ui.UList');
import('system.web.ui.ListItem');

import('system.web.ui.HyperLink');

/**
 * Mini administrador de links que visa servir como exemplo do
 * funcionamento da sincronização dos componentes NeoBase entre Cliente versus Servidor 
 */
class LinksManager extends Page {
	
	/**
	 * @var Form
	 */
	public $insertLinkForm;
	
	/**
	 * @var TextBox
	 */
	public $linkTitle;
	
	/**
	 * @var TextBox
	 */
	public $linkUrl;
	
	/**
	 * @var Submit
	 */
	public $insertLink;
	
	/**
	 * @var Button
	 */
	public $sinchronizeButton;
	
	/**
	 * @var Panel
	 */
	public $statusMsg;
	
	/**
	 * @var UList
	 */
	public $linksList;
	
	private $lastQuantity = 0;
	
	function init()
	{
		//alistando um evento javascript para o botão de que insere novos links na lista
		//veja documentação do método addEventListener
		$this->insertLink->OnClick = 'newLink(); return false';
		
		//alistando um evento do servidor para o botão que sincroniza as atualizações, feitas por JS, na interface
		$this->sinchronizeButton->OnClick = array($this, 'sinc');
	}
	
	//função do evento de sincronização, da interface no cliente, com o servidor
	function sinc(Component $sender, $args)
	{
		//ao chamar a função o NeoBase já terá atualizado o estado da interface
		//precisamos somente contar a nova quantidade de elementos da lista de links
		//para saber quantos links foram adicionados
		$currQuantity = count($this->linksList->getChildren());
		
		//calculo a diferença entre a quantidade de links na última atualização com a nova quantidade
		$alterQuantity = $currQuantity - $this->lastQuantity;
		
		//atualizao a quantidade de links da última sincronização
		//para ter como calcular a diferença de quantidades numa nova chamada a este evento
		$this->lastQuantity = $currQuantity;
		
		//limpa painel de mensagem
		$this->statusMsg->removeChildren();
		
		//insere nova mensagem no painel de mensagens 
		//com a quantidade de links adicionados desde a última sincronização
		$this->statusMsg->addChild('Alterações salvas: <strong>'.$alterQuantity.'</strong> Links Adicionados.');
	}
}