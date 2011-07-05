<?php
/**
 * Arquivo MSSQLDriver.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/**
 * Classe MSSQLDriver
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
class MSSQLDriver implements IDbDriver
{
	/**
	 * Function MSSQL Begin Transaction
	 * @static
	 *
	 * @param resource $conn
	 * @param string $transaction_name
	 */
	public static function beginTransaction($conn,$transaction_name)
	{
		if ( !empty($transaction_name) )
		{
			$string_transaction = "BEGIN TRANSACTION $transaction_name";
			echo "<br />STRING TRANSACTION: ".$string_transaction."<br />";
			mssql_query($string_transaction);
			return true;
		}
		return false;
	}

	/**
	 * Function MSSQL Connect
	 *
	 * @static
	 * @param string $host = host name or IP (string)
	 * @param string $user = user name (string)
	 * @param string $pass = password  (string)
	 * @param string $dbName = database name (string)
	 * @param string $port = port connection (port)
	 * @param string $socket = socket of connectin (socket)
	 * @return resource identifier on success, or FALSE  on error
	 */
	public static function connect ($host,$user,$pass,$dbName,$port,$socket)
	{
		if (!empty($port))
		{
			$host = $host.','.$port;
		}
		return mssql_connect($host,$user,$pass);
	}

	/**
	 * Function MSSQL Persistent Connect
	 *
	 * @static
	 * @param string $host = host name or IP (string)
	 * @param string $user = user name (string)
	 * @param string $pass = password  (string)
	 * @param string $dbName = database name (string)
	 * @param string $port = port connection (port)
	 * @param string $socket = socket of connectin (socket)
	 * @return resource identifier on success, or FALSE  on error
	 */
	public static function pconnect ($host,$user,$pass,$dbName,$port,$socket)
	{
		if (!empty($port))
		{
			$host = $host.':'.$port;
		}

		return mssql_pconnect($host,$user,$pass);
	}

	/**
	 * Function MSSQL Close
	 * Calls internal PHP function 'mssql_close' for MSSQL databases.
	 *
	 * @static
	 * @param link $conn - MSSQL link identifier of connection
	 * @return boolean - true on success or false on failure.
	 */
	public static function close($conn)
	{
		return mssql_close($conn);
	}

	/**
	 * Function MSSQL Commit
	 *
	 * @static
	 * @param link $conn - link connection
	 */
	public static function commit($conn,$commit_name)
	{
		if ( !empty($commit_name) )
		{
			$string_commit = "COMMIT TRANSACTION $commit_name";
			mssql_query($string_commit);
			return true;
		}
		return false;
	}

	/**
	 * Function MSSQL Escape String
	 * Calls internal PHP function 'addslashes'.
	 *
	 * @param link $conn - MSSQL link identifier of connection
	 * @param string $string
	 * @static
	 * @return string - string with special formatted characters
	 **/
	public static function escapeString($conn,$string)
	{
		return addslashes($string);
	}

	/**
	 * Function MSSQL Execute NonQuery
	 * Calls internal PHP function 'mssql_query' for MSSQL databases.
	 *
	 * @param link $conn - link connection
	 * @param string $query - SQL command
	 * @static
	 * @return boolean - true on success or false on error.
	 **/
	public static function executeNonQuery($conn,$query)
	{
		return mssql_query($query,$conn);
	}

	/**
	 * Function MSSQL Execute Query
	 * Calls internal PHP function 'mssql_query' for MSSQL databases.
	 *
	 * @param link $conn - MSSQL link identifier of connection
	 * @param string $query - SQL command
	 *
	 * @return resultset - MSSQL result resource on success or false on error.
	 **/
	public static function executeQuery($conn,$query)
	{
		return mssql_query($query,$conn);
	}

	/**
	 * Function fetchField
	 *
	 * @param resultset $resource
	 * @param int $index
	 * @static
	 * @return array Meta
	 */
	public static function fetchField($resultset,$identifier)
	{
		$index = $identifier["index"];
		$tableName = $identifier["tableName"];

		$stat = mssql_fetch_field($resultset,$index);

		$name 		= $stat->name;
		$maxLength 	= $stat->max_length;

		if ($stat->numeric == 1)
		{ $isNumeric = true; }
		else
		{ $isNumeric = false; }

		$type = $stat->type;

		//Obter 'isNotNull' do campo
		$query_Nullable = "sp_columns $tableName";
		$rs_IsNull = mssql_query($query_Nullable);
		$index = 0;
		while (  ($res = mssql_fetch_row($rs_IsNull))   )
		{
			if ($res[3] == $name)
			{ break 1; }

			$index ++;
		}
		$isNotNull = !$res[10];

		//Obter 'isPrimaryKey' do campo
		$isPrimaryKey = 0;
		$query_IsPK = 'sp_pkeys "'.$tableName.'"';
		$rs_IsPK = mssql_query($query_IsPK);
		$res = mssql_fetch_row($rs_IsPK);
		if ($res[3] == $name)
		{
			$isPrimaryKey = 1;
		}

		$meta = array();
		$meta["index"] 			= $index;
		$meta["tableName"] 		= $tableName;
		$meta["name"] 			= $name;
		$meta["size"] 			= $maxLength;
		$meta["isNotNull"] 		= $isNotNull;
		$meta["type"] 			= $type;
		$meta["isBlob"] 		= null;
		$meta["isMultipleKey"] 	= null;
		$meta["isNumeric"] 		= null;
		$meta["isPrimaryKey"] 	= $isPrimaryKey;
		$meta["isUniqueKey"] 	= null;
		$meta["isUnsigned"] 	= null;
		$meta["isZeroFill"] 	= null;

		return $meta;
	}

	/**
	 * Function MSSQL Fetch Row
	 * Calls internal PHP function 'mssql_fetch_row' for MSSQL databases.
	 *
	 * @param resource $resultset
	 * @static
	 * @return array - array that corresponds to the fetched row, or false if there are no more rows.
	 **/
	public static function fetchRow($resultset)
	{
		return mssql_fetch_row($resultset);
	}

	/**
	 * Function MSSQL Get Affected Rows
	 * Calls internal PHP function 'mssql_rows_affected' for MSSQL databases.
	 *
	 * @param array $identifier - contains the connection and resource
	 *
	 * @return int - number of records affected by the query
	 **/
	public static function getAffectedRows($identifier)
	{
		$conn = $identifier ["conn"];
		$resource = $identifier ["resource"];

		return mssql_rows_affected($conn);
	}

	/**
	 * Function MSSQL Get Error Index
	 *
	 * @param link $conn - MSSQL link identifier of connection
	 * @return int $resultError["code" - code of specific error
	 */
	public static function getErrorIndex($resourceLink, $conn)
	{
		 $resultError = self::getSqlServerError($conn);
		 return $resultError["code"];
	}

	/**
	 * Function MSSQL Get Error Message
	 *
	 * @static
	 * @param link $conn - MSSQL link identifier of connection
	 * @return string $message - text of the specifc error
	 */
	public static function getErrorMessage($resourceLink, $conn)
	{
		 $resultError = self::getSqlServerError($conn);
		 return $resultError["text"];
	}

	/**
	 * Function getFieldIndex
	 *
	 * @static
	 * @param resource $resultset
	 * @param string $fieldName
	 * @return int index of field or null if $fieldName have a inexistent string name field in resultset
	 */
	public static function getFieldIndex($resultset,$fieldName)
	{
		$j = mssql_num_fields ($resultset);

		$index = null;

		for ($i=0; $i<$j; $i++)
		{
			if (msssql_field_name($resultset,$i) == $fieldName)
			{
				$index = $i;
				break 1;
			}
		}
		return $index;
	}

	/**
	 * Function getFieldName
	 * Calls internal PHP function 'mssql_field_name' for MSSQL databases.
	 *
	 * @static
	 * @param resource $resultset
	 * @param int $fieldIndex
	 * @return string name of field or null if $fieldIndex is out of range
	 */
	public static function getFieldName ($resultset,$fieldIndex)
	{
		 return mssql_field_name ($resultset,$fieldIndex);
	}
	
	/**
	 * Function getLastID()<br><br>
	 * This function return the last autoincrement value of last INSERT command.
	 * 
	 * @author Luciano (23/10/2006)
	 * 
	 * @param Object $conn MSSQL link of connection
	 * @param string $tableName
	 * @param string $fieldName
	 * @return int
	 */
	public static function getLastID($conn,$tableName,$fieldName)
	{
		return mssql_query('@@IDENTITY',$conn);
	}
	

	/**
	 * Function helpTable
	 * @static
	 * @param tableName
	 * @param link $conn - MSSQL link identifier of connection
	 */
	public static function helpTable($tableName,$conn)
	{
		$query = "sp_columns ".$tableName;

		$rs = mssql_query($query);

		$count = mssql_num_fields($rs);
		$num = mssql_num_rows($rs);
		$result = array();

		for ($i=0; $i<$num; $i++)
		{
			$row = mssql_fetch_row($rs);

			for ($j=0; $j<$count; $j++)
			{
				$result[$j][$i] = $row[$j];
			}
		}
		$count = count ($result[3]);

		for ($i=0; $i<$count; $i++)
		{
			$isPrimaryKey = 0;
			if (self::isPrimaryKey($tableName,$result[3][$i]) === true)
			{
				$isPrimaryKey = 1;
			}
			$result[$j+1][$i] = $isPrimaryKey;
		}

		$stats = array();
		//index - column_name - description
		//0 - table_qualifier
		//1 - table_owner
		//2 - table_name
		//3 - column_name
		//4 - data_type
		//5 - type_name
		//6 - precision
		//7 - length
		//8 - scale
		//9 - radix
		//10- nullable
		//11- remarks
		//12- column_def
		//13- isPrimaryKey

		$stats["Field_Name"]	= $result[3];
		$stats["Type"]			= $result[5];
		$stats["Computed"]		= null;
		$stats["Length"]		= $result[7];
		$stats["Precision"]		= $result[6];
		$stats["Scale"]			= $result[8];
		$stats["Nullable"]		= $result[10];
		$stats["isBlob"]		= null;
		$stats["isMultipleKey"] = null;
		$stats["isNumeric"]		= null;
		$stats["isPrimaryKey"]	= $result[13];
		$stats["isUniqueKey"]	= null;
		$stats["isUnsigned"] 	= null;
		$stats["isZeroFill"]	= null;

		return $stats;
	}

	/**
	 * Function MSSQL Result Field Count
	 * Calls internal PHP function 'mssql_num_fields' for MSSQL databases.
	 * @static
	 * @param resource $resultset - MSSQL result
	 * @return int - number of fields in a result set.
	 **/
	public static function resultFieldsCount($resultset)
	{
		return mssql_num_fields($resultset);
	}

	/**
	 * Function MSSQL Result Rows Count
	 * Calls internal PHP function 'mssql_num_rows' for MSSQL databases.
	 *
	 * @param resource $resultset - MSSQL result
	 * @return the number of rows in a result set.
	 **/
	public static function resultRowsCount($resultset)
	{
		return mssql_num_rows ($resultset);
	}

	/**
	 * Function MSSQL Rollback
	 * @static
	 * @param link $conn - MSSQL link identifier of connection
	 * @return boolean true if succesful or false if failure
	 */
	public static function rollback($conn,$rollback_name)
	{
		if ( !empty($rollback_name) )
		{
			$string_rollback = "ROLLBACK TRANSACTION $rollback_name";
			mssql_query($string_rollback);
			return true;
		}
		return false;
	}

	/**
	 * Function Row Seek
	 * Calls internal PHP function 'mssql_data_seek' for MSSQL databases.
	 *
	 * @param resultset $resultset - MSSQL result
		 * @param int $offSet - value of offSet to row
		 * @static
		 * @return boolean - true on success or false on failure.
	 **/
	public static function rowSeek($resultset,$offSet)
	{
		return mssql_data_seek($resultset,$offSet);
	}

	/**
	 * Function Set Timeout
	 *
	 * @param link $conn - MSSQL link identifier of connection
	 * @param int $time - value (in second) of time to wait
	 * @static
	 * @return boolean	true if successful or
	 * 					false if not.
	 **/
	public static function setTimeOut ($conn,$time)
	{
		if (is_integer($time))
		{
			$time = $time * 1000;
			$query_time = "SET LOCK_TIMEOUT ".$time;
			mssql_query($query_time);
			return true;
		}
		return false;
	}

	/**
	 * Function Get SqlServerError()<br>
	 * SQLServer does not have a satisfactory function of error message.
	 * To resolve this problem, this function searchs the text of the error found with the parameter $code.
	 *
	 * @access private
	 * @static
	 * @param string $con
	 * @return A string of error found.
	 **/
	private static function getSqlServerError ($conn)
	{
		$getError = array();

		$sql    = "select @@ERROR as code";
		$result = mssql_query($sql, $conn);
		$row    = mssql_fetch_array($result);
		$code 	= $row["code"]; // código do erro
		$getError["code"] = $code;
		$sql    = "select cast (description as varchar(255)) as errtxt from master.dbo.sysmessages where error = $code";
		$result = mssql_query($sql, $conn);
		$row    = mssql_fetch_array($result);

		if ($row)
		{
			$text  = $row["errtxt"]; // string do erro
		}
		else{$text  = "onknown error";}

		$getError["text"] = $text;
		mssql_free_result($result);
		return $getError;
		//return "[$code] $text";
	}

	/**
	 * Function isPrimaryKey()
	 * This function checks if a field is primary key or not.
	 * Through the parameter $tableName, that contains the table name
	 * of the field, a store procedure is called and executed through sending
	 * the table name. If the third property [COLUMN_NAME] possess the name of
	 * the field, means that this is primary key.
	 *
	 * @access private
	 * @static
	 * @param string $tableName - name of specified table
	 * @param string $fieldName - name of field containded in the table
	 *
	 * @return boolean 	- true if the field is a Primary Key or
	 * 					false if not.
	 **/
	private static function isPrimaryKey($tableName,$fieldName)
	{
		$isPrimaryKey = false;
		$query_IsPK = 'sp_pkeys "'.$tableName.'"';
		$rs_IsPK = mssql_query($query_IsPK);
		$res = mssql_fetch_row($rs_IsPK);
		if ($res[3] == $fieldName)
		{
			$isPrimaryKey = true;
		}
		return $isPrimaryKey;
	}
}