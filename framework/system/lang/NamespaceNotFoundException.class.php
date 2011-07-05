<?php
/**
 * Arquivo da classe NamespaceNotFoundException
 *
 * Esse arquivo ainda nÃ£o foi documentado
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @namespace system.lang
 */

/**
 * Classe NamespaceNotFoundException
 *
 * Essa classe ainda nÃ£o foi documentada
 *
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 1.0
 * @namespace system.lang
 */
class NamespaceNotFoundException extends SystemException
{
	/**
	 * The required namespace
	 *
	 * @var string
	 */
	private $ns;

	/**
	 * Constructor
	 *
	 * @param string $ns The missing namespace
	 * @param string|ErrorMsg $message $extraParams  String or ErrorMsg instance Custom message
	 * @param array $strings Strings to replace in custom message hooks ({0}, {1}, ...)
	 */
	public function __construct($ns, $message='', array $tokens = array(), $guityStep = null)
	{
		$this->ns = $ns;

		if($message == '')
			$message = ErrorMsg::$Import_NamespaceNotFound;

		$tokens['ns'] = $ns;

		parent::__construct($message, $tokens, $guityStep);
	}

	/**
	 * @return string The required namespace
	 */
	public function getNamespace() {
		return $this->ns ;
	}

	/**
	 * @param string $ns The required namespace
	 */
	public function setNamespace($ns) {
		$this->ns = $ns ;
	}
}