<?php require_once( '../loadBaze.php' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:php="http://www.neoconn.com/namespaces/php" version="1.0">
<head>
	<title>Hello World Example</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style type="text/css">
		.FIELD_BOX{
			float:left;
			margin:10px;
		}
		.STATUS_MSG{
			width:100%;
			float:left;
			text-align:center;
			font-family:courier;
			font-size:14px;
			color:brown;
		}
		.LINK_FORM{
			font-family:courier;
			float:left;
		}
		.LIST_BOX{
			float:left;
			width:90%;
			border:1px solid #595959;
			background-color:#D4D5D6;
		}
		.LIST_TITLE{
			padding:5px;
			background-color:#D4D5D6;
			font-family:courier;
			font-size:14px;
			border:1px solid #595959;
		}
	</style>
	<script type="text/javascript">
		var alterCount = 0;
	
		//inserindo nova funcionalidade para o tipo String em JS
		//trim remove espaços no início e no fim de um texto
		String.prototype.trim = function()
		{
			return this.replace(/^\s*/, "").replace(/\s*$/, "");
		}
		
		//função JS para atualizar componentes do Neobase
		//adiciona novo item em uma UList
		function newLink()
		{
			//pega os valores nos TextBoxs
			//"$C" é um operador mágico para pegar elementos na página, através do ID
			//que sejam componentes do Neobase. Assim pode manipulá-lo como no arquivo .code
			var title = $C('linkTitle').get('value');
			var url = $C('linkUrl').get('value');
			
			//executa o trim nos valores capturados para evitar
			//títulos ou urls somente com espaços
			var titleValue = title.trim();
			var urlValue = url.trim();
			
			//testa se campos foram preenchidos
			if(titleValue.length == 0 || urlValue == 0)
			{
				//limpa o campo de mensagem
				$C('statusMsg').removeChildren();
				//insere uma nova mensagem de erro no campo de mensagem
				$C('statusMsg').addChild('Não pode haver campos em branco');
			}
			else
			{
				//contador auxiliar para gerar id's diferentes para os objetos que forem criados
				alterCount++;
				
				//cria novo componente HyperLink do NeoBase para inserir na lista
				var novoLink = new HyperLink();
				//set id
				novoLink.set('id', titleValue + '_' + alterCount);
				//set href
				novoLink.set('href', urlValue);
				//set target
				novoLink.set('target', '_blank');
				//adiciona texto do link 
				//(addChild é usado para inserir conteúdo que normalmente estaria dentro da tag HTML)
				novoLink.addChild(titleValue);
				//set php:runat=server para informar que elemento é manipulado no servidor
				novoLink.set('php:runat', 'server');
				
				//cria novo item de lista
				var novoItem = new ListItem();
				//set id
				novoItem.set('id', 'item_' + titleValue + '_' + alterCount);
				//set php:runat=server
				novoItem.set('php:runat', 'server');
				
				//adiciona o link no item de lista
				novoItem.addChild(novoLink);
				
				//insere o item de lista na lista, já colocada no HTML
				$C('linksList').addChild(novoItem);
				
				//limpa o painel de mensagem
				$C('statusMsg').removeChildren();
				//adiciona nova mensagem de sucesso no painel de mensagens
				$C('statusMsg').addChild(new Literal('Link adicionado com sucesso'));
			}
		}
	</script>
</head>
<body>
	<h1>Links Manager</h1>
	<php:form id="insertLinkForm" class="LINK_FORM" php:runat="server">
		<div class="FIELD_BOX">
			Título:<br />
			<php:textbox id="linkTitle" php:runat="server" />
		</div>
		<div class="FIELD_BOX">
			URL:<br />
			<php:textbox id="linkUrl" php:runat="server" />
		</div>
		<div class="FIELD_BOX">
			<br /><php:submit id="insertLink" value="Inserir link" style="margin-right:10px;" php:runat="server" />
			<php:button id="sinchronizeButton" php:runat="server" value="Salvar alterações" />
		</div>
		<php:panel id="statusMsg" class="STATUS_MSG" php:runat="server"></php:panel>
	</php:form>
	<fieldset class="LIST_BOX">
		<legend class="LIST_TITLE">Meus Links</legend>
		<php:ulist id="linksList" php:runat="server">
		</php:ulist>
	</fieldset>
</body>
</html>