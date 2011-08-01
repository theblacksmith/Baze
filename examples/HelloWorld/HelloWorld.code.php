<?php

require_once 'system/web/ui/Page.class.php';
require_once 'system/web/ui/form/TextBox.class.php';
require_once 'system/web/ui/form/Submit.class.php';

class HelloWorld extends Page {

	/**
	 * @var TextBox
	 */
	public $txtEntrada;

	/**
	 * @var Button
	 */
	public $btnTransfer;

	/**
	 * @var TextBox
	 */
	public $txtSaida;

	/**
	 * @var Button
	 */
	public $btnChange;

	/// @todo: O Estado da página não está sendo restaurado quando o postback é recebido
	/// Não to falando da atualização das alterações feitas no browser. 
	/// Ex: nada do que é setado nos objetos no método init() é persistido até o postback
	 
	public function init(){

		// adicionando um evento JavaScript ao botão btnChange
		//$this->btnChange->OnClick = '$C("txtEntrada").set("value","Hello World!");';
		$this->btnChange->OnClick = '$("txtEntrada").value = "Hello World!";';

		// adicionando um evento de PostBack ao botão btnBotao
		$this->btnTransfer->OnClick = array($this, 'write');
	}

	public function write(Component $sender, $args)
	{
		//atualiza o valor do componente txtSaída com o valor do componente txtEntrada
		$this->txtSaida->Value = $this->txtEntrada->Value;
	}
}