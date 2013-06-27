<?php
/**
*
* Layout for the shopping cart and the mail
* shows the chosen adresses of the shopper
* taken from the cart in the session
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
 echo "\n";
 echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'). "\n";
echo sprintf("%'-64.64s",'');
 echo "\n";
  foreach ($this->userfields['fields'] as $field) {
		if(!empty($field['value'])){
			echo $field['title'].': '.$this->escape($field['value'])."\n";
		}
	}
 echo "\n";
echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'). "\n";
echo sprintf("%'-64.64s",'');
 echo "\n";


	 foreach ($this->shipmentfields['fields'] as $field) {
		if(!empty($field['value'])){
			echo $field['title'].': '.$this->escape($field['value'])."\n";
		}
	}

 echo "\n";