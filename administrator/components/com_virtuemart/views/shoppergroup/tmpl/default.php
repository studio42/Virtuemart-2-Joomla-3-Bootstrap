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
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
  <div id="editcell">
	  <table class="adminlist" cellspacing="0" cellpadding="0">
	    <thead>
		    <tr>
				<th width="10">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->shoppergroups); ?>);" />
				</th>
				<th>
					<?php echo JText::_('COM_VIRTUEMART_SHOPPERGROUP_NAME'); ?>
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
	    </thead><?php

	    $k = 0;
	    for ($i = 0, $n = count( $this->shoppergroups ); $i < $n; $i++) {
		    $row = $this->shoppergroups[$i];
			$published = JHTML::_('grid.published', $row, $i );
		    $checked = JHTML::_('grid.id', $i, $row->virtuemart_shoppergroup_id,null,'virtuemart_shoppergroup_id');
		    $editlink = JROUTE::_('index.php?option=com_virtuemart&view=shoppergroup&task=edit&virtuemart_shoppergroup_id[]=' . $row->virtuemart_shoppergroup_id); ?>

	      <tr class="row<?php echo $k ; ?>">
			    <td width="10">
				    <?php echo $checked; ?>
			    </td>
			    <td align="left">
			      <a href="<?php echo $editlink; ?>"><?php echo $row->shopper_group_name; ?></a>
			    </td>
			    <td align="left">
				    <?php echo $row->shopper_group_desc; ?>
			    </td>
			    <td>
					<?php
					if ($row->default == 1) {
					    if (JVM_VERSION===1) {
						?>
						<img src="templates/khepri/images/menu/icon-16-default.png" alt="<?php echo JText::_( 'COM_VIRTUEMART_SHOPPERGROUP_DEFAULT' ); ?>" />
						<?php
					    }  else {
						echo JHtml::_('image','menu/icon-16-default.png', JText::_('COM_VIRTUEMART_SHOPPERGROUP_DEFAULT'), NULL, true);
						}
					} else {
						?>
						&nbsp;
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

	      </tr><?php
		    $k = 1 - $k;
	    } ?>
	    <tfoot>
		    <tr>
		      <td colspan="10">
			      <?php echo $this->sgrppagination->getListFooter(); ?>
		      </td>
		    </tr>
	    </tfoot>
	  </table>
  </div>

	<?php echo $this->addStandardHiddenToForm($this->_name,$this->task); ?>
</form><?php
AdminUIHelper::endAdminArea(); ?>