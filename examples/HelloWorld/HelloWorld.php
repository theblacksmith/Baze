<?php require_once( '../loadBaze.php' ); ?>
<html xmlns:php="http://www.neoconn.com/namespaces/php">
<head>
	<title>Hello World Example</title>
	<style type="text/css">
		.TRANSFER_BUTTON{
			margin-left:5px;
			margin-right:5px;
		}
	</style>
</head>
<body>
	<h1>Hello World</h1>
	<php:TextBox id="txtEntrada" php:runat="server" value="Altere esse texto!" />
	<php:Button id="btnTransfer" php:runat="server" class="TRANSFER_BUTTON" value="Tranferir &gt;&gt;" />
	<php:TextBox id="txtSaida" php:runat="server" readonly="readonly" value="Esse campo recebe o texto transferido!" />
	<br /><br />
	<php:Submit id="btnChange" php:runat="server" value="Escreve 'Hello World!'" />
</body>
</html>