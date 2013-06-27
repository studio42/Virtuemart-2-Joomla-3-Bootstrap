<?php
/**
*
* Users table
*
* @package	VirtueMart
* @subpackage User
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: user_shoppergroup.php 2420 2010-06-01 21:12:57Z oscar $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTableData'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtabledata.php');

/**
 * user_shoppergroup_xref table class
 * The class is used to link users to shoppergroups.
 *
 * @package	VirtueMart
 * @author Max Milbers
 */

 class TableVmusers extends VmTableData {

	/** @var int Vendor ID */
	var $virtuemart_user_id		= 0;
	var $user_is_vendor 		= 0;
	var $virtuemart_vendor_id 	= 0;
	var $customer_number 		= 0;
	var $perms					= 0;
	var $virtuemart_paymentmethod_id = 0;
	var $virtuemart_shipmentmethod_id = 0;
	var $agreed					= 0;


	/**
	 * @param $db Class constructor; connect to the database
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_vmusers', 'virtuemart_user_id', $db);

		$this->setPrimaryKey('virtuemart_user_id');

		$this->setLoggable();

		$this->setTableShortCut('vmu');
	}

}
