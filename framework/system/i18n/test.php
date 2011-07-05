<?php

$lang = "en";
$locale = "en_US";

if (isset($_GET["loc"]))
{
	$lang = split('_',$_GET["loc"]);
	$lang = $lang[0];
	$locale = $_GET["loc"];
}

putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages", "./locale");
textdomain("messages");
bind_textdomain_codeset("messages", "UTF-8");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$lang?>" lang="<?=$lang?>">
<head>
	<title><?=_("Hello World!")?></title>
	
</head>
<body>

	<h1><?=_("PHP Localization Benchmark")?></h1>
	
	<p><?=_("This is just a sample page to show how PHP gettex extension works.")?></p>

</body>
</html>
