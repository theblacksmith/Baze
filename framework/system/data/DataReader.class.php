<?php
/**
 * Arquivo DataReader.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

 //import("system.data.NomeDaClasse");
import( 'system.Collection' );
import( 'system.data.SQLParser' );
import( 'system.data.DataField' );
import( 'system.data.DataRow' );
import( 'system.data.DataColumn' );
import( 'system.data.DbTable' );
import( 'system.data.ResultSetInfo' );	

/**
 * Classe DataReader
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package  Baze.classes.data
 */
 class DataReader
 {
	/**
	 * DataReader Properties
	 */

	 /**#@+
	 * @access private
	 */
	private $commandText;			// String: command SQL that it generated the resource
	private $dataColumnsCollection;	// Collection DataColumn Objects
	private $dbDriver;				// IDbDriver Object
	private $dbTableCollection;		// Collection of DbTable Objects
	private $fieldCount;			//int
	//private $isClosed;			//boolean
	private $item;					//int
	private $numRows;				//int
	private $parser;				//object parser of SQL command
	private $resultset;				//resource
	private $resultInfo;			//all info of resource
	/**#@- */

	/**
	 * Function __construct
	 * This is the construction function of the classroom.  It is essential that
	 * the parameters $result, $dbDriver and $ct are not empty.
	 *
	 * @param IDbDriver $dbDriver
	 * @param resource $result
	 * @param string $ct
	 * @param vocabulary $dbms
	 * @param connection $conn
	 */
	function __construct ($dbDriver, $result, $ct, $dbms,$conn)
	{
		if ( empty($dbDriver) || empty($result) || empty($ct) )
		{
			trigger_error ('ERROR: Invalid parameters in construct function in DataReader class.',E_USER_ERROR);
		}

		$this->dbDriver = $dbDriver;
		$this->resultset = $result;
		
		$this->resultInfo = new ResultSetInfo($result, $dbDriver, $conn);

		$this->commandText = $ct;

		$this->fieldCount = $this->dbDriver->resultFieldsCount($this->resultset);
		$this->numRows = $this->dbDriver->resultRowsCount($this->resultset);
		$this->item = 0;			
		
		$this->dbTableCollection = $this->resultInfo->getTables();
		$this->dataColumnsCollection = $this->resultInfo->getColumns();
	}


	/**
	 * Function fetchRow<br>
	 * This function has as objective to create and to return a DataRow object.
	 * The function gets an Array contends the values of the current line of the result using the command
	 * fetchRow of driver. For each value of the Array, a DataField object is created and kept in one another Array.
	 * In the end, is created a DataRow object sending Array DataField the object and returned this object.
	 *
	 * @return DataRow Object - if no values in $row, will be returned null.
	 **/
	public function fetchRow()
	{
		$row = $this->dbDriver->fetchRow($this->resultset);

		if ($row === false)
		{
			return null;
		}

		$count = count($row);
		$arrayFieldsObjs = array();

		for ($i=0; $i<$count; $i++)
		{
			$arrayFieldsObjs[$i] = new DataField($row[$i],$this->dataColumnsCollection->getByPosition($i));
		}
		$objRow = new DataRow($arrayFieldsObjs);
		$this->item++;

		return $objRow;
	}

	/**
	 * Function getColumnName()<br><br>
	 * This function returns the column name.  It receives the
	 * parameter $columnNumber that contains the index of the column and
	 * makes the search of its name.
	 * 
	 * @param int $columnNumber - index of specified column
	 * @return string name of column specified or null if index is invalid.
	 */
	public function getColumnName ($columnNumber)
	{
		if ( is_integer($columnNumber) )
		{
			if ($columnNumber < 0 || $columnNumber >= $this->fieldCount)
			{
				trigger_error ("ERROR: Invalid index parameter in function getColumnName. Column Number ($columnNumber).");
				return null;
			}
			$column = $this->resultInfo->getColumn($columnNumber);
			if (! is_object($column))
			{
				echo $this->resultInfo->getColumn($columnNumber - 2)->getName()."<br />\n";
				echo $this->fieldCount."<br />\n";
				echo $columnNumber."<br />\n";
				echo $this->commandText; exit;					
			}
			return $column->getName();
		}
		trigger_error ("ERROR: Invalid parameter. Not integer numeric value in function getColumnName in DataReader class!");
		return null;
	}

	/**
	 * Function getColumnNumber<br>
	 * This function returns the column number.  It receives the
	 * parameter $columnName that contains the name of the column and
	 * makes the search of its index.
	 *
	 * @param string $columnName
	 * @return int number of column specified in $columnName or
	 *         -1 if $columnName not found.
	 */
	public function getColumnNumber ($columnName = null)
	{
		$column = $this->resultInfo->getColumn($columnName);
		
		if (is_object($column))
		{
			return $column->getIndex();
		}
		return null;
	}

	/**
	 * Function Get Field<br>
	 * This function returns the field object referring in the specified field in parameter $fieldIdentifier, through the
	 * function fetchrow of driver in the current line.
	 * The identification $fieldIdentifier can contain the index(int) of the requested field or its name(string).
	 *
	 * @param mixed $fieldIdentifier - number or name of the column specific
	 * @return DataField Object specified or
	 *         null if $fieldIdentified have a invalid value
	 */
	public function getField ($fieldIdentifier = null) //note: fieldIdentifier can be a int(index) or string(column name)value
	{
		$objField = null;
		$row = $this->dbDriver->fetchRow($this->resultset);

		if ( is_integer($fieldIdentifier))
		{
			if (($fieldName = $this->getColumnName($fieldIdentifier)))
			{
				$objField = new DataField ($row[$fieldIdentifier],$this->dataColumnsCollection->get($fieldIdentifier));
			}
			else
			{
				trigger_error ("ERROR: Invalid column index in <b>getField</b> method [Index = $fieldIdentifier].",E_USER_ERROR);
				return null;
			}
		}

		if ( is_string($fieldIdentifier))
		{
			if (($fieldNumber = $this->getColumnNumber($fieldIdentifier)) >= 0)
			{
				$objField = new DataField ($row[$fieldNumber],$this->dataColumnsCollection->get($fieldNumber));
			}
			else
			{
				trigger_error ("ERROR: Invalid column name in <b>getField</b> method [Name = $fieldIdentifier].",E_USER_ERROR);
				return null;
			}
		}

		$this->dbDriver->rowSeek($this->resultset,$this->item);
		return $objField;
	}

	/**
	 * Function getIndexRow<br>
	 * This function returns the position from the current line in work.
	 *
	 * @return int - index of current row
	 */
	public function getIndexRow ()
	{
		return $this->item;
	}

	/**
	 * Function getSQLParser<br>
	 * This function returns the SQLParser object that was generated in the creation of this class.
	 *
	 * @return Object SQLParser
	 **/
	public function getSQLParser()
	{
		return $this->parser;
	}

	/**
	 * Function Get Value<br>
	 * This function returns the value contained in the specified field in parameter $fieldIdentifier, through the
	 * function fetchrow of driver in the current line.
	 * The identification $fieldIdentifier can contain the index(int) of the requested field or its name(string).
	 *
	 * @return mixed - value that the field has. If index of the field or the name was not valid, an error will be emitted returning null.
	 **/
	public function getValue ($fieldIdentifier)
	{
		if ($fieldIdentifier === null)
		{
			trigger_error ("Error: Field identifier is null in getValue parameter.");
			return null;
		}

		if ( is_integer($fieldIdentifier))
		{
			$res = $this->dbDriver->fetchRow($this->resultset,$fieldIdentifier);
			$this->dbDriver->rowSeek($this->resultset,$this->item);
			return $res[$fieldIdentifier];
		}

		if ( is_string($fieldIdentifier))
		{
			$index = $this->getColumnNumber($fieldIdentifier);

			if ($index<0)
			{
				trigger_error ("Error: Invalid column name in getValue parameter.");
				return null;
			}
			$this->dbDriver->rowSeek($this->resultset,$this->item);
			$res = $this->dbDriver->fetchRow($this->resultset,$index);
			$this->dbDriver->rowSeek($this->resultset,$this->item);
			return $res[$index];
		}

		trigger_error ("Error:Invalid type of getValue parameter.");
		return null;
	}

	/**
	 * Function Has Rows<br>
	 * Verify if exist rows in resultset property
	 *
	 * @return boolean - true if numRows>0 or false
	 **/
	public function hasRows()
	{
		if ($this->numRows > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Function indexTableName<br>
	 * This function receives the parameter $tableName contends the specific
	 * table name, returning its index in the table collection of the class.
	 *
	 * @param string $tableName - name of specified table
	 * @return int - index of table in dbTableCollection
	 **/
	public function indexTableName($tableName)
	{
		//obter o numero de tabelas na coleçãos
		$count = $this->dbTableCollection->size();

		//para cada tabela obter o nome
		for ($i=0; $i<$count; $i++)
		{
			$objTable = $this->dbTableCollection->getByPosition($i);
			$tName = $objTable->getName();

			//se for igual o $tableName retornar o indicie
			if ($tName == $tableName)
			{ return $i; }
		}
		//terminou a contagem de tabelas e não achou, retornar indice negativo
		return -1;

	}

	/**
	 * Function Next<br>
	 * This function executes the command fetchrow of driver returning no value.
	 * The hand starts to indicate the next field to each successful execution.
	 *
	 * @return boolean 	- The command returns true case fetchrow was successful,
	 * 					or false case occurs the opposite.
	 **/
	public function next ()
	{
		if ($this->dbDriver->fetchRow($this->resultset))
		{
			if ($this->item < $this->numRows-1)
			{
				$this->item ++;
				return true;
			}
		}
		return false;
	}

	/**
	 * Function numFields<br>
	 * This function returns the number of fields (columns) existing in the result,
	 * using the command resultFieldsCount of driver.
	 *
	 * @return int - number of fields in resultset
	 **/
	public function numFields()
	{
		return $this->dbDriver->resultFieldsCount($this->resultset);
	}

	/**
	 * Function Num Rows<br>
	 * The function returns the number of rows in the result.
	 *
	 * @return int - number of rows in the resultset.
	 **/
	public function numRows()
	{
		return $this->dbDriver->resultRowsCount($this->resultset);
	}

	/**
	 * Function Row Seek<br>
	 * This function moves the internal hand of the result for the requested indice.
	 * In case that the indice is not between the correct interval, an error will be emitted.
	 *
	 * @param int $offSet - the number of specified row, must be in range of total rows of result.
	 * @return boolean - Returns true in success case.  From the the opposite, it returns false.
	 **/
	public function rowSeek ($offSet)
	{
		if ( is_integer($offSet) )
		{
			if ($offSet < 0 || $offSet > $this->numRows-1)
			{
				trigger_error ("ERROR: Invalid index parameter in function rowSeek in DataReader class!",E_USER_ERROR);
				return false;
			}

			if ( $this->dbDriver->rowSeek($this->resultset,$offSet) )
			{
				$this->item = $offSet;
				return true;
			}
		}
		trigger_error ("ERROR: Invalid type parameter. Not string value in function ".__FUNCTION__." in DataReader class!");
		return false;
	}
}