<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Shipment
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 5628 2012-03-08 09:00:21Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
	<table class="table table-striped">
		<thead>
		<tr>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="autosize">
				<?php echo JText::_('COM_VIRTUEMART_NAME'); ?>
			</th>
            <th>
				<?php echo JText::_('COM_VIRTUEMART_SHIPPING_SHOPPERGROUPS'); ?>
			</th>
			<th class="autosize">
				<?php echo JText::_('COM_VIRTUEMART_SHIPMENTMETHOD'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_VIRTUEMART_LIST_ORDER'); ?>
			</th>
			<th width="20" class="autosize"><?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?></th>
			 <th class="hidden-phone"><?php echo $this->sort('virtuemart_shipmentmethod_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
		</thead>
		<?php

		for ($i=0, $n=count( $this->shipments ); $i < $n; $i++) {
			$row = $this->shipments[$i];
			$published = $this->toggle( $row->published, $i, 'published');
			/**
			 * @todo Add to database layout published column
			 */
			$row->published = 1;
			$checked = JHTML::_('grid.id', $i, $row->virtuemart_shipmentmethod_id);
			$editlink = JROUTE::_('index.php?option=com_virtuemart&view=shipmentmethod&task=edit&cid[]=' . $row->virtuemart_shipmentmethod_id);
			?>
			<tr >
				<td width="10">
					<?php echo $checked; ?>
				</td>
				<td align="left">
					<?php echo $this->editLink($row->virtuemart_shipmentmethod_id, JText::_($row->shipment_name)) ?>
					<?php if ($row->shipment_desc) echo 'div class="small">'.$row->shipment_desc.'</div>' ?>
				</td>
				<td>
					<?php echo $row->shipmentShoppersList; ?>
				</td>
				<td align="left">
					<?php echo $row->shipment_element; //JHTML::_('link', $editlink, JText::_($row->shipment_element)); ?>
				</td>
				<td align="left">
					<?php echo $row->ordering; ?>
				</td>
				<td><?php echo $published; ?></td>
				<td align="center" class="hidden-phone">
					<?php echo $row->virtuemart_shipmentmethod_id; ?>
				</td>
			</tr>
			<?php
		}
		?>
		<tfoot>
			<tr>
				<td colspan="7" data-cols-phone="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php echo $this->addStandardHiddenToForm(); ?>
