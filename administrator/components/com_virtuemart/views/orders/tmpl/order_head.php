<?php
/**
 * Print orderdetails
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: order_print.php 6043 2012-05-21 21:40:56Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
<div class="row-fluid">
	<div class="span6">
		<table class="table table-condensed" cellspacing="0" cellpadding="0">
			<?php
				$print_url = juri::root().'index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=' . $this->orderbt->virtuemart_order_id . '&order_number=' .$this->orderbt->order_number. '&order_pass=' .$this->orderbt->order_pass;
				$print_link = "<a  class='hasTooltip btn-block' title=\"".JText::_('COM_VIRTUEMART_PRINT')."\" href=\"javascript:void window.open('$print_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
				$print_link .= $this->orderbt->order_number . ' <i class="icon icon-print pull-right"></i></a>';
			?>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') ?></strong></td>
				<td><?php echo  $print_link;?></td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_PASS') ?></strong></td>
				<td><?php echo  $this->orderbt->order_pass;?></td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_DATE') ?></strong></td>
				<td><?php  echo vmJsApi::date($this->orderbt->created_on,'LC2',true); ?></td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></strong></td>
				<td><?php echo $this->orderstatuslist[$this->orderbt->order_status]; ?></td>
			</tr>
			<tr>
				<td class="key"></td>
				<td><a href="#updateOrderStatus" class="show_element btn btn-block"><span class="icon icon-apply"></span><?php echo JText::_('COM_VIRTUEMART_ORDER_UPDATE_STATUS') ?></a>
			</td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_NAME') ?></strong></td>
				<td><?php
						$username=$this->orderbt->company ? $this->orderbt->company." ":"";
						$username.=$this->orderbt->first_name." ".$this->orderbt->last_name." ";
					if ($this->orderbt->virtuemart_user_id) {
						echo $this->editLink($this->orderbt->virtuemart_user_id, $username, 'virtuemart_user_id',
							array('title' => JText::_ ('COM_VIRTUEMART_ORDER_EDIT_USER') . ' ' . $username, 'class' =>'hasTooltip btn-block'),'user' ) ;
						
					} else {
						vmdebug('my this',$this);
						echo $this->orderbt->first_name.' '.$this->orderbt->last_name;
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_IPADDRESS') ?></strong></td>
				<td><?php echo $this->orderbt->ip_address; ?></td>
			</tr>
			<?php
			if ($this->orderbt->coupon_code) { ?>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_COUPON_CODE') ?></strong></td>
				<td><?php echo $this->orderbt->coupon_code; ?></td>
			</tr>
			<?php } ?>

		</table>
	</div>
	<div class="span6">
		<table width="100%" class="table table-condensed">
			<?php
			if ($this->orderbt->invoiceNumber and !shopFunctions::InvoiceNumberReserved($this->orderbt->invoiceNumber) ) {
				$invoice_url = juri::root().'index.php?option=com_virtuemart&view=invoice&layout=invoice&format=pdf&tmpl=component&virtuemart_order_id=' . $this->orderbt->virtuemart_order_id . '&order_number=' .$this->orderbt->order_number. '&order_pass=' .$this->orderbt->order_pass;
				$invoice_link = "<a class='hasTooltip btn-block' title=\"".JText::_('COM_VIRTUEMART_INVOICE_PRINT')."\"  href=\"javascript:void window.open('$invoice_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
				$invoice_link .=   $this->orderbt->invoiceNumber . '<i class="icon icon-print pull-right"></i></a>';?>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_INVOICE') ?></strong></td>
				<td><?php echo $invoice_link; ?></td>
			</tr>
			<?php } ?>
			<?php if ($this->orderbt->customer_note || true) { ?>
			<tr>
				<td class="key" colspan="2"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_SHIPMENT') ?></td>
			</tr>
				<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?></td>
				<?php
				$model = VmModel::getModel('paymentmethod');
				$payments = $model->getPayments();
				$model = VmModel::getModel('shipmentmethod');
				$shipments = $model->getShipments();
				?>
				<td>
					<input  type="hidden" size="10" name="virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>"/>
					<!--
					<? echo VmHTML::select("virtuemart_paymentmethod_id", $payments, $this->orderbt->virtuemart_paymentmethod_id, '', "virtuemart_paymentmethod_id", "payment_name"); ?>
					<span id="delete_old_payment" style="display: none;"><br />
						<input id="delete_old_payment" type="checkbox" name="delete_old_payment" value="1" /> <label class='' for="" title="<?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_DELETE_DESC'); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_DELETE'); ?></label>
					</span>
					-->
					<?php
					foreach($payments as $payment) {
						if($payment->virtuemart_paymentmethod_id == $this->orderbt->virtuemart_paymentmethod_id) echo $payment->payment_name;
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPMENT_LBL') ?></td>
				<td>
					<input type="hidden" size="10" name="virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>"/>
					<!--
					<? echo VmHTML::select("virtuemart_shipmentmethod_id", $shipments, $this->orderbt->virtuemart_shipmentmethod_id, '', "virtuemart_shipmentmethod_id", "shipment_name"); ?>
					<span id="delete_old_shipment" style="display: none;"><br />
						<input id="delete_old_shipment" type="checkbox" name="delete_old_shipment" value="1" /> <label class='' for=""><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
					</span>
					-->
					<?php
					foreach($shipments as $shipment) {
						if($shipment->virtuemart_shipmentmethod_id == $this->orderbt->virtuemart_shipmentmethod_id) echo $shipment->shipment_name;
					}
					?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_CUSTOMER_NOTE') ?></td>
				<td valign="top">
					<textarea rows="4" cols="50" class="span12" name="customer_note"><?php echo $this->orderbt->customer_note; ?></textarea>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
