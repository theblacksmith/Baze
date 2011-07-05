<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Controller_Request_Exception */
require_once 'Zend/Controller/Request/Exception.php';

/** Zend_Controller_Request_Abstract */
require_once 'Zend/Controller/Request/Abstract.php';

/** Zend_Uri */
require_once 'Zend/Uri.php';

/**
 * Zend_Controller_Request_Http
 *
 * HTTP request object for use with Zend_Controller family.
 *
 * @uses Zend_Controller_Request_Abstract
 * @package Zend_Controller
 * @subpackage Request
 */
class Zend_Controller_Request_Http extends Zend_Controller_Request_Abstract
{

}