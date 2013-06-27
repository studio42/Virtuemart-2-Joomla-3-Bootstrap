<?php

/**

*

* Description

*

* @package	VirtueMart

* @subpackage Extensions

* @author StephanieS

* @link http://www.virtuemart.net

* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.

* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php

* VirtueMart is free software. This version may have been modified pursuant

* to the GNU General Public License, and as distributed it includes or

* is derivative of works licensed under the GNU General Public License or

* other free or open source software licenses.

* @version $Id: default.php 5928 2012-04-20 11:58:15Z alatak $

*/



// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
//if($virtuemart_vendor_id==1 || $perm->check( 'admin' )){

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="editcell">
		<table class="adminlist" cellspacing="0" cellpadding="0">
		<thead>
		<tr>
			<th>
				<?php   ?>
			</th>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->usergroups); ?>);" />
			</th>
			<th width="60">
				<?php echo JText::_('COM_VIRTUEMART_USERGROUPS_LIST_NAME'); ?>
			</th>
			<th width="20">
				<?php echo JText::_('COM_VIRTUEMART_USERGROUPS_LEVEL'); ?>
			</th>
		</tr>
		</thead>
		<?php
		$k = 0;

		for ($i=0, $n=count( $this->usergroups ); $i < $n; $i++) {

			$row = $this->usergroups[$i];
			$checked = JHTML::_('grid.id', $i, $row->virtuemart_shoppergroup_id);
//			$published = JHTML::_('grid.published', $row, $i);
			$editlink = JROUTE::_('index.php?option=com_virtuemart&view=usergroups&task=edit&cid[]=' . $row->virtuemart_shoppergroup_id);
			?>
			<tr class="<?php echo "row".$k; ?>">
				<td width="10" align="right">
					<?php echo $row->virtuemart_shoppergroup_id; ?>
				</td>
				<td width="10">
					<?php echo $checked; ?>
				</td>
				<td align="left">
					<a href="<?php echo $editlink; ?>"><?php echo $row->group_name; ?></a>
				</td>
				<td>
					<?php echo JText::_($row->group_level); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		<tfoot>
			<tr>
				<td colspan="21">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>