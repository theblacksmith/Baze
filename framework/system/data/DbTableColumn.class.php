<?php
/**
 * Arquivo DbTableColumn.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/**
 * Classe DbTableColumn
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
 class DbTableColumn
 {
	 
	/**
	 * DbTableColumn Properties
	 *
	 * @access private
	 */
	private $flags; 	//array
	private $maxLength;	//int
	private $name;		//string
	private $table;		//DbTable Object
	private $tableName;	//string
	private $type;		//Vocabulary (float, int...)
	private $index;


	/**
	 * Function __construct
	 * This is the constructor method of the DbTableColumn class.
	 *
	 * @param array $meta - information on the column.
	 * @param DbTable $table - DbTable Objetc
	 */
	function __construct ($meta, $table = null)
	{
		if ($meta == null)
		{
			trigger_error ('Error: Null parameters in __construct function in DbTableColumn class!');
		}

		$this->maxLength 	= $meta['size'];
		$this->name			= $meta['name'];			
		$this->tableName	= $meta['tableName'];
		$this->type 		= $meta['type'];
		$this->index		= $meta['index'];

		if (is_object($table) && get_class($table) == 'DbTable')
		{
			$this->table = $table;
		}
		
		$this->setFlags($meta);
	}

	
	/**
	 * Function getFlags
	 * This function returns the Array flags of the column.
	 *
	 * @return array - flags property of DbTableColumn
	 */
	public function getFlags()
	{
		return $this->flags;
	}

	/**
	 * Function getMaxLength
	 * This function returns the maximum value that the column can support.
	 *
	 * @return int  - maxLength property of DbTableColumn
	 */
	public function getMaxLength()
	{
		return $this->maxLength;
	}
	
	/**
	 * Function getIndex()<br><br>
	 * 
	 * @author Luciano (25/20/2006)
	 * @return int
	 */
	public function getIndex()
	{
		return $this->index;
	}
	

	/**
	 * Function getName
	 * This function returns the database name of the column.
	 *
	 * @return string - name property of the DbTableColumn class
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Function getTable
	 * This function returns the DbTable Object of the column.
	 *
	 * @return object - DbTable object property of the DbTableColumn class
	 */
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * Funciton getTableName
	 * This function returns the table name of the column.
	 *
	 * @return string - tableName property of the DbTableColumn class
	 */
	public function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * Function getType
	 * This function returns the type of the column.
	 *
	 * @return string - type property of the DbTableColumn class.
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Function isBlob
	 * This function returns the boolean flag "isBlob" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 */
	public function isBlob()
	{
		return $this->flags['isBlob'];
	}

	/**
	 * Function isMultipleKey
	 * This function returns the boolean flag "isMultipleKey" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 */
	public function isMultipleKey()
	{
		return $this->flags['isMultipleKey'];
	}

	/**
	 * Function isNotNull
	 * This function returns the boolean flag "isNotNull" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 */
	public function isNotNull()
	{
		return $this->flags["isNotNull"];
	}

	/**
	 * Function isNumeric
	 * This function returns the boolean flag "isNumeric" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 */
	public function isNumeric()
	{
		return $this->flags["isNumeric"];
	}

	/**
	 * Function isPrimaryKey
	 * This function returns the boolean flag "isPrimaryKey" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 **/
	public function isPrimaryKey()
	{
		return $this->flags["isPrimaryKey"];
	}

	/**
	 * Function isUniqueKey
	 * This function returns the boolean flag "isUniqueKey" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 **/
	public function isUniqueKey()
	{
		return $this->flags["isUniqueKey"];
	}

	/**
	 * Function isUnsigned
	 * This function returns the boolean flag "isUnsigned" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 **/
	public function isUnsigned()
	{
		return $this->flags["isUnsigned"];
	}

	/**
	 * Function isZeroFill
	 * This function returns the boolean flag "isZeroFill" from flags property of the column.
	 *
	 * @return boolean 	- The boolean value can assume 1 or 0, true or false, true or null, 't' or 'f'
	 * 					depending on the database that is in use.
	 **/
	public function isZeroFill()
	{
		return $this->flags["isZeroFill"];
	}

	/**
	 * Function setFlags
	 * This function adjusts possible flags of the column. Keeping to the
	 * boolean values in the array flag property
	 *
	 * @return void
	 **/
	private function setFlags ($meta)
	{
		$this->flags["isBlob"] 		 = $meta["isBlob"];
		$this->flags["isMultipleKey"]= $meta["isMultipleKey"];
		$this->flags["isNotNull"] 	 = $meta["isNotNull"];
		$this->flags["isNumeric"] 	 = $meta["isNumeric"];
		$this->flags["isPrimaryKey"] = $meta["isPrimaryKey"];
		$this->flags["isUniqueKey"]  = $meta["isUniqueKey"];
		$this->flags["isUnsigned"] 	 = $meta["isUnsigned"];
		$this->flags["isZeroFill"] 	 = $meta["isZeroFill"];
	}
	
	/**
	 * Function setTable()<br><br>
	 * 
	 * @author Luciano (24/10/2006)
	 * 
	 * @param DbTable $table
	 */
	public function setTable(DbTable $table)
	{
		$this->table = $table;
	}
	
	/**
	 * Function setIndex()<br><br>
	 * 
	 * @author Luciano (25/10/2006)
	 * 
	 * @param int $index
	 */
	public function setIndex($index)
	{
		if (is_int($index) && $index >= 0)
		{
			$this->index = $index;
			return true;
		}
		return false;
	}
 }