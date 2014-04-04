<?php defined('_JEXEC') or die();
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

$currency = CurrencyDisplay::getInstance ();
 ?>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
	
	<table class="table table-striped">
	    <thead>
		<tr>
		    <th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
		    </th>
		    <th  class="autosize">
				<?php echo $this->sort('coupon_code', 'COM_VIRTUEMART_COUPON_CODE')  ?>
		    </th>
		    <th class="autosize">
				<?php echo $this->sort('coupon_type', 'COM_VIRTUEMART_COUPON_TYPE')  ?>
		    </th>
		    <th>
			<?php echo JText::_('COM_VIRTUEMART_VALUE'); ?>
		    </th>
		    <th class="autosize">
				<?php echo $this->sort('coupon_value_valid', 'COM_VIRTUEMART_COUPON_VALUE_VALID_AT')  ?>
		    </th>
		     <th class="autosize"><?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?></th>
		</tr>
	    </thead>
	    <?php

	    for ($i=0, $n=count($this->coupons); $i < $n; $i++) {
		$row = $this->coupons[$i];
		$canDo = $this->canChange($row->created_by);
		$published = $this->toggle( $row->published, $i, 'published',$canDo );
		$checked = JHTML::_('grid.id', $i, $row->virtuemart_coupon_id);
		?>
	    <tr >
		<td width="10">
			<?php echo $checked; ?>
		</td>
		<td align="left">
		   <?php echo $this->editLink($row->virtuemart_coupon_id,$row->coupon_code,'virtuemart_coupon_id') ?>
		</td>
		<td align="left">
			<?php echo JText::_($row->coupon_type); ?>
		</td>
		<td>
			<?php
			if ( $row->percent_or_total==='percent')
				echo $row->coupon_value.'%';
			else
				echo $currency->priceDisplay($row->coupon_value); //.' '.$this->vendor_currency;
			?>
		</td>
		<td align="left">
			<?php echo $currency->priceDisplay($row->coupon_value_valid); // echo $this->vendor_currency; 
			?>
		</td>
		<td align="left">
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
	<?php echo $this->addStandardHiddenToForm();  ?>
