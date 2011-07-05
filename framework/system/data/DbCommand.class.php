<?php
/**
 * Arquivo DbCommand.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

import( 'system.data.DataReader' );
	
/**
 * Classe DbCommand
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package  Baze.classes.data
 */
class DbCommand
{

	private $wrongChars = "\"\\?:|";
	private $commandNonQuery = array ('create','delete','insert','update','use','alter','start','commit','if','prepare');
	private $commandQuery = array ('select','show');
	private $storeProcedures = array ('sp_');
	private $FLAG_SP = null;

	/**
	 * DbCommand Properties
	 *
	 * @access private
	 **/
	private $affectedRows;				//int
	private $commandText;				//string
	private $commandTimeOut;			//int
	private $dbConn;					//DbConnection Object
	private $dbParamentersCollection; 	//Collection Object
	private $transaction;				//DbTransaction Object
	private $dbDriver;					//IDbDriver Obj
	private $dbms;						//Vocabulary Types of Compatibles databases

	/**
	* <b>Function Construct DbCommand</b><br>
	* This is the construction method of the class.
	* The parameter $dbConn must contain the DbConnection object or will
	* be emitted an error message.  The parameter $dbms must contain the
	* driver to be used in the functions that use access to the database.
	* The parameter $time, for default, possesss value 10.
	*
	* @param Object $dbConn - DbConnection Object
	* @param Vocabulary $dbms - dbms specific of the database
	* @param int $time - value (in seconds) of time limits operations query
	**/
	public function __construct ($dbConn,$dbms,$time = 10)
	{
		if (! $this->setDriver($dbConn))
		{
			trigger_error ("Error: invalid connection type in contruct function DbCommand!");
		}

		$this->setTimeOut($this->dbConn->getConnection(),$time);

		$this->affectedRows = null;
		$this->dbConn = $dbConn;
		$this->dbDriver = $dbConn->getDriver();
		$this->dbms = $dbms;
	}
	
	/**
	 * Function beginTransaction()<br><br>
	 * 
	 * @author Luciano (26/10/2006)
	 * @param string $transactionName
	 */
	public function beginTransaction($transactionName = null)
	{
		return $this->dbDriver->beginTransaction($this->dbConn->getConnection(), $transactionName);
	}
	
	/**
	 * Function commit()<br><br>
	 * 
	 * @author Luciano (26/10/2006)
	 * @param string $transactionName
	 */
	public function commit($transactionName = null)
	{
		return $this->dbDriver->commit($this->dbConn->getConnection(), $transactionName);
	}
	

	/**
	 * Function Escape String
	 * This function defines the value of number of lines affected for
	 * commands INSERT, DELETE or UPDATE.
	 *
	 * @param resultset $resource - resultset for the command nonQuery
	 */
	private function defineAffectedRows($resource)
	{
		if ($resource && $this->FLAG_SP === false)
		{
			$identifier = array();
			$identifier["conn"] = $this->dbConn->getConnection();
			$identifier["resource"] = $resource;
			$this->affectedRows = $this->dbDriver->getAffectedRows($identifier);
		}
		else
		{
			$this->affectedRows = null;
		}
	}

	/**
	 * Function Escape String
	 * This function receives one string by parameter and makes treatment in specials characters.
	 *
	 * @param $str
	 * @return string - string treated
	 */
	public function escapeString ($str)
	{
		return $this->dbDriver->escapeString ($this->dbConn->getConnection(),$str);
	}

	/**
	 * <b>Function Execute Non Query</b><br>
	 * This function makes the execution of command SQL of the type 'nonQuery'.
	 * The parameter $nonQuery is optional. If $nonQuery possess  null value then the
	 * execution of the command 'executeNonQuery' of driver receives
	 * command contained in the property $commandText from the class. But the property
	 * $commandText cannot be empty.
	 *
	 * Note: 'nonQuery' command means not to be of type "SELECT"
	 *
	 * @param string $nonQuery - query string of the command to execute
	 * @return boolean	- true true if it was success.
	 *					- false if query had some error of execution
	 * 					of the command or if the command is not of
	 * 					the type 'nonQuery'.
	 */
	public function executeNonQuery ($nonQuery = null)
	{
		if ( $nonQuery == null   &&   $this->commandText !== null )
		{
			$resource = $this->dbDriver->executeNonQuery($this->dbConn->getConnection(),$this->commandText);
			if ($resource)
			{
				$this->defineAffectedRows($resource);
				return true;
			}
			
			$noError = $this->dbDriver->getErrorIndex($resource, $this->dbConn->getConnection());
			$msgError = $this->dbDriver->getErrorMessage($resource, $this->dbConn->getConnection());
			$error = $noError." ".$msgError;

			trigger_error ("ERROR: $error",E_USER_ERROR);
			return false;
		}

		if ( $this->validateNonQuery($nonQuery) && $this->validateParm($nonQuery) )
		{
			$resource = $this->dbDriver->executeNonQuery($this->dbConn->getConnection(),$nonQuery);
			$this->commandText = $nonQuery;

			if ($resource)
			{
				$this->defineAffectedRows($resource);
				return true;
			}

			$noError = $this->dbDriver->getErrorIndex($resource, $this->dbConn->getConnection());
			$msgError = $this->dbDriver->getErrorMessage($resource, $this->dbConn->getConnection());
			$error = $noError.' '.$msgError;
			trigger_error ("ERROR: $error");
			trigger_error("<br />ERROR: verify your command text and certifies that it does not contain errors!\n<br />$nonQuery",E_USER_ERROR);
			return false;
		}
		trigger_error ("ERROR: Invalid NonQuery command in executeQuery() or setCommandText()!\n<br />$nonQuery", E_USER_ERROR);
		return false;
	}

	/**
	 * <b>Function Execute Query</b><br>
	 * This function makes the execution of command SQL of the type 'Query'.
	 * The parameter $Query is optional. If $query contains null value then the
	 * execution of the command 'executeQuery' of driver receives
	 * command contained in the property $commandText from the class. But the property
	 * $commandText cannot be empty.
	 *
	 * Note: 'Query' command means to be only command SELECT
	 *
	 * @param string $query - query string of the command to execute
	 * @return boolean	- true true if it was success.
	 *					- false if query had some error of execution
	 * 					of the command or if the command is not of
	 * 					the type 'Query'.
	 */
	public function executeQuery ($query = null)
	{

		if ( $query == null   &&   $this->commandText !== null )
		{
			$resource = $this->dbDriver->executeQuery($this->dbConn->getConnection(),$this->commandText);
		}
		else 
		{
			if ( $this->validateQuery($query) )
			{
				$resource = $this->dbDriver->executeQuery($this->dbConn->getConnection(),$query);
				$this->commandText = $query;
			}
			else
			{
				trigger_error ('ERROR: Invalid query in DbCommand: line '.__LINE__.' on '.__FUNCTION__.'!',E_USER_ERROR);
				return null;
			}
		}
		
		if ($resource === false)
		{ $this->showErrorMessage($resource, $query); } 

		$obj = new DataReader ($this->dbDriver,$resource,$this->commandText,$this->dbms,$this->dbConn->getConnection());
		$this->affectedRows = $this->defineAffectedRows(null);
		return $obj;
	}

	/**
	 * <b>Function Execute Scalar</b><br>
	 * This function makes the execution of command SQL of the type 'Scalar'.
	 * The parameter $scalar is optional. If $scalar contains null value then the
	 * execution of the command 'executeQuery' of driver receives
	 * command contained in the property $commandText from the class. But the property
	 * $commandText cannot be empty.
	 *
	 * Note: 'Scalar' means to make a search that will return an only result.
	 *
	 * @param string $scalar - query string of the command to execute
	 * @return boolean	- true true if it was success.
	 *					- false if query had some error of execution of the command.
	 */
	public function executeScalar ($escalar)
	{
		if ($escalar==null && $this->validateQuery($this->commandText))
		{
			$resource = $this->dbDriver->executeQuery($this->dbConn->getConnection(),$this->commandText);
		}
		else
		{
			if ($this->validateParm($escalar))
			{
				$resource = $this->dbDriver->executeQuery($this->dbConn->getConnection(),$escalar);
				$this->commandText = $escalar;
			}
			else
			{
				trigger_error ("Error: Invalid QUERY command in executeQuery() or setCommandText()!");
				return false;
			}
		}
		$res = $this->dbDriver->fetchRow($resource);
		return $res[0];
	}

	/**
	 * Function Get Affected Rows
	 * This function returns the number of affected rows for the last
	 * command from type INSERT, DELETE, UPDATE.
	 *
	 * @return int - number of rows affected by non-sql command
	 */
	public function getAffectedRows ()
	{
		return $this->affectedRows;
	}

	/**
	 * Function Get Command Text
	 * This function returns the last SQL command text
	 *
	 * @return string - last SQL command text
	 */
	public function getCommandText ()
	{
		return $this->commandText;
	}

	/**
	 * Function Get Connection
	 * This function returns connection link of database,
	 * using the function 'getConnection' of the DbConnection object.
	 *
	 * @return connection - connection link of database.
	 */
	public function getConnection()
	{
		return $this->dbConn->getConnection();
	}

	/**
	 * Function getErrorMessage
	 * This function returns the last error message from the database.
	 * Maybe in some databases the index of the error cannot be gotten.
	 *
	 * @return string - last error message
	 */
	 public function getErrorMessage($resource)
	 {
			 $errorNo = $this->dbDriver->getErrorIndex($resource, $this->dbConn->getConnection());
		 $errorMsg= $this->dbDriver->getErrorMessage($resource, $this->dbConn->getConnection());

		 $error = $errorNo.' '.$errorMsg;
		 return $error;
	 }

	/**
	 * Function getInfo()
	 * This function returns all the possible information from a table.
	 * The order of the results contained in the Array depends of the specified data base.
	 *
	 * @param string $tableName - name of the specified table
	 * @return array - all info and flags of specified table
	 */
	public function getInfo($tableName)
	{
		return $this->dbDriver->helpTable($tableName,$this->dbConn->getConnection());
	}
	
	/**
	 * Function getLastID()<br><br>
	 * This function return the last autoincrement value of last INSERT command.
	 * 
	 * @author Luciano (23/10/2006)
	 * 
	 * @param string $tableName
	 * @param string $fieldName
	 * @return int
	 */
	public function getLastID($tableName = null, $fieldName = null)
	{
		return $this->dbDriver->getLastID($this->dbConn->getConnection(), $tableName, $fieldName);
	}
	

	/**
	 * Function Set Command Text
	 * This function set value of the property commandText for the value of the parameter $command.
	 * First, the prime command of the parameter is analyzed validating it.
	 *
	 * @param string $command - SQL command text
	 * @return boolean - true if the $command is valid
	 * 					- false if have an invalid value in $commad parameter
	 */
	public function setCommandText($command)
	{
		if ( empty($command) )
		{
			trigger_error ('Error: Command Text is null in setCommandText() parameter!');
			return false;
		}

		if ( !$this->validateParm($command) )
		{
			trigger_error ("Error: Invalid command type ($command) in setCommandText() parameter!");
			return false;
		}

		$this->commandText = $command;
		return true;
	}

	/**
	 * Function Set Connection
	 * This function set the connection driver using the function
	 * 'getDriver' of the DbConnection object.
	 *
	 * @access private
	 * @param Object $conn - DbConnection Object
	 * @return true if successful or false if fail
	 **/
	private function setDriver($conn)
	{
		if ( empty($conn) )
		{
			trigger_error ("Error: Connection type is null in setDriver()!");
			return false;
		}

		$this->dbConn = $conn;
		$this->dbDriver = $conn->getDriver();
		return true;
	}

	/**
	 * Function setTimeOut
	 * This function adjusts 'timeOut' property of database.
	 *
	 * @param $time
	 * @return true if successful or false if fail
	 **/
	public function setTimeOut ($time)
	{
		if ( !is_integer($time) )
		{
			return false;
		}

		if ($this->dbDriver->setTimeOut($this->dbConn->getConnection(),$time))
		{
			return true;
		}
		return false;
	}

	/**
	 * Function showErrorMessage()
	 * This function has as objective to show the last error message of database.
	 * String of error is composed for the number of the error and the text.
	 * However, in some data base, the number or the text can be inexistent.
	 *
	 * @access private
	 * @return string - $error
	 **/
	 private function showErrorMessage($resultset, $commandText)
	 {
		 $errorNo = $this->dbDriver->getErrorIndex($resultset, $this->dbConn->getConnection());
		 $errorMsg= $this->dbDriver->getErrorMessage($resultset, $this->dbConn->getConnection());

		 $error = $errorNo." ".$errorMsg;
		 trigger_error ("ERROR: $error<br />\n".$commandText,E_USER_ERROR);
	 }

	/**
	 * Function Validate Parm
	 * This function validates the first command of SQL string.
	 * A command SQL can assume the role of "query", "nonQuery" or "store procedure".
	 *
	 * @access private
	 * @param $parm
	 * @return true or false
	 **/
	private function validateParm($parm)
	{
		$commandPrime = "";

		//if (strlen($parm) != strcspn($parm,$this->wrongChars))
		//{ return false; }

		if (substr_count($parm,' ')>0)
		{
			$commandPrime = substr($parm,0,strpos($parm,' '));
			$commandPrime = strtolower($commandPrime);
		}
		else
		{ $commandPrime = $parm; }

		if (in_array($commandPrime,$this->commandNonQuery) || in_array($commandPrime,$this->commandQuery))
		{
			$this->FLAG_SP = false;
			return true;
		}

		if (substr_count($commandPrime,'sp_')>0)
		{
			$this->FLAG_SP = true;
			return true;
		}
		return false;
	}

	/**
	 * Function Validate Query
	 * This function verifies if first command SQL is of the type 'query'.
	 * Command 'query ' is always a command that returns a result from search.
	 *
	 * @access private
	 * @param $query
	 * @return true of false
	 **/
	private function validateQuery($query)
	{
		$query = strtolower($query);

		//Buscando o primeiro comando da query (geralmente 'SELECT')
		if (substr_count($query,' ')>0)
		{
			$command = trim(substr($query,0,strpos($query,' ')));
		}
		else
		{ $command = trim($query); }

		
		if (in_array($command,$this->commandQuery))
		{
			$this->FLAG_SP = false;
			return true;
		}

		if (substr_count($command,'sp_')>0)
		{
			$this->FLAG_SP = true;
			return true;
		}
		return false;
	}

	/**
	 * <b>Function Validate NonQuery</b><br>
	 * This function verifies if first command SQL is of the type 'nonQuery'.
	 * Command 'nonQuery' is a command that returns a boolean result.

	 * @acess private
	 * @param $nonQuery
	 * @return true or false
	 **/
	private function validateNonQuery ($nonQuery)
	{
		$command = substr($nonQuery,0,strpos($nonQuery,' '));
		$command = strtolower($command);

		if (in_array($command,$this->commandQuery))
		{ return false; }

		return true;
	}

	/**
	 * Function getTablesNames()<br>
	 *
	 * @author Luciano (11/07/06)
	 * @return Array - all user table names in the database
	 */
	public function getTablesNames()
	{
		return $this->dbDriver->showTables($this->dbConn->getConnection());
	}
}