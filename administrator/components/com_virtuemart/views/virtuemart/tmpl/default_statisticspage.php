<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_system.php 3477 2011-06-11 12:50:50Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<table class="adminlist" cellspacing="0" cellpadding="0">
		<tr>
			<th colspan="2" class="title"><?php echo JText::_('COM_VIRTUEMART_STATISTIC_STATISTICS') ?></th>
		</tr>
		<tr>
		  	<td width="50%">
		  		<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=user');?>">
					<?php echo JText::_('COM_VIRTUEMART_STATISTIC_CUSTOMERS') ?>
				</a>
			</td>
		  	<td width="50%"> <?php echo $this->nbrCustomers ?></td>
		</tr>
		<tr>
		  	<td width="50%">
		  		<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=product');?>">
					<?php echo JText::_('COM_VIRTUEMART_STATISTIC_ACTIVE_PRODUCTS') ?>
				</a>
			</td>
		  <td width="50%"> <?php echo $this->nbrActiveProducts ?> </td>
		</tr>
		<tr>
		  <td width="50%"><?php echo JText::_('COM_VIRTUEMART_STATISTIC_INACTIVE_PRODUCTS') ?>:</td>
		  <td width="50%"> <?php  echo $this->nbrInActiveProducts ?></td>
		</tr>
		<tr>
			<td width="50%">
		  		<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=product&group=featured');?>">
					<?php echo JText::_('COM_VIRTUEMART_SHOW_FEATURED') ?>
				</a>
			</td>
		  <td width="50%"><?php echo $this->nbrFeaturedProducts ?></td>
		</tr>
		<tr>
			<th colspan="2" class="title">
		  		<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=orders');?>">
					<?php echo JText::_('COM_VIRTUEMART_ORDER_MOD') ?>
				</a>
			</th>
		</tr>
		<?php
		$sum = 0;
		for ($i=0, $n=count( $this->ordersByStatus ); $i < $n; $i++) {
			$row = $this->ordersByStatus[$i];
			$link = JROUTE::_('index.php?option=com_virtuemart&view=orders&show='.$row->order_status_code);
			?>
			<tr>
		  		<td width="50%">
		  			<a href="<?php echo $link; ?>"><?php echo $row->order_status_name; ?></a>
				</td>
		  		<td width="50%">
		  			<?php echo $row->order_count; ?>
		  		</td>
			</tr>
		<?php
			$sum = $sum + $row->order_count;
		} ?>
		<tr>
		  <td width="50%"><strong><?php echo JText::_('COM_VIRTUEMART_STATISTIC_SUM') ?>:</strong></td>
		  <td width="50%"><strong><?php echo $sum ?></strong></td>
		</tr>
		<tr>
			<th colspan="2" class="title"><?php echo JText::_('COM_VIRTUEMART_STATISTIC_NEW_ORDERS') ?></th>
		</tr>
		<?php
		for ($i=0, $n=count($this->recentOrders); $i < $n; $i++) {
			$row = $this->recentOrders[$i];
			$link = JROUTE::_('index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id='.$row->virtuemart_order_id);
			?>
		  	<tr>
				<td width="50%">
					<a href="<?php echo $link; ?>"><?php echo $row->order_number; ?></a>
			  	</td>
				<td width="50%">
					<?php echo $row->order_total ?>
				</td>
			</tr>
			<?php
		} ?>
		<tr>
		  <th colspan="2" class="title"><?php echo JText::_('COM_VIRTUEMART_STATISTIC_NEW_CUSTOMERS') ?></th>
		</tr>
		<?php
		for ($i=0, $n=count($this->recentCustomers); $i < $n; $i++) {
			$row = $this->recentCustomers[$i];
			$link = JROUTE::_('index.php?option=com_virtuemart&view=user&virtuemart_user_id='.$row->virtuemart_user_id);
			?>
			<tr>
		  		<td colspan="2">
		  			<a href="<?php echo $link; ?>">
		  				<?php echo   $row->first_name . ' ' . $row->last_name. ' (' . $row->order_number . ') '; ?>
		  			</a>
		  		</td>
			</tr>
		<?php
		}?>
	</table>