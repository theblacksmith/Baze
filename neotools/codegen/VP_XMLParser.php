<?php

class VP_XMLParser extends XMLParser
{
	public function __constructor($filePath)
	{
		parent::__construct($filePath=null);
	}
	
	public function parse()
	{
		
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
		}
}