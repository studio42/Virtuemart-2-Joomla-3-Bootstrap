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
?>
<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
<table class="table table-striped table-hover" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th>
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
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
			$canDo = $this->canChange($product->created_by);
			$published = $this->toggle( $product->published, $i, 'published',$canDo);
			
			//<!-- low_stock_notification  -->
			// current reel stock
			$inStock = $product->product_in_stock - $product->product_ordered ;
			if ($inStock < 1) $stockLabel = 'label-important';
			else if ($inStock <= $product->low_stock_notification) $stockLabel = 'label-warning';
			else $stockLabel = 'label-success';
			if ( $inStock < 1) $stockstatut ="OUT";
			elseif ( $inStock < $product->low_stock_notification ) $stockstatut ="LOW";
			else $stockstatut = "NORMAL";
			$orderedLabel = $stockLabel;
			$stockstatut= $stockLabel.'" title="'.jText::_('COM_VIRTUEMART_STOCK_LEVEL_'.$stockstatut);
			if ($product->product_ordered == 0 ) {
				$orderedLabel = '';
			}
			?>
			<tr <?php echo $inStock < $product->product_ordered ? 'class="error"': '' ?>>
				<!-- Checkbox -->
				<td><?php echo $checked; ?></td>
				<!-- Product name -->
				<td>
					<?php echo $this->editLink($product->virtuemart_product_id, $product->product_name, 'virtuemart_product_id',
						array('class'=> 'hasTooltip', 'title' => JText::_('COM_VIRTUEMART_EDIT').' '.$product->product_name), 'product') ?>
				</td>
				<td><?php echo $product->product_sku; ?></td>
				<td width="5%"><a href="#updateStockModal" role="button" data-toggle="modal" class="updateStock" data-title="<?php echo $product->product_name ?>" data-id="<?php echo $product->virtuemart_product_id ?>"><span class="label <?php echo $stockstatut ?>"><?php echo $product->product_in_stock; ?></span></a></td>
				<td width="5%"><span class="label <?php echo $orderedLabel ?>"><?php echo $product->product_ordered; ?></span></td>
				<td><?php echo $product->product_price_display; ?></td>
				<td><?php echo $product->product_instock_value; ?></td>
				<td><?php echo $product->product_weight." ". $product->weigth_unit_display; ?></td>
				<td><?php echo $published; ?></td>
			</tr>
		<?php
			$i++;
		}
	}
	?>
	</tbody>
	<tfoot>
		<tr>
		<td colspan="9">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
		</tr>
	</tfoot>
</table>
<?php echo $this->addStandardHiddenToForm();  ?>