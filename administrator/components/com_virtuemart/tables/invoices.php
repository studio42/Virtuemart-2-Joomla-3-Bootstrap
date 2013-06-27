<?php
/**
*
* Orders table
*
* @package	VirtueMart
* @subpackage Orders
* @author RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: orders.php 5339 2012-01-30 16:42:50Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Orders table class
 * The class is is used to manage the orders in the shop.
 *
 * @package	VirtueMart
 * @author Max Milbers
 */
class TableInvoices extends VmTable {

	/** @var int Primary key */
	var $virtuemart_invoice_id = 0;

	var $virtuemart_vendor_id = 0;

	var $virtuemart_order_id = 0;

	var $invoice_number = '';

	var $order_status = '';

	var $xhtml = '';

	/**
	 *
	 * @author Max Milbers
	 * @param $db Class constructor; connect to the database
	 *
	 */
	function __construct($db) {
		parent::__construct('#__virtuemart_invoices', 'virtuemart_invoice_id', $db);

		$this->setUniqueName('invoice_number');
		$this->setLoggable();

		$this->setTableShortCut('inv');
	}

}

