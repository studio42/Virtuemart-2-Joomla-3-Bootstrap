<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 6556 2012-10-17 18:15:30Z kkmediaproduction $
 */

//vmdebug('$this->category',$this->category);
//vmdebug ('$this->category ' . $this->category->category_name);
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
JHTML::_ ('behavior.modal');
// NEW !! overide for ordering list and manufacturers
JHtml::addIncludePath(JPATH_COMPONENT . '/layouts');
/* javascript for list Slide
  Only here for the order list
  can be changed by the template maker
*/
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";

$document = JFactory::getDocument ();
$document->addScriptDeclaration ($js);

$editLink = $this->editLink('category',$this->category->virtuemart_category_id,$this->category->created_by);
// set the current parent category for simplier adding new
$idLink = '&category_parent_id='.$this->category->virtuemart_category_id;
$newCatLink = $this->newLink('category',$idLink,$this->category->created_by);
$idLink = '&virtuemart_category_id='.$this->category->virtuemart_category_id;
$newProdLink = $this->newLink('product',$idLink,$this->category->virtuemart_category_id);
if ($newProdLink || $newCatLink || $editLink) { ?>
	<div class="btn-group pull-right">
		<?php
		 echo $newCatLink;
		 echo $newProdLink;
		 echo $editLink;
		?>
	</div>
	<div class="clearFix clear"></div>
<?php }
if (empty($this->keyword) and !empty($this->category)) {
	?>
<div class="category_description">
	<?php echo $this->category->category_description; ?>
</div>
<?php
}

/* Show child categories */

if (VmConfig::get ('showCategory', 1) && empty($this->keyword) && !empty($this->category->haschildren) ) {

	// Calculating Categories Per Row
	$perRow = VmConfig::get ('categories_per_row', 3);
	$spanClass = 'span'.floor (12/$perRow );
	$cCount = 1 ;
	$cTotal = count ($this->category->children);

	?>
	<div class="category-view">
	  <ul class="thumbnails">
		<?php // Start the Output
		foreach ($this->category->children as $category) {

			// Category Link
			$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id, FALSE);
			?>
			<li class="<?php echo $spanClass ?>">
			  <div class="thumbnail">
				<div class="text-center">
					<?php if (!empty($category->images)) { ?>
						<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
							<?php echo $category->images[0]->displayMediaFull("", false); ?>
						</a>
						<?php
						}
					?>
				</div>
				<div class="caption">
					<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
						<h5><?php echo $category->category_name ?></h5>
					</a>
				</div>
			  </div>
			</li>
			<?php 
			// see if whe must add a new line
			if ($cCount == $perRow && $cTotal>0 ) { ?>
				</ul>
				<ul class="thumbnails">
				<?php 
				$cCount =0;
			}
			$cTotal--;
			$cCount++;
		} ?>

	  </ul>
	</div>

	<?php

}
?>
<div class="browse-view">
<?php
if (!empty($this->keyword)) {
	?>
<h3><?php echo $this->keyword; ?></h3>
	<?php
} ?>
<?php if ($this->search !== NULL) { ?>
<form action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=category&limitstart=0&virtuemart_category_id=' . $this->category->virtuemart_category_id, FALSE); ?>" method="get">

	<!--BEGIN Search Box -->
	<div class="virtuemart_search">
		<?php echo $this->searchcustom ?>
		<br/>
		<?php echo $this->searchcustomvalues ?>
		<input name="keyword" class="inputbox" type="text" size="20" value="<?php echo $this->keyword ?>"/>
		<input type="submit" value="<?php echo JText::_ ('COM_VIRTUEMART_SEARCH') ?>" class="button" onclick="this.form.keyword.focus();"/>
	</div>
	<input type="hidden" name="search" value="true"/>
	<input type="hidden" name="view" value="category"/>

</form>
<!-- End Search Box -->
	<?php } ?>

<?php // Show products in category
if (!empty($this->products)) {
	$pTotal = count($this->products);
	$spanClass = 'span'.floor (12/$this->perRow);
	$pCount = 1 ;
	?>
<div class="orderby-displaynumber">
	<?php echo JHtml::_('vm.ordering',$this->orderByList['orderby']) ?>
	<?php echo JHtml::_('vm.manufacturers', $this->orderByList['manufacturer']) ?>

	<div class="pull-right"><?php /* echo $this->vmPagination->getResultsCounter ();?><br/><?php */ echo $this->vmPagination->getLimitBox ($this->category->limit_list_step); ?></div>
	<div class="vm-pagination pagination">
		<?php echo $this->vmPagination->getPagesLinks (); ?>
		<span style="float:right"><?php echo $this->vmPagination->getPagesCounter (); ?></span>
	</div>

	<div class="clear"></div>
</div> <!-- end of orderby-displaynumber -->

<h1><?php echo $this->category->category_name; ?></h1>

	<ul class="thumbnails">
	  <?php foreach ($this->products as $product) { ?>

		<li class="<?php echo $spanClass ?>">
		  <div class="thumbnail">
			<div class="text-center">
				<a href="<?php echo $product->link; ?>" title="<?php echo $this->category->category_name.' : '.$product->product_name ?>">
					<?php echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false); ?>
				</a>
			</div>
			<div class="caption">
			  <h4 class="text-center"><?php echo $product->product_name ?></h4>
			  <p class="text-center" style="height:40px;"> 
				<?php // Product Short Description
				if (!empty($product->product_s_desc)) {
					echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 60, '...') ;
				 } ?>
			  </p>
			  <h3><?php echo JHTML::link ($product->link, /*JText::_ ('JSHOW').*/' <i class="icon icon-arrow-right-2"></i> ', array('title' => $product->product_name, 'class' => 'pull-right')); ?><span class=""><?php echo $this->currency->createPriceDiv ('salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $product->prices,true); ?></span></h3>
			</div>
		  </div>
		</li>
		<?php 
		// see if whe must add a new line
		if ($pCount == $this->perRow && $pTotal>0 ) { ?>
			</ul>
			<ul class="thumbnails">
			<?php 
			$pCount =0;
		}
		$pTotal--;
		$pCount++;
	  } ?>
	</ul>
<div class="vm-pagination pagination"><?php echo $this->vmPagination->getPagesLinks (); ?><span style="float:right"><?php echo $this->vmPagination->getPagesCounter (); ?></span></div>

	<?php
} elseif ($this->search !== NULL) {
	echo JText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
}
?>
</div><!-- end browse-view -->