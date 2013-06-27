<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Coupon
* @author RickG
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
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->coupons); ?>);" />
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_COUPON_CODE'); ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_COUPON_PERCENT_TOTAL'); ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_COUPON_TYPE'); ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_VALUE'); ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_COUPON_VALUE_VALID_AT'); ?>
		    </th>
		     <th><?php echo $this->sort('virtuemart_coupon_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
	    </thead>
	    <?php
	    $k = 0;
	    for ($i=0, $n=count($this->coupons); $i < $n; $i++) {
		$row = $this->coupons[$i];

		$checked = JHTML::_('grid.id', $i, $row->virtuemart_coupon_id);
		$editlink = JROUTE::_('index.php?option=com_virtuemart&view=coupon&task=edit&cid[]=' . $row->virtuemart_coupon_id);
		?>
	    <tr class="row<?php echo $k; ?>">
		<td width="10">
			<?php echo $checked; ?>
		</td>
		<td align="left">
		    <a href="<?php echo $editlink; ?>"><?php echo $row->coupon_code; ?></a>
		</td>
		<td>
			<?php echo JText::_($row->percent_or_total); ?>
		</td>
		<td align="left">
			<?php echo JText::_($row->coupon_type); ?>
		</td>
		<td>
			<?php echo JText::_($row->coupon_value); ?>
		    <?php if ( $row->percent_or_total=='percent') echo '%' ;
		    else echo $this->vendor_currency;   ?>
		</td>
		<td align="left">
			<?php echo JText::_($row->coupon_value_valid); ?> <?php echo $this->vendor_currency; ?>
		</td>
		<td align="left">
			<?php echo JText::_($row->virtuemart_coupon_id); ?>
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

    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="controller" value="coupon" />
    <input type="hidden" name="view" value="coupon" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>



<?php AdminUIHelper::endAdminArea(); ?>