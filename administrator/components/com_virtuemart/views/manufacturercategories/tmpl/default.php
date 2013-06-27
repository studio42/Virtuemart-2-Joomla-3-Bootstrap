<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer Category
* @author Patrick Kohl
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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<table class="adminlist" cellspacing="0" cellpadding="0">
		<thead>
		<tr>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->manufacturerCategories); ?>);" />
			</th>
			<th>
				<?php echo  JText::_('COM_VIRTUEMART_MANUFACTURER_CATEGORY_NAME'); ?>
			</th>
			<th>
				<?php echo  JText::_('COM_VIRTUEMART_MANUFACTURER_CATEGORY_DESCRIPTION'); ?>
			</th>
			<th>
				<?php echo  JText::_('COM_VIRTUEMART_MANUFACTURER_CATEGORY_LIST'); ?>
			</th>
			<th width="20">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISH'); ?>
			</th>
			   <th><?php echo $this->sort('virtuemart_manufacturercategories_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
		</thead>
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->manufacturerCategories ); $i < $n; $i++) {
			$row = $this->manufacturerCategories[$i];

			$checked = JHTML::_('grid.id', $i, $row->virtuemart_manufacturercategories_id);
			$published = JHTML::_('grid.published', $row, $i);
			$editlink = JROUTE::_('index.php?option=com_virtuemart&view=manufacturercategories&task=edit&virtuemart_manufacturercategories_id=' . $row->virtuemart_manufacturercategories_id);
			$manufacturersList = JROUTE::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturercategories_id=' . $row->virtuemart_manufacturercategories_id);

			?>
			<tr class="row<?php echo $k ; ?>">
				<td width="10">
					<?php echo $checked; ?>
				</td>
				<td align="left">
					<a href="<?php echo $editlink; ?>"><?php echo $row->mf_category_name; ?></a>

				</td>
				<td>
					<?php echo JText::_($row->mf_category_desc); ?>
				</td>
				<td>
					<a title="<?php echo JText::_('COM_VIRTUEMART_MANUFACTURER_SHOW'); ?>" href="<?php echo $manufacturersList; ?>"><?php echo JText::_('COM_VIRTUEMART_SHOW'); ?></a>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<td align="right">
		    <?php echo $row->virtuemart_manufacturercategories_id; ?>
		</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>

	<?php echo $this->addStandardHiddenToForm(); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>