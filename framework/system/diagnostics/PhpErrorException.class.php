<?php 
/**
 * Arquivo da classe PhpErrorException
 * 
 * Esse arquivo ainda nÃ£o foi documentado
 * 
 * @author Saulo Vallory 
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package system.exception
 */

/** 
 * Classe PhpErrorException 
 * 
 * Essa classe ainda nÃ£o foi documentada
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks 
 * @license http://baze.saulovallory.com/license 
 * @version SVN: $Id$ 
 * @since 1.0 
 * @package system.exception
 */
class PhpErrorException extends BazeException
{
	/**
	 * The environment vars provided by PHP error handler
	 *
	 * @var array
	 */
	private $envVars;
	
	/**
	 * Sets the environment vars provided by PHP error handler
	 *
	 * @param array $envVars
	 */
	public function setEnvVars($envVars)
	{
		$this->envVars = $envVars;
	}
	
	/**
	 * Sets the environment vars provided by PHP error handler
	 *
	 * @return array
	 */
	public function getEnvVars()
	{
		return $this->envVars;
	}
}