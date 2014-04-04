<?php
/**
 * Print orderdetails
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: order_print.php 6043 2012-05-21 21:40:56Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
	<table class="table table-striped table-condensed" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_VIRTUEMART_ORDER_HISTORY_DATE_ADDED') ?></th>
				<th><?php echo JText::_('COM_VIRTUEMART_ORDER_HISTORY_CUSTOMER_NOTIFIED') ?></th>
				<th><?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_STATUS') ?></th>
				<th><?php echo JText::_('COM_VIRTUEMART_COMMENT') ?></th>
			</tr>
		</thead>
		<?php
		foreach ($this->orderdetails['history'] as $this->orderbt_event ) {
			echo "<tr>";
			echo "<td>". vmJsApi::date($this->orderbt_event->created_on,'LC2',true) ."</td>\n";
			if ($this->orderbt_event->customer_notified == 1) {
				echo '<td align="center">'.JText::_('COM_VIRTUEMART_YES').'</td>';
			}
			else {
				echo '<td align="center">'.JText::_('COM_VIRTUEMART_NO').'</td>';
			}
			if(!isset($this->orderstatuslist[$this->orderbt_event->order_status_code])){
				if(empty($this->orderbt_event->order_status_code)){
					$this->orderbt_event->order_status_code = 'unknown';
				}
				$_orderStatusList[$this->orderbt_event->order_status_code] = JText::_('COM_VIRTUEMART_UNKNOWN_ORDER_STATUS');
			}

			echo '<td align="center">'.$this->orderstatuslist[$this->orderbt_event->order_status_code].'</td>';
			echo "<td>".$this->orderbt_event->comments."</td>\n";
			echo "</tr>\n";
		}
		?>
		<tr>
			<td colspan="4">
			<a href="#updateOrderStatus" class="show_element btn btn-block"><span class="icon icon-apply"></span><?php echo JText::_('COM_VIRTUEMART_ORDER_UPDATE_STATUS') ?></a>

			</td>
		</tr>

		<?php
			// Load additional plugins
			$_dispatcher = JDispatcher::getInstance();
			$_returnValues1 = $_dispatcher->trigger('plgVmOnUpdateOrderBEPayment',array($this->orderID));
			$_returnValues2 = $_dispatcher->trigger('plgVmOnUpdateOrderBEShipment',array(  $this->orderID));
			$_returnValues = array_merge($_returnValues1, $_returnValues2);
			$_plg = '';
			foreach ($_returnValues as $_returnValue) {
				if ($_returnValue !== null) {
					$_plg .= ('	<td colspan="4">' . $_returnValue . "</td>\n");
				}
			}
			if ($_plg !== '') {
				echo "<tr>\n$_plg</tr>\n";
			}
		?>

	</table>
