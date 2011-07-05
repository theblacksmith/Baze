<?php
/**
 * Arquivo DataColumn.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/**
 * Classe DataColumn
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package  Baze.classes.data
 */
 class DataColumn
 {
		/**
	 * DbTableColumn Properties
	 *
	 * @access private
	 **/
	private $dbTableColumn;	//DbTableColumn Object
	private $name;			//string
	private $alias;			//string
	private $index;			//int

	/**
	 * Function __construct
	 * This is the construction method of the class.
	 * When creating the object, the parameter $dbTableColumn may contain null value or the DbTableColumn object.
	 *
	 * @param Object $dbTableColumn - DbTableColumn Object
	 * @param string $name - real name of column (not alias)
	 * @param string $alias - alias of column
	 * @param int $index - index of column (essential). Parameter must be a integer and above of zero.
	 **/
	function __construct ($dbTableColumn,$name,$alias,$index)
	{
		if (! is_integer($index) || $index<0)
		{
			trigger_error ("Error: Index parameter invalid in Construct Function DataColumn");
		}

		$this->dbTableColumn = $dbTableColumn;
		$this->name = $name;
		$this->alias = $alias;
		$this->index = $index;
	}

	/**
	 * Function getDbColumn
	 * This function returns the DbTableColumn object, case the column contained
	 * in resultset is not a calculated column.
	 *
	 * @return DbTableColumn Object or null
	 **/
	public function getDbColumn()
	{
		return $this->dbTableColumn;
	}

	/**
	 * Function getAlias
	 * This function returns the alias property this class.
	 *
	 * @return string - alias of DataColumn class.
	 **/
	public function getAlias()
	{
		return $this->alias;
	}

	/**
	 * Function getIndex
	 * This function returns the index property this class.
	 *
	 * @return int - index of DataColumn class.
	 **/
	public function getIndex()
	{
		return $this->index;
	}

	/**
	 * Function getName<br>
	 * This function returns the name property this class.
	 *
	 * @return string - name property of column
	 **/
	public function getName ()
	{
		if ($this->dbTableColumn = null)
		{
			return $this->alias;
		}
		return $this->name;
	}

	/**
	 * Function getMaxLength<br>
	 * This function returns the MaxLength of specified database column,
	 * if the class has the object of the database column.
	 *
	 * @return int - space in n-bytes of max length of the column,
	 * 			or null if this class no have an object DbTableColumn.
	 **/
	public function getMaxLength()
	{
		if ($this->dbTableColumn != null)
		{
			return $this->dbTableColumn->getMaxLength();
		}
		return null;
	}

 }