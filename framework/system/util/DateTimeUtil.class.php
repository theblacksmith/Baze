<?php
/**
 * Arquivo DateTimeUtil.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

/* _MIN_TIME_STAMP refers to timestamp of Fri, 13 Dec 1901 20:45:54 GMT */
define( '_MIN_TIMESTAMP' , -2147483646 );

/* _MAX_TIME_STAMP refers to timestamp of the 00:00:00 - 01/18/2038 */
define( '_MAX_TIMESTAMP' , 2147396400 );

//debug_print_backtrace();

/**
 * Classe DateTimeUtil
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class DateTimeUtil
{
	private $year;
	private $month;
	private $day;

	private $hour;
	private $minute;
	private $second;

	private $timeStamp;

	/**
	 *
	 */
	function __construct( $second = -1 , $minute = -1 , $hour = -1 , $day = -1 , $month = -1 , $year = -1 )
	{
		if( $hour == -1 )
			$hour = date( 'H' );
		$this->hour = ( int )$hour;

		if( $minute == -1 )
			$minute = date( 'i' );
		$this->minute = ( int )$minute;

		if( $second == -1 )
			$second = date( 's' );
		$this->second = ( int )$second;

		if( $month == -1 )
			$month = date( 'm' );
		$this->month = ( int )$month;

		if( $day == -1 )
			$day = date( 'd' );
		$this->day = ( int )$day;

		if( $year == -1 )
			$year = date( 'Y' );
		$this->year = ( int )$year;

		$this->timeStamp = @mktime( $this->hour , $this->minute , $this->second , $this->month , $this->day , $this->year );

/*		if( $this->timeStamp < 0 )
		{
			$this->day 	 	= ( int )date( "d" );
			$this->month 	= ( int )date( "m" );
			$this->year  	= ( int )date( "Y" );
			$this->hour  	= ( int )date( "H" );
			$this->minute	= ( int )date( "i" );
			$this->second	= ( int )date( "s" );

			$this->timeStamp = @mktime( $this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year );
		}
*/	}

	function getTimestamp( )
	{
		return $this->timeStamp;
	}

	private function setDateVariantDate( $variantDate )
	{
		$strDateTimeUtil = trim ( $variantDate );

		$arrDateTimeUtil = split( ' ', $strDateTimeUtil );
		if( count( $arrDateTimeUtil ) == 2 )
		{
			$arrDate = split( '/', $arrDateTimeUtil[ 0 ] );
			$arrTime = split( ':', $arrDateTimeUtil[ 1 ] );

			if( count( $arrDate ) == 3 && count( $arrTime ) == 3 )
			{
				$this->day 	  =	( int )$arrDate[ 0 ];
				$this->month  =	( int )$arrDate[ 1 ];
				$this->year   =	( int )$arrDate[ 2 ];

				$this->hour   = ( int )$arrTime[ 0 ];
				$this->minute = ( int )$arrTime[ 1 ];
				$this->second = ( int )$arrTime[ 2 ];

				return true;
			}
		}

		return false;
	}

	function setDateFromTimestamp( $timestamp )
	{
		if( $timestamp >= _MIN_TIMESTAMP && $timestamp <= _MAX_TIMESTAMP )
		{
			$strDateTimeUtil = date( "d/m/Y H:i:s", $timestamp );

			if ( $this->setDateFromVariantDate( $strDateTimeUtil ))
			{
				$this->timeStamp = $timestamp;
				return true;
			}
		}

		trigger_error( 'invalid timestamp' );
		return false;
	}

	function setHour( $hour )
	{
		if( is_numeric( $hour ) )
			$this->changeField( 'hour', $hour );
	}

	function getHour( )
	{
		return $this->hour;
	}

	function setMinute( $minute )
	{
		if( is_numeric( $minute ) )
			$this->changeField( 'minute', $minute );
	}

	function getMinute( )
	{
		return $this->minute;
	}

	function setSecond( $second )
	{
		if( is_numeric( $second ) )
			$this->changeField( 'second', $second );
	}

	function getSecond( )
	{
		return $this->second;
	}

	function setYear( $year )
	{
		if( is_numeric( $year ) )
			$this->changeField( 'year', $year );
	}

	function getYear( )
	{
		return $this->year;
	}

	function setMonth( $month )
	{
		if( is_numeric( $month ) )
			$this->changeField( 'month', $month );
	}

	function getMonth( )
	{
		return $this->month;
	}

	function setDay( $day )
	{
		if( is_numeric( $day ) )
			$this->changeField( 'day', $day );
	}

	function getDay( )
	{
		return $this->day;
	}

	static function getAge($currDate, $birthday)
	{
		$y1 = date('Y', $currDate);
		$m1 = date('m', $currDate);
		$d1 = date('d', $currDate);
		
		$y2 = date('Y', $birthday);
		$m2 = date('m', $birthday);
		$d2 = date('d', $birthday);
		
		//faz a comparação das datas
		$anos = ($y1 - $y2);
		if ( ($m1 < $m2)
			|| (($m1 == $m2) && ($d1 < $d2)) )
		{
			return ($anos - 1);
		}
		
		if ( ($m1 > $m2)
			||(($m1 == $m2) && ($d1 >= $d2)) )
		{
			return $anos;
		}		
	}
	
	private function changeField( $fieldName, $value )
	{
		$auxiliarReg = $this->$fieldName;
		$this->$fieldName = $value;

		$timestamp = @mktime( $this->hour , $this->minute , $this->second , $this->month , $this->day , $this->year );

		if( $timestamp > ( -1 ) && $this->validateVariantDate( date( "d/m/Y H:i:s", $timestamp ) ) )
			return $this->setDateFromTimestamp( $timestamp );

		$this->$field = $auxiliarReg;
		return false;
	}

	function setDateFromVariantDate( $variantDate )
	{
		$day   = $this->day;
		$month = $this->month;
		$year  = $this->year;

		$hour   = $this->hour;
		$minute = $this->minute;
		$second = $this->second;

		if ( $this->setDateVariantDate( $variantDate ))
		{
			$timeStamp = @mktime( $this->hour , $this->minute , $this->second , $this->month , $this->day , $this->year );
			if( $timeStamp >= _MIN_TIMESTAMP )
			{
				//$this->setDateFromTimestamp( $timeStamp );
				$this->timeStamp = $timeStamp;
				return true;
			}

			$this->day   = $day;
			$this->month = $month;
			$this->year  = $year;

			$this->hour   = $hour;
			$this->minute = $minute;
			$this->second = $second;
		}

		return false;
	}

	/**
	 * validates date
	 */
	static function validateVariantDate( $variantDate )
	{
		$timeStamp = @DateTimeUtil::getTimestampFromVariantDate( $variantDate );

		$auxiliarVarDate = @date( "d/m/Y H:i:s" , $timeStamp );

		if( $auxiliarVarDate != $variantDate )
			return false;

		return true;
	}

	static function getTimestampFromVariantDate( $variantDate )
	{
		$strDateTimeUtil = trim( $variantDate );

		$arrDateTimeUtil = split( ' ' , $strDateTimeUtil );
		
		if( count( $arrDateTimeUtil ) == 2 )
		{
			$arrDate = split( '[/.-]' , $arrDateTimeUtil[ 0 ] );
			$arrTime = split( ':' , $arrDateTimeUtil[ 1 ] );

			if( count( $arrDate ) == 3 && count( $arrTime ) == 3 )
			{
				$day    = ( int )$arrDate[ 0 ];
				$month  = ( int )$arrDate[ 1 ];
				$year   = ( int )$arrDate[ 2 ];

				$hour   = ( int )$arrTime[ 0 ];
				$minute = ( int )$arrTime[ 1 ];
				$second = ( int )$arrTime[ 2 ];

				$timeStamp = strtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second);
				
				if( $timeStamp >= _MIN_TIMESTAMP )
				{
					$dateString = date('d/m/Y H:i:s', $timeStamp);
					if($dateString == $variantDate)
						return $timeStamp;
				}

				trigger_error( 'date not supported' );
				return -1;
			}
		}

		trigger_error( 'invalid date format' );
		return -1;
	}
	
	/**
	 * @desc Esta função recebe uma string no formato dia|mês|ano e retorna o timestamp correspondente.
	 * 
	 * @author Luciano
	 * @since 2007-04-24
	 * 
	 * @param string $simpleDate [ 'dd/mm/aaaa' | 'dd-mm-aaaa']
	 * 
	 * @return mixed Um int no caso de retornar um timestamp ou false
	 */
	static function getTimestampFromSimpleDate( $simpleDate )
	{
		$strDateTimeUtil = trim( $simpleDate );

		$arrDate = split( '[/.-]' , $strDateTimeUtil);

		if( count( $arrDate ) == 3)
		{
			$day    = ( int )$arrDate[ 0 ];
			$month  = ( int )$arrDate[ 1 ];
			$year   = ( int )$arrDate[ 2 ];
			
			$timeStamp = @mktime( 0, 0, 0, $month , $day, $year);
			
			return $timeStamp;
		}
		
		//invalid date format
		return false;
	}
	

	static function getTimestampFromDBDateTimeUtil( $dbDateTimeUtilString )
	{
		return strtotime( $dbDateTimeUtilString );
	}

	/**
	 * Função que valida uma uma data no formato dd/mm/aaaa
	 * e retorna o timestamp da data ou null
	 */
	static function inspectDate( $date )
	{
		$dateStr = trim($date);
		$dateArr = split( '/', $dateStr );
		if( (count($dateArr) != 3) || !DateTimeUtil::validateVariantDate($dateStr . ' 00:00:00') )
		{
			return null;
		}

		return DateTimeUtil::getTimestampFromVariantDate( $dateStr . ' 00:00:00' );
	}
}