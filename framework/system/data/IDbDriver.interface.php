<?php
/**
 * Arquivo IDbDriver.interface.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */

/**
 * Interface IDbDriver
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.data
 */
 interface IDbDriver
 {
	 public static function beginTransaction($conn,$transaction_name);
	 public static function connect ($host,$user,$pass,$dbName,$port,$socket);
	 public static function pconnect ($host,$user,$pass,$dbName,$port,$socket);
	 public static function close($conn);
	 public static function commit($conn,$commit_name);
	 public static function escapeString($conn,$string);
	 public static function executeNonQuery($conn,$query);
	 public static function executeQuery($conn,$query);
	 public static function fetchRow($resultset);
	 public static function fetchField($resultset,$identifier);
	 public static function getAffectedRows($identifier);
	 public static function getErrorIndex($resourceLink, $conn);
	 public static function getErrorMessage($resourceLink, $conn);
	 public static function getFieldIndex($resultset,$fieldName);
	 public static function getFieldName ($resultset,$fieldIndex);
	 public static function getLastID($conn,$tableName,$fieldName);
	 public static function helpTable($tableName,$conn);
	 public static function resultFieldsCount($resultset);
	 public static function resultRowsCount($resultset);
	 public static function rollback($conn,$rollback_name);
	 public static function rowSeek($resultset,$offSet);
		 public static function setTimeOut($conn,$time);
 }