<?php

define('TYPE', 0);
define('GETTER', 1);
define('SETTER', 2);
define('DOC', 4);

class OrderMode
{
	const GettersThanSetters = 'gts';
	const GroupByProperty = 'gbp';
}

class BeanGenerator
{
	private static $orderMode;
		
	public static function setOrderMode($orderMode)
	{
		if($orderMode == OrderMode::GettersThanSetters || $orderMode == OrderMode::GroupByProperty)
			self::$orderMode = $orderMode;
		else
			throw new Exception('Invalid OrderMode. Use OrderMode class constants.');
	}
	
	private static function generate($className, array $props, $orderMode = null)
	{
		if($orderMode === null)
			$orderMode = self::$orderMode;
			
		ksort($props);
		
		$code = "class $className \n{";
		
		switch($orderMode)
		{
			case OrderMode::GettersThanSetters :
				
				$Ps = '';
				$Gs = '';
				$Ss = '';
				
				foreach ($props as $name => $opts) {
					
					$Ps .= "\n\tprivate \$$name;\n";
						
					if($opts & GETTER)
						$Gs .= "\n".self::genGet($name) . "\n";
						
					if($opts & SETTER)
						$Ss .= "\n".self::genSet($name) . "\n";
				}
				
				$code .= $Gs . $Ss;
				
				break;
				
			case OrderMode::GroupByProperty :
			default :
				
				$Ps = '';
				$Ms = '';
				
				foreach ($props as $name => $opts) {
					
					$Ps .= "\n\tprivate \$$name;\n";
					
					if($opts & GETTER)
						$Ms .= "\n".self::genGet($name) . "\n";
						
					if($opts & SETTER)
						$Ms .= "\n".self::genSet($name) . "\n";
				}
				
				$code .= $Ps . $Ms;
				break;
		}
		
		$code .= '}';
		
		return $code;
	}
	
	private static function genGet($name)
	{
		return 	"\tpublic function get".ucfirst($name).'() {'.
				"\n\t\treturn \$$name;\n" .
				"\t}";
	}
	
	private static function genSet($name)
	{
		return 	"\tpublic function set".ucfirst($name).'($value) {'.
				"\n\t\t\$this->$name = \$value;\n" .
				"\t}";
	}
	
	public static function getCode($className, array $props, $orderMode = null)
	{
		return self::generate($className, $props, $orderMode);
	}
	
	public static function echoCode($className, array $props, $orderMode = null)
	{
		echo self::generate($className, $props, $orderMode);
	}
}


$props = array(
	'authType' => array(
		TYPE => 'string',
		GETTER => true,
		SETTER => false,
		DOC => 'the name of the authentication scheme used to protect the servlet.'
	),
	
	'authType' => array(
		TYPE => 'array',
		GETTER => true,
		SETTER => false,
		DOC => 'the name of the authentication scheme used to protect the servlet.'
	),
	
	'authType' => array(
		TYPE => 'array',
		GETTER => true,
		SETTER => false,
		DOC => 'the name of the authentication scheme used to protect the servlet.'
	),
);

echo '<pre>';
BeanGenerator::echoCode('Post', $props);
