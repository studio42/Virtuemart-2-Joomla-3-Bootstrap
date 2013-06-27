<?php
/**
 * Popup form to edit the formstatus
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
 * @version $Id: order_editstatus.php 6468 2012-09-18 22:00:43Z Milbo $
 */
?>

<form action="index.php" method="post" name="orderStatForm" id="orderStatForm">
<fieldset>
<table class="admintable" width="100%">
	<tr>
		<td align="center" colspan="2">
		<h1><?php echo JText::_('COM_VIRTUEMART_ORDER_UPDATE_STATUS') ?></h1>
		</td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></td>
		<td><?php echo $this->orderStatSelect; ?>
		</td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_COMMENT') ?></td>
		<td><textarea rows="6" cols="35" name="comments"></textarea>
		</td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_NOTIFY') ?></td>
		<td><?php echo VmHTML::checkbox('customer_notified', true); ?>
		</td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_HISTORY_INCLUDE_COMMENT') ?></td>
		<td><br />
		<?php echo VmHTML::checkbox('include_comment', true); ?>
		</td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_UPDATE_LINESTATUS') ?></td>
		<td><br />
		<?php echo VmHTML::checkbox('orders['.$this->orderID.'][update_lines]', true); ?>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" class="key">
		<a href="#" class="orderStatFormSubmit" >
			<span class="icon-nofloat vmicon vmicon-16-save"></span>&nbsp;<?php echo JText::_('COM_VIRTUEMART_SAVE'); ?></a>&nbsp;&nbsp;&nbsp;
		<a href="#" title="<?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?>" onClick="javascript:document.orderStatForm.reset();" class="show_element[updateOrderStatus]">
			<span class="icon-nofloat vmicon vmicon-16-remove"></span>&nbsp;<?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?></a>
		</td>
<!--
		<input type="submit" value="<?php echo JText::_('COM_VIRTUEMART_SAVE');?>" style="font-size: 10px" />
		<input type="button"
			onclick="javascript: window.parent.document.getElementById( 'sbox-window' ).close();"
			value="<?php echo JText::_('COM_VIRTUEMART_CANCEL');?>" style="font-size: 10px" /></td>
 -->
	</tr>
</table>
</fieldset>

<!-- Hidden Fields -->
<input type="hidden" name="task" value="updatestatus" />
<input type="hidden" name="last_task" value="updatestatus" />
<input type="hidden" name="option" value="com_virtuemart" />
<input type="hidden" name="view" value="orders" />
<input type="hidden" name="current_order_status" value="<?php echo $this->currentOrderStat; ?>" />
<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
