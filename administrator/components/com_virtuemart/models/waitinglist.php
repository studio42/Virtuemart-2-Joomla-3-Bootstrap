<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage Product
 * @author Seyi, Max Milbers, Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: waitinglist.php 6425 2012-09-11 20:17:08Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');


/**
 * Model for VirtueMart wating list
 *
 */
class VirtueMartModelWaitingList extends VmModel {

	/**
	 * Load the customers on the waitinglist
	 */
	public function getWaitingusers ($virtuemart_product_id, $is_new = TRUE) {

		if (!$virtuemart_product_id) {
			return FALSE;
		}

		//Sanitize param
		$virtuemart_product_id = (int)$virtuemart_product_id;

		$db = JFactory::getDBO ();
		$q = 'SELECT * FROM `#__virtuemart_waitingusers`
				LEFT JOIN `#__users` ON `virtuemart_user_id` = `id`
				WHERE `virtuemart_product_id`=' . $virtuemart_product_id . '
				' . ($is_new ? ' AND `notified`=0 ' : '');
		$db->setQuery ($q);
		return $db->loadObjectList ();
	}

	/**
	 * Notify customers product is back in stock
	 *
	 * @author RolandD
	 * @author Christopher Rouseel
	 * @todo Add Itemid
	 * @todo Do something if the mail cannot be send
	 * @todo Update mail from
	 * @todo Get the from name/email from the vendor
	 */
	public function notifyList ($virtuemart_product_id, $subject = '', $mailbody = '', $max_number = 0) {

		if (!$virtuemart_product_id) {
			return FALSE;
		}

		//sanitize id
		$virtuemart_product_id = (int)$virtuemart_product_id;
		$max_number = (int)$max_number;

		if (!class_exists ('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}
		$vars = array();
		$waitinglistModel = VmModel::getModel ('waitinglist');
		$waiting_users = $waitinglistModel->getWaitingusers ($virtuemart_product_id);

		/* Load the product details */
		$db = JFactory::getDbo ();
		$q = "SELECT l.product_name,product_in_stock FROM `#__virtuemart_products_" . VMLANG . "` l
				JOIN `#__virtuemart_products` p ON p.virtuemart_product_id=l.virtuemart_product_id
			   WHERE p.virtuemart_product_id = " . $virtuemart_product_id;
		$db->setQuery ($q);
		$item = $db->loadObject ();
		$vars['productName'] = $item->product_name;
		/*
		if ($item->product_in_stock <= 0) {
			return FALSE;
		}
		*/
		$url = JURI::root () . 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id;
		$vars['link'] = '<a href="'. $url.'">'. $item->product_name.'</a>';


		if (empty($subject)) {
			$subject = JText::sprintf('COM_VIRTUEMART_PRODUCT_WAITING_LIST_EMAIL_SUBJECT', $item->product_name);
		}
		$vars['subject'] = $subject;
		$vars['mailbody'] = $mailbody;

		$virtuemart_vendor_id = 1;
		$vendorModel = VmModel::getModel ('vendor');
		$vendor = $vendorModel->getVendor ($virtuemart_vendor_id);
		$vendorModel->addImages ($vendor);
		$vars['vendor'] = $vendor;

		$vars['vendorAddress']= shopFunctions::renderVendorAddress($virtuemart_vendor_id);

		$vendorEmail = $vendorModel->getVendorEmail ($virtuemart_vendor_id);
		$vars['vendorEmail'] = $vendorEmail;

		$i = 0;
		foreach ($waiting_users as $waiting_user) {
			$vars['user'] =  $waiting_user->name ;
			if (shopFunctionsF::renderMail ('productdetails', $waiting_user->notify_email, $vars, 'productdetails')) {
				$db->setQuery ('UPDATE #__virtuemart_waitingusers SET notified=1 WHERE virtuemart_waitinguser_id=' . $waiting_user->virtuemart_waitinguser_id);
				$db->query ();
				$i++;
			}
			if (!empty($max_number) && $i >= $max_number) {
				break;
			}
		}
		return TRUE;
	}

	/**
	 * Add customer to the waiting list for specific product
	 *
	 * @author Seyi Awofadeju
	 * @return insert_id if the save was successful, false otherwise.
	 */
	public
	function adduser ($data) {

		JRequest::checkToken () or jexit ('Invalid Token, in notify customer');

		$field = $this->getTable ('waitingusers');

		if (!$field->bind ($data)) { // Bind data
			vmError ($field->getError ());
			return FALSE;
		}

		if (!$field->check ()) { // Perform data checks
			vmError ($field->getError ());
			return FALSE;
		}

		$_id = $field->store ();
		if ($_id === FALSE) { // Write data to the DB
			vmError ($field->getError ());
			return FALSE;
		}

		//jexit();
		return $_id;
	}

}
// pure php no closing tag
