<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Paymentmethod
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit_edit.php 5215 2012-01-03 17:31:57Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<?php echo $this->langList; ?>
<div class="col50">
    <fieldset>
        <legend><?php echo JText::_('COM_VIRTUEMART_PAYMENTMETHOD'); ?></legend>
        <table class="admintable">
		<?php echo VmHTML::row('input','COM_VIRTUEMART_PAYMENTMETHOD_FORM_NAME','payment_name',$this->payment->payment_name); ?>
     	<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISH','published',$this->payment->published); ?>
		<?php echo VmHTML::row('textarea','COM_VIRTUEMART_PAYMENT_FORM_DESCRIPTION','payment_desc',$this->payment->payment_desc); ?>
		<?php echo VmHTML::row('raw','COM_VIRTUEMART_PAYMENT_CLASS_NAME', $this->vmPPaymentList ); ?>
		<?php echo VmHTML::row('raw','COM_VIRTUEMART_PAYMENTMETHOD_FORM_SHOPPER_GROUP', $this->shopperGroupList ); ?>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_LIST_ORDER','ordering',$this->payment->ordering,'class="inputbox"','',4,4); ?>
	    <?php
	    if (Vmconfig::get('multix', 'none') !== 'none') {
			echo VmHTML::row('raw', 'COM_VIRTUEMART_VENDOR', $this->vendorList);
	    }
	    ?>
          </table>
    </fieldset>
</div>

