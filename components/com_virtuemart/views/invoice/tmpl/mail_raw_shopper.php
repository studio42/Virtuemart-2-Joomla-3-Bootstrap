<?php

/**
 *
 * Layout for the shopper mail, when he confirmed an ordner
 *
 * The addresses are reachable with $this->BTaddress, take a look for an exampel at shopperadresses.php
 *
 * With $this->cartData->paymentName or shipmentName, you get the name of the used paymentmethod/shippmentmethod
 *
 * In the array order you have details and items ($this->orderDetails['details']), the items gather the products, but that is done directly from the cart data
 *
 * $this->orderDetails['details'] contains the raw address data (use the formatted ones, like BTaddress). Interesting informatin here is,
 * order_number ($this->orderDetails['details']['BT']->order_number), order_pass, coupon_code, order_status, order_status_name,
 * user_currency_rate, created_on, customer_note, ip_address
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers, Valerie Isaksen
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
echo strip_tags(JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER')) . "\n" . "\n";
echo strip_tags(JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_SUMMARY', $this->vendor->vendor_store_name)) . "\n" . "\n";

echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_CONTENT', $this->shopperName, $this->vendor->vendor_store_name, $this->orderDetails['details']['BT']->order_total, $this->orderDetails['details']['BT']->order_number, $this->orderDetails['details']['BT']->order_pass, $this->orderDetails['details']['BT']->created_on) . "\n" . "\n";

echo "\n" . strip_tags(JText::sprintf('COM_VIRTUEMART_MAIL_ORDER_STATUS', $this->orderDetails['details']['BT']->order_status_name));

echo "\n\n";
$nb = count($this->orderDetails['history']);
if ($this->orderDetails['history'][$nb - 1]->customer_notified && !(empty($this->orderDetails['history'][$nb - 1]->comments))) {
    echo $this->orderDetails['history'][$nb - 1]->comments;
}
    echo "\n\n";

    echo "\n\n";
    echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER_LINK') . ' : ' . JURI::root() . 'index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $this->orderDetails['details']['BT']->order_number . '&order_pass=' . $this->orderDetails['details']['BT']->order_pass . "\n";

    if (!empty($this->orderDetails['details']['BT']->customer_note)) {
	echo "\n" . strip_tags(JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_QUESTION', $this->orderDetails['details']['BT']->customer_note));
    }
    echo "\n\n";

//TODO if silent registration logindata
//TODO if Paymentmethod needs Bank account data of vendor
//We may wish to integrate later a kind of signature


