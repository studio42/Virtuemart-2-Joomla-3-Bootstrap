<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz
 * @author RolandD,
 * @todo handle child products
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 3605 2011-07-04 10:23:23Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// addon for joomla modal Box
			if (isset($this->type)) {
			$document = JFactory::getDocument();
			$document->setTitle($this->product->product_name);
			$document->setName($this->product->product_name);
			$document->setDescription( $this->product->product_s_desc);
			}


/* Let's see if we found the product */
if (empty ( $this->product )) {
	echo JText::_ ( 'COM_VIRTUEMART_PRODUCT_NOT_FOUND' );
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}
?>

<div class="productdetails-view">

	<?php // Product Title ?>
	<h1><?php echo $this->product->product_name ?></h1>
	<?php // Product Title END ?>
	<?php // Showing The Additional Images
	if(!empty($this->product->images) && count($this->product->images)>0) {
		echo $this->product->images[0]->displayMediaFull('class="product-image"',false); ?>
		<div class="additional-images">
		<?php // List all Images
		foreach ($this->product->images as $image) {
			echo $image->displayMediaThumb('class="product-image"'); //'class="modal"'

		} ?>
		</div>
	<?php } // Showing The Additional Images END ?>

	<?php // Product Short Description
	if (!empty($this->product->product_s_desc)) { ?>
	<div class="product-short-description">
		<?php /** @todo Test if content plugins modify the product description */
		echo $this->product->product_s_desc; ?>
	</div>
	<?php } // Product Short Description END ?>

	<div>


		<div class="width50 floatright">
			<div class="spacer-buy-area">

				<?php // TO DO in Multi-Vendor not needed at the moment and just would lead to confusion
				/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
				$text = JText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
				echo '<span class="bold">'. JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
				*/ ?>

				<?php
				$rating = empty($this->rating)? JText::_('COM_VIRTUEMART_UNRATED'):$this->rating->rating;
				echo JText::_('COM_VIRTUEMART_RATING') . $rating;

				// Product Price
				if ($this->show_prices) { ?>
				<div class="product-price" id="productPrice<?php echo $this->product->virtuemart_product_id ?>">
				<?php
				if ($this->product->product_unit && VmConfig::get ( 'price_show_packaging_pricelabel' )) {
					echo "<strong>" . JText::_ ( 'COM_VIRTUEMART_CART_PRICE_PER_UNIT' ) . ' (' . $this->product->product_unit . "):</strong>";
				} else {
					echo "<strong>" . JText::_ ( 'COM_VIRTUEMART_CART_PRICE' ) . "</strong>";
				}
				echo $this->currency->createPriceDiv ( 'variantModification', 'COM_VIRTUEMART_PRODUCT_VARIANT_MOD', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'basePriceWithTax', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'discountedPriceWithoutTax', 'COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'salesPriceWithDiscount', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'salesPrice', 'COM_VIRTUEMART_PRODUCT_SALESPRICE', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'priceWithoutTax', 'COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'discountAmount', 'COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT', $this->product->prices );
				echo $this->currency->createPriceDiv ( 'taxAmount', 'COM_VIRTUEMART_PRODUCT_TAX_AMOUNT', $this->product->prices ); ?>
				</div>
				<?php } ?>

				<?php // Availability Image
				/* TO DO add width and height to the image */
				if (!empty($this->product->product_availability)) { ?>
				<div class="availability">
					<?php echo JHTML::image(JURI::root().VmConfig::get('assets_general_path').'images/availability/'.$this->product->product_availability, $this->product->product_availability, array('class' => 'availability')); ?>
				</div>
				<?php } ?>

				<?php // Ask a question about this product
				$url = JRoute::_(juri::root().'index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id='.$this->product->virtuemart_product_id.'&virtuemart_category_id='.$this->product->virtuemart_category_id.'&tmpl=component'); ?>
				<div class="ask-a-question">
					<a class="ask-a-question modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $url ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
				</div>

				<?php // Manufacturer of the Product
				if(VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) { ?>
				<div class="manufacturer">
				<?php
					$link = JRoute::_(juri::root().'index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id='.$this->product->virtuemart_manufacturer_id.'&tmpl=component');
					$text = $this->product->mf_name;

					/* Avoid JavaScript on PDF Output */
					if (strtolower(JRequest::getWord('output')) == "pdf"){
						echo JHTML::_('link', $link, $text);
					} else { ?>
						<span class="bold"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') ?></span><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a>
				<?PHP } ?>
				</div>
				<?php } ?>

			</div>
		</div>
	<div class="clear"></div>
	</div>

	<?php // Product Description
	if (!empty($this->product->product_desc)) { ?>
	<div class="product-description">
		<?php /** @todo Test if content plugins modify the product description */ ?>
		<span class="title"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span>
		<?php echo $this->product->product_desc; ?>
	</div>
	<?php } // Product Description END ?>
	<?php // Product custom_fields TODO relation to Childs
	if (!empty($this->product->customfields)) { ?>
		<div class="product-fields">
		<?php
		$custom_title = null ;
		foreach ($this->product->customfields as $field){
			?><div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field_type ?>">
			<?php if ($field->custom_title != $custom_title) { ?>
				<span class="product-fields-title" ><strong><?php echo JText::_($field->custom_title); ?></strong></span>
				<?php //echo JHTML::tooltip($field->custom_tip, $field->custom_title, 'tooltip.png');
			} ?>
			<span class="product-field-display"><?php echo $field->display ?></span>
			<span class="product-field-desc"><?php echo jText::_($field->custom_field_desc) ?></span>
			</div>
			<?php
			$custom_title = $field->custom_title;
		} ?>
		</div>
		<?php
	} // Product custom_fields END ?>

	<?php // Product Packaging
	$product_packaging = '';
	if ($this->product->product_box) { ?>
	<div class="product-box">
		<?php
	        echo JText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
	    ?>
	</div>
	<?php } // Product Packaging END ?>

	<?php // Product Files
	// foreach ($this->product->images as $fkey => $file) {
		// Todo add downloadable files again
		// if( $file->filesize > 0.5) $filesize_display = ' ('. number_format($file->filesize, 2,',','.')." MB)";
		// else $filesize_display = ' ('. number_format($file->filesize*1024, 2,',','.')." KB)";

		/* Show pdf in a new Window, other file types will be offered as download */
		// $target = stristr($file->file_mimetype, "pdf") ? "_blank" : "_self";
		// $link = JRoute::_('index.php?view=productdetails&task=getfile&virtuemart_media_id='.$file->virtuemart_media_id.'&virtuemart_product_id='.$this->product->virtuemart_product_id);
		// echo JHTMl::_('link', $link, $file->file_title.$filesize_display, array('target' => $target));
	// }
	?>

	<?php // Related Products
/*	if ($this->product->related && !empty($this->product->related)) {
		$iRelatedCol = 1;
		$iRelatedProduct = 1;
		$RelatedProducts_per_row = 4 ;
		$Relatedcellwidth = ' width'.floor ( 100 / $RelatedProducts_per_row );
		$verticalseparator = " vertical-separator"; ?>

		<div class="related-products-view">
			<h4><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS_HEADING') ?></h4>

		<?php // Start the Output
		foreach ($this->product->related as $rkey => $related) {

			// Show the horizontal seperator
			if ($iRelatedCol == 1 && $iRelatedProduct > $RelatedProducts_per_row) { ?>
				<div class="horizontal-separator"></div>
			<?php }

			// this is an indicator wether a row needs to be opened or not
			if ($iRelatedCol == 1) { ?>
				<div class="row">
			<?php }

			// Show the vertical seperator
			if ($iRelatedProduct == $RelatedProducts_per_row or $iRelatedProduct % $RelatedProducts_per_row == 0) {
				$show_vertical_separator = ' ';
			} else {
				$show_vertical_separator = $verticalseparator;
			}

					// Show Products ?>
					<div class="product floatleft<?php echo $Relatedcellwidth . $show_vertical_separator ?>">
						<div class="spacer">
							<div>
								<h3><?php echo JHTML::_('link', $related->link, $related->product_name); ?></h3>

								<?php // Product Image
								echo JHTML::link($related->link, $related->images[0]->displayMediaThumb('title="'.$related->product_name.'"')); ?>

								<div class="product-price">
								<?php /** @todo Format pricing  ?>
								<?php if (is_array($related->price)) echo $related->price['salesPrice']; ?>
								</div>

								<div>
								<?php // Product Details Button
								echo JHTML::link($related->link, JText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $related->product_name, 'class' => 'product-details' ) ); ?>
								</div>
							</div>
						<div class="clear"></div>
						</div>
					</div>
			<?php
			$iRelatedProduct ++;

			// Do we need to close the current row now?
			if ($iRelatedCol == $RelatedProducts_per_row) { ?>
				<div class="clear"></div>
				</div>
			<?php
			$iRelatedCol = 1;
			} else {
				$iRelatedCol ++;
			}
		}
		// Do we need a final closing row tag?
		if ($iRelatedCol != 1) { ?>
			<div class="clear"></div>
			</div>
		<?php } ?>
		</div>
	<?php } */ ?>

	<?php // Customer Reviews
	if($this->allowRating || $this->showReview) {
		$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
		$ratingsShow = VmConfig::get('vm_num_ratings_show',3); // TODO add  vm_num_ratings_show in vmConfig
		$starsPath = JURI::root().VmConfig::get('assets_general_path').'images/stars/';
		$stars = array();
		$showall = JRequest::getBool('showall', false);
		for ($num=0 ; $num <= $maxrating; $num++  ) {
			$title = (JText::_("VM_RATING_TITLE").' : '. $num . '/' . $maxrating) ;
			$stars[] = JHTML::image($starsPath.$num.'.gif', JText::_($num.'_STARS'), array("title" => $title) );
		} ?>

	<div class="customer-reviews">
	<?php
	}

	if($this->showReview) {
		$alreadycommented = false;
		?>
		<h4><?php echo JText::_('COM_VIRTUEMART_REVIEWS') ?></h4>

		<div class="list-reviews">
			<?php
			$i=0;
			foreach($this->rating_reviews as $review ) {
				if ($i % 2 == 0) {
   					$color = 'normal';
				} else {
					$color = 'highlight';
				}

				/* Check if user already commented */
//				if ($review->virtuemart_userid == $this->user->id) {
//					$alreadycommented = true;
//				} ?>

				<?php // Loop through all reviews
				if (!empty($this->rating_reviews)) { ?>
				<div class="<?php echo $color ?>">
					<span class="date"><?php echo JHTML::date($review->created_on, JText::_('DATE_FORMAT_LC')); ?></span>
					<?php //echo $stars[ $review->review_rating ] //Attention the review rating is the rating of the review itself, rating for the product is the vote !?>
					<blockquote><?php echo $review->comment; ?></blockquote>
					<span class="bold"><?php echo $review->customer ?></span>
				</div>
				<?php
				}
				$i++ ;
				if ( $i == $ratingsShow && !$showall) break;
			}

			if (count($this->rating_reviews) < 1) {
				// "There are no reviews for this product" ?>
				<span class="step"><?php echo JText::_('COM_VIRTUEMART_NO_REVIEWS') ?></span>
			<?php
			} else {
				/* Show all reviews */
				if (!$showall && count($this->rating_reviews) >= $ratingsShow ) {
					$attribute = array('class'=>'details', 'title'=>JText::_('COM_VIRTUEMART_MORE_REVIEWS'));
					echo JHTML::link($this->more_reviews, JText::_('COM_VIRTUEMART_MORE_REVIEWS'),$attribute);
				}
			} ?>
		<div class="clear"></div>
		</div>

<?php
	}
//					} else {
//						echo '<strong>'.JText::_('COM_VIRTUEMART_DEAR').$this->user->name.',</strong><br />' ;
//						echo JText::_('COM_VIRTUEMART_REVIEW_ALREADYDONE');
//					}

	if($this->allowRating || $this->showReview) {
	?>
	</div>
	<?php
	}


	// else echo JText::_('COM_VIRTUEMART_REVIEW_LOGIN'); // Login to write a review!
	?>
</div>