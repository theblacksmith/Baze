<?php require_once( '../loadBaze.php' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:php="http://www.neoconn.com/namespaces/php" version="1.0">
<head>
	<title>Calculator example</title>
</head>

<body id="body1">
	<php:textbox id="txtNum1" php:runat="server" value="2" />
	<php:textbox id="txtNum2" php:runat="server" value="2" />
	<php:textbox id="txtResult" value="result" php:runat="server" />
	<br />	<br />
	<php:button id="cmbSom" php:runat="server" value=" + " />

<php:button id="cmbSub" php:runat="server" value=" - " />

<php:button id="cmbMul" php:runat="server" value=" x " />

<php:button id="cmbDiv" php:runat="server" value=" / " />

		<script>//showDebugLog();</script>
</body>
</html>