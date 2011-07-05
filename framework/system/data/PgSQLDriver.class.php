<?php
/**
 * Arquivo PgSQLDriver.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/**
 * Classe PgSQLDriver
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
 class PgSQLDriver implements IDbDriver
 {
	/**
	 * Function beginTransaction<br>
	 *
	 * @param link $conn - link de conexão
	 * @param string $transaction_name - transaction name for begin
	 */
	public static function beginTransaction($conn,$transaction_name)
	{
		$string_transaction = "BEGIN TRANSACTION";
		return pg_query($conn, $string_transaction);
	}

	/**
	 * Function PgSQL Connect
	 *
	 * @access public
	 * @param string $host = host name or IP (string)
	 * @param string $user = user name (string)
	 * @param string $pass = password  (string)
	 * @param string $dbName = database name (string)
	 * @param string $port = port connection (port)
	 * @param string $socket = socket of connectin (socket)
	 * @return resource connection
	 */
	public static function connect ($host,$user,$pass,$dbName,$port,$socket)
	{
		$stringConn = "host=".$host." dbname=".$dbName." user=".$user." password=".$pass;

		if (!empty($port))
		{
			$stringConn = $stringConn." port=".$port;
		}
		return pg_connect($stringConn);
	}

	/**
	 * Function PgSQL Persistent Connect
	 *
	 * @access public
	 * @param string $host = host name or IP (string)
	 * @param string $user = user name (string)
	 * @param string $pass = password  (string)
	 * @param string $dbName = database name (string)
	 * @param string $port = port connection (port)
	 * @param string $socket = socket of connectin (socket)
	 *
	 */
	public static function pconnect ($host,$user,$pass,$dbName,$port,$socket)
	{
		$stringConn = "host=".$host." dbname=".$dbName." user=".$user." password=".$pass;
		$port = "5432";
		if (!empty($port))
		{
			$stringConn = $stringConn." port=".$port;
		}
		$stringConn.=" connect_timeout=100;";
		return pg_pconnect($stringConn);
	}

	/**
	 * Function PgSQL Close
	 */
	public static function close($conn)
	{
		return pg_close ($conn);
	}

	/**
	 * Function PgSQL Commit
	 */
	public static function commit($conn,$commit_name)
	{
		$string_commit = "COMMIT TRANSACTION";
		return pg_query($conn,$string_commit);
	}

	/**
	 * Function PgSQL Escape String
	 */
	public static function escapeString($conn,$string)
	{
		return pg_escape_string($string);
	}

	/**
	 * Function PgSQL Execute NonQuery
	 */
	public static function executeNonQuery($conn,$query)
	{
		return pg_query($conn,$query);
	}

	/**
	 * Function PgSQL Execute Query
	 */
	public static function executeQuery($conn,$query)
	{
		return pg_query($conn,$query);
	}

	/**
	 * Function fetchField
	 *
	 * @param resultset $resource
	 * @param array $identifier array('index','name','tableName')
	 * @return array $meta
	 */
	public static function fetchField($resource,$identifier)
	{
		$status = pg_result_status($resource);

		if ($status == 0 || $status == 7)
		{
			return null;
		}

		$index = $identifier['index'];
		$name = $identifier['name'];

		$tableName = strtolower($identifier['tableName']);

		$queryStruct = 	"SELECT a.attname AS name,
						t.typname AS type,
						a.attlen AS size,
						a.atttypmod AS len,
						a.attnotnull  AS notnull,
						a.attstorage AS i
 	 						FROM pg_attribute a ,
 	 						pg_class c,
 	 						pg_type t
  							WHERE c.relname = '$tableName'
  							AND a.attrelid = c.oid
  							AND a.atttypid = t.oid
  							AND a.attname = '$name'";

		$res = pg_query($queryStruct);

		$field = array();

		$field = pg_fetch_row($res);

		$name = $field[0];
		$type = $field[1];
		$size = $field[2];

		if($field[3]<0 && $field[5]!="x")
		{
			$field[3]=(strlen(pow(2,($field[3]*8)))+1);
		}
		$len = $field[3] - 4;

		if ( $field[4] == 't')
		{ $isNotNull = 1; }
		else { $isNotNull = 0; }

      		$meta = array();
		$meta["index"] 			= $index;
		$meta["tableName"] 		= $tableName;
		$meta["name"] 			= $name;
		$meta["maxLength"] 		= $size;
		$meta["isNotNull"] 		= $isNotNull;
		$meta["type"] 			= $type;
		$meta["size"]			= $len;

		$meta["isBlob"] 		= null;
		$meta["isMultipleKey"] 	= null;
		$meta["isNumeric"] 		= null;
		$meta["isPrimaryKey"] 	= null;
		$meta["isUniqueKey"] 	= null;
		$meta["isUnsigned"] 	= null;
		$meta["isZeroFill"] 	= null;

		return $meta;
	}

	/**
	 * Function PgSQL Fetch Row
	 */
	public static function fetchRow($resultset)
	{
		return pg_fetch_row($resultset);
	}

	/**
	 * Function PgSQL Get Affected Rows
	 */
	public static function getAffectedRows($identifier)
	{
		$conn = $identifier ["conn"];
		$resource = $identifier ["resource"];
		return pg_affected_rows($resource);

	}

	/**
	 * Function PgSQL Get Error Index
	 * @param resource $conn - connection resource of PostGresql
	 * @return void - Postgre does not have a function to return only the index
	 */
	public static function getErrorIndex($resourceLink, $conn)
	{
		return null;
	}

	/**
	 * Function PgSQL Get Error Message
	 * @param Object $conn - Postgree link of connection
	 * @return string $message - text of the specifc error
	 */
	public static function getErrorMessage($resourceLink, $conn)
	{
		 return pg_last_error($conn);
	}

	/**
	 * Function getFieldIndex
	 * @param resource $resultset
	 * @param string $fieldName
	 * @return int index of field or null if $fieldName have a inexistent string name field in resultset
	 */
	public static function getFieldIndex($resultset,$fieldName)
	{
		$i = pg_field_num($resultset,$fieldName);

		if ($i == -1)
		{
			return null;
		}
		return $i;
	}

	/**
	 * Function getFieldName
	 * @param resource $resultset
	 * @param int $fieldIndex
	 * @return string name of field or null if $fieldIndex is out of range
	 */
	public static function getFieldName ($resultset,$fieldIndex)
	{
		return pg_field_name($resultset,$fieldIndex);
	}
	
	/**
	 * Function getLastID()<br><br>
	 * This function return the last autoincrement value of last INSERT command.
	 * 
	 * @author Luciano (23/10/2006)
	 * 
	 * @param Object $conn Postgree link of connection
	 * @param string $tableName
	 * @param string $fieldName
	 * @return int
	 */
	public static function getLastID($conn,$tableName,$fieldName)
	{
		return pg_query($conn,"SELECT currval('".$tableName.'_sequence_'.$fieldName."')");
	}
	
	/**
	 * Function getTableName()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 * 
	 * @param resource $result
	 * @param int $index
	 * @param Object $conn Postgree link of connection
	 * @return string - table name of specified column in result
	 */
	public static function getTableName($result, $index, $conn)
	{
		return pg_field_table($result, $index);
	}
	

	/**
	 * Function helpTable
	 * @param tableName
	 * @return array - result stats of specified table
	 */
	public static function helpTable($tableName,$conn)
	{

		$tableName = strtolower($tableName);

		$queryStruct = 	"SELECT a.attname AS name,
						t.typname AS type,
						a.attlen AS size,
						a.atttypmod AS len,
						a.attnotnull  AS notnull,
						a.attstorage AS i
 	 						FROM pg_attribute a ,
 	 						pg_class c,
 	 						pg_type t
  							WHERE c.relname = '$tableName'
  							AND a.attrelid = c.oid
  							AND a.atttypid = t.oid";

		$res = pg_query($queryStruct);
		$i = 0;
		while ($q = pg_fetch_assoc($res))
		{
              $a[$i]["type"]=$q["type"];

              $a[$i]["name"]=$q["name"];

			if ($q["notnull"] == 't')
			{
				$a[$i]["isNotNull"] = 1;
			}
			else { $a[$i]["isNotNull"] = 0; }

			if($q["len"]<0 && $q["i"]!="x")
			{
				$a[$i]["len"]=(strlen(pow(2,($q["size"]*8)))+1);
			}
			else
			{
				$a[$i]["len"]=$q["len"];
			}

			$a[$i]["size"]=$q["size"];

			$i++;
      		}

		$count = count ($a);
		$fields = array();

		for ($i=0; $i<$count; $i++)
		{
			$fields["name"][$i] = $a[$i]["name"];
			$fields["type"][$i] = $a[$i]["type"];
			$fields["len"][$i] = $a[$i]["len"];
			$fields["isNotNull"][$i] = $a[$i]["isNotNull"];
		}

		$stats["Field_Name"] 	= $fields["name"];
		$stats["Type"] 			= $fields["type"];
		$stats["Computed"] 		= null;
		$stats["Length"] 		= $fields["len"];
		$stats["Precision"] 	= null;
		$stats["Scale"] 		= null;
		$stats["Nullable"] 		= $fields["isNotNull"];
		$stats["isBlob"]		= null;
		$stats["isMultipleKey"] = null;
		$stats["isNumeric"]		= null;
		$stats["isPrimaryKey"]	= null;
		$stats["isUniqueKey"]	= null;
		$stats["isUnsigned"] 	= null;
		$stats["isZeroFill"]	= null;

		$stats["Constraint_Type"]= null;
		$stats["Constraint_Name"]= null;

		return $stats;
	}

	/**
	 * Function PgSQL Result Field Count
	 * @param resource $resultset
	 * @return int number of columns in $resultset
	 */
	public static function resultFieldsCount($resultset)
	{
		return pg_num_fields($resultset);
	}

	/**
	 * Function PgSQL Result Rows Count
	 */
	public static function resultRowsCount($resultset)
	{
		return pg_num_rows ($resultset);
	}

	/**
	 * Function PgSQL Rollback
	 */
	public static function rollback($conn,$rollback_name)
	{
		$string_rollback = "ROLLBACK";
		return pg_query($conn,$string_rollback);

	}

	/**
	 * Function Row Seek
	 */
	public static function rowSeek($resultset,$offSet)
	{
		return pg_result_seek($resultset,$offSet);
	}

	/**
	 * Function Set Timeout
	 */
	public static function setTimeOut($conn,$time)
	{
		return true;
	}
 }