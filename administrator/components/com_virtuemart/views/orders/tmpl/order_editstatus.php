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

$document = JFactory::getDocument();
//TODO : Warning this is not checked !!!! Studio 42
$document->addScriptDeclaration ( "

		function cancelOrderStatFormEdit(e) {
			jQuery('#orderStatForm').each(function(){
				this.reset();
			});
			jQuery('#order_items_status')
				.find('option:selected').prop('selected', true)
				.end().trigger('liszt:updated');
			jQuery('div#updateOrderStatus').hide();
			e.preventDefault();
		}

		");
?>
<form action="index.php" method="post" name="orderStatForm" id="orderStatForm">
<div class="modal-header"> <button type="button" class="close" aria-hidden="true">&times;</button>
	<h3><?php echo JText::_('COM_VIRTUEMART_ORDER_UPDATE_STATUS') ?></h3>
</div>

<div>
<table width="100%">
	<tr>
		<td align="center" colspan="2">
		
		</td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></td>
		<td><?php echo $this->orderStatusSelect; ?>
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
<!--	<tr>

		<input type="submit" value="<?php echo JText::_('COM_VIRTUEMART_SAVE');?>" style="font-size: 10px" />
		<input type="button"
			onclick="javascript: window.parent.document.getElementById( 'sbox-window' ).close();"
			value="<?php echo JText::_('COM_VIRTUEMART_CANCEL');?>" style="font-size: 10px" /></td>

	</tr> -->
</table>
</div>
<div class="modal-footer">
	<a href="#" class="orderStatFormSubmit btn btn-success" >
		<span class="icon icon-apply"></span>&nbsp;<?php echo JText::_('COM_VIRTUEMART_SAVE'); ?>
	</a>&nbsp;&nbsp;&nbsp;
	<button type="reset" title="<?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?>" class="orderStatFormReset btn btn-warning">
		<span class="icon icon-cancel"></span> <?php echo JText::_('COM_VIRTUEMART_CANCEL'); ?>
	</button>
<!-- Hidden Fields -->
<input type="hidden" name="last_task" value="updatestatus" />
<input type="hidden" name="current_order_status" value="<?php echo $this->currentOrderStat; ?>" />
<input type="hidden" name="virtuemart_order_id" value="<?php echo $this->orderID; ?>" />
<?php echo $this->addStandardHiddenToForm(null,'updatestatus'); ?>

</div>
</form>