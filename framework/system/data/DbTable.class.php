<?php
/**
 * Arquivo DbTable.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
import( 'system.Collection' );
import( 'system.data.DbTableColumn' );

/**
 * Classe DbTable
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
 class DbTable
 {
		/**
	 * Data Table Property
	 * @access private
	 **/
	private $alias;				// string
	private $columnCollection; 	// collection of DbTableColumn Objects
	private $name;				// string
	private $numCols;			// int
	private $meta;				// meta

	/**
	 * Construct Function
	 * This is the constructor method of the DbTable class.
	 *
	 * @param DbDriver $dbDriver - driver of specified dbms
	 * @param string $name - name of table
	 **/
	function __construct ($name = null)
	{
		$this->columnCollection = new Collection();
		$this->name = $name;
	}

	/**
	 * Function addColumn
	 * This function creates and adds the DbTableColumn object in the
	 * collection of columns. First, all the information of the specific
	 * field are extracted by the function 'fetchField' of driver.
	 *
	 * @param resultset $resultset - resource
	 * @param DbTableColumn $dbColumn 
	 */
	public function addColumn(DbTableColumn $dbColumn)
	{
		$dbColumn->setTable($this);
		
		$this->columnCollection->add($dbColumn, $dbColumn->getName() );
		$this->numCols++;
	}

	/**
	 * Function getColumn
	 * This function returns the object of the specific column.
	 * The parameter 'to identifier' can contain the index or name of the field.
	 * Through 'identifier', the function makes a search of the field in its collection.
	 *
	 * @param mixed $identifier - identification value of specified column to get, may be a name or index
	 * @return DbTableColumn Object or null if unsuccessful
	 **/
	public function getColumn($identifier)
	{
		if (is_integer($identifier) && ($identifier < 0 || $identifier > $this->numCols))
		{
			$obj = $this->columnCollection->getByPosition($identifier);
			return $obj;
		}

		if (is_string($identifier))
		{
			return $this->columnCollection->get($identifier);
		}
		return null;
	}

	/**
	 * Function getName
	 * This function returns the table name.
	 *
	 * @return string - name property of DbTable
	 **/
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Function getAlias
	 * This function returns the table alias
	 *
	 * @return string - alias property of DbTable
	 **/
	public function getAlias()
	{
		return $this->alias;
	}

	/**
	 * Function setAlias
	 * This function set the table alias
	 *
	 * @param string $alias - alias of table
	 * @return void
	 */
	public function setAlias($alias)
	{
		$this->alias = $alias;
	}
	
	/**
	 * Function setMeta
	 * This function set the table alias
	 *
	 * @param array  $meta - info of table
	 * @return void
	 */
	public function setMeta($meta)
	{
		$this->meta = $meta;
	}
	
	
	/**
	 * Function setName()<br><br>
	 * This function set the table name
	 * 
	 * @author Luciano (24/10/2006)
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
 }