<?php
/**
 * Arquivo Script.class.php
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
 * Classe Script
 * 
 * @author Saulo Vallory
 * @copyright 2007 Neoconn Networks
 * @license http://baze.saulovallory.com/license
 * @version SVN: $Id$
 * @since 0.9
 * @package Baze.classes.web
 */
class Script extends Component
{
	/**#@+
	 * Tag Properties <script>
	 *
	 * @access private
	 * @var string
	 */
	protected $charset;
	protected $defer;
	protected $language;
	protected $src;
	protected $type;
	protected $xmlSpace;
	/**#@-*/

	function __construct($src = "", $type = "text/javascript")
	{
		$this->set("src", $src);
		$this->set("type", $type);
	}

	/**
	 * @access public
	 * @return string
	 */
	public function getXHTML()
	{

		return $this->getDefaultXHTML() .
		       $this->getPropertiesList() .
			   ">" .
		       $this->closeTag();
	}

	/**
	 * @access private
	 * @return string
	 */
	private function getDefaultXHTML()
	{
		$xhtml = null;

		$xhtml .= "\n<script";

		return $xhtml;
	}

	/**
	 * @access private
	 * @return string
	 */
	private function closeTag()
	{
		return "</script>";
	}
}