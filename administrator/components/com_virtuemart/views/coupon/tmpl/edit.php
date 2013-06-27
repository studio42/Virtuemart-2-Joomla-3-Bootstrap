<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage Coupon
 * @author RickG
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: edit.php 6347 2012-08-14 15:49:02Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start', 'COM_VIRTUEMART_COUPON_DETAILS');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

	<fieldset>
	    <legend><?php echo JText::_('COM_VIRTUEMART_COUPON_DETAILS'); ?></legend>
	    <table class="admintable">
			<?php echo VmHTML::row('input','COM_VIRTUEMART_COUPON','coupon_code',$this->coupon->coupon_code,'class="inputbox"','',20,32); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_VALUE','coupon_value',$this->coupon->coupon_value,'class="inputbox"','',10,32); ?>

			<?php
				$radioOptions = array();
				$radioOptions[] = JHTML::_('select.option', 'percent', JText::_('COM_VIRTUEMART_COUPON_PERCENT'));
				$radioOptions[] = JHTML::_('select.option', 'total', JText::_('COM_VIRTUEMART_COUPON_TOTAL'));
				echo VmHTML::row('radio','COM_VIRTUEMART_COUPON_PERCENT_TOTAL','percent_or_total',$radioOptions,$this->coupon->percent_or_total); ?>
			<?php
				$listOptions = array();
				$listOptions[] = JHTML::_('select.option', 'permanent', JText::_('COM_VIRTUEMART_COUPON_TYPE_PERMANENT'));
				$listOptions[] = JHTML::_('select.option', 'gift', JText::_('COM_VIRTUEMART_COUPON_TYPE_GIFT'));
				 echo VmHTML::row('select','COM_VIRTUEMART_COUPON_TYPE', 'coupon_type', $listOptions ,$this->coupon->coupon_type,'','value', 'text',false) ; ?>
 			<?php echo VmHTML::row('input','COM_VIRTUEMART_COUPON_VALUE_VALID_AT','coupon_value_valid', $this->coupon->coupon_value_valid, 'class="inputbox"','',10,255,' ' . $this->vendor_currency ); ?>
			<?php echo VmHTML::row('raw','COM_VIRTUEMART_COUPON_START',  vmJsApi::jDate($this->coupon->coupon_start_date , 'coupon_start_date') ); ?>
			<?php echo VmHTML::row('raw','COM_VIRTUEMART_COUPON_EXPIRY', vmJsApi::jDate($this->coupon->coupon_expiry_date,'coupon_expiry_date') ); ?>
	    </table>
	</fieldset>
    <input type="hidden" name="virtuemart_coupon_id" value="<?php echo $this->coupon->virtuemart_coupon_id; ?>" />

 	<?php echo $this->addStandardHiddenToForm(); ?>
</form>


    <?php
    AdminUIHelper::imitateTabs('end');
    AdminUIHelper::endAdminArea();
    ?>


