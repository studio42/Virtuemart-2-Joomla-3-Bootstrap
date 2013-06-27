<?php
/**
*
* Calc controller
*
* @package	VirtueMart
* @subpackage Calc
* @author Max Milbers, jseros
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: paymentmethod.php 6071 2012-06-06 15:33:04Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Calculator Controller
 *
 * @package    VirtueMart
 * @subpackage Calculation tool
 * @author Max Milbers
 */
class VirtuemartControllerPaymentmethod extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	public function __construct() {
		parent::__construct();

	}


	function save($data = 0){
		$data = JRequest::get('post');
		// TODO disallow html in paym_name ?
		$data['payment_name'] = JRequest::getVar('payment_name','','post','STRING',JREQUEST_ALLOWHTML);
		$data['payment_desc'] = JRequest::getVar('payment_desc','','post','STRING',JREQUEST_ALLOWHTML);

		parent::save($data);
	}

}
// pure php no closing tag
