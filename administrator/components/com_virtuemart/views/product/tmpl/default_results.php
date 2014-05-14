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
* @version $Id: product.php 3304 2011-05-20 06:57:27Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

if ($product_parent_id=JRequest::getInt('product_parent_id', false))   $col_product_name='COM_VIRTUEMART_PRODUCT_CHILDREN_LIST'; else $col_product_name='COM_VIRTUEMART_PRODUCT_NAME';


$listDirn = strtolower($this->lists['filter_order_Dir']);
$saveOrder = ($this->lists['filter_order'] == 'pc.ordering');
?>

<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
	<table class="table table-striped"<?php if ($saveOrder) echo ' id="productList"' ?>>
	<thead>
	<tr>
		<?php if( $this->virtuemart_category_id ) { ?>
			<th width="1%" class="nowrap center hidden-phone">
				<?php echo $this->sort( 'pc.ordering' , 'COM_VIRTUEMART_ORDERING') ?>
			</th>
		<?php } ?>
		<th width="20px">
			<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
		</th>
		<th><?php echo $this->sort('product_name',$col_product_name) ?> </th>
		<?php if (!$product_parent_id ) { ?>
                <th><span class="hidden-phone"><?php echo $this->sort('product_parent_id','COM_VIRTUEMART_PRODUCT_CHILDREN_OF'); ?>
					</span>
					<span class="visible-phone icon-chevron-down hasTip" title="<?php echo JText::_( 'COM_VIRTUEMART_PRODUCT_CHILDREN_OF'); ?>"></span>
				</th>
                <?php } ?>
		<th width="80px"><span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_PRODUCT_PARENT_LIST_CHILDREN'); ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_PARENT_LIST_CHILDREN'); ?></span></th>
		<th width="48px" ><?php echo JText::_('COM_VIRTUEMART_PRODUCT_MEDIA'); ?></th>
		<!--<th class="hidden-phone"><?php echo $this->sort('product_sku') ?></th>-->
		<th width="80px" class="hidden-phone"><?php echo $this->sort('product_price', 'COM_VIRTUEMART_PRODUCT_PRICE_TITLE') ; ?></th>
<?php /*		<th><?php echo JHTML::_('grid.sort', 'COM_VIRTUEMART_CATEGORY', 'c.category_name', $this->lists['filter_order_Dir'], $this->lists['filter_order'] ); ?></th> */ ?>
<th><span class="hidden-phone"><?php echo JText::_( 'COM_VIRTUEMART_CATEGORY'); ?></span><span class="visible-phone icon-folder hasTip" title="<?php echo JText::_( 'COM_VIRTUEMART_CATEGORY'); ?>"></span></th>

		<th  class="hidden-phone"><?php echo $this->sort('mf_name', 'COM_VIRTUEMART_MANUFACTURER_S') ; ?></th>
		<th width="40px" class="autosize">
			<span class="hidden-phone"><?php echo JText::_('COM_VIRTUEMART_REVIEW_S'); ?></span>
			<div class="visible-phone icon-comments hasTip" title="<?php echo JText::_( 'COM_VIRTUEMART_REVIEW_S'); ?>"> </div>
		</th>
		<th width="40px"  class="hidden-phone"><?php echo $this->sort('product_special', 'COM_VIRTUEMART_PRODUCT_FORM_SPECIAL'); ?>
			 </th>
		<th width="40px"  class="hidden-phone"><?php echo $this->sort('published') ; ?></th>
		<th class="hidden-phone"><?php echo $this->sort('p.virtuemart_product_id', 'COM_VIRTUEMART_ID')  ?></th>
	  </tr>
	</thead>
	<tbody>
	<?php
	if ($total = count($this->productlist) ) {
		$i = 0;
		
		$canPublish = ShopFunctions::can('publish');
		foreach ($this->productlist as $key => $product) {
			$checked = JHTML::_('grid.id', $i , $product->virtuemart_product_id);
			$canDo = $this->canChange($product->created_by) ;
			$published = $this->toggle( $product->published, $i, 'published',$canDo && $canPublish);
			// featured bootstrap style , canDo is the permission 
			$is_featured = vmHtml::featured($product->product_special, $i, $canDo);
			?>
			<tr sortable-group-id="<?php echo $this->virtuemart_category_id; ?>">
				<?php if( $this->virtuemart_category_id ) { ?>
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = 'sortable-handler';
						if (!$canDo)
						{
							$iconClass = 'sortable-handler inactive';
						}
						elseif (!$saveOrder)
						{
							$iconClass = 'inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
						}
						?>
						<span class="<?php echo $iconClass ?>">
							<i class="icon-menu"></i>
						</span>
						<?php if ($canDo && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $product->ordering; ?>" class="width-20 text-area-order " />
						<?php endif; ?>
					</td>
				<?php } ?>
				<td align="right" ><?php echo $checked; ?></td>
				<?php
				$link = 'index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$product->virtuemart_product_id.'&product_parent_id='.$product->product_parent_id.$this->tmpl;
                /* Product list should be ordered */
				?>
				<td>
					<?php echo JHTML::_('link', JRoute::_($link), $product->product_name, array('class'=> 'hasTooltip', 'title' => JText::_('COM_VIRTUEMART_EDIT').' '.$product->product_name)); ?>
					<div class="small"><?php echo $product->product_sku; ?> 
						<span class="visible-phone"><?php echo $published; ?> <?php	echo $is_featured; ?></span>
					</div>
				</td>
				<?php if (!$product_parent_id ) { ?>
					<td class="autosize"><?php
						if ($product->product_parent_id  ) {
							VirtuemartViewProduct::displayLinkToParent($product->product_parent_id);
						}
						?>
					</td>
				<?php } ?>
				<td>
					<?php
					 VirtuemartViewProduct::displayLinkToChildList($product->virtuemart_product_id , $product->product_name);
					?>
				</td>
				<?php
					/* Create URL */
					$link = JRoute::_('index.php?view=media&virtuemart_product_id='.$product->virtuemart_product_id.'&option=com_virtuemart'.$this->tmpl);
				?>
				<td align="center">
					<?php // We show the images only when less than 31 products are displayeed -->
					$mediaLimit = (int)VmConfig::get('mediaLimit',30);
					if($this->pagination->limit<=$mediaLimit or $total<=$mediaLimit){
						// Product list should be ordered
						$this->model->addImages($product,1);
						$img = '<span class="badge badge-'.($product->mediaitems ? 'info' : 'default').'">'.$product->mediaitems.'</span>'.$product->images[0]->displayMediaThumb('',false );
					} else {
						$img = '<span class="badge badge-'.($product->mediaitems ? 'info' : 'default').'">'.$product->mediaitems.'</span>';
					}
					echo JHTML::_('link', $link, $img,  array('class' => 'hasTooltip thumbnail' ,'title' => JText::_('COM_VIRTUEMART_MEDIA_MANAGER').' '.$product->product_name));
				 ?></td>
				<!--<td class="hidden-phone"><?php echo $product->product_sku; ?></td>-->
				<td align="right" class="hidden-phone"><?php echo isset($product->product_price_display)? $product->product_price_display:JText::_('COM_VIRTUEMART_NO_PRICE_SET') ?></td>
				<td>
					<?php //echo JHTML::_('link', JRoute::_('index.php?view=category&task=edit&virtuemart_category_id='.$product->virtuemart_category_id.'&option=com_virtuemart'), $product->category_name);
					echo $product->categoriesList;
					?>
				</td>
				<?php /* 
					// Reorder only when category ID is present 
					if ($this->virtuemart_category_id ) {
						$ordering = true;
						?>
						<td class="order">
						<span class="icon-move"></span>
							<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', JText::_('COM_VIRTUEMART_MOVE_UP'), $ordering ); ?></span>
							<span><?php echo $this->pagination->orderDownIcon( $i, $total , true, 'orderdown', JText::_('COM_VIRTUEMART_MOVE_DOWN'), $ordering ); ?></span>
							<input class="ordering input-mini" type="text" name="order[<?php echo $product->id?>]" id="order[<?php echo $i?>]" size="5" value="<?php echo $product->ordering; ?>" style="text-align: center" />
							<?php // echo vmCommonHTML::getOrderingField( $product->ordering ); ?>
						</td>
						<?php
					}  */?>
				<td class="hidden-phone">
					<?php echo $this->editLink(	$product->virtuemart_manufacturer_id, $product->mf_name, 'virtuemart_manufacturer_id',
					array('class'=> 'hasTooltip', 'title' => JText::_('COM_VIRTUEMART_EDIT').' '.$product->mf_name,'product'), 'manufacturer') ?>
				</td>
				<!-- Reviews -->
				<?php $link = 'index.php?option=com_virtuemart&view=ratings&task=listreviews&virtuemart_product_id='.$product->virtuemart_product_id.$this->tmpl; ?>
				<td align="center" ><?php echo JHTML::_('link', $link, $product->reviews); ?></td>
				<td align="center" class="hidden-phone">
					<?php
						echo $is_featured;
					?>
				 </td>
				<td align="center"  class="hidden-phone"><?php echo $published; ?></td>
				<td align="right"  class="hidden-phone"><?php echo $product->virtuemart_product_id; ?></td>
			</tr>
		<?php
	
			$i++;
		}
	}
	$colspan = 13;
	if ($this->virtuemart_category_id ) ++$colspan;
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="<?php echo $colspan ?>" data-cols-phone="<?php echo $colspan ?>">
			<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>
	<!-- Hidden Fields -->
	<input type="hidden" name="product_parent_id" value="<?php echo JRequest::getInt('product_parent_id', 0); ?>" />
	<?php echo $this->addStandardHiddenToForm();
// if ($saveOrder)
// {
	// TODO right ordering !!!
	$saveOrderingUrl = 'index.php?option=com_virtuemart&view=product&task=saveorder&format=json';
	// only recall if script is loaded
	if (jRequest::getword('format') == 'raw') { ?>
	<script>	
		sortableList = new jQuery.JSortableList('#productList tbody','adminForm','<?php echo strtolower($listDirn) ?>', '<?php echo $saveOrderingUrl ?>','','');
	</script>
	<?php
	}
	else
		JHtml::_('sortablelist.sortable', 'productList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
// }
?>

