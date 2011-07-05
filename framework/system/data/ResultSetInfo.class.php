<?php
/**
 * Arquivo ResultSetInfo.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
import( 'system.Collection' );
import( 'system.data.DbTable' );
import( 'system.data.DbTableColumn' );

/**
 * Classe ResultSetInfo
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
class ResultSetInfo
{
	private $result;
	private $conn;
	private $dbDriver;
	
	private $tables;
	private $columns;
	
	private $auxcont;
	
	function __construct ($result, $dbDriver, $conn)
	{
		$this->result = $result;
		$this->conn = $conn;
		$this->dbDriver = $dbDriver;
		
		$this->tables = new Collection();
		$this->columns = new Collection();
		
		$this->setColumnsInfo();
		
		$this->setTablesInfo(); 
		$this->auxcont = 0;
	}
	
	/**
	 * Function setColumnsInfo()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 */
	private function setColumnsInfo()
	{
		$numColumns = $this->dbDriver->resultFieldsCount($this->result);
		
		for ($i=0; $i<$numColumns; $i++)
		{
			$columnName = $this->dbDriver->getFieldName($this->result,$i);
			
			$identifier = array (
									'index' => $i, 
									'name' => $columnName,
									'tableName' => $this->dbDriver->getTableName($this->result, $i, $this->conn) 
								);
								
			$meta = $this->dbDriver->fetchField($this->result, $identifier);
			$meta['index'] = $i;
			$this->auxcont ++;
			$this->columns->add( new DbTableColumn($meta), $columnName);
		}
	}
	
	/**
	 * Function setTablesInfo()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 */
	private function setTablesInfo()
	{
		$numColumns = count($this->columns);
		
		$tables = array();
		
		for ($i=0; $i<$numColumns; $i++)
		{
			$tableName = $this->columns->getByPosition($i)->getTableName();
			
			if (! in_array($tableName,$tables) )
			{
				
				$table = new DbTable($tableName);
				
				$meta = $this->dbDriver->helpTable($tableName, $this->conn);
				
				$table->setMeta($meta);
				
				$this->tables->add($table, $tableName);
			}
		}
	}
	
	/**
	 * Function getNumColumns()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 * 
	 * @return int - number of columns in result
	 */
	public function getNumColumns()
	{
		return $this->columns->size();
	}
	
	/**
	 * Function getColumn()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 * 
	 * @param mixed $index
	 * @return array - info of specified column
	 */
	public function getColumn($index)
	{
		if (is_int($index))
		{
			return $this->columns->getByPosition($index);
		}
		if (is_string($index))
		{
			return $this->columns->get($index);
		}
		return null;
	}
	
	/**
	 * Function getColumns()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 * 
	 * @return Collection of DbTableColumn
	 */
	public function getColumns()
	{
		return $this->columns;
	}
	
	/**
	 * Function getTables()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 * 
	 * @return Collection of DbTable
	 */
	public function getTables()
	{
		return $this->tables;
	}
}