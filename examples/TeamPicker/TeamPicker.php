<?php require_once( '../loadBaze.php' ); ?>
<html xmlns:php="http://www.neoconn.com/namespaces/php">
<head>
	<title>Team Picker Example</title>
	<link type="text/css" href="styles.css" rel="stylesheet" />
</head>
<body>
	<php:Panel id="players" class="team" php:runat="server">
		<h2>Players</h2>
	</php:Panel>
	
	<php:Panel id="team" class="team" php:runat="server">
		<h2>Team</h2>
		<php:Player id="mack" class="player" php:runat="server">Mack</php:Player>
		<php:Player id="zack" class="player" php:runat="server">Zack</php:Player>
		<php:Player id="jonh" class="player" php:runat="server">John</php:Player>
		<php:Player id="bart" class="player" php:runat="server">Bart</php:Player>
		<php:Player id="black" class="player" php:runat="server">Black</php:Player>
	</php:Panel>
	
</body>
</html>