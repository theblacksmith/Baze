<?php

	/**
	 * VSF
	 * @package vsf
	 */

	/**
	 * class PhpUtils
	 */
class PhpUtils
{

	/**
	 * Function getFileName
	 *
	 * @return string
	 * @static function
	 */
	static function getFileName()
	{
		$name = split("\.", basename($_SERVER['SCRIPT_NAME']));

		return $name[0];
	}

	static function stripPhpCode($str)
	{
		$start = strpos($str,"<?");

		while($start !== false)
		{
			$end = strpos($str,"?>",$start);

			$str = substr_replace($str,"",$start,$end - $start + 2);

			$start = strpos("<?",$str);
		}

		return $str;
	}

	/**
	 * Function strToChar
	 *
	 * @param string $str
	 * @return string
	 */
	static function strToChar($str)
	{
		for($i=0; $i < strlen($str); $i++)
		{
			$chars[$i] = substr($str,$i,1);
		}

		return $chars;
	}

	/**
	 * Function getChar
	 *
	 * @param string $str
	 * @param int $i
	 */
	static function getChar($str,$i)
	{
		/*echo "<script>alert(\"".substr($str,$i,1)."\");</script>";*/
		return substr($str,$i,1);
	}

	/**
	 * Function debug
	 *
	 * @param string $s
	 */
	static function debug($s)
	{
		global $__debug;

		if($__debug)
			echo "<script>alert(\"".$s."\");</script>";
	}

	/**
	 * Function strParse
	 *
	 * @param string $str
	 * @return array
	 */
	static function strParse($str)
	{
		//$varValues;

		global $__debug;
		$__debug = false;

		$str = trim($str);
		$str = stripslashes($str);
		$length = strlen($str);
		$varValues = array();

		// checked id=\"myForm\" name='firstForm' action=\"nada.php\"
		$i=0;
		while($i < $length)
		{
			self::debug("Entrou no while lá em cima");

			$var=""; $val=""; $quote = "";
			$ch = self::getChar($str,$i);

			// limpa os espacos, se parou no '=' nem vai entrar, senao vai parar no = ou numa letra
			while($ch == ' ' && $i < $length)
			{
				$i++;
				$ch = self::getChar($str,$i);
			}

			// Pega a propriedade
			self::debug("comecou a pegar a propriedade");
			while($ch != '=' && $ch != ' ' && $i < $length)
			{
				$var = $var.$ch;

				$i++;
				$ch = self::getChar($str,$i);
			}
			self::debug("o nome da propriedade é: ".$var);

			// limpa os espacos, se parou no '=' nem vai entrar, senao vai parar no = ou numa letra
			while($ch == ' ' && $i < $length)
			{
				$i++;
				$ch = self::getChar($str,$i);
			}

			// se parou no igual pega o valor
			if($ch == "=")
			{
				self::debug("A propriedade tem valor");

				$i++;
				$ch = self::getChar($str,$i);

				// limpa os espacos
				while($ch == ' ' && $i < $length)
				{
					$i++;
					$ch = self::getChar($str,$i);
				}

				// Pega o delimitador
				if($ch == '"')
				{
					$quote = '"';
					$i++;
					$ch = self::getChar($str,$i);
				}
				else if($ch == "'")
				{
					$quote = "'";
					$i++;
					$ch = self::getChar($str,$i);
				}
				else
				{
					$quote = ' ';
				}

				self::debug("o delimitador é: \\\"\\".$quote."\\\"");

				// pega o valor
				self::debug("Começou a pegar o valor");
				while($ch != $quote && $i < $length)
				{
					$val = $val.$ch;

					$i++;
					$ch = self::getChar($str,$i);

					if($ch == "\\")
					{
						// coloca a barra de escape na string
						$val = $val.$ch;
						$i++;

						// pega o caracter seguinte e coloca também
						$ch = self::getChar($str,$i);

						$val = $val.$ch;
						$i++;

						$ch = self::getChar($str,$i);
					}
				}
				self::debug("o valor é: \\\"".$val."\\\" o");

				$i++;
				$ch = self::getChar($str,$i);
			}

			if($val != "")
				$varValues[strtolower($var)] = $val;
			else
				$varValues[strtolower($var)] = null;
		}

		return $varValues;
	}

	/**
	 * Function strParseStyle
	 *
	 * @param string $str
	 * @return array
	 */
	static function strParseStyle($str)
	{
		$varValues = array();

		$str = trim($str);

		$arr = split(";",$str);

		for($i=0; $i < sizeof($arr); $i++)
		{
			if($arr[$i] != "")
			{
				$aux = split(":", $arr[$i]);

				$var = trim($aux[0]);
				$value = trim($aux[1]);

				$varValues[$var] = $value;
			}
		}

		return $varValues;
	}

	public static function convertXmlToArray($xml)
	{
		$ar = array();
		foreach ( $xml->children() as $k => $v ) {
			// recurse the child
			$child = self::convertXmlToArray( $v );
			//print "Recursed down and found: " . var_export($child, true) . "\n";
			// if it's not an array, then it was empty, thus a value/string
			if ( count($child) == 0 ) {
				$child = self::convertXmlValue($v);
			}
			// add the childs attributes as if they where children
			foreach ( $v->attributes() as $ak => $av ) {
				// if the child is not an array, transform it into one
				if ( !is_array( $child ) ) {
					$child = array( "value" => $child );
					}
				if ($ak == 'id') {
					// special exception: if there is a key named 'id'
					// then we will name the current key after that id
					$k = self::convertXmlValue($av);
				} else {
					// otherwise, just add the attribute like a child element
					$child[$ak] = self::convertXmlValue($av);
				}
			}
			 // if the $k is already in our children list, we need to transform
			 // it into an array, else we add it as a value
			 if ( !in_array( $k, array_keys($ar) ) ) {
				 $ar[$k] = $child;
			 } else {
				 // if the $ar[$k] element is not already an array, then we need to make it one
				 if ( !is_array( $ar[$k] ) ) { $ar[$k] = array( $ar[$k] ); }
				 $ar[$k][] = $child;
			 }
			}
	
		return $ar;
	}

	/**
	 * Converte o valor de um nó do xml em boolean caso
	 * o texto seja idêntico a true ou false
	 */
	private static function convertXmlValue($value)
	{
		$value = (string) $value; // convert from simplexml to string
		// handle booleans specially
		$lwr = strtolower($value);
		if ($lwr === "false") {
			$value = false;
		} elseif ($lwr === "true") {
			$value = true;
		}
		return $value;
	}
}