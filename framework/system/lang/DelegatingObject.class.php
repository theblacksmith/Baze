<?php
/**
 * Arquivo DelegatingObject.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */

/**
 * Classe DelegatingObject
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.system
 */
class DelegatingObject {
	protected $delegates = array ();
	
	protected function _call($methodName, $parameters) {
		$delegated = false;
		$methodName = strtolower ( $methodName );
		
		foreach ( $this->delegates as $delegate ) {
			$class = new ReflectionClass ( $delegate );
			$methods = $class->getMethods ();
			
			foreach ( $methods as $method ) {
				if ($methodName == $method->getName ()) {
					$delegated = true;
					
					return call_user_func ( array ($delegate, $methodName ), $parameters );
				}
			}
		}
		
		if (! $delegated) {
			$step = array_pop ( debug_backtrace () );
			
			die ( sprintf ( 'Fatal error: Call to undefined method %s() in %s on line %s.', $step ['function'], $step ['file'], $step ['line'] ) );
		}
		return null;
	}
	
	public function enlist($delegate) {
		$this->delegates [] = $delegate;
	}
	
	public function dismiss($delegate) {
		if (($key = array_search ( $delegate, $this->delegates )) === true) {
			unset ( $this->delegates [$key] );
			return true;
		}
		
		return false;
	}
	
	public static function dismissAll($object) {
		unset ( $object );
	}
}