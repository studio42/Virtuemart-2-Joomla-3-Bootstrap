<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage ShopperGroup
* @author Markus �hler
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6370 2012-08-23 16:05:28Z Milbo $
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
					<?php echo $this->sort('shopper_group_name','COM_VIRTUEMART_SHOPPERGROUP_NAME'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_VIRTUEMART_SHOPPERGROUP_DESCRIPTION'); ?>
				</th>
				<th width="20">
					<?php echo JText::_('COM_VIRTUEMART_DEFAULT'); ?>
				</th>
				<th width="30px" >
					<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
				</th>
				<?php if((Vmconfig::get('multix','none')!='none') && $this->showVendors){ ?>
				<th>
					<?php echo JText::_('COM_VIRTUEMART_VENDOR'); ?>
				</th>
				<?php } ?>
					  <th><?php echo $this->sort('virtuemart_shoppergroup_id', 'COM_VIRTUEMART_ID')  ?></th>

		    </tr>
	    </thead>
		<tbody>
		<?php
	    $k = 0;
	    for ($i = 0, $n = count( $this->shoppergroups ); $i < $n; $i++) {
		    $row = $this->shoppergroups[$i];
			$published = $this->toggle( $row->published, $i, 'published');
		    $checked = JHTML::_('grid.id', $i, $row->virtuemart_shoppergroup_id,null,'virtuemart_shoppergroup_id');
			?>

		  <tr>
				<td width="10">
					<?php echo $checked; ?>
				</td>
				<td align="left">
				  <?php echo $this->editLink($row->virtuemart_shoppergroup_id, $row->shopper_group_name, 'virtuemart_shoppergroup_id[]') ?>
				</td>
				<td align="left">
					<?php echo $row->shopper_group_desc; ?>
				</td>
				<td>
					<?php
					if ($row->default == 1) {
						echo JHtml::_('image','menu/icon-16-default.png', JText::_('COM_VIRTUEMART_SHOPPERGROUP_DEFAULT'), NULL, true);
					} else { ?>
						<a href="#" 
							onclick="return listItemTask('cb<?php echo $i ?>','default')" class="btn btn-micro hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ENABLE_ITEM') ?>">
							<i class="icon-star-empty"></i>
						</a>
						<?php
					} ?>
			    </td>
				<td><?php echo $published; ?></td>
				<?php if((Vmconfig::get('multix','none')!='none') && $this->showVendors){ ?>
					<td align="left">
						<?php echo $row->virtuemart_vendor_id; ?>
					</td>
				<?php } ?>
				<td align="left">
					<?php echo $row->virtuemart_shoppergroup_id; ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7">
				  <?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php echo $this->addStandardHiddenToForm($this->_name,$this->task);  ?>
	