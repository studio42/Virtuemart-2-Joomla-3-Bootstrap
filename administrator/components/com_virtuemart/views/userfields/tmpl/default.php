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
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();

?>

<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm">
	<div id="header">
	<div id="filterbox">
		<table>
			<tr>
				<td width="100%">
					<?php echo JText::_('COM_VIRTUEMART_FILTER'); ?>:
					<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_('COM_VIRTUEMART_GO'); ?></button>
					<button onclick="document.adminForm.search.value='';this.form.submit();"><?php echo JText::_('COM_VIRTUEMART_RESET'); ?></button>
				</td>
			</tr>
		</table>
	</div>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter();?></div>

	</div>

	<div id="editcell">
		<table class="adminlist jgrid">
		<thead>
		<tr>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->userfieldsList); ?>);" />
			</th>

			<th>
			<?php  echo $this->sort('name','COM_VIRTUEMART_FIELDMANAGER_NAME')  ?>
			</th>
			<th>
			<?php echo JText::_('COM_VIRTUEMART_FIELDMANAGER_TITLE'); ?>
			</th>
			<th>
			<?php echo $this->sort('type','COM_VIRTUEMART_FIELDMANAGER_TYPE') ?>
			</th>
			<th width="20">
				<?php echo JText::_('COM_VIRTUEMART_FIELDMANAGER_REQUIRED'); ?>
			</th>
			<th width="20">
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
		$k = 0;
		for ($i = 0, $n = count($this->userfieldsList); $i < $n; $i++) {
			$row = $this->userfieldsList[$i];
// 			vmdebug('my rows',$row);
			$coreField = (in_array($row->name, $this->lists['coreFields']));
			$image = (JVM_VERSION===1) ? 'checked_out.png' : 'admin/checked_out.png';
			$image = JHtml::_('image.administrator', $image, '/images/', null, null, JText::_('COM_VIRTUEMART_FIELDMANAGER_COREFIELD'));
			$checked = '<div style="position: relative;">'.JHTML::_('grid.id', $i, $row->virtuemart_userfield_id);
			if ($coreField) $checked.='<span class="hasTip" style="position: absolute; margin-left:-3px;" title="'. JText::_('COM_VIRTUEMART_FIELDMANAGER_COREFIELD').'">'. $image .'</span>';
			$checked .= '</div>';
			// There is no reason not to allow moving of the core fields. We only need to disable deletion of them
			// ($coreField) ?
			// 	'<span class="hasTip" title="'. JText::_('COM_VIRTUEMART_FIELDMANAGER_COREFIELD').'">'. $image .'</span>' :
				
			$editlink = JROUTE::_('index.php?option=com_virtuemart&view=userfields&task=edit&virtuemart_userfield_id=' . $row->virtuemart_userfield_id);
			$required = $this->toggle($row->required, $i, 'toggle.required', $coreField);
//			$published = JHTML::_('grid.published', $row, $i);
			$published = $this->toggle($row->published, $i, 'toggle.published', $coreField);
			$registration = $this->toggle($row->registration, $i, 'toggle.registration', $coreField);
			$shipment = $this->toggle($row->shipment, $i, 'toggle.shipment', $coreField);
			$account = $this->toggle($row->account, $i, 'toggle.account', $coreField);
			$ordering = ($this->lists['filter_order'] == 'ordering');
			$disabled = ($ordering ?  '' : 'disabled="disabled"');
		?>
			<tr class="row<?php echo $k ; ?>">
				<td width="10">
					<?php echo $checked; ?>
				</td>

				<td align="left">
					<a href="<?php echo $editlink; ?>"><?php echo JText::_($row->name); ?></a>
				</td>
				<td align="left">
					<?php echo JText::_($row->title); ?>
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
					<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', JText::_('COM_VIRTUEMART_MOVE_UP'), $ordering ); ?></span>
					<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', JText::_('COM_VIRTUEMART_MOVE_DOWN'), $ordering ); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>
			<td width="10">
					<?php echo JText::_($row->virtuemart_userfield_id); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
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
</div>

	<?php echo $this->addStandardHiddenToForm(); ?>
</form>

<?php AdminUIHelper::endAdminArea(); ?>
