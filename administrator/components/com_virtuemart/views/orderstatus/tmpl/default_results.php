<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage OrderStatus
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6307 2012-08-07 07:39:45Z alatak $
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

			<th>
			<?php echo $this->sort('order_status_name') ?>
			</th>
			<th>
			<?php echo $this->sort('order_status_code') ?>
			</th>
			<th>
				<?php echo JText::_('COM_VIRTUEMART_ORDER_STATUS_STOCK_HANDLE'); ?>
			</th>
			<th>
			<?php  echo $this->sort('ordering')  ?>
			<?php echo JHTML::_('grid.order',  $this->orderStatusList ); ?>
			</th>
			<th width="20" class="autosize">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
			</th>
			<th><?php echo $this->sort('virtuemart_orderstate_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
		</thead>
		<?php

		$image = JHtml::_('image', 'admin/checked_out.png' , null, null, JText::_('COM_VIRTUEMART_ORDER_STATUS_CODE_CORE'));
        $vmCoreStatusCode= $this->lists['vmCoreStatusCode'];
		for ($i = 0, $n = count($this->orderStatusList); $i < $n; $i++) {
			$row = $this->orderStatusList[$i];
			$canDo = $this->canChange($row->created_by);
			$published = $this->toggle( $row->published, $i, 'published',$canDo);
			$checked = JHTML::_('grid.id', $i, $row->virtuemart_orderstate_id);
			$coreStatus = (in_array($row->order_status_code, $this->lists['vmCoreStatusCode']));
			$checked = ($coreStatus) ?
				'<span class="hasTooltip" title="'. JText::_('COM_VIRTUEMART_ORDER_STATUS_CODE_CORE').'">'. $image .'</span>' :
				JHTML::_('grid.id', $i, $row->virtuemart_orderstate_id);

		?>
			<tr >
				<td width="10">
					<?php echo $checked; ?>
				</td>
				<td align="left">
					<?php echo $this->editLink($row->virtuemart_orderstate_id, $row->order_status_name, 'virtuemart_orderstate_id[]') ?>
					<?php
						echo " (".ShopFunctions::altText($row->order_status_name,'COM_VIRTUEMART_ORDER_STATUS').")";
					?>
					<?php if ( $row->order_status_description ) echo '<div class="small">'.JText::_($row->order_status_description).'</div>' ?>
				</td>
				<td align="left">
					<?php echo $row->order_status_code; ?>
				</td>
				<td align="left">
					<?php echo  JText::_($this->stockHandelList[$row->order_stock_handle]); ?>
				</td>
				<td align="center" class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderUp', JText::_('COM_VIRTUEMART_MOVE_UP')); ?></span>
					<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderDown', JText::_('COM_VIRTUEMART_MOVE_DOWN')); ?></span>
					<input class="ordering  input-mini" type="text" name="order[<?php echo $i?>]" id="order[<?php echo $i?>]" size="5" value="<?php echo $row->ordering; ?>" style="text-align: center" />
				</td>
				<td align="center"><?php echo $published; ?></td>
				<td width="10">
					<?php echo $row->virtuemart_orderstate_id; ?>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
	<?php echo $this->addStandardHiddenToForm(); ?>
