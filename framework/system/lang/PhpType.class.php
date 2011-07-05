<?php

class PhpType
{
	const ArrayType = 0;
	const Boolean = 1;
	const Float = 2;
	const Integer = 3;
	const Number = 4;
	const Object = 5;
	const Scalar = 6;
	const String = 7;

	/**
	 * Converte o valor passado para o tipo boolean.
	 * A string "false" e strings vazias serão convertidas
	 * para false. Qualquer outra string é convertida para
	 * true.
	 *
	 * @param mixed $value
	 * @return object
	 */
	public static function toBoolean($value)
	{
		if (is_string($value))
			return strcasecmp($value,'false')==0 || $value !== "";
		else
			return (boolean)$value;
	}

	/**
	 * Converte o valor passado para string. Caso a variável
	 * seja do tipo boolean uma string "true" ou "false" será retornada
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function toString($value)
	{
		if (is_bool($value))
			return $value?'true':'false';
		else
			return (string)$value;
	}

	/**
	 * Converte o valor passado para o tipo float.
	 *
	 * @param mixed $value
	 * @return object
	 */
	public static function toFloat($value)
	{
		return (float)$value;
	}

	/**
	 * Converte o valor passado para o tipo integer.
	 *
	 * @param mixed $value
	 * @return object
	 */
	public static function toInteger($value)
	{
		return (integer)$value;
	}

	/*
	 * Converte o valor passado para o tipo object.
	 *
	 * @param mixed $value
	 * @return object
	 *./
	public static function toObject($value)
	{
		return (object)$value;
	}

	*/

	/**
	 * Chega o tipo da variável passada. Caso o tipo não seja igual ao que foi passado
	 * em type, uma exceção do tipo InvalidArgumentTypeException é lançada.
	 *
	 * @throws InvalidArgumentTypeException
	 */
	public static function ensureArgumentType($name, $value, $type)
	{
		switch($type)
		{
			case self::ArrayType :
				if(is_array($value))
					return true;
				break;

			case self::String :
				if(is_string($value))
					return true;
				break;

			case self::Integer :
				if(is_integer($value))
					return true;
				break;

			case self::Boolean :
				if(is_bool($value))
					return true;
				break;

			case self::Number :
				if(is_numeric($value))
					return true;
				break;

			case self::Object :
				if(is_object($value))
					return true;
				break;

			case self::Scalar :
				if(is_scalar($value))
					return true;
				break;

			case self::Float :
				if(is_float($value))
					return true;
				break;
		}

		if(is_object($value))
			$argType = get_class($value);
		else
			$argType = gettype($value);

		throw new InvalidArgumentTypeException(ErrorMessages::InvalidArgument_TypeMismatch,
													$name, $type, $argType);
	}
}