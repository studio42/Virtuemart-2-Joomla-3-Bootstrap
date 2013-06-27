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
 * @version $Id: edit_edit.php 3420 2011-06-04 12:37:20Z Electrocity $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<?php echo $this->langList; ?>
<div class="col50">
    <fieldset>
        <legend><?php echo JText::_('COM_VIRTUEMART_SHIPMENTMETHOD'); ?></legend>
        <table class="admintable">
	    <?php echo VmHTML::row('input', 'COM_VIRTUEMART_SHIPPING_FORM_NAME', 'shipment_name', $this->shipment->shipment_name); ?>
	    <?php echo VmHTML::row('booleanlist', 'COM_VIRTUEMART_PUBLISH', 'published', $this->shipment->published); ?>
	    <?php echo VmHTML::row('textarea', 'COM_VIRTUEMART_SHIPPING_FORM_DESCRIPTION', 'shipment_desc', $this->shipment->shipment_desc); ?>
	    <?php echo VmHTML::row('raw', 'COM_VIRTUEMART_SHIPPING_CLASS_NAME', $this->pluginList); ?>
	    <?php echo VmHTML::row('raw', 'COM_VIRTUEMART_SHIPPING_FORM_SHOPPER_GROUP', $this->shopperGroupList); ?>
	    <?php echo VmHTML::row('input', 'COM_VIRTUEMART_LIST_ORDER', 'ordering', $this->shipment->ordering, 'class="inputbox"', '', 4, 4); ?>
	    <?php
	    if (Vmconfig::get('multix', 'none') !== 'none') {
			echo VmHTML::row('raw', 'COM_VIRTUEMART_VENDOR', $this->vendorList);
	    }
	    ?>
        </table>
    </fieldset>
</div>


