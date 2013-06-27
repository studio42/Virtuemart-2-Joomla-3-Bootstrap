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

				if ($this->showBasePrice) {
					echo $this->currency->createPriceDiv ( 'basePrice', 'COM_VIRTUEMART_PRODUCT_BASEPRICE', $this->product->prices );
					echo $this->currency->createPriceDiv ( 'basePriceVariant', 'COM_VIRTUEMART_PRODUCT_BASEPRICE_VARIANT', $this->product->prices );
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

				<?php // Add To Cart Button
				if (!VmConfig::get('use_as_catalog',0)) { ?>
				<div class="addtocart-area">

	<?php // Product custom_fields
	if (!empty($this->product->customfieldsCart)) {  ?>
	<div class="product-fields">
		<?php foreach ($this->product->customfieldsCart as $field)
		{ ?><div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field_type ?>">
			<span class="product-fields-title" ><strong><?php echo $field->custom_title ?></strong></span>
			<?php echo JHTML::tooltip($field->custom_tip, $field->custom_title, 'tooltip.png'); ?>
			<span class="product-field-display"><?php echo $field->display ?></span>

			<span class="product-field-desc"><?php echo $field->custom_field_desc ?></span>
			</div><br/ >
			<?php
		}
		?>
	</div>
	<?php }
	 /* Product custom Childs
	  * to display a simple link use $field->virtuemart_product_id as link to child product_id
	  * custom_value is relation value to child
	  */

	if (!empty($this->product->customsChilds)) {  ?>
		<div class="product-fields">
			<?php foreach ($this->product->customsChilds as $field) {  ?>
				<div style="display:inline-block;" class="product-field product-field-type-<?php echo $field->field->field_type ?>">
				<span class="product-fields-title" ><strong><?php echo $field->field->custom_title ?></strong></span>
				<span class="product-field-desc"><?php echo $field->field->custom_value ?></span>
				<span class="product-field-display"><?php echo $field->display ?></span>

				</div><br/ >
				<?php
			} ?>
		</div>
	<?php } ?>

				<div class="clear"></div>
				</div>
			<?php }  // Add To Cart Button END ?>

				<?php // Availability Image
				/* TO DO add width and height to the image */
				if (!empty($this->product->product_availability)) { ?>
				<div class="availability">
					<?php echo JHTML::image(JURI::root().VmConfig::get('assets_general_path').'images/availability/'.$this->product->product_availability, $this->product->product_availability, array('class' => 'availability')); ?>
				</div>
				<?php } ?>

				<?php // Ask a question about this product
				$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id='.$this->product->virtuemart_product_id.'&virtuemart_category_id='.$this->product->virtuemart_category_id.'&tmpl=component'); ?>
				<div class="ask-a-question">
					<a class="ask-a-question modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $url ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
				</div>

				<?php // Manufacturer of the Product
				if(VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) { ?>
				<div class="manufacturer">
				<?php
					$link = JRoute::_('index.php?option=com_virtuemart&view=manufacturer&virtuemart_manufacturer_id='.$this->product->virtuemart_manufacturer_id.'&tmpl=component');
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
	if ($this->product->packaging || $this->product->box) { ?>
	<div class="product-packaging">
		<span class="bold"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_PACKAGING2') ?></span>
		<br />
		<?php
		if ($this->product->packaging) {
			$product_packaging .= JText::_('COM_VIRTUEMART_PRODUCT_PACKAGING1').$this->product->packaging;
			if ($this->product->box) $product_packaging .= '<br />';
		}
		if ($this->product->box) $product_packaging .= JText::_('COM_VIRTUEMART_PRODUCT_PACKAGING2').$this->product->box;
		echo str_replace("{unit}",$this->product->product_unit ? $this->product->product_unit : JText::_('COM_VIRTUEMART_PRODUCT_FORM_UNIT_DEFAULT'), $product_packaging); ?>
	</div>
	<?php } 
	// Product Packaging END 

	// Customer Reviews
	if( $this->showReview ) {
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

	// Loop through all reviews
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

	if( $this->showReview ) {
	?>
	</div>
	<?php
	}
	?>
</div>