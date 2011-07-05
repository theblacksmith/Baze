<?php
/**
 * 
 */


/**
 * 
 */
class XmlTransform
{
	/**
	 * @author Luciano AJ
	 * @since 1.0
	 */
	public function __construct()
	{
		
	}
	
	
	
	
	/**
	 * @author Lucino AJ
	 * @since 1.0
	 * 
	 * @param mixed $arr
	 * @return string
	 */
	public static function array2String($arr)
	{
	 	if(!is_array($arr))
	 	{
	 		return '"' . $arr . '"';
	 	}
	 		 	
	 	if(count($arr)== 1)
	 	{
	 		$key = array_keys($arr);
	 		
	 		if(!is_array($arr[$key[0]]))
	 		{
	 			return $arr;
	 		}
	 	}

	 	$stringChildren = '';
	 	
	 	$stringChildren.= "array(";
		foreach($arr as $index => $child)
		{			
			$stringChildren.= "\n\t'" . $index . "' => "  . self::array2String($child) . ',';
		}
		$stringChildren.= ')';
		return $stringChildren;
	}

	/**
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @param DOMNode $elem
	 * @return array	
	 */
	public static function xml2Array(DOMNode $elem)
	{		 
	 	if($elem instanceof DOMText)
	 	{
	 		return $elem->wholeText;
	 	}
	 	
	 	$children = $elem->childNodes;
	 	
	 	if($children->length == 1)
	 	{
	 		if($children->item(0) instanceof DOMText)
	 		{
	 			return $children->item(0)->wholeText;
	 		}
	 	}

	 	$arrayChildren = array();
	 	
		for($i = 0; $i < $children->length; $i++)
		{
			$child = $children->item($i);
			
		 	if( isset($arrayChildren[$child->localName]) )
		 	{
		 		$keys = array_keys($arrayChildren[$child->localName]);
		 		
		 		$valid = true;
		 		
		 		//Varrendo os nomes das tags para saber se é um CONJUNTO ou se são apenas filhos 
				//Nota: Conjunto != Filhos, pois:
				//arrayConjunto{0 => 'laranja', 1 => 'maçã' ... n => 'goiaba' }  não é igual a    arrayFilhos('a' => 'laranja', 'b' => 'maçã' ... 'n' => 'goiaba')
		 		foreach($keys as $key)
		 		{
		 			if(!is_numeric($key))
		 			{
		 				$valid = false;
		 			}
		 		}
		 		
		 		if($valid)
		 		{
		 			$arrayChildren[$child->localName][] = self::xml2Array($child);
		 		}
		 		else
		 		{		 			
		 			$old = $arrayChildren[$child->localName];
		 			$arrayChildren[$child->localName] = array($old, self::xml2Array($child)); 
		 		}
		 	}
		 	else
		 	{
		 		$arrayChildren[$child->localName] = self::xml2Array($child);
		 	}
		}
		
		return $arrayChildren;
	}

	/**
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @param string $xml o XML em si 
	 * @return string
	 */
	public static function xml2String($xml)
	{
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->loadXML($xml);
		
		$arr = self::xml2Array($doc->documentElement);
		
		return self::array2String($arr);
	}
	
	
	/**
	 * @author Luciano AJ
	 * @since 1.0
	 * 
	 * @param string $xmlPath caminho do arquivo XML
	 * @return string
	 */
	public static function xmlFile2String($xmlPath)
	{
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->load($xmlPath);
		
		$arr = self::xml2Array($doc->documentElement);
		
		return self::array2String($arr);
	}
}

$obj = new XmlTransform();

$xml = '
<core>
	<projects>
		<namespace>Space</namespace>
		<project>
			<name>Projeto 1</name>
			<include>class</include>
		</project>
		<project>
			<nome>Projeto 2	</nome>
			<include>myClass</include>
		</project>
		<project>
			<nome>Projeto 3</nome>
			<include>otherPath</include>
		</project>

	</projects>
	<config>
		<data>
			<database>mysql</database>
			<user>usuario</user>
			<pass>minhapass</pass>
			<host>larry</host>
		</data>
		<enabled>true</enabled>
	</config>
</core>';

$arr = $obj->xml2String($xml);

echo($arr);