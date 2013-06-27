<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6307 2012-08-07 07:39:45Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
AdminUIHelper::startAdminArea();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="header">
		<div id="filterbox">
		<table>
		  <tr>
			 <td align="left">
				<?php echo $this->displayDefaultViewSearch('filter_product') ?>
				<?php echo $this->lists['stockfilter'] ?>
			 </td>
		  </tr>
		</table>
		</div>
		<div id="resultscounter"><?php echo $this->pagination->getResultsCounter();?></div>
	</div>

	<div style="text-align: left;">
	<table class="adminlist" cellspacing="0" cellpadding="0">
	<thead>
	<tr>
		<th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->inventorylist); ?>')" /></th>
		<th><?php echo $this->sort('product_name') ?></th>
		<th><?php echo $this->sort('product_sku')?></th>
		<th><?php echo $this->sort('product_in_stock','COM_VIRTUEMART_PRODUCT_FORM_IN_STOCK') ?></th>
		<th><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_ORDERED_STOCK') ?> </th>
		<th><?php echo $this->sort('product_price','COM_VIRTUEMART_PRODUCT_FORM_PRICE_COST') ?></th>
		<th><?php echo $this->sort('product_price', 'COM_VIRTUEMART_PRODUCT_INVENTORY_PRICE') ?></th>
		<th><?php echo $this->sort('product_weight','COM_VIRTUEMART_PRODUCT_INVENTORY_WEIGHT') ?></th>
		<th><?php echo $this->sort('published')?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if (count($this->inventorylist) > 0) {
		$i = 0;
		$k = 0;
		$keyword = JRequest::getWord('keyword');
		foreach ($this->inventorylist as $key => $product) {
			$checked = JHTML::_('grid.id', $i , $product->virtuemart_product_id);
			$published = JHTML::_('grid.published', $product, $i );
			
			//<!-- low_stock_notification  -->
			if ( $product->product_in_stock - $product->product_ordered < 1) $stockstatut ="out";
			elseif ( $product->product_in_stock - $product->product_ordered < $product->low_stock_notification ) $stockstatut ="low";
			else $stockstatut = "normal";
			
			$stockstatut='class="stock-'.$stockstatut.'" title="'.jText::_('COM_VIRTUEMART_STOCK_LEVEL_'.$stockstatut).'"';
			?>
			<tr class="row<?php echo $k ; ?>">
				<!-- Checkbox -->
				<td><?php echo $checked; ?></td>
				<!-- Product name -->
				<?php
				$link = 'index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$product->virtuemart_product_id.'&product_parent_id='.$product->product_parent_id;
				?>
				<td><?php echo JHTML::_('link', JRoute::_($link), $product->product_name, array('title' => JText::_('COM_VIRTUEMART_EDIT').' '.$product->product_name)); ?></td>
				<td><?php echo $product->product_sku; ?></td>
				<td <?php echo $stockstatut; ?>><?php echo $product->product_in_stock; ?></td>
				<td <?php echo $stockstatut; ?> width="5%"><?php echo $product->product_ordered; ?></td>
				<td><?php echo $product->product_price_display; ?></td>
				<td><?php echo $product->product_instock_value; ?></td>
				<td><?php echo $product->product_weight." ". $product->weigth_unit_display; ?></td>
				<td><?php echo $published; ?></td>
			</tr>
		<?php
			
			$k = 1 - $k;
			$i++;
		}
	}
	?>
	</tbody>
	<tfoot>
		<tr>
		<td colspan="16">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
		</tr>
	</tfoot>
	</table>
</div>
<!-- Hidden Fields -->
<input type="hidden" name="filter_order" value="<?php echo $this->lists['filter_order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['filter_order_Dir']; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_virtuemart" />
<input type="hidden" name="view" value="inventory" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php AdminUIHelper::endAdminArea(); ?>