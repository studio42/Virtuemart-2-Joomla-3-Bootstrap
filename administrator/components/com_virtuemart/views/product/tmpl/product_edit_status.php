<?php
/**
*
* Information regarding the product status
*
* @package	VirtueMart
* @subpackage Product
* @author RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: product_edit_status.php 6058 2012-06-06 08:19:35Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
<fieldset>
				<legend><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRODUCT_STATUS_LBL'); ?></legend>
<table class="adminform" width="100%">
	<tr class="row0">
		<td width="25%" >
			<div style="text-align:right;font-weight:bold;">
			<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_IN_STOCK') ?></div>
		</td>
		<td width="20%">
			<input  type="text" class="inputbox js-change-stock"  name="product_in_stock" value="<?php echo $this->product->product_in_stock; ?>" size="10" />

			<?php 
			/*if (isset($this->waitinglist) && count($this->waitinglist) > 0) { 
				$link=JROUTE::_('index.php?option=com_virtuemart&view=product&task=sentproductemailtoshoppers&virtuemart_product_id='.$this->product->virtuemart_product_id.'&token='.JUtility::getToken() ); 


					<a href="<?php echo $link ?>">
					<span class="icon-nofloat vmicon icon-16-messages"></span><?php echo Jtext::_('COM_VIRTUEMART_PRODUCT_NOTIFY_USER'); ?>
					</a>


			}*/ ?>
		</td>
		<td width="20%" >
			<div style="text-align:right;font-weight:bold;">
			<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_ORDERED_STOCK') ?></div>
		</td>
		<td colspan="2">
			<input type="text" class="inputbox js-change-stock"  name="product_ordered" value="<?php echo $this->product->product_ordered; ?>" size="10" />
		</td>
	</tr>
	<tr class="row1">
	<!-- low stock notification -->
		<td>
			<div style="text-align:right;font-weight:bold;">
				<?php echo JText::_('COM_VIRTUEMART_LOW_STOCK_NOTIFICATION'); ?>
			</div>
		</td>
		<td>
			<input type="text" class="inputbox" name="low_stock_notification" value="<?php echo $this->product->low_stock_notification; ?>" size="3" />
		</td>
		<td>
			<div style="text-align:right;font-weight:bold;">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_STEP_ORDER') ?>
			</div>
		</td>
		<td>
			<input type="text" class="inputbox"  name="step_order_level" value="<?php echo $this->product->step_order_level; ?>" size="10" />
		</td>
	<!-- end low stock notification -->
	</tr>
	<tr class="row0">
		<td>
			<div style="text-align:right;font-weight:bold;">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_MIN_ORDER') ?>
			</div>
		</td>
		<td>
			<input type="text" class="inputbox"  name="min_order_level" value="<?php echo $this->product->min_order_level; ?>" size="10" />
		</td>
		<td>
			<div style="text-align:right;font-weight:bold;">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_MAX_ORDER') ?>
			</div>
		</td>
		<td>
			<input type="text" class="inputbox"  name="max_order_level" value="<?php echo $this->product->max_order_level; ?>" size="10" />
		</td>
	</tr>
	<tr class="row1">
		<td >
			<div style="text-align:right;font-weight:bold;">
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_AVAILABLE_DATE') ?>
			</div>
		</td>
		<td colspan="3">
			<?php

			echo vmJsApi::jDate($this->product->product_available_date, 'product_available_date'); ?>
		</td>
	</tr>
	<tr class="row0">
		<td valign="top" >
			<div style="text-align:right;font-weight:bold;">
				<?php echo JText::_('COM_VIRTUEMART_AVAILABILITY') ?>
			</div>
		</td>
		<td colspan="2">
			<input type="text" class="inputbox" id="product_availability" name="product_availability" value="<?php echo $this->product->product_availability; ?>" />
			<span class="icon-nofloat vmicon vmicon-16-info tooltip" title="<?php echo '<b>'.JText::_('COM_VIRTUEMART_AVAILABILITY').'</b><br/ >'.JText::_('COM_VIRTUEMART_PRODUCT_FORM_AVAILABILITY_TOOLTIP1') ?>"></span>

			<?php echo JHTML::_('list.images', 'image', $this->product->product_availability, " ", $this->imagePath); ?>
			<span class="icon-nofloat vmicon vmicon-16-info tooltip" title="<?php echo '<b>'.JText::_('COM_VIRTUEMART_AVAILABILITY').'</b><br/ >'.JText::sprintf('COM_VIRTUEMART_PRODUCT_FORM_AVAILABILITY_TOOLTIP2',  $this->imagePath ) ?>"></span>
		</td>
		<td><img border="0" id="imagelib" alt="<?php echo JText::_('COM_VIRTUEMART_PREVIEW'); ?>" name="imagelib" src="<?php if ($this->product->product_availability) echo JURI::root(true).$this->imagePath.$this->product->product_availability;?>"/></td>

	</tr>
</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_PRODUCT_SHOPPERS'); ?></legend>
		<?php echo $this->loadTemplate('customer'); ?>
</fieldset>




<script type="text/javascript">
	jQuery('#image').change( function() {
		var $newimage = jQuery(this).val();
		jQuery('#product_availability').val($newimage);
		jQuery('#imagelib').attr({ src:'<?php echo JURI::root(true).$this->imagePath ?>'+$newimage, alt:$newimage });
		});
	jQuery('.js-change-stock').change( function() {

		var in_stock = jQuery('.js-change-stock[name="product_in_stock"]');
		var ordered = jQuery('.js-change-stock[name="product_ordered"]');
		var product_in_stock= parseInt(in_stock.val());
		if ( oldstock == "undefined") var oldstock = product_in_stock ;
		var product_ordered=parseInt(ordered.val());
		if (product_in_stock>product_ordered && product_in_stock!=oldstock )
			jQuery('#notify_users').attr('checked','checked');
		else jQuery('#notify_users').attr('checked',false);
	});
</script>


