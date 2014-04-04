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
?>
<div class="virtuemart-admin-area row-fluid order">
<?php 
AdminUIHelper::startAdminArea();
// AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_ORDER_PRINT_PO_LBL');

// Get the plugins
JPluginHelper::importPlugin('vmpayment');
JPluginHelper::importPlugin('vmshopper');
JPluginHelper::importPlugin('vmshipment');

$document = JFactory::getDocument();
$document->addScriptDeclaration ( "
		jQuery( function($) {

			$('.orderedit').hide();
			$('.ordereditI').show();
			$('.orderedit').css('backgroundColor', 'lightgray');

			jQuery('.updateOrderItemStatus').click(function() {
				document.orderItemForm.task.value = 'updateOrderItemStatus';
				document.orderItemForm.submit();
				return false
			});

			jQuery('select#virtuemart_paymentmethod_id').change(function(){
				jQuery('span#delete_old_payment').show();
				jQuery('input#delete_old_payment').attr('checked','checked');
			});

		});

		function enableEdit(e)
		{
			jQuery('.orderedit').each( function()
			{
				var d = jQuery(this).css('visibility')=='visible';
				jQuery(this).toggle();
				jQuery('.orderedit').css('backgroundColor', d ? 'white' : 'lightgray');
				jQuery('.orderedit').css('color', d ? 'blue' : 'black');
			});
			jQuery('.ordereditI').each( function()
			{
				jQuery(this).toggle();
			});
			e.preventDefault();
		};

		function cancelEdit(e) {
			jQuery('#orderItemForm').each(function(){
				this.reset();
			});
			jQuery('.selectItemStatusCode')
				.find('option:selected').prop('selected', true)
				.end().trigger('liszt:updated');
			jQuery('.orderedit').hide();
			jQuery('.ordereditI').show();
			e.preventDefault();
		}

		function resetOrderHead(e) {
			jQuery('#orderForm').each(function(){
				this.reset();
			});
			jQuery('select#virtuemart_paymentmethod_id')
				.find('option:selected').prop('selected', true)
				.end().trigger('liszt:updated');
			jQuery('select#virtuemart_shipmentmethod_id')
				.find('option:selected').prop('selected', true)
				.end().trigger('liszt:updated');
			e.preventDefault();
		}

		");

?>

<form name='adminForm' id="adminForm">
		<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
		<?php echo $this->addStandardHiddenToForm(); ?>
</form>
<div class="row-fluid">
	<a class="updateOrder btn  btn-primary" href="#"><span class="icon icon-apply"></span>
		<?php echo JText::_('COM_VIRTUEMART_ORDER_SAVE_USER_INFO'); ?></a>
	<a class="btn" href="#" onClick="javascript:resetOrderHead(event);" ><span class="icon icon-undo"></span>
		<?php echo JText::_('COM_VIRTUEMART_ORDER_RESET'); ?></a>
		<!--
		&nbsp;&nbsp;
		<a class="createOrder" href="#"><span class="icon-nofloat vmicon vmicon-16-new"></span>
		<?php echo JText::_('COM_VIRTUEMART_ORDER_CREATE'); ?></a>
		-->
</div>
<form action="index.php" method="post" name="orderForm" id="orderForm"><!-- Update order head form -->
<?php 
	// order head tabs
	$tabarray['head'] = 'COM_VIRTUEMART_ORDER_PRINT_PO_LBL';
	$tabarray['btadress'] = 'COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL';
	$tabarray['stadress'] = 'COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL';
	$tabarray['history'] = 'COM_VIRTUEMART_ORDER_HISTORY';
	AdminUIHelper::buildTabs ( $this, $tabarray,0 ); ?>

	<input type="hidden" name="task" value="updateOrderHead" />
	<input type="hidden" name="option" value="com_virtuemart" />
	<input type="hidden" name="view" value="orders" />
	<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
	<input type="hidden" name="old_virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>" />
	<input type="hidden" name="old_virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<div style="display: none;"
	class="modal"
	id="updateOrderStatus"><?php echo $this->loadTemplate('editstatus'); ?>
</div>
<form action="index.php" method="post" name="orderItemForm" id="orderItemForm"><!-- Update linestatus form -->
	<table class="table table-striped" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<!--<th class="title" width="5%" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_ACTIONS') ?></th> -->
				<th class="title" width="3" align="left">&nbsp;</th>
				<th class="title" width="10" align="left"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_QTY') ?></th>
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
				<span class='ordereditI'><?php echo $item->product_quantity; ?></span>
				<input class='orderedit' type="text" size="3" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_quantity]" value="<?php echo $item->product_quantity; ?>"/>
			</td>
			<td>
				<?php
					echo $item->order_item_name;
					if (!empty($item->product_attribute)) {
							if(!class_exists('VirtueMartModelCustomfields'))require(JPATH_VM_ADMINISTRATOR.'/models'.DS.'customfields.php');
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
				<?php if(empty($item->virtuemart_product_id)) { ?>
					<span class='orderedit'>Product ID:</span>
					<input class='orderedit' type="text" size="10" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][virtuemart_product_id]" value="<?php echo $item->virtuemart_product_id; ?>"/>
				<?php } ?>
			</td>
			<td>
				<?php echo $item->order_item_sku; ?>
			</td>
			<td align="center">
				<!--<?php echo $this->orderstatuslist[$item->order_status]; ?><br />-->
				<?php echo $this->itemstatusupdatefields[$item->virtuemart_order_item_id]; ?>

			</td>
			<td align="right" style="padding-right: 5px;">
				<?php
				$item->product_discountedPriceWithoutTax = (float) $item->product_discountedPriceWithoutTax;
				if (!empty($item->product_priceWithoutTax) && $item->product_discountedPriceWithoutTax != $item->product_priceWithoutTax) {
					echo '<span style="text-decoration:line-through">'.$this->currency->priceDisplay($item->product_item_price) .'</span><br />';
					echo '<span >'.$this->currency->priceDisplay($item->product_discountedPriceWithoutTax) .'</span><br />';
				} else {
					echo '<span >'.$this->currency->priceDisplay($item->product_item_price) .'</span><br />'; 
				}
				?>
				<input class='orderedit' type="hidden" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_item_price]" value="<?php echo $item->product_item_price; ?>"/>
			</td>
			<td align="right" style="padding-right: 5px;">
				<?php echo $this->currency->priceDisplay($item->product_basePriceWithTax); ?>
				<input class='orderedit' type="hidden" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_basePriceWithTax]" value="<?php echo $item->product_basePriceWithTax; ?>"/>
			</td>
			<td align="right" style="padding-right: 5px;">
				<?php echo $this->currency->priceDisplay($item->product_final_price); ?>
				<input class='orderedit' type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_final_price]" value="<?php echo $item->product_final_price; ?>"/>
			</td>
			<td align="right" style="padding-right: 5px;">
				<?php echo $this->currency->priceDisplay( $item->product_tax); ?>
				<input class='orderedit' type="text" size="12" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_tax]" value="<?php echo $item->product_tax; ?>"/>
				<span style="display: block; font-size: 80%;" title="<?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE_DESC'); ?>">
					<input class='orderedit' type="checkbox" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][calculate_product_tax]" value="1" checked /> <label class='orderedit' for="calculate_product_tax"><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
				</span>
			</td>
			<td align="right" style="padding-right: 5px;">
				<?php echo $this->currency->priceDisplay( $item->product_subtotal_discount); ?>
				<input class='orderedit' type="text" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_subtotal_discount]" value="<?php echo $item->product_subtotal_discount; ?>"/>
			</td>
			<td align="right" style="padding-right: 5px;">
				<?php 
				$item->product_basePriceWithTax = (float) $item->product_basePriceWithTax;
				if(!empty($item->product_basePriceWithTax) && $item->product_basePriceWithTax != $item->product_final_price ) {
					echo '<span style="text-decoration:line-through" >'.$this->currency->priceDisplay($item->product_basePriceWithTax,$this->currency,$item->product_quantity) .'</span><br />' ;
				}
				elseif (empty($item->product_basePriceWithTax) && $item->product_item_price != $item->product_final_price) {
					echo '<span style="text-decoration:line-through">' . $this->currency->priceDisplay($item->product_item_price,$this->currency,$item->product_quantity) . '</span><br />';
				}
				echo $this->currency->priceDisplay($item->product_subtotal_with_tax);
				?>
				<input class='orderedit' type="hidden" size="8" name="item_id[<?php echo $item->virtuemart_order_item_id; ?>][product_subtotal_with_tax]" value="<?php echo $item->product_subtotal_with_tax; ?>"/>
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
					<!--
					&nbsp;<a class="newOrderItem" href="#"><span class="icon-nofloat vmicon vmicon-16-new"></span><?php echo JText::_('COM_VIRTUEMART_NEW_ITEM'); ?></a>
					&nbsp;&nbsp;
					-->
					<div class="btn-group">
					<a class="updateOrderItemStatus btn btn-success" href="#"><span class="icon icon-save"></span> <?php echo JText::_('COM_VIRTUEMART_SAVE'); ?></a>
					&nbsp;&nbsp;
					<a class="btn" href="#" onClick="javascript:cancelEdit(event);" ><span class="icon icon-remove"></span> <?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?></a>
					&nbsp;&nbsp;
					<a class="btn" href="#" onClick="javascript:enableEdit(event);"><span class="icon icon-edit"></span> <?php echo JText::_('COM_VIRTUEMART_EDIT'); ?></a>
					</div>
				</td>


				<td colspan="6">
					<?php // echo JHTML::_('image',  'administrator/components/com_virtuemart/assets/images/vm_witharrow.png', 'With selected'); $this->orderStatSelect; ?>
					&nbsp;&nbsp;&nbsp;

				</td>
		</tr>

	<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
	<input type="hidden" name="virtuemart_paymentmethod_id" value="<?php echo $this->orderbt->virtuemart_paymentmethod_id; ?>" />
	<input type="hidden" name="virtuemart_shipmentmethod_id" value="<?php echo $this->orderbt->virtuemart_shipmentmethod_id; ?>" />
	<input type="hidden" name="order_total" value="<?php echo $this->orderbt->order_total; ?>" />
	<?php echo $this->addStandardHiddenToForm(); ?>
 <!-- Update linestatus form -->
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
			echo $this->currency->priceDisplay($this->orderbt->coupon_discount);  ?></td>
		</tr>
		<?php
			//}
		}?>



<?php
	foreach($this->orderdetails['calc_rules'] as $rule){
		if ($rule->calc_kind == 'DBTaxRulesBill') { ?>
		<tr >
			<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
			<td align="right" colspan="3" > </td>

			<td align="right">
			<!--
				<?php echo  $this->currency->priceDisplay($rule->calc_amount);?>
				<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>][calc_tax]" value="<?php echo $rule->calc_amount; ?>"/>
			-->
			</td>
			<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
			<td align="right"  style="padding-right: 5px;">
				<?php echo  $this->currency->priceDisplay($rule->calc_amount);?>
				<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
			</td>
		</tr>
		<?php
		} elseif ($rule->calc_kind == 'taxRulesBill') { ?>
		<tr >
			<td colspan="5"  align="right"  ><?php echo $rule->calc_rule_name ?> </td>
			<td align="right" colspan="3" > </td>
			<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
			<td align="right"> </td>
			<td align="right"  style="padding-right: 5px;">
				<?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?>
				<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
			</td>
		</tr>
		<?php
		 } elseif ($rule->calc_kind == 'DATaxRulesBill') { ?>
		<tr >
			<td colspan="5"   align="right"  ><?php echo $rule->calc_rule_name ?> </td>
			<td align="right" colspan="3" > </td>

			<td align="right"> </td>
			<td align="right"><?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?></td>
			<td align="right"  style="padding-right: 5px;">
				<?php echo  $this->currency->priceDisplay($rule->calc_amount);  ?>
				<input class='orderedit' type="text" size="8" name="calc_rules[<?php echo $rule->calc_kind ?>][<?php echo $rule->virtuemart_order_calc_rule_id ?>]" value="<?php echo $rule->calc_amount; ?>"/>
			</td>
		</tr>

		<?php
		 }

	}
	?>

		<tr>
			<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING') ?>:</strong></td>
			<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment); ?>
				<input class='orderedit' type="text" size="8" name="order_shipment" value="<?php echo $this->orderbt->order_shipment; ?>"/>
			</td>
			<td  align="right" style="padding-right: 5px;">&nbsp;</td>
			<td  align="right" style="padding-right: 5px;">&nbsp;</td>
			<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment_tax); ?>
				<input class='orderedit' type="text" size="12" name="order_shipment_tax" value="<?php echo $this->orderbt->order_shipment_tax; ?>"/>
			</td>
			<td  align="right" style="padding-right: 5px;">&nbsp;</td>
			<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_shipment+$this->orderbt->order_shipment_tax); ?></td>

		</tr>
		 <tr>
			<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT') ?>:</strong></td>
			<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment); ?>
				<input class='orderedit' type="text" size="8" name="order_payment" value="<?php echo $this->orderbt->order_payment; ?>"/>
			</td>
			<td  align="right" style="padding-right: 5px;">&nbsp;</td>
			<td  align="right" style="padding-right: 5px;">&nbsp;</td>
			<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment_tax); ?>
				<input class='orderedit' type="text" size="12" name="order_payment_tax" value="<?php echo $this->orderbt->order_payment_tax; ?>"/>
			</td>
			<td  align="right" style="padding-right: 5px;">&nbsp;</td>
			<td  align="right" style="padding-right: 5px;"><?php echo $this->currency->priceDisplay($this->orderbt->order_payment+$this->orderbt->order_payment_tax); ?></td>

		 </tr>


		<tr>
			<td align="right" colspan="5"><strong><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?>:</strong></td>
			<td align="right" style="padding-right: 5px;">&nbsp;</td>
			<td align="right" style="padding-right: 5px;">&nbsp;</td>
			<td align="right" style="padding-right: 5px;">&nbsp;</td>
			<td align="right" style="padding-right: 5px;">
				<?php echo $this->currency->priceDisplay($this->orderbt->order_billTaxAmount); ?>
				<input class='orderedit' type="text" size="12" name="order_billTaxAmount" value="<?php echo $this->orderbt->order_billTaxAmount; ?>"/>
				<span style="display: block; font-size: 80%;" title="<?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE_DESC'); ?>">
					<input class='orderedit' type="checkbox" name="calculate_billTaxAmount" value="1" checked /> <label class='orderedit' for="calculate_billTaxAmount"><?php echo JText::_('COM_VIRTUEMART_ORDER_EDIT_CALCULATE'); ?></label>
				</span>
			</td>
			<td align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_billDiscountAmount); ?></strong>
			<td align="right" style="padding-right: 5px;"><strong><?php echo $this->currency->priceDisplay($this->orderbt->order_total); ?></strong>
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
</form>
&nbsp;
<div class="row-fluid">
	<div class="span6">
		<?php
		JPluginHelper::importPlugin('vmshipment');
		$_dispatcher = JDispatcher::getInstance();
		$returnValues = $_dispatcher->trigger('plgVmOnShowOrderBEShipment',array(  $this->orderID,$this->orderbt->virtuemart_shipmentmethod_id, $this->orderdetails));

		foreach ($returnValues as $returnValue) {
			if ($returnValue !== null) {
				echo $returnValue;
			}
		}
		?>
	</div>
	<div class="span6"><?php
		JPluginHelper::importPlugin('vmpayment');
		$_dispatcher = JDispatcher::getInstance();
		$_returnValues = $_dispatcher->trigger('plgVmOnShowOrderBEPayment',array( $this->orderID,$this->orderbt->virtuemart_paymentmethod_id, $this->orderdetails));

		foreach ($_returnValues as $_returnValue) {
			if ($_returnValue !== null) {
				echo $_returnValue;
			}
		}
		?>
	</div>

</div>



<?php
// AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); ?>
</div>
<script type="text/javascript">

jQuery('.show_element,.orderStatFormReset,#updateOrderStatus .close').click(function() {
  jQuery('#updateOrderStatus').toggle();
  return false;
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
	return false;
});
jQuery('.updateOrder').click(function() {
	document.orderForm.submit();
	return false;
});
jQuery('.createOrder').click(function() {
	document.orderForm.task.value = 'CreateOrderHead';
	document.orderForm.submit();
	return false;
});
jQuery('.newOrderItem').click(function() {
	document.orderItemForm.task.value = 'newOrderItem';
	document.orderItemForm.submit();
	return false;
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

	return false;
});

var editingItem = 0;

</script>
