<?php
defined('_JEXEC') or die('');

/**
 * Renders the email for the shoppers from the waiting list, or who bought this product
 * @package	VirtueMart
 * @subpackage product details
 * @author Seyi
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: mail_raw_notify.php 6300 2012-07-26 00:40:10Z Milbo $
 */

echo $this->vendorAddress;
echo "\n";
echo "\n";
echo JText::sprintf ('COM_VIRTUEMART_MAIL_SHOPPER_NAME', $this->user->name);
echo "\n";
echo "\n";
if(!empty($this->mailbody)) {
	echo $this->mailbody;
} else {
	echo str_replace( "<br />", "\n", JText::sprintf('COM_VIRTUEMART_CART_NOTIFY_MAIL_RAW', $this->productName,$this->link) );
}

echo "\n";

// $uri    = JURI::getInstance();
// $prefix = $uri->toString(array('scheme', 'host', 'port'));
$link = JURI::root().'index.php?option=com_virtuemart';

echo "\n\n";
$link= JHTML::_('link', $link, $this->vendor->vendor_name) ;

//	echo JText::_('COM_VIRTUEMART_MAIL_VENDOR_TITLE').$this->vendor->vendor_name.'<br/>';
/* GENERAL FOOTER FOR ALL MAILS */
echo JText::_('COM_VIRTUEMART_MAIL_FOOTER' ) . $link;
echo "\n";
echo $this->vendor->vendor_name ."\n".$this->vendor->vendor_phone .' '.$this->vendor->vendor_store_name ."\n".strip_tags($this->vendor->vendor_store_desc)."\n".str_replace('<br />',"\n",$this->vendor->vendor_legal_info);

