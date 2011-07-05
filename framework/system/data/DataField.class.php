<?php
/**
 * Arquivo DataField.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/**
 * Classe DataField
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package  Baze.classes.data
 */
 class DataField
 {
		/**
	 * DataField Properties
	 *
	 * @access private
	 */
	private $column;	//DbTableColumn Object
	private $length;	//int
	//private $table;		//DbTable Object
	private $value;		//mixed

	/**
	 * <b>Contruct Function</b><br>
	 * This is the constructor method of this class.
	 *
	 * Note: The parameter $column must be of the DataColumn type
	 *
	 * @param mixed $value - Any value that the field may possess.
	 * @param DataColumn $column - DataColumn of the column SQL result.
	 **/
	public function __construct ($value,$column)
	{
		if ( ($column!= null)   &&   (get_class($column)!='DataColumn') )
		{
			trigger_error ("ERROR: Invalid second parameter in __construct function method",E_USER_ERROR);
		}
		else
		{
			$this->column = $column;
			$this->length = strlen ($value);
			$this->value = $value;

			//$this->table = $column->getTable();
		}
	}

	/**
	 * <b>Function getAlias()</b><br>
	 * This function returns the alias property that the field possesss, case has it.
	 *
	 * @return string - alias of field column or null, case the field does not have an alias
	 **/
	public function getAlias()
	{
		if (  get_class($this->column)=='DataColumn'  )
		{
			return $this->column->getAlias();
		}
		return null;
	}

	/**
	 * <b>Function getColumn()</b><br>
	 * This function returns the DbTableColumn object from the field,
	 * case this is associated with the some table of the data base
	 *
	 * @return Object - DbTableColumn object
	 **/
	public function getColumn()
	{
		if (  get_class($this->column)=='DataColumn'  )
		{
			return $this->column->getDbColumn();
		}
		return null;
	}

	/**
	 * <b>Function getFlags</b><br>
	 * This function returns the associative flags of the field
	 * <br>
	 * Note: Each database possesss your model to show the flags.
	 * Verify the returned Array to get the information in correct way.
	 *
	 * @return array - flags of field properties
	 **/
	public function getFlags()
	{
		if (  get_class($this->column)=='DataColumn'  )
		{
			$column = $this->column->getDbColumn();
			return $column->getFlags();
		}
		return null;
	}

	/**
	 * <b>Function getIndex()</b><br>
	 * This function returns the field indice in relation to the gotten result.
	 *
	 * @return int - index of field column
	 **/
	public function getIndex()
	{
		if (  get_class($this->column)=='DataColumn'  )
		{
			return $this->column->getIndex();
		}
		return null;
	}

	/**
	 * <b>Function getLength()</b><br>
	 * This function returns length property, or either, the internal length of the field
	 *
	 * @return int - space in n-bytes of value stored in field
	 **/
	public function getLength()
	{
		return $this->length;
	}

	/**
	 * <b>Function getName</b><br>
	 * This function returns the name of the field in relation to the table of
	 * the data base.  If the field is a calculated column, the function returns the alias.
	 *
	 * @return string - name of field column
	 **/
	public function getName()
	{
		if (  get_class($this->column)=='DataColumn'  )
		{
			return $this->column->getName();
		}
		return null;
	}

	/**
	 * <b>Function getMaxLength</b><br>
	 * This function returns the maxLength property, or either, the size
	 * of the field in relation to the gotten result.
	 *
	 * @return 	int - space in n-bytes of max length of in field or
	 * 			null if this field is a nonQuery field
	 **/
	public function getMaxLength()
	{
		if (  get_class($this->column)=='DataColumn'  )
		{
			return $this->column->getMaxLength();
		}
		return null;
	}

	/*
	 * <b>Function getTable</b><br>
	 * This function returns the DbTable object associated with the field
	 *
	 * @return Object - DbTable object or null if this column does not belong to a DbTable
	 *
	public function getTable()
	{
		$column = $this->column->getDbColumn();

		if ($column != null)
		{
			return $column->getTable();
		}
		return null;
	}*/

	/**
	 * <b>Function getType</b><br>
	 * This function returns the type of the field in relation to the table of the database.
	 *
	 * @return vocabulary - value type of field column
	 **/
	public function getType()
	{
		if (  get_class($this->column)=='DataColumn'  )
		{
			$column = $this->column->getDbColumn();
			return $column->getType();
		}
		return null;
	}

	/**
	 * <b>Function getValue<b><br>
	 * This function returns the value contained in the field
	 *
	 * @return mixed - value stored in field
	 **/
	public function getValue()
	{
		return $this->value;
	}
}