<?php
/**
*
* Layout for the shopping cart, look in mailshopper for more details
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
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
/* TODO Chnage the footer place in helper or assets ???*/
if (empty($this->vendor)) {
		$vendorModel = VmModel::getModel('vendor');
		$this->vendor = $vendorModel->getVendor();
}

//$link = shopFunctionsF::getRootRoutedUrl('index.php?option=com_virtuemart');
$link = JURI::root().'index.php?option=com_virtuemart';

echo '<br/><br/>';
//$link='<b>'.JHTML::_('link', JURI::root().$link, $this->vendor->vendor_name).'</b> ';

//	echo JText::_('COM_VIRTUEMART_MAIL_VENDOR_TITLE').$this->vendor->vendor_name.'<br/>';
/* GENERAL FOOTER FOR ALL MAILS */
	echo JText::_('COM_VIRTUEMART_MAIL_FOOTER' ) . '<a href="'.$link.'">'.$this->vendor->vendor_name.'</a>';
        echo '<br/>';
	echo $this->vendor->vendor_name .'<br />'.$this->vendor->vendor_phone .' '.$this->vendor->vendor_store_name .'<br /> '.$this->vendor->vendor_store_desc.'<br />'.$this->vendor->vendor_legal_info;

