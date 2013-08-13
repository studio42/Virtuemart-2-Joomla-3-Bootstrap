<?php
/**
*
* Layout for the shopper mail, when he confirmed an ordner
*
* The addresses are reachable with $this->BTaddress['fields'], take a look for an exampel at shopper_adresses.php
*
* With $this->cartData->paymentName or shipmentName, you get the name of the used paymentmethod/shippmentmethod
*
* In the array order you have details and items ($this->orderDetails['details']), the items gather the products, but that is done directly from the cart data
*
* $this->orderDetails['details'] contains the raw address data (use the formatted ones, like BTaddress['fields']). Interesting informatin here is,
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
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">

  <tr>
    <td width="30%">
		<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER'); ?><br />
		<strong><?php echo $this->orderDetails['details']['BT']->order_number ?></strong>

	</td>
    <td width="30%">
		<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_PASSWORD'); ?><br />
		<strong><?php echo $this->orderDetails['details']['BT']->order_pass ?></strong>
	</td>
    <td width="40%">
    	<p>
 			<a class="default" title="<?php echo $this->vendor->vendor_store_name ?>" href="<?php echo JURI::root().'index.php?option=com_virtuemart&view=orders&layout=details&order_number='.$this->orderDetails['details']['BT']->order_number.'&order_pass='.$this->orderDetails['details']['BT']->order_pass; ?>">
			<?php echo JText::_('COM_VIRTUEMART_MAIL_SHOPPER_YOUR_ORDER_LINK'); ?></a>
		</p>
	</td>
  </tr>
  <tr>
    <td colspan="3"><p>
				<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_TOTAL_ORDER',$this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total,$this->currency) ); ?></p></td>
  </tr>
  <td colspan="3"><p>
				<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_ORDER_STATUS',JText::_($this->orderDetails['details']['BT']->order_status_name)) ; ?></p></td>
  </tr>
  <?php $nb=count($this->orderDetails['history']);
  if($this->orderDetails['history'][$nb-1]->customer_notified && !(empty($this->orderDetails['history'][$nb-1]->comments))) { ?>
  <tr>
    <td colspan="3">
		<?php echo  nl2br($this->orderDetails['history'][$nb-1]->comments); ?>
	</td>
  </tr>
  <?php } ?>
  <?php if(!empty($this->orderDetails['details']['BT']->customer_note)){ ?>
  <tr>
    <td colspan="3">
		<?php echo JText::sprintf('COM_VIRTUEMART_MAIL_SHOPPER_QUESTION',nl2br($this->orderDetails['details']['BT']->customer_note)) ?>

	</td>
  </tr>
  <?php } ?>
</table>
