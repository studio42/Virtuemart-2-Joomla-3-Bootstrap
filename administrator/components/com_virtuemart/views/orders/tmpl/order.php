<?php
/**
 * Display form details
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
 * @version $Id: order.php 6395 2012-09-05 07:57:05Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_ORDER_PRINT_PO_LBL');

// Get the plugins
JPluginHelper::importPlugin('vmpayment');
JPluginHelper::importPlugin('vmshopper');
JPluginHelper::importPlugin('vmshipment');

?>

<form name='adminForm' id="adminForm">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<table class="adminlist" style="table-layout: fixed;">
	<tr>
		<td valign="top">
		<table class="adminlist" cellspacing="0" cellpadding="0">
			<tr>
				<th colspan="2"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_LBL') ?></th>
			</tr>
			<?php
			    $print_url = juri::root().'index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=' . $this->orderbt->virtuemart_order_id . '&order_number=' .$this->orderbt->order_number. '&order_pass=' .$this->orderbt->order_pass;
			    $print_link = "<a title=\"".JText::_('COM_VIRTUEMART_PRINT')."\" href=\"javascript:void window.open('$print_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
			    $print_link .=   $this->orderbt->order_number . ' </a>';
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
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_NAME') ?></strong></td>
				<td><?php
						$username=$this->orderbt->company ? $this->orderbt->company." ":"";
						$username.=$this->orderbt->first_name." ".$this->orderbt->last_name." ";
					if ($this->orderbt->virtuemart_user_id) {
						$userlink = JROUTE::_ ('index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]=' . $this->orderbt->virtuemart_user_id);
						echo JHTML::_ ('link', JRoute::_ ($userlink), $username, array('title' => JText::_ ('COM_VIRTUEMART_ORDER_EDIT_USER') . ' ' . $username));
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
			<?php
			if ($this->orderbt->invoiceNumber and !shopFunctions::InvoiceNumberReserved($this->orderbt->invoiceNumber) ) {
				$invoice_url = juri::root().'index.php?option=com_virtuemart&view=invoice&layout=invoice&format=pdf&tmpl=component&virtuemart_order_id=' . $this->orderbt->virtuemart_order_id . '&order_number=' .$this->orderbt->order_number. '&order_pass=' .$this->orderbt->order_pass;
				$invoice_link = "<a title=\"".JText::_('COM_VIRTUEMART_INVOICE_PRINT')."\"  href=\"javascript:void window.open('$invoice_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\"  >";
				$invoice_link .=   $this->orderbt->invoiceNumber . '</a>';?>
			<tr>
				<td class="key"><strong><?php echo JText::_('COM_VIRTUEMART_INVOICE') ?></strong></td>
				<td><?php echo $invoice_link; ?></td>
			</tr>
			<?php } ?>
		</table>
		</td>
		<td valign="top">
		<table class="adminlist" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_VIRTUEMART_ORDER_HISTORY_DATE_ADDED') ?></th>
					<th><?php echo JText::_('COM_VIRTUEMART_ORDER_HISTORY_CUSTOMER_NOTIFIED') ?></th>
					<th><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_STATUS') ?></th>
					<th><?php echo JText::_('COM_VIRTUEMART_COMMENT') ?></th>
				</tr>
			</thead>
			<?php
			foreach ($this->orderdetails['history'] as $this->orderbt_event ) {
				echo "<tr>";
				echo "<td>". vmJsApi::date($this->orderbt_event->created_on,'LC2',true) ."</td>\n";
				if ($this->orderbt_event->customer_notified == 1) {
					echo '<td align="center">'.JText::_('COM_VIRTUEMART_YES').'</td>';
				}
				else {
					echo '<td align="center">'.JText::_('COM_VIRTUEMART_NO').'</td>';
				}
				if(!isset($this->orderstatuslist[$this->orderbt_event->order_status_code])){
					if(empty($this->orderbt_event->order_status_code)){
						$this->orderbt_event->order_status_code = 'unknown';
					}
					$_orderStatusList[$this->orderbt_event->order_status_code] = JText::_('COM_VIRTUEMART_UNKNOWN_ORDER_STATUS');
				}

				echo '<td align="center">'.$this->orderstatuslist[$this->orderbt_event->order_status_code].'</td>';
				echo "<td>".$this->orderbt_event->comments."</td>\n";
				echo "</tr>\n";
			}
			?>
			<tr>
				<td colspan="4">
				<a href="#" class="show_element"><span class="vmicon vmicon-16-editadd"></span><?php echo JText::_('COM_VIRTUEMART_ORDER_UPDATE_STATUS') ?></a>
				<div style="display: none; background: white;"
					class="element-hidden vm-absolute"
					id="updateOrderStatus"><?php echo $this->loadTemplate('editstatus'); ?>
				</div>
				</td>
			</tr>

			<?php
				// Load additional plugins
				$_dispatcher = JDispatcher::getInstance();
				$_returnValues1 = $_dispatcher->trigger('plgVmOnUpdateOrderBEPayment',array($this->orderID));
				$_returnValues2 = $_dispatcher->trigger('plgVmOnUpdateOrderBEShipment',array(  $this->orderID));
				$_returnValues = array_merge($_returnValues1, $_returnValues2);
				$_plg = '';
				foreach ($_returnValues as $_returnValue) {
					if ($_returnValue !== null) {
						$_plg .= ('	<td colspan="4">' . $_returnValue . "</td>\n");
					}
				}
				if ($_plg !== '') {
					echo "<tr>\n$_plg</tr>\n";
				}
			?>

		</table>
		</td>
	</tr>
	<?php if ($this->orderbt->customer_note) { ?>
	<tr>
		<!-- Customer Note -->
		<td valign="top" width="30%" colspan="2">
		<table class="adminlist" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_CUSTOMER_NOTE') ?></th>
				</tr>
			</thead>
			<tr>
				<td valign="top" align="left" width="50%"><?php echo $this->orderbt->customer_note; ?></td>
			</tr>
		</table>
		</td>
	</tr>
	<?php } ?>
</table>
&nbsp;
<table width="100%">
	<tr>
		<td width="50%" valign="top">
		<table class="adminlist" width="100%">
			<thead>
				<tr>
					<th  style="text-align: center;" colspan="2"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL') ?></th>
				</tr>
			</thead>

			<?php
			foreach ($this->userfields['fields'] as $_field ) {
				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				'.$_field['title']."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo '				'.$_field['value']."\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n";
			}
			?>

		</table>
		</td>
		<td width="50%" valign="top">
		<table class="adminlist" width="100%">
			<thead>
				<tr>
					<th   style="text-align: center;" colspan="2"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL') ?></th>
				</tr>
			</thead>

			<?php
			foreach ($this->shipmentfields['fields'] as $_field ) {
				echo '		<tr>'."\n";
				echo '			<td class="key">'."\n";
				echo '				'.$_field['title']."\n";
				echo '			</td>'."\n";
				echo '			<td>'."\n";
				echo '				'.$_field['value']."\n";
				echo '			</td>'."\n";
				echo '		</tr>'."\n";
			}
			?>

		</table>
		</td>
	</tr>
</table>

<table width="100%">
	<tr>
		<td colspan="2">
		<form action="index.php" method="post" name="orderItemForm" id="orderItemForm"><!-- Update linestatus form -->
		<table class="adminlist" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<!--<th class="title" width="5%" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_ACTIONS') ?></th> -->
					<th class="title" width="3" align="left">&nbsp;</th>
					<th class="title" width="47" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_QUANTITY') ?></th>
					<th class="title" width="*" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_NAME') ?></th>
					<th class="title" width="10%" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SKU') ?></th>
					<th class="title" width="10%"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_ITEM_STATUS') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_NET') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_BASEWITHTAX') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_GROSS') ?></th>
					<th class="title" width="50"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_TAX') ?></th>
					<th class="title" width="50"> <?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_DISCOUNT') ?></th>
					<th class="title" width="5%"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></th>
				</tr>
			</thead>
		<?php foreach ($this->orderdetails['items'] as $item) { ?>
			<!-- Display the order item -->
			<tr valign="top" id="showItem_<?php echo $item->virtuemart_order_item_id; ?>" data-itemid="<?php echo $item->virtuemart_order_item_id; ?>">
				<!--<td>
					<?php $removeLineLink=JRoute::_('index.php?option=com_virtuemart&view=orders&orderId='.$this->orderbt->virtuemart_order_id.'&orderLineId='.$item->virtuemart_order_item_id.'&task=removeOrderItem'); ?>
					<a class="vmicon vmicon-16-bug" title="<?php echo JText::_('remove'); ?>" onclick="javascript:confirmation('<?php echo $removeLineLink; ?>');"></a>

					<a href="javascript:enableItemEdit(<?php echo $item->virtuemart_order_item_id; ?>)"> <?php echo JHTML::_('image',  'administrator/components/com_virtuemart/assets/images/icon_16/icon-16-category.png', "Edit", NULL, "Edit"); ?></a>
				</td> -->
				<td>

				</td>
				<td>
					<?php echo $item->product_quantity; ?>
				</td>
				<td>
					<?php
						echo $item->order_item_name;
						if (!empty($item->product_attribute)) {
								if(!class_exists('VirtueMartModelCustomfields'))require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'customfields.php');
								$product_attribute = VirtueMartModelCustomfields::CustomsFieldOrderDisplay($item,'BE');
							echo '<div>'.$product_attribute.'</div>';
						}
						$_dispatcher = JDispatcher::getInstance();
						$_returnValues = $_dispatcher->trigger('plgVmOnShowOrderLineBEShipment',array(  $this->orderID,$item->virtuemart_order_item_id));
						$_plg = '';
						foreach ($_returnValues as $_returnValue) {
							if ($_returnValue !== null) {
								$_plg .= $_returnValue;
							}
						}
						if ($_plg !== '') {
							echo '<table border="0" celspacing="0" celpadding="0">'
								. '<tr>'
								. '<td width="8px"></td>' // Indent
								. '<td>'.$_plg.'</td>'
								. '</tr>'
								. '</table>';
						}
					?>
				</td>
				<td>
					<?php echo $item->order_item_sku; ?>
				</td>
				<td align="center">
					<?php echo $this->orderstatuslist[$item->order_status]; ?><br />
					<?php echo $this->itemstatusupdatefields[$item->virtuemart_order_item_id]; ?>

				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($item->product_item_price); ?>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($item->product_basePriceWithTax); ?>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($item->product_final_price); ?>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay( $item->product_tax); ?>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay( $item->product_subtotal_discount); ?>
				</td>
				<td align="right" style="padding-right: 5px;">
					<?php echo $this->currency->priceDisplay($item->product_subtotal_with_tax); ?>
				</td>
			</tr>
			<!-- TODO updating all correctly on do a new Cart<tr>
				<td>
					<input type="checkbox" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>]" value="<?php echo $item->virtuemart_order_item_id; ?>" />
				</td>
				<td>
					<input type="text" size="3" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_quantity]" value="<?php echo $item->product_quantity; ?>"/>
				</td>
				<td>
					<?php
						echo $item->order_item_name;
						if (!empty($item->product_attribute)) {
							echo '<div>'.$item->product_attribute.'</div>';
						}
					?>
				</td>
				<td>
					<?php echo $item->order_item_sku; ?>
				</td>
				<td align="center">


				</td>
				<td>
					<input type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_item_price]" value="<?php echo $item->product_item_price; ?>"/>
				</td>
				<td>
					<input type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_final_price]" value="<?php echo $item->product_final_price; ?>"/>
				</td>
				<td>
					<?php echo $this->currency->priceDisplay($item->product_quantity * $item->product_final_price); ?>
				</td>
			</tr> -->
		<?php } ?>
			<tr id="updateOrderItemStatus">

					<td colspan="5">
						&nbsp;						<a class="updateOrderItemStatus" href="#"><span class="icon-nofloat vmicon vmicon-16-save"></span><?php echo JText::_('COM_VIRTUEMART_SAVE'); ?></a>
						&nbsp;&nbsp;&nbsp;
						<a href="#" onClick="javascript:resetForm(0);"><span class="icon-nofloat vmicon vmicon-16-remove"></span><?php echo '&nbsp;'. JText::_('COM_VIRTUEMART_CANCEL'); ?></a>
					</td>


					<td colspan="6">
						<?php // echo JHTML::_('image',  'administrator/components/com_virtuemart/assets/images/vm_witharrow.png', 'With selected'); $this->orderStatSelect; ?>
						&nbsp;&nbsp;&nbsp;

					</td>
			</tr>
		<!--/table -->
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form> <!-- Update linestatus form -->
		<!--table class="adminlist" cellspacing="0" cellpadding="0" -->
			<tr>
				<td align="left" colspan="1"><?php $editLineLink=JRoute::_('index.php?option=com_virtuemart&view=orders&orderId='.$this->orderbt->virtuemart_order_id.'&orderLineId=0&tmpl=component&task=editOrderItem'); ?>
				<!-- <a href="<?php echo $editLineLink; ?>" class="modal"> <?php echo JHTML::_('image',  'administrator/components/com_virtuemart/assets/images/icon_16/icon-16-editadd.png', "New Item"); ?>
				New Item </a>--></td>
				<td align="right" colspan="4">
				<div align="right"><strong> <?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SUBTOTAL') ?>:
				</strong></div>
				</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_subtotal); ?></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_tax); ?></td>
				<td align="right"> <?php echo $this->currency->priceDisplay($this->orderbt->order_discountAmount); ?></td>
				<td width="15%" align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_salesPrice); ?></td>
			</tr>
			<?php
			/* COUPON DISCOUNT */
			//if (VmConfig::get('coupons_enable') == '1') {

				if ($this->orderbt->coupon_discount > 0 || $this->orderbt->coupon_discount < 0) {
					?>
			<tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT') ?></strong></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><?php
				echo "- ".$this->currency->priceDisplay($this->orderbt->coupon_discount);  ?></td>
			</tr>
			<?php
				//}
			}?>



	<?php
		foreach($this->orderdetails['calc_rules'] as $rule){
			if ($rule->calc_kind== 'DBTaxRulesBill') { ?>
			<tr >
				<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>

				<td align="right"> </td>
				<td align="right"> </td>
				<td align="right"  style="padding-right: 5px;"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?> </td>
			</tr>
			<?php
			} elseif ($rule->calc_kind == 'taxRulesBill') { ?>
			<tr >
				<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>
				<td align="right"> </td>
				<td align="right"><?php    ?> </td>
				<td align="right"  style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($rule->calc_amount);   ?> </td>
			</tr>
			<?php
			 } elseif ($rule->calc_kind == 'DATaxRulesBill') { ?>
			<tr >
				<td colspan="5"   align="right"  ><?php echo $rule->calc_rule_name ?> </td>
				<td align="right" colspan="3" > </td>

				<td align="right"> </td>
				<td align="right"> </td>
				<td align="right"  style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($rule->calc_amount);  ?> </td>
			</tr>

			<?php
			 }

		}
		?>



			<tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?>:</strong></td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment); ?></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment_tax); ?></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment+$this->orderbt->order_shipment_tax); ?></td>

			</tr>
			 <tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT') ?>:</strong></td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment); ?></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment_tax); ?></td>
				 <td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment+$this->orderbt->order_payment_tax); ?></td>

			 </tr>


			<tr>
				<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>:</strong></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_billTaxAmount); ?></td>
				<td   align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_discountAmount); ?></strong>
				<td   align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_total); ?></strong>
				</td>
			</tr>
<?php if ($this->orderbt->user_currency_rate != 1.0) { ?>
		<tr>
				<td align="right" colspan="5"><em><?php echo JText::_('COM_VIRTUEMART_ORDER_USER_CURRENCY_RATE') ?>:</em></td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td  align="right" style="padding-right: 5px;">&nbsp;</td>
			<td  align="right" style="padding-right: 5px;">&nbsp;</td>
				<td   align="right" style="padding-right: 5px;"><em><?php echo  $this->orderbt->user_currency_rate ?></em></td>
			</tr>
			<?php }
			?>
		</table>
		</td>
	</tr>
</table>
&nbsp;
<table width="100%">
	<tr>
		<td valign="top" width="50%"><?php
		JPluginHelper::importPlugin('vmshipment');
		$_dispatcher = JDispatcher::getInstance();
		$returnValues = $_dispatcher->trigger('plgVmOnShowOrderBEShipment',array(  $this->orderID,$this->virtuemart_shipmentmethod_id, $this->orderdetails));
		foreach ($returnValues as $returnValue) {
			if ($returnValue !== null) {
				echo $returnValue;
			}
		}
		?>
		</td>
		<td valign="top"><?php
		JPluginHelper::importPlugin('vmpayment');
		$_dispatcher = JDispatcher::getInstance();
		$_returnValues = $_dispatcher->trigger('plgVmOnShowOrderBEPayment',array( $this->orderID,$this->orderbt->virtuemart_paymentmethod_id, $this->orderdetails));
		foreach ($_returnValues as $_returnValue) {
			if ($_returnValue !== null) {
				echo $_returnValue;
			}
		}
		?></td>
	</tr>

</table>



<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); ?>

<script type="text/javascript">

jQuery('.show_element').click(function() {
  jQuery('.element-hidden').toggle();
  return false
});
// jQuery('select#order_items_status').change(function() {
	////selectItemStatusCode
	// var statusCode = this.value;
	// jQuery('.selectItemStatusCode').val(statusCode);
	// return false
// });
jQuery('.updateOrderItemStatus').click(function() {
	document.orderItemForm.task.value = 'updateOrderItemStatus';
	document.orderItemForm.submit();
	return false
});
function confirmation(destnUrl) {
	var answer = confirm("<?php echo addslashes( JText::_('COM_VIRTUEMART_ORDER_DELETE_ITEM_JS') ); ?>");
	if (answer) {
		window.location = destnUrl;
	}
}
/* JS for editstatus */

jQuery('.orderStatFormSubmit').click(function() {
	//document.orderStatForm.task.value = 'updateOrderItemStatus';
	document.orderStatForm.submit();

	return false
});

var editingItem = 0;

</script>
