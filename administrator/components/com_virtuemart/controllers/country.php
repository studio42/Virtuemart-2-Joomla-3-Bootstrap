<?php
/**
*
* Country controller
*
* @package	VirtueMart
* @subpackage Country
* @author RickG
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL 2, see COPYRIGHT.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: country.php 5255 2012-01-10 15:55:50Z electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Country Controller
 *
 * @package    VirtueMart
 * @subpackage Country
 * @author RickG
 */
class VirtuemartControllerCountry extends VmController {

	function __construct() {
		parent::__construct();

	}

}

//pure php no tag
