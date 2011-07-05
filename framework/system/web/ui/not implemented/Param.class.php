<?php
/**
 * Arquivo Param.class.php
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */

/**
 * Import
 */
import( 'system.web.ui.Component' );

/**
 * Classe Param
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Param extends Component
{

	/**#@+
	 * Param Tag Properties
	 *
	 * @access protected
	 * @var string
	 */
	//protected $id;	[Propriedade Herdada]
	protected $name;
	protected $type;
	protected $value;
	protected $valuetype;
	/**#@-*/

	/**
	 * @access public
	 * @return string
	 */
	public function getXHTML()
	{

		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
		       $this->closeTag();
	}

	/**
	 * @access private
	 * @return string
	 */
	private function getDefaultXHTML()
	{
		$xhtml = null;

		$xhtml .= "\n<param";

		return $xhtml;
	}

	/**
	 * @access private
	 * @return string
	 */
	private function closeTag()
	{
		return "/>";
	}
}