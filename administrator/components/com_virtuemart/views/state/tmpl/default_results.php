<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage State
* @author RickG, Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6048 2012-05-30 20:18:53Z Milbo $
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
			<?php echo   JText::_('COM_VIRTUEMART_STATE_NAME'); ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_ZONE_ASSIGN_CURRENT_LBL'); ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_STATE_2_CODE'); ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_STATE_3_CODE'); ?>
		    </th>
		    <th width="20">
			<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
		    </th>
		</tr>
	    </thead>
	    <?php


	    for ($i=0, $n=count( $this->states ); $i < $n; $i++) {
		$row = $this->states[$i];

		$checked = JHTML::_('grid.id', $i, $row->virtuemart_state_id,null,'virtuemart_state_id');
		$published = $this->toggle( $row->published, $i, 'published');

		?>
	    <tr >
		<td width="10">
			<?php echo $checked; ?>
		</td>
		<td align="left">
		    <?php echo $this->editLink($row->virtuemart_state_id, $row->state_name, 'virtuemart_state_id') ?>
		</td>
		<td align="left">
			<?php echo $row->virtuemart_worldzone_id; ?>
		</td>
		<td>
			<?php echo $row->state_2_code; ?>
		</td>
		<td>
			<?php echo $row->state_3_code; ?>
		</td>
		<td>
			<?php echo $published; ?>
		</td>
	    </tr>
		<?php
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
    <input type="hidden" name="virtuemart_country_id" value="<?php echo $this->virtuemart_country_id; ?>" />
	<?php echo $this->addStandardHiddenToForm(); ?>