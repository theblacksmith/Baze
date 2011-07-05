<?php
/**
 * Arquivo DataRow.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
import ( 'system.Collection' );

/**
 * Classe DataRow
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package  Baze.classes.data
 */
 class DataRow
 {
		/**
	 * DataRow Property
	 *
	 * @access private
	 **/
	private $fieldCollection; //collection of DataField Objects
	private $numFields;

	/**
	 * Function _construct<br>
	 * This is the construction method of the DataRow class.
	 * When receiving an Array from objects of the DataField type,
	 * each object is added in the fields collection of the class.
	 *
	 * @param array $row - array of DataField Objects
	 **/
	function __construct($row)
	{
		$this->numFields = 0;
		$this->fieldCollection = new Collection ();

		if ($row)
		{
			$count = count($row);

			for ($i=0; $i<$count; $i++)
			{
				$this->fieldCollection->add($row[$i]);
				$this->numFields++;
			}
		}
		else
		{
			trigger_error ("ERROR: Invalid parameter in __construct function in DataRow Class!",E_USER_ERROR);
		}
	}

	/**
	 * Function getField<br>
	 * This function returns the DataField object specified for the parameter $numCol.
	 *
	 * Note: $index need be in range of list collection ($index >=0 || $index < numFields)
	 *
	 * @param $numCol - number of specified column for field required or null if $numCol have a invalid value.
	 * @return DataField Object or null in failure.
	 **/
	public function getField($numCol)
	{
		if ($this->verifyIndex($numCol))
		{
			return $this->fieldCollection->get($numCol);
		}

		trigger_error ("Error: Invalid index parameter in getField() function.");
		return null;
	}

	/**
	 * Function getValue<br>
	 * This function return real value of cell identified by $index
	 *
	 * Note: $index need be in range of list collection ($index >=0 || $index < numFields)
	 *
	 * @param int $index
	 * @return mixed
	 **/
	public function getValue($index)
	{
		if ($this->verifyIndex($index))
		{
			$objField = $this->fieldCollection->get($index);
			return $objField->getValue();
		}
		return null;
	}

	/**
	 * Function numFields
	 * This function returns the numFields property of this class.
	 *
	 * @return int - number of fields
	 **/
	public function numFields()
	{
		return $this->numFields;
	}

	/**
	 * Function toArrayValues<br>
	 * This function gets the value contained in each field in the collection and stores it in an Array.
	 *
	 * @return array of all values elements in collection list.
	 **/
	public function toArrayValues()
	{
		$row = array();

		for ($i=0;$i<$this->numFields;$i++)
		{
			$objField = $this->fieldCollection->get($i);
			$row[$i] = $objField->getValue();
		}
		return $row;
	}

	/**
	 * Function verifyIndex<br>
	 * This function confirms if $index is in the scale of the collection of the list
	 *
	 * @access private
	 * @param int $index
	 * @return boolean true if $index not out of range or false
	 **/
	 private function verifyIndex ($index)
	 {
		 if (!is_integer($index))
		 { return false; }

		 if ($index >= 0 && $index < $this->numFields)
		 { return true; }

		 return false;
	 }
}