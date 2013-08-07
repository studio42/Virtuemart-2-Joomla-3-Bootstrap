<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Userfields
* @author Oscar van Eijk
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

	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter();?></div>
	<table class="table table-striped">
		<thead>
		<tr>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>

			<th>
			<?php  echo $this->sort('name','COM_VIRTUEMART_FIELDMANAGER_NAME')  ?>
			</th>
			<th>
			<?php echo $this->sort('type','COM_VIRTUEMART_FIELDMANAGER_TYPE') ?>
			</th>
			<th width="20" class="autosize">
				<?php echo JText::_('COM_VIRTUEMART_FIELDMANAGER_REQUIRED'); ?>
			</th>
			<th width="20" class="autosize">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
			</th>
			<th width="20">
				<?php echo JText::_('COM_VIRTUEMART_FIELDMANAGER_SHOW_ON_REGISTRATION'); ?>
			</th>
			<th width="20">
				<?php echo JText::_('COM_VIRTUEMART_FIELDMANAGER_SHOW_ON_SHIPPING'); ?>
			</th>
			<th width="20">
				<?php echo JText::_('COM_VIRTUEMART_FIELDMANAGER_SHOW_ON_ACCOUNT'); ?>
			</th>
			<th>
			<?php echo $this->sort('ordering','COM_VIRTUEMART_FIELDMANAGER_REORDER') ?>
			<?php echo JHTML::_('grid.order',  $this->userfieldsList ); ?>
			</th>
			 <th><?php echo $this->sort('virtuemart_userfield_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
		</thead>
		<?php

		// var_dump($this->lists['coreFields']);
		for ($i = 0, $n = count($this->userfieldsList); $i < $n; $i++) {
			$row = $this->userfieldsList[$i];
// 			vmdebug('my rows',$row);

			$coreField = (!in_array($row->name, $this->lists['coreFields']));
			$image = 'admin/checked_out.png';
			$image = JHtml::_('image', $image, '/images/', null, null, JText::_('COM_VIRTUEMART_FIELDMANAGER_COREFIELD'));
			$checked = '<div style="position: relative;">'.JHTML::_('grid.id', $i, $row->virtuemart_userfield_id);
			if ($coreField) $checked.='<span class="hasTooltip" style="position: absolute; margin-left:-3px;" title="'. JText::_('COM_VIRTUEMART_FIELDMANAGER_COREFIELD').'">'. $image .'</span>';
			$checked .= '</div>';
			// There is no reason not to allow moving of the core fields. We only need to disable deletion of them
			// ($coreField) ?
			// 	'<span class="hasTooltip" title="'. JText::_('COM_VIRTUEMART_FIELDMANAGER_COREFIELD').'">'. $image .'</span>' :
				
			$required = $this->toggle($row->required, $i, 'toggle.required', $coreField);
//			$published = $this->toggle( $row->published, $i, 'published');
			$published = $this->toggle($row->published, $i, 'toggle.published', $coreField);
			$registration = $this->toggle($row->registration, $i, 'toggle.registration', $coreField);
			$shipment = $this->toggle($row->shipment, $i, 'toggle.shipment', $coreField);
			$account = $this->toggle($row->account, $i, 'toggle.account', $coreField);
			$ordering = ($this->lists['filter_order'] == 'ordering');
			$disabled = ($ordering ?  '' : 'disabled="disabled"');
			?>
			<tr >
				<td width="10">
					<?php echo $checked; ?>
				</td>

				<td align="left">
					<?php echo $this->editLink($row->virtuemart_userfield_id, JText::_($row->name), 'virtuemart_userfield_id') ; ?>
					<div class="small"><?php echo JText::_($row->title); ?></div>
				</td>
				<td align="left">
					<?php echo JText::_($row->type); ?>
				</td>
				<td align="center">
					<?php echo $required; ?>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<td align="center">
					<?php echo $registration; ?>
				</td>
				<td align="center">
					<?php echo $shipment; ?>
				</td>
				<td align="center">
					<?php echo $account; ?>
				</td>
				<td class="order">
					<?php if ($ordering ) { ?>
						<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', JText::_('COM_VIRTUEMART_MOVE_UP'), $ordering ); ?></span>
						<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', JText::_('COM_VIRTUEMART_MOVE_DOWN'), $ordering ); ?></span>
					 <?php } ?>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="ordering input-mini" style="text-align: center" />
				</td>
				<td width="10">
					<?php echo JText::_($row->virtuemart_userfield_id); ?>
				</td>
			</tr>
			<?php
		}
		?>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<?php echo $this->addStandardHiddenToForm(); ?>

