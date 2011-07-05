<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
.error_title {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 18px;
	font-weight: bold;
	color: #0099FF;
}

#error_title_bkg {
	border: 2px solid #0099FF;
	padding: 10px;
}

.description_tit,.source_tit {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #0099FF;
	font-weight: bold;
}

.description_txt, .source_txt {
	display: block;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #666666;
}

#description_txt_bkg, #source_txt_bkg {
	background-color: #EBEBEB;
	padding: 10px;
}

.code_txt_bkg {
	width: 100%;
	background-color: #D2E9FF;
	padding: 10px 0;
	overflow: hidden;
}

.code_txt {
	display: block;
	background-color: #D2E9FF;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #666666;
	padding-left: 10px;
}

.line_error {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #0099FF;
	font-weight: bold;
}

.highlight {
	background-color: #FFFF99;
}

</style>
</head>
<? //$ex = new BazeException(); ?>
<body>
	<div id="error_title_bkg">
		<span class="error_title"><?=get_class($ex);?></span>
	</div>
	<br /><br />

	<span class="description_tit">Description</span>
	<br />

	<div id="description_txt_bkg">
		<span class="description_txt"><?=$ex->getMessage();?></span>
	</div>
	<br /><br />

	<span class="source_tit">Source File</span>
	<br />

	<div id="source_txt_bkg">
		<span class="source_txt"><?=$ex->getGuiltyFile().' : '.$ex->getGuiltyLine();?></span>
	</div>
	<br /><br />

	<div class="code_txt_bkg">
<?
	$lines = $ex->getSourceLines();
	$nLines = count($lines);

	if($nLines == 0) : ?>

		<span class="code_txt">Source code is not availble</span>

<? 	else :
			$highlight = $ex->getGuiltyLine();

		foreach($lines as $num => $line) :
			$line = (trim($line)=='' ? '' : preg_replace(array('|\s|','|\t|','[\r\n]','|<|'), array('&nbsp;','&nbsp;&nbsp;&nbsp;&nbsp;','','&lt;'), $line));

			if($num == $highlight) : ?>
		<span class="code_txt highlight"><?=$num?>. <?=$line?></span>
<?			else : ?>
		<span class="code_txt"><?=$num?>. <?=$line?></span>
<?			endif; ?>
<?		endforeach; ?>

<?	endif; ?>
	</div>

	<div class="backtrace"><pre>
	<?
		$lines = $ex->getGuiltyTrace();
		print_r($lines);
	?>
	</pre></div>
</body>
</html>
