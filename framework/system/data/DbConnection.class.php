<?php
/**
 * Arquivo DbConnection.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
import( 'system.data.IDbDriver' );
import( 'system.data.MySQLDriver' );
import( 'system.data.PgSQLDriver' );
import( 'system.data.MSSQLDriver' );
import( 'system.data.DbCommand' );

/**
 * Classe DbConnection
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
class DbConnection
{
	/**
	 * Constants Enum
	 *
	 */
	/**The connection to the data source is broken. This can occur only after the connection has been opened. A connection in this state may be closed and then re-opened. (This value is reserved for future versions of the product.)  */
	const CONNECTION_BROKEN = 1;

	/**The connection is closed. */
	const CONNECTION_CLOSED = 2;

	/**The connection object is connecting to the data source. (This value is reserved for future versions of the product.)  */
	const CONNECTION_CONNECTING = 3;

	/**The connection object is executing a command. (This value is reserved for future versions of the product.)  */
	const CONNECTION_EXECUTING = 4;

	/**The connection object is retrieving data. (This value is reserved for future versions of the product.)  */
	const CONNECTION_FETCHING = 5;

	/**The connection is open. */
	const CONNECTION_OPEN = 6;

	/**The connection not ready. */
	const UNPREPARED = 7;

	/**
	 * Constants Strings
	 *
	 * @access public
	 */
	private $wrongChars = "\"\\?*:/@|<> " ;

	/**
	 * DataConnection Properties
	 *
	 * @access private
	 */
	private $connection;		//resource
	private $connectionTimeOut;	//int
	private $database;			//ex: "meubanco"
	private $dataSource;		//string
	private $dbAddress;			//ex: "localhost || ip"
	private $dbDriver;			//IDbDriver Object
	private $dbms; 				// O nome propriamente dito do SGBD usado
	private $password;			//string
	private $port;
	private $serverVersion;		//string
	private $socket;
	private $state;				//ConnectionState
	private $transName;			//string name of transaction
	private $user;				//ex: "joaodasilva"

	/**
	 * Function construct DataConnection <br>
	 * construtor method of class DataConnection.
	 *
	 * @param string $connectionString 	- string of connection keeping
	 * 									the following standard: DBMS://username:password@address/[databaseName]
	 * 									Comment: 'address' may be in the following format: host[:port]
	 **/
	function __construct($connectionString = null)
	{
		$this->state = self::UNPREPARED;

		if (!empty($connectionString) && is_string($connectionString))
		{
			$this->state = self::CONNECTION_CLOSED;
			$this->setConnectionString($connectionString);
		}
	}

	/**
	 * Function Begin Transaction
	 **/
	public function beginTransaction($transaction_name = null)
	{
		if ($this->state = self::CONNECTION_OPEN)
		{
			$this->transName = $transaction_name;
			return $this->dbDriver->beginTransaction($this->connection,$transaction_name);
		}
		trigger_error ("ERROR: Database is not ready or not open.",E_USER_ERROR);
		return false;
	}

	/**
	 * Function Close
	 * A boolean function to close the connection with the data base.
	 *
	 * @access public
	 * @return true or false
	 **/
	public function close()
	{
		$ret = $this->dbDriver->close($this->connection);
		if ($ret)
		{
			$this->state = self::CONNECTION_CLOSED;
			$this->connection = null;
		}
		return $ret;
	}

	/**
	 * Function Create Command
	 * This function, simply creates a DbCommand object sending as parameter its proper instance and used driver.
	 *
	 * @return DbCommand Object
	 **/
	public function createCommand()
	{
		$obj = new DbCommand($this,$this->dbms);
		return $obj;
	}

	/**
	 * Function commit
	 * @param string $commit_name
	 */
	public function commit ($commit_name = null)
	{
		if ($this->state = self::CONNECTION_OPEN)
		{
			if ( $commit_name != null )
			{
				return $this->dbDriver->commit($this->connection,$commit_name);
			}
			return $this->dbDriver->commit($this->connection,$this->transName);
		}

		trigger_error ("ERROR: Database is not ready or not open.",E_USER_ERROR);
		return false;

	}

	/**
	 * Function getConnection
	 * This function returns the connection link to the database.
	 *
	 * @return link - connection link
	 **/
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Function Get Database()<br>
	 * this function returns the database name contained in connection string.
	 *
	 * Note:In case that the user changes the database with SQL command, the
	 * property dbName will not be modified, because this property keeps only
	 * database name sent by connection string.
	 *
	 * @access public
	 * @return string of database property
	 **/
	public function getDatabase()
	{
		return $this->database;
	}

	/**
	 * Function Get DbAddress()<br>
	 * This function returns the database address (IP, host, URL...).
	 *
	 * @access public
	 * @return string of the dbAddress property
	 **/
	public function getDbAddress()
	{
		return $this->dbAddress;
	}

	/**
	 * Function Get Dbms()<br>
	 * This function returns the type of the used database.
	 *
	 * @access public
	 * @return string of the dbms property
	 **/
	public function getDBMS()
	{
		return $this->dbms;
	}

	/**
	 * Function getDriver
	 * This function returns the driver used for the specific database.
	 *
	 * @return string - Driver of dbms used
	 **/
	public function getDriver()
	{
		return $this->dbDriver;
	}

	/**
	 * Function getPort
	 * This function returns the used port for the specific database.
	 *
	 * @return string - port of link connection
	 **/
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * Function Get State()<br>
	 * This function returns the database status.
	 * The content depends of the flag activated for the functioning of the data base.
	 *
	 * @return the state of connection
	 **/
	function getState()
	{
		return $this->state;
	}

	/**
	 * Function Get User()<br>
	 * This function returns the user name of connectino string
	 * or adjusted for the function 'setUser'.
	 *
	 * @access public
	 * @return string of the user property
	 **/
	public function getUser ()
	{
		return $this->user;
	}

	/**
	 * Function Open()<br>
	 * This function make a single connection with database. Persistent connection
	 * will be accried through case that it receives 'true' for parameter.
	 *
	 * @return The function normally have true/false returns. However, in case that already to exist
	 * a connection opened in the database, will be trigger an error message.
	 *
	 * @access public
	 * @param boolean $persistent
	 * @param int $flag
	 **/
	public function open($persistent = false)
	{

		if ($this->state == self::CONNECTION_CLOSED || $this->state == self::UNPREPARED)
		{
			if ($persistent == true)
			{
				/*MySQLi do not support persistent connection*/
				if ($this->dbms == "MySQL")
				{
					trigger_error ("ERROR: Not possible make a persistent connection in MYSQLi DataBaze!");
					return false;
				}
				if (!($this->connection = $this->dbDriver->pconnect($this->dbAddress,$this->user,$this->password,$this->database,$this->port,$this->socket)))
				{
					trigger_error ("ERROR: the connection cannot be established.",E_USER_ERROR);
					return false;
				}
			}

			if (!($this->connection = $this->dbDriver->connect($this->dbAddress,$this->user,$this->password,$this->database,$this->port,$this->socket)))
			{
				trigger_error ("ERROR: the connection cannot be established.",E_USER_ERROR);
				return false;
			}
		}
		else
		{
			trigger_error ("ERROR: Has an open connection or the connection cannot be established.",E_USER_ERROR);
			return false;
		}
		$this->state = self::CONNECTION_OPEN;
		return true;
	}

	/**
	 * Function rollback<br>
	 *
	 */
	public function rollback ($rollback_name = null)
	{
		if ($this->state = self::CONNECTION_OPEN)
		{
			if ( $rollback_name != null )
			{
				return $this->dbDriver->rollback($this->connection,$rollback_name);
			}
			return $this->dbDriver->rollback($this->connection,$this->transName);
		}

		trigger_error ("ERROR: Database is not ready or not open.",E_USER_ERROR);
		return false;

	}

	/**
	 * Function Set Connection String <br>
	 * This function disassembles the connection string in parts,
	 * attributing the values of the specific properties or either,
	 * following the necessary formatting, the function extracts of
	 * connection string the type, the user, the password, the address
	 * and the database name for the connection with the database.
	 *
	 * Note: - string of connection keeping the following standard:
	 * 					DBMS://username:password@address/[databaseName]
	 * 					Comment: 'address' may be in the following format: host[:port]
	 *
	 * @param $connectionString
	 * @return true or false
	 **/
	public function setConnectionString ($connectionString)
	{
		if (!is_string($connectionString))
		{
			trigger_error ("ERROR: Invalid Conection Type",E_USER_ERROR);
			return false;
		}

		if ((substr_count($connectionString,":")<2) && (substr_count($connectionString,"/")!=3) && (substr_count($connectionString,"://")!=1) )
		{
			trigger_error ("ERROR: Invalid Conection Type!",E_USER_ERROR);
			return false;
		}

		if ($this->state == self::CONNECTION_OPEN)
		{
			trigger_error ("ERROR: a connection type is open!");
			return false;
		}

		$i = strlen($connectionString);

		$dbms = substr($connectionString,0,strpos($connectionString,':'));

		$connectionString = substr($connectionString,strlen($dbms)+3,$i);
		$i = strlen($connectionString);

		$user = substr($connectionString,0,strpos($connectionString,':'));

		$connectionString = substr($connectionString,strlen($user)+1,$i);
		$i = strlen($connectionString);

		$pass = substr($connectionString,0,strpos($connectionString,'@'));

		$connectionString = substr($connectionString,strlen($pass)+1,$i);
		$i = strlen($connectionString);

		$dbAddress = substr($connectionString,0,strpos($connectionString,'/'));

		$port = null;
		$k=0;
		if (substr_count($dbAddress,':')>0)
		{
			$aux = $dbAddress;
			$k = strlen($aux);
			$dbAddress = substr($aux,0,strpos($aux,':'));

			$aux = substr($aux,strlen($dbAddress)+1,$k);
			$port = $aux;
			$k = strlen($port)+1;

		}

		$connectionString = substr($connectionString,strlen($dbAddress)+$k+1,$i);
		$i = strlen($connectionString);

		$database = $connectionString;

		if ( empty($dbms) || empty($user) || empty($dbAddress))
		{
			trigger_error ("ERROR: Invalid Conection Type",E_USER_ERROR);
			return false;
		}

		$this->setDBMS($dbms);
		$this->setUser($user);
		$this->setPassword($pass);
		$this->setPort($port);
		$this->setDbAddress($dbAddress);
		$this->setDatabase($database);

		$this->state = self::CONNECTION_CLOSED;
		return true;
	}

	/**
	 * Function Set Database()<br>
	 * Attribues to the value received from parameter for database property
	 *
	 * @access public
	 * @param string $database
	 */
	public function setDatabase($database)
	{
		if ($this->state == self::CONNECTION_CLOSED || $this->state== self::UNPREPARED)
		{
			if (strlen($database) != strcspn($database,$this->wrongChars))
			{
				trigger_error ("Error: invalid char in setdatabase() parameter");
				return false;
			}

			$this->database = $database;
			return true;
		}
		trigger_error ("Error: it has an open connection");
		return false;
	}

	/**
	 * Function Set DbAddress()<br>
	 * Attribues to the value received from parameter for variable dbAddress
	 *
	 * @access public
	 * @param string $dbAddress
	 */
	public function setDbAddress($dbAddress)
	{
		if ($this->state == self::CONNECTION_CLOSED || $this->state== self::UNPREPARED)
		{
			if (strlen($dbAddress) != strcspn($dbAddress,$this->wrongChars))
			{
				trigger_error ("Error: invalid char in setDbAddress() parameter");
				return false;
			}

			$this->dbAddress = $dbAddress;
			return true;
		}

		trigger_error ("Error: it has an open connection");
		return false;
	}

	/**
	 * function setDbms()<br>
	 * it attributes to the value received from parameter for variable dbms
	 *
	 * @access public
	 * @param string $dbms
	 */
	function setDBMS($dbms)
	{
		if ($this->state == self::CONNECTION_CLOSED || $this->state== self::UNPREPARED)
		{
			//se houver caracteres inválidos: retorna erro
			if (strlen($dbms) != strcspn($dbms,$this->wrongChars))
			{
				trigger_error ("Error: invalid char in setDbms() parameter");
				return false;
			}

			$dbms = strtolower($dbms);

			switch ($dbms)
			{
				case 'mysql':
				case 'mysqli':
					$this->dbms = "MySQL";
					break;

				case 'pgsql':
				case 'postgree':
				case 'postgres':
				case 'postgre':
				case 'postgresql':
						$this->dbms = "PgSQL";
						break;

				case 'mssql':
				case 'sqlserver':
						$this->dbms = "MSSQL";
						break;

				default:
					trigger_error("Error: Not compatible DataBaze in ConnectionString parameter.\nPlease use MYSQLI, POSTGRES or MSSQL DataBaze", E_USER_ERROR);
					return false;
			}
			$nameDriver = $this->dbms."Driver";
			$this->dbDriver = new $nameDriver();

			return true;
		}

		trigger_error ("Error: it has an open connection");
		return false;
	}

	/**
	 * Function Set Password()<br>
	 * it attributes to the value received from parameter for variable password
	 *
	 * @access public
	 * @param string $pass
	 **/
	public function setPassword($pass)
	{
		if ($this->state == self::CONNECTION_CLOSED || $this->state== self::UNPREPARED)
		{
			if (strlen($pass) != strcspn($pass,$this->wrongChars))
			{
				trigger_error("ERROR: invalid char in setPassword() parameter.",E_USER_ERROR);
				return false;
			}

			$this->password = $pass;
			return true;
		}
		trigger_error ("Error: it has an open connection");
		return false;
	}

	/**
	 * Function setPort<br>
	 * The function adjusts the connection port.
	 *
	 * @param string $port
	 **/
	public function setPort($port)
	{
		if (!empty($port))
		{
			$this->port = $port;
			return true;
		}
		return false;
	}

	/**
	 * Function Set User()<br>
	 * The function attributes the value received from parameter for variable user
	 *
	 * @access public
	 * @param string $user
	 **/
	public function setUser($user)
	{
		if ($this->state == self::CONNECTION_CLOSED || $this->state== self::UNPREPARED)
		{
			if (strlen($user) != strcspn($user,$this->wrongChars))
			{
				trigger_error ("Error: invalid char in setUser() parameter");
				return false;
			}

			$this->user = $user;
			return true;
		}
		trigger_error ("ERROR: it has an open connection");
		return false;
	}

}