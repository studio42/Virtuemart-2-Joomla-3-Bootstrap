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
* @version $Id: orders.php 6210 2012-07-04 00:15:41Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Orders table class
 * The class is is used to manage the orders in the shop.
 *
 * @package	VirtueMart
 * @author RolandD
 * @author Max Milbers
 */
class TableOrders extends VmTable {

	/** @var int Primary key */
	var $virtuemart_order_id = 0;
	/** @var int User ID */
	var $virtuemart_user_id = 0;
	/** @var int Vendor ID */
	var $virtuemart_vendor_id = 0;
	/** @var int Order number */
	var $order_number = NULL;
	var $order_pass = NULL;

	/** @var decimal Order total */
	var $order_total = 0.00000;
	/** @var decimal Products sales prices */
	var $order_salesPrice = 0.00000;
	/** @var decimal Order Bill Tax amount */
	var $order_billTaxAmount = 0.00000;
	/** @var string Order Bill Tax */
	var $order_billTax = 0;
	/** @var decimal Order Bill Tax amount */
	var $order_billDiscountAmount = 0.00000;
	/** @var decimal Order  Products Discount amount */
	var $order_discountAmount = 0.00000;
	/** @var decimal Order subtotal */
	var $order_subtotal = 0.00000;
	/** @var decimal Order tax */
	var $order_tax = 0.00000;

	/** @var decimal Shipment costs */
	var $order_shipment = 0.00000;
	/** @var decimal Shipment cost tax */
	var $order_shipment_tax = 0.00000;
	/** @var decimal Shipment costs */
	var $order_payment = 0.00000;
	/** @var decimal Shipment cost tax */
	var $order_payment_tax = 0.00000;
	/** @var decimal Coupon value */
	var $coupon_discount = 0.00000;
	/** @var string Coupon code */
	var $coupon_code = NULL;
	/** @var decimal Order discount */
	var $order_discount = 0.00000;
	/** @var string Order currency */
	var $order_currency = NULL;
	/** @var char Order status */
	var $order_status = NULL;
	/** @var char User currency id */
	var $user_currency_id = NULL;
	/** @var char User currency rate */
	var $user_currency_rate = NULL;
	/** @var int Payment method ID */
	var $virtuemart_paymentmethod_id = NULL;
	/** @var int Shipment method ID */
	var $virtuemart_shipmentmethod_id = NULL;
	/** @var text Customer note */
	var $customer_note = 0;
	/** @var string Users IP Address */
	var $ip_address = 0;


	/**
	 *
	 * @author Max Milbers
	 * @param $db Class constructor; connect to the database
	 *
	 */
	function __construct($db) {
		parent::__construct('#__virtuemart_orders', 'virtuemart_order_id', $db);

		$this->setUniqueName('order_number');
		$this->setLoggable();

		$this->setTableShortCut('o');
	}

	function check(){

		if(empty($this->order_number)){
			if(!class_exists('VirtueMartModelOrders')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'orders.php');
			$this->order_number = VirtueMartModelOrders::generateOrderNumber((string)time());
		}

		if(empty($this->order_pass)){
			$this->order_pass = 'p_'.substr( md5((string)time().$this->order_number ), 0, 5);
		}

		return parent::check();
	}


	/**
	 * Overloaded delete() to delete records from order_userinfo and order payment as well,
	 * and write a record to the order history (TODO Or should the hist table be cleaned as well?)
	 *
	 * @var integer Order id
	 * @return boolean True on success
	 * @author Oscar van Eijk
	 * @author Kohl Patrick
	 */
	function delete( $id=null , $where = 0 ){

		$this->_db->setQuery('DELETE from `#__virtuemart_order_userinfos` WHERE `virtuemart_order_id` = ' . $id);
		if ($this->_db->query() === false) {
			vmError($this->_db->getError());
			return false;
		}
		/*vm_order_payment NOT EXIST  have to find the table name*/
		$this->_db->setQuery( 'SELECT `payment_element` FROM `#__virtuemart_paymentmethods` , `#__virtuemart_orders`
			WHERE `#__virtuemart_paymentmethods`.`virtuemart_paymentmethod_id` = `#__virtuemart_orders`.`virtuemart_paymentmethod_id` AND `virtuemart_order_id` = ' . $id );
		$paymentTable = '#__virtuemart_payment_plg_'. $this->_db->loadResult();

		$this->_db->setQuery('DELETE from `'.$paymentTable.'` WHERE `virtuemart_order_id` = ' . $id);
		if ($this->_db->query() === false) {
			vmError($this->_db->getError());
			return false;
		}		/*vm_order_shipment NOT EXIST  have to find the table name*/
		$this->_db->setQuery( 'SELECT `shipment_element` FROM `#__virtuemart_shipmentmethods` , `#__virtuemart_orders`
			WHERE `#__virtuemart_shipmentmethods`.`virtuemart_shipmentmethod_id` = `#__virtuemart_orders`.`virtuemart_shipmentmethod_id` AND `virtuemart_order_id` = ' . $id );
		$shipmentName = $this->_db->loadResult();

		if(empty($shipmentName)){
			vmError('Seems the used shipmentmethod got deleted');
			//Can we securely prevent this just using
		//	'SELECT `shipment_element` FROM `#__virtuemart_shipmentmethods` , `#__virtuemart_orders`
		//	WHERE `#__virtuemart_shipmentmethods`.`virtuemart_shipmentmethod_id` = `#__virtuemart_orders`.`virtuemart_shipmentmethod_id` AND `virtuemart_order_id` = ' . $id );
		}
		$shipmentTable = '#__virtuemart_shipment_plg_'. $shipmentName;

		$this->_db->setQuery('DELETE from `'.$shipmentTable.'` WHERE `virtuemart_order_id` = ' . $id);
		if ($this->_db->query() === false) {
			vmError('TableOrders delete Order shipmentTable = '.$shipmentTable.' `virtuemart_order_id` = '.$id.' dbErrorMsg '.$this->_db->getError());
			return false;
		}

		$_q = 'INSERT INTO `#__virtuemart_order_histories` ('
				.	' virtuemart_order_history_id'
				.	',virtuemart_order_id'
				.	',order_status_code'
				.	',created_on'
				.	',customer_notified'
				.	',comments'
				.') VALUES ('
				.	' NULL'
				.	','.$id
				.	",'-'"
				.	',NOW()'
				.	',0'
				.	",'Order deleted'"
			.')';

		$this->_db->setQuery($_q);
		$this->_db->query(); // Ignore error here
		return parent::delete($id);

	}

}

