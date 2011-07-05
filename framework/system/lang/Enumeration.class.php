<?php

/**
 * Emulates an enum functionality
 *
 *
 *
 */
class Enumeration {

	protected $value;
	protected static $stringTable;
	protected static $enumValueTable;

	protected function __construct($value)
	{
		$this->value = $value;
	}

	/**
	 * Initializes the enum replacing the public static
	 * properties of the class $className with instances
	 * of the class itself. The class passed as parameter must
	 * extend the class Enumeration.
	 *
	 * @param string $className
	 */
	public static function init($className)
	{
		$rc = new ReflectionClass($className);
		$statics = $rc->getProperties();

		if(!$rc->isSubclassOf('Enumeration'))
			throw new InvalidArgumentException(ErrorMsg::$Enumeration_InvalidClass, $className);

		$n = count($statics);
		for($i=0; $i < $n; $i++)
		{
			$prop = $statics[$i];
			if($prop->isPublic() && $prop->isStatic())
			{
				$val = $prop->getValue();
				if(!$val) $val = $i;

				$obj = new $className($i);
				$prop->setValue($obj);

				self::$stringTable[$i] = (string)$val;
				self::$enumValueTable[$i] = $obj;
			}
		}
	}

	public function __toString()
	{
		return self::$stringTable[$this->value];
	}

	/**
	 * @return string O valor string associado a esse membro do enum.
	 * Caso nenhuma string tenha sido associada, a função retornará o valor
	 * inteiro do enum convertido para string
	 */
	public function toString()
	{
		return self::$stringTable[$this->value];
	}

	/**
	 * @return int O valor inteiro desse membro do enum
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string[]
	 */
	public function getStringTable()
	{
		return self::$stringTable;
	}

	/**
	 * @return Enumeration[]
	 */
	public function getEnumValueTable()
	{
		return self::$enumValueTable;
	}
}