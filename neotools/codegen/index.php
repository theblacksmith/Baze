<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns:php="http://www.neoconn.com/namespaces/php" version="1.0">
<head>
	<title>Code generator</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>

<?php

require('metaModel.php');
require('projectXMLParser.php');

$showForm = false;

if(isset($_POST['filePath']))
{
	if(!file_exists($_POST['filePath']))
		$showForm = true;
	else
	{
//		$content = file_get_contents($_POST["filePath"]);
		
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->load($_POST['filePath']);
		$xPath = new DOMXPath($doc);
		
		$classes = $xPath->query("//Model[@modelType = 'Class']");
		
		//echo $classes->length . ' classes found.<br><br>';
		
		for($j=0, $len=$classes->length; $j < $len ; $j++)
		{
			$cls = $classes->item($j);

			//echo ' Classe: ' . $cls->getAttribute("name") . '<BR />';
			$metaClass = new MetaClass($cls->getAttribute('name'));
			
			$atts = $xPath->query('ChildModels/Model[@modelType=\'Attribute\']', $cls);
			$operations = $xPath->query('ChildModels/Model[@modelType=\'Operation\']', $cls);
			
			//echo 'atributos <BR />';
			for($i=0, $length=$atts->length; $i < $length ; $i++)
			{
				$att = $atts->item($i);
				//echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $att->getAttribute("name") . '<BR />';
				
				$pAtt = parseAttribute($att, $xPath);
				$metaClass->addAttribute($pAtt);
			}
		
			//echo 'metodos <BR />';
			for($i=0, $length=$operations->length; $i < $length ; $i++)
			{
				$op = $operations->item($i);
				//echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $op->getAttribute("name") . '<BR />';
				$pMeth = parseMethod($op, $xPath);
				$metaClass->addMethod($pMeth);
			}
			
			//echo "<br /><br />";
			
			echo '<b>C&oacute;digo</b><br><pre>' . $metaClass->generateCode() . '</pre>';
		}
	}
}
else
	$showForm = true;

if($showForm)
{
?>
	<form method="post">
		
		<input type="text" name="filePath" value="neobase.xml" />
		<input type="submit" />

	</form>
<?
}
?>

</body>
</html>