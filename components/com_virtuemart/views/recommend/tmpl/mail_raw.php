<?php


defined('_JEXEC') or die('');

/**
 * Renders the email for recommend to a friend
 * @package	VirtueMart
 * @subpackage product details
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id$
 */

echo $this->vendorAddress;
echo "\n";
echo "\n";
echo JText::sprintf ('COM_VIRTUEMART_MAIL_SHOPPER_NAME', $this->user->name);
echo "\n";
echo "\n";

	 echo JText::sprintf('COM_VIRTUEMART_RECOMMEND_MAIL_MSG', $this->product->product_name,   $this->comment);


echo "\n";

// $uri    = JURI::getInstance();
// $prefix = $uri->toString(array('scheme', 'host', 'port'));
$link = JURI::root().'index.php?option=com_virtuemart';

echo "\n\n";
$link= JHTML::_('link', $link, $this->vendor->vendor_name) ;


/* GENERAL FOOTER FOR ALL MAILS */
echo JText::_('COM_VIRTUEMART_MAIL_FOOTER' ) . $link;
echo "\n";
echo $this->vendor->vendor_name ."\n".$this->vendor->vendor_phone .' '.$this->vendor->vendor_store_name ."\n".strip_tags($this->vendor->vendor_store_desc)."\n".str_replace('<br />',"\n",$this->vendor->vendor_legal_info);


echo JText::sprintf('COM_VIRTUEMART_RECOMMEND_MAIL_MSG', $this->product->product_name, $this->comment);

//$uri    = JURI::getInstance();
//$prefix = $uri->toString(array('scheme', 'host', 'port'));
$link = JURI::root().'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$this->product->virtuemart_product_id ;

echo '<br /><b>'.JHTML::_('link',$link, $this->product->product_name).'</b>';
include(JPATH_VM_SITE.DS.'views'.DS.'cart'.DS.'tmpl'.DS.'mail_html_footer.php');
