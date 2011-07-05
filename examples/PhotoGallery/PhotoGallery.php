<?php require_once( '../loadBaze.php' ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:php="http://www.neoconn.com/namespaces/php" version="1.0">
<head>
	<title>Hello World Example</title>
	<style type="text/css">
		.SEARCH_FORM{
			background-color:#C4C4C4;
			font-family:verdana;
			font-size:20px;
			padding:20px;
		}
		.SPAN_TAG{
			font-family:courier;
			font-size:45px;
			font-weight:bold;
			padding:10px;
		}
		.GALLERY_PANEL img{
			margin:10px;
			border:2px solid black;
		}
	</style>
</head>
<body>
	<h1>Photo Gallery</h1>
	<php:form id="searchForm" php:runat="server" class="SEARCH_FORM">
		Tag: <php:textbox id="txtTagValue" php:runat="server" />
		<php:submit id="btnSearch" value="Buscar!" php:runat="server" />
	</php:form>
	<php:span id="spnTag" class="SPAN_TAG" php:runat="server"></php:span>
	<php:panel id="galleryPanel" class="GALLERY_PANEL" php:runat="server">
	</php:panel>
</body>
</html>