<?php defined('_JEXEC') or die('Restricted access');


// Separator
$verticalseparator = " vertical-separator";

foreach ($this->products as $type => $productList ) {

if (empty($productList)) continue;
// Calculating Products Per Row
$perRow = VmConfig::get ( 'homepage_products_per_row', 3 ) ;
$spanClass = 'span'.floor (12/$perRow );
$pCount = 1 ;
$pTotal = count($productList);
$productTitle = JText::_('COM_VIRTUEMART_'.$type.'_PRODUCT')

?>

<div class="<?php echo $type ?>-view browse-view">

	<h4><?php echo $productTitle ?></h4>
	<ul class="thumbnails">
	 <?php foreach ( $productList as $product ) { ?>

		<li class="<?php echo $spanClass ?>">
		  <div class="thumbnail">
			<div class="text-center">
				<a href="<?php echo $product->link; ?>" title="<?php echo $product->product_name ?>">
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
		if ($pCount == $perRow && $pTotal>0 ) { ?>
			</ul>
			<ul class="thumbnails">
			<?php 
			$pCount =0;
		}
		$pTotal--;
		$pCount++;
	  } ?>
	</ul>
</div>
<?php }
