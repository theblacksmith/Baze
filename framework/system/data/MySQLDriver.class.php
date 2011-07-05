<?php
/**
 * Arquivo MySQLDriver.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/**
 * Classe MySQLDriver
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

 class MySQLDriver implements IDbDriver
 {
	/**
	* From IDbDriver
	*/

	/**
	 * Function MySQLi Begin Transaction
	 *
	 * @static
	 * @param resource $conn
	 * @param string $transaction_name
	 */
	public static function beginTransaction($conn,$transaction_name)
	{
		return mysqli_autocommit($conn, FALSE);
	}

	/**
	 * Function MySQLi Connect
	 * @param string $host = host name or IP (string)
	 * @param string $user = user name (string)
	 * @param string $pass = password  (string)
	 * @param string $dbName = database name (string)
	 * @param string $port = port connection (port)
	 * @param string $socket = socket of connectin (socket)
	 * @return object connection MSQLi
	 */
	public static function connect($host, $user, $pass, $dbName, $port, $socket)
	{
		$conn = null;
		if ( empty ($host))
		{
			$conn = mysqli_connect();
		}
		elseif ( empty($user))
		{
			$conn = mysqli_connect($host);
		}
		elseif ( empty($pass))
		{
			$conn =  mysqli_connect($host,$user);
		}
		elseif ( empty($dbName))
		{
			$conn =  mysqli_connect($host,$user,$pass);
		}
		elseif ( empty($port))
		{
			$conn =  mysqli_connect($host,$user,$pass,$dbName);
		}
		elseif( empty($socket))
		{
			$conn =  mysqli_connect($host,$user,$pass,$dbName,$port);
		}
		else
		{ $conn =  mysqli_connect($host,$user,$pass,$dbName,$port,$socket); }

		if ($conn != null)
		{
			mysqli_query($conn,'SET NAMES utf8');
		}
		return $conn;
	}

	/**
	 * Function MySQLi Persistent Connect
	 *
	 * @static
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $dbName
	 * @param string $port
	 * @param string $socket
	 */
	public static function pconnect($host, $user, $pass, $dbName, $port, $socket)
	{
			//do nothing
			return false;
	}

	/**
	 * Function MySQLi Close
	 *
	 * @static
	 * @param MYSQLIlink $conn
	 */
	public static function close($conn)
	{
		return mysqli_close($conn);
	}

	/**
	 * Function MySQLi Commit
	 *
	 * @static
	 * @param MYSQLIlink $conn
	 * @param string $commit_name
	 */
	public static function commit($conn,$commit_name)
	{
		mysqli_commit($conn);
		mysqli_autocommit($conn, TRUE);
	}

	/**
	 * Function MySQLi Escape String
	 *
	 * @static
	 * @param MYSQLIlink $conn
	 * @param string $string
	 */
	public static function escapeString($conn,$string)
	{
		return mysqli_real_escape_string($conn,$string);
	}

	/**
	 * Function MySQLi Execute NonQuery
	 *
	 * @static
	 * @param MYSQLIlink $conn
	 * @param string $query
	 */
	public static function executeNonQuery($conn,$query)
	{
		//echo"<br />\n ".__METHOD__." - ".$query."($conn) <br />\n";
		return mysqli_query($conn,$query);
	}

	/**
	 * Function MySQLi Execute Query
	 *
	 * @static
	 * @param MYSQLIlink $conn
	 * @param string $query
	 */
	public static function executeQuery($conn,$query)
	{
		//$mylog = new MyLog('ViewAccessData.txt');
		
		//echo"<br />\n ".__METHOD__." - ".$query."($conn) <br />\n";
		//ob_start();
		//debug_print_backtrace();
		//$mylog->debug(ob_get_clean());
		
		return mysqli_query ($conn,$query);
	}

	/**
	 * Function fetchField
	 *
	 * @static
	 * @param resultset $resource
	 * @param mixed $identifier
	 * @return array Meta
	 */
	public static function fetchField($resultset,$identifier)
	{
		$index = $identifier["index"];
		
		//echo'<pre>';
		//print_r ($identifier);
		//echo'</pre>';
		
		mysqli_field_seek($resultset,$index);

		$info = mysqli_fetch_field($resultset);

		$name = $info->orgname;
		$tableName = $info->orgtable;
		$maxLength = $info->max_length;
		$numType = $info->type;
		$type = self::getTypeByEnum($numType);

		$flags = self::getFlags($info->flags);

		if ($type == "TINYBLOB" || $type == "MEDIUMBLOB" || $type == "LONGBLOB" || $type == "BLOB")
		{ $isBlob = 1; }
		else
		{ $isBlob = 0; }

		if (($numType>=0 && $numType <=5) || $numType == 8 || $numType == 9)
		{ $isNumeric = 1; }
		else
		{ $isNumeric = 0; }

		$meta = array();
		$meta["index"] 			= $index;
		$meta["tableName"] 		= $tableName;
		$meta["name"] 			= $name;
		$meta["size"]			= $maxLength;
		$meta["isNotNull"] 		= $flags["isNotNull"];
		$meta["type"] 			= $type;
		$meta["isBlob"] 		= $isBlob;
		$meta["isMultipleKey"] 	= $flags["isMultipleKey"];
		$meta["isNumeric"] 		= $isNumeric;
		$meta["isPrimaryKey"] 	= $flags["isPrimaryKey"];
		$meta["isUniqueKey"] 	= $flags["isUniqueKey"];
		$meta["isUnsigned"] 	= $flags["isUnsigned"];
		$meta["isZeroFill"] 	= $flags["isZeroFill"];

		return $meta;
	}

	/**
	 * Function MySQLi Fetch Row
	 *
	 * @static
	 * @param $resultset
	 * @return array - that corresponds to the fetched row, or NULL if there are no more rows.
	 */
	public static function fetchRow($resultset)
	{
		return mysqli_fetch_row ($resultset);
	}

	/**
	 * Function MySQLi Affected Rows
	 *
	 * @static
	 * @param array $identifier
	 * @return int 	- the number of rows affected by the last INSERT, UPDATE, or DELETE query associated
	 * 				with the provided link  parameter. If the last query was invalid, this function will return -1.
	 */
	public static function getAffectedRows($identifier)
	{
		$conn = $identifier ["conn"];
		$resource = $identifier ["resource"];
		return mysqli_affected_rows($conn);
	}

	/**
	 * Function MySQLi Get Error Index
	 * @param Object $conn - MySQLi link of connection to a MySQL Server
	 * @return int $index - code of specific error or zero if no errors have occured.
	 */
	public static function getErrorIndex($resourceLink, $conn)
	{
		 return mysqli_errno($conn);
	}

	/**
	 * Function MySQLi Get Error Message()<br>
	 *
	 * @param Object $conn - MySQLi link of connection to a MySQL Server
	 * @return string $message - text of the specifc error
	 */
	public static function getErrorMessage($resourceLink, $conn)
	{
		return mysqli_error ($conn) ;
	}

	/**
	 * Function getFieldIndex()<br>
	 *
	 * @param resource $resultset
	 * @param string $fieldName
	 *
	 * @return int index of field or null if $fieldName have a inexistent string name field in resultset
	 */
	public static function getFieldIndex($resultset,$fieldName)
	{
		$index = null;
		$i = 0;

		mysqli_field_seek($resultset,0);

		while ($obj = mysqli_fetch_field($resultset))
		{
			if ($obj->name == $fieldName || $obj->orgname == $fieldName)
			{
				$index = $i;
				break 1;
			}
			$i++;
		}
		return $index;
	}

	/**
	 * Function getFieldName()<br>
	 * This function returns the name of specified field in $fieldIndex parameter.
	 *
	 * @param resource $resultset
	 * @param int $fieldIndex
	 *
	 * @return string name of field or null if $fieldIndex is out of range
	 */
	public static function getFieldName($resultset,$fieldIndex)
	{
		if (mysqli_field_seek($resultset,$fieldIndex))
		{
			$obj = mysqli_fetch_field($resultset);
			return $obj->name;
		}
		return null;
	}
	
	/**
	 * Function getLastID()<br><br>
	 * This function return the last autoincrement value of last INSERT command.
	 * 
	 * @author Luciano (23/10/2006)
	 * 
	 * @param Object $conn MySQL link of connection
	 * @param string $tableName
	 * @param string $fieldName
	 * @return int
	 */
	public static function getLastID($conn, $tableName, $fieldName)
	{
		return mysqli_insert_id($conn);
	}
	
	/**
	 * Function getTableName()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 * 
	 * @param resource $result
	 * @param int $index
	 * @param Object $conn MySQL link of connection
	 * @return string - table name of specified column in result
		 */
		public static function getTableName($result, $index, $conn)
		{
			mysqli_field_seek ( $result, $index );
			
			$field = mysqli_fetch_field($result);
			
			return $field->table;
		}

				
		/**
		 * Function helpTable()<br>
		 * Gets all the possible information of a table of the data base.
		 *
		 * @param string $tableName
		 * @param MYSQLILink $conn
		 *
		 * @return array - result stats of specified table
		 */
		public static function helpTable($tableName,$conn)
		{
			$query = "Select * from `$tableName`";
			//echo"<br />\n ".__METHOD__." - ".$query."($conn) <br />\n";			
			$result = mysqli_query($conn,$query);

			if ($result === null  ||  $result === false)
			{
				trigger_error ("ERROR: Impossible get info from $tableName. Verify your table name parameter.",E_USER_ERROR);
				return null;
			}

			$fieldName 		= array();
			$fieldLength 	= array();
			$fieldType 		= array();
			$fieldIsNotNull = array();
			$fieldisBlob 	= array();
			$fieldisMultipleKey	= array();
			$fieldisNumeric 	= array();
			$fieldisPrimaryKey 	= array();
			$fieldisAutoIncrement = array();
			$fieldisUniqueKey 	= array();
			$fieldisUnsigned 	= array();
			$fieldisZeroFill 	= array();

			$ind = 0;

			while ( $info = mysqli_fetch_field($result) )
			{
				$fieldName[$ind] = $info->name;
				$fieldLength[$ind] = $info->max_length;
				$flags = $info->flags;

				$numType = $info->type;
				$fieldType[$ind] = self::getTypeByEnum($numType);

				$flags = array();
				$flags = self::getFlags($info->flags);

				if ($fieldType[$ind] == "TINYBLOB" || $fieldType[$ind] == "MEDIUMBLOB" || $fieldType[$ind] == "LONGBLOB" || $fieldType[$ind] == "BLOB")
				{ $isBlob = 1; }
				else { $isBlob = 0; }

				if (($numType>=0 && $numType <=5) || $numType == 8 || $numType == 9)
				{ $isNumeric = 1; }
				else { $isNumeric = 0; }

				$fieldNullable[$ind] = !$flags["isNotNull"];

				$fieldisBlob[$ind] 			= $isBlob;
				$fieldisMultipleKey[$ind] 	= $flags["isMultipleKey"];
				$fieldisNumeric[$ind] 		= $isNumeric;
				$fieldisPrimaryKey[$ind] 	= $flags["isPrimaryKey"];
				$fieldisAutoIncrement[$ind] = $flags["isAutoIncrement"]; 
				$fieldisUniqueKey[$ind] 	= $flags["isUniqueKey"];
				$fieldisUnsigned[$ind] 		= $flags["isUnsigned"];
				$fieldisZeroFill[$ind] 		= $flags["isZeroFill"];
				$fieldKeyColumn[$ind]		= self::infoSchemaKeyColumn($tableName,$info->name,$conn);
				$ind++;
			}

			$stats["Field_Name"] 	= $fieldName;
			$stats["Type"] 			= $fieldType;
			$stats["Computed"] 		= "_not_indentified";
			$stats["Length"] 		= $fieldLength;
			$stats["Precision"] 	= "_not_indentified";
			$stats["Scale"] 		= "_not_indentified";
			$stats["Nullable"] 		= $fieldNullable;
			$stats["isBlob"]		= $fieldisBlob;
			$stats["isMultipleKey"] = $fieldisMultipleKey;
			$stats["isNumeric"]		= $fieldisNumeric;
			$stats["isPrimaryKey"]	= $fieldisPrimaryKey;
			$stats["isUniqueKey"]	= $fieldisUniqueKey;
			$stats["isAutoIncrement"] = $fieldisAutoIncrement;
			$stats["isUnsigned"] 	= $fieldisUnsigned;
			$stats["isZeroFill"]	= $fieldisZeroFill;
			$stats["Key_Column"]	= $fieldKeyColumn;

			$stats["Constraint_Type"]= "_not_indentified";
			$stats["Constraint_Name"]= "_not_indentified";

			return $stats;
		}

		/**
		 * Function MySQLi Result Field Count()<br>
		 * This function returns the number of fields from specified result set.
		 * 
		 * @param resource $resultset
		 * @return int - number of columns in $resultset
		 */
		public static function resultFieldsCount($resultset)
		{
			return mysqli_num_fields($resultset);
		}

		/**
		 * Function MySQLi Result Rows Count()<br>
		 * This function get the number of rows in the result set.
		 * 
		 * @param resource $resultset
		 * @return int - number of rows in $resultset
		 */
		public static function resultRowsCount($resultset)
		{
			return mysqli_num_rows($resultset);
		}

		/**
		 * Function MySQLi Rollback()<br>
		 * This function rollbacks the current transaction for the database specified by the link parameter
		 *
		 * @param MYSQLILink $conn
		 * @param string $rollback_name
		 *
		 * @return boolean - true on success or false on failure.
		 */
		public static function rollback($conn,$rollback_name)
		{
			return mysqli_rollback($conn);
		}

		/**
		 * Function Row Seek()<br>
		 * This function adjusts the internal pointer of the result for a specific offset.
		 *
		 * @param resource $resultset
		 * @param int $offSet - must be between zero and the total number of rows minus one.
		 *
		 * @return boolean - true on success or false on failure.
		 */
		public static function rowSeek($resultset,$offSet)
		{
			return mysqli_data_seek($resultset,$offSet);
		}

		/**
		 * Function MySQLi Set Timeout()<br>
		 * Set connection timeout in seconds.
		 *
		 * @param MYSQLILink $conn
		 * @param int $time
		 */
		public static function setTimeOut ($conn,$time)
		{
			return mysqli_options($conn,MYSQLI_OPT_CONNECT_TIMEOUT,$time);
		}

		/**
		 * Function getFlags()<br>
		 * This function receives a decimal number contends flags of a field.
		 * It converts the decimal number for binary number and analyse each bit of the number
		 * and attributes true or false for each position of the specific number each flag.
		 *
		 * @param int $num
		 */
		private static function getFlags($num)
		{
			$flags = array();

			/*$flags["isMultipleKey"] = 0;
			$flags["isNotNull"] = 0;
			$flags["isNumeric"] = 0;
			$flags["isPrimaryKey"] = 0;
			$flags["isUniqueKey"] = 0;
			$flags["isUnsigned"] = 0;
			$flags["isZeroFill"] = 0;*/

			$off = "00000000000000";

			$stringBin = decbin($num);
			$stringBin = (string) $stringBin;
			$stringBin = $off.$stringBin;
			$leng = strlen($stringBin);
			
			//echo $num . ' = ' . $stringBin ."\n";
			
			$flags["isNotNull"]		= (int)$stringBin{$leng-1};

			$flags["isPrimaryKey"] 	= (int)$stringBin{$leng-2};

			$flags["isUniqueKey"]	= (int)$stringBin{$leng-3};

			$flags["isMultipleKey"]	= (int)$stringBin{$leng-4};
			
			$flags["isBlob"] 		= (int)$stringBin{$leng-5};

			$flags["isUnsigned"]	= (int)$stringBin{$leng-6};

			$flags["isZeroFill"]	= (int)$stringBin{$leng-7};
			
			$flags['isAutoIncrement'] = (int)$stringBin{$leng-10};
			
			//TODO: não está de acordo
			/*$flags['isTimestamp']	= (int)$stringBin{$leng-9};
			
			$flags['isSet']			= (int)$stringBin{$leng-10};
			
			$flags['isNum']			= (int)$stringBin{$leng-11};
			
			$flags['isPartKey']		= (int)$stringBin{$leng-12};
			
			$flags['isGroup']		= (int)$stringBin{$leng-13};*/

			return $flags;
		}

		/**
		 * Function getTypeByEnum()<br>
		 * This function return the type of a field associated to the specific number of each type.
		 *
		 * Note: The number flag is specific of the result gotten in the fetchField function
		 *       and follows the standards of the MySQL database.
		 *
		 * @param int $num
		 * @return string
		 */
		private static function getTypeByEnum($num)
		{
			switch ($num)
			{
				case 0:
					return "DECIMAL";

				case 1:
					return "TINYINT";

				case 2:
					return "SHORINT";

				case 3:
					return "LONGINT";

				case 4:
					return "FLOAT";

				case 5:
					return "DOUBLE";

				case 6:
					return "DEFAULT_NULL";

				case 7:
					return "TIMESTAMP";

				case 8:
					return "BIGINT";

				case 9:
					return "MEDIUMINT";

				case 10:
					return "DATE";

				case 11:
					return "TIME";

				case 12:
					return "DATETIME";

				case 13:
					return "YEAR";

				case 14:
					return "DATE";

				case 247:
					return "ENUM";

				case 248:
					return "SET";

				case 249:
					return "TINYBLOB";

				case 250:
					return "MEDIUMBLOB";

				case 251:
					return "LONGBLOB";

				case 252:
					return "BLOB";

				case 253:
					return "VARCHAR";

				case 254:
					return "CHAR";

				case 255:
					return "GEOMETRY";

				default:
					return "not_identified";
			} //close switch
		}//close function getTypeByEnum

		public static function infoSchemaKeyColumn($tableName, $columnName, $conn)
		{
			$query = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
			WHERE table_name = '$tableName'
			AND column_name LIKE '$columnName'";
			//echo"<br />\n ".__METHOD__." - ".$query."($conn) <br />\n";
			$result = mysqli_query($conn,$query);

			$info = array();

			if (!$result)
			{
				trigger_error ("ERROR ".mysqli_error($conn),E_USER_ERROR);
				return null;
			}

			while ($row = mysqli_fetch_row($result))
			{
				for ($i=0; $i<count($row);$i++)
				{
					$info[self::getFieldName($result,$i)] = $row[$i];
				}
			}
			return $info;
		}

		public static function infoSchemaTableConstraints($tableName,$columnName,$conn)
		{
			$query = "SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
			WHERE table_name = '$tableName'";
			//echo"<br />\n ".__METHOD__." - ".$query."($conn) <br />\n";
			$result = mysqli_query($conn,$query);

			$info = array();

			if (!$result)
			{
				trigger_error ("ERROR ".mysqli_error($conn),E_USER_ERROR);
				return null;
			}

			while ($row = mysqli_fetch_row($result))
			{
				for ($i=0; $i<count($row);$i++)
				{
					$info[self::getFieldName($result,$i)] = $row[$i];
				}
			}
			return $info;
		}

		public static function infoSchemaColumn($tableName,$columnName,$conn)
		{
			$query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_name = '$tableName'
			AND column_name LIKE '$columnName'";
			//echo"<br />\n ".__METHOD__." - ".$query."($conn) <br />\n";
			$result = mysqli_query($conn,$query);

			$info = array();

			if (!$result)
			{
				trigger_error ("ERROR ".mysqli_error($conn),E_USER_ERROR);
				return null;
			}

			while ($row = mysqli_fetch_row($result))
			{
				for ($i=0; $i<count($row);$i++)
				{
					$info[self::getFieldName($result,$i)] = $row[$i];
				}
			}
			return $info;
		}

		public static function showTables($conn)
		{
			// echo "<br />\n SHOW TABLES($conn) <br />\n";			
			$result = mysqli_query($conn,'SHOW TABLES');

			$tables = array();
			$i = 0;

			while ($row = mysqli_fetch_row($result))
			{
				$tables[$i] = $row[0];
				$i++;
			}

			return $tables;
		}

	 }
	/*
	 $obj = new MySQLDriver();
	 $conn = $obj->connect('lulamolusco','root','sbrubles','neocommunity',null,null);
	 $obj->infoSchemaColumn('Log','ownerID',$conn);
	$obj->showTables($conn);*/