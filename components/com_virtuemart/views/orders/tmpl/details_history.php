<?php
/**
*
* Order history view
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
* @version $Id: details_history.php 4252 2011-10-04 21:36:23Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<table width="100%" cellspacing="2" cellpadding="4" border="0">
	<tr align="left" class="sectiontableheader">
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_DATE') ?></th>
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></th>
		<th align="left" ><?php echo JText::_('COM_VIRTUEMART_ORDER_COMMENT') ?></th>
	</tr>
<?php
	foreach($this->orderdetails['history'] as $_hist) {
		if (!$_hist->customer_notified) {
			continue;
		}
?>
		<tr valign="top">
			<td align="left">
				<?php echo vmJsApi::date($_hist->created_on,'LC2',true); ?>
			</td>
			<td align="left" >
				<?php echo $this->orderstatuses[$_hist->order_status_code]; ?>
			</td>
			<td align="left" >
				<?php echo $_hist->comments; ?>
			</td>
		</tr>
<?php
	}
?>
</table>
