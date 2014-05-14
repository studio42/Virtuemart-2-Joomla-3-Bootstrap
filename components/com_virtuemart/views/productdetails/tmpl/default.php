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
 * @version $Id: default.php 6530 2012-10-12 09:40:36Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
if (!isset($this->newlayout)){
	$this->newlayout = JRequest::getCmd( 'layout', 'default');
	if ($layout !='default') {
		$this->setLayout($this->newlayout);
		echo $this->loadTemplate();
	}
	// var_dump($this);
}
// addon for joomla modal Box
JHTML::_('behavior.modal');
// JHTML::_('behavior.tooltip');
if(VmConfig::get('usefancy',0)){
	vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
	vmJsApi::css('jquery.fancybox-1.3.4');
	$box = "$.fancybox({
				href: '" . $this->askquestion_url . "',
				type: 'iframe',
				height: '550'
			});";
} else {
	vmJsApi::js( 'facebox' );
	vmJsApi::css( 'facebox' );
	$box = "$.facebox({
				iframe: '" . $this->askquestion_url . "',
				rev: 'iframe|550|550'
			});";
}
$document = JFactory::getDocument();
$document->addScriptDeclaration("
//<![CDATA[
	jQuery(document).ready(function($) {
		$('a.ask-a-question').click( function(){
			".$box."
			return false ;
		});
	/*	$('.additional-images a').mouseover(function() {
			var himg = this.href ;
			var extension=himg.substring(himg.lastIndexOf('.')+1);
			if (extension =='png' || extension =='jpg' || extension =='gif') {
				$('.main-image img').attr('src',himg );
			}
			console.log(extension)
		});*/
	});
//]]>
");
/* Let's see if we found the product */
if (empty($this->product)) {
    echo JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
    echo '<br /><br />  ' . $this->continue_link_html;
    return;
}

?>

<div class="productdetails-view productdetails">

    <?php
	if ($this->document->_mime !== 'application/pdf') {
		// Product Navigation
		if (VmConfig::get('product_navigation', 1)) {
		?>
			<div class="product-neighbours">
			<?php
			if (!empty($this->product->neighbours ['previous'][0])) {
			$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
			echo JHTML::_('link', $prev_link, $this->product->neighbours ['previous'][0]
				['product_name'], array('class' => 'previous-page'));
			}
			if (!empty($this->product->neighbours ['next'][0])) {
			$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
			echo JHTML::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('class' => 'next-page'));
			}
			?>
			<div class="clear"></div>
			</div>
		<?php } // Product Navigation END
		?>

		<?php // Back To Category Button
		if ($this->product->virtuemart_category_id) {
			$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
			$categoryName = $this->product->category_name ;
		} else {
			$catURL =  JRoute::_('index.php?option=com_virtuemart');
			$categoryName = jText::_('COM_VIRTUEMART_SHOP_HOME') ;
		}
		?>
		<div class="back-to-category">
			<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo JText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
		</div>

		<?php 
	}
	// Product Title   ?>
    <h1><?php echo $this->product->product_name ?></h1>
    <?php // Product Title END   ?>

    <?php // afterDisplayTitle Event
    echo $this->product->event->afterDisplayTitle ?>

    <?php
	if ($this->document->_mime !== 'application/pdf') {
		// Product Edit Link
		echo $this->edit_link;
		// Product Edit Link END
		?>

		<?php
		// PDF - Print - Email Icon
		if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_button_enable')) {
		?>
			<div class="icons">
			<?php
			//$link = (JVM_VERSION===1) ? 'index2.php' : 'index.php';
			$link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;
			$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';

			if (VmConfig::get('pdf_icon', 1) == '1') {
			echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_button_enable', false);
			}
			echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
			echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend');
			?>
			<div class="clear"></div>
			</div>
		<?php } // PDF - Print - Email Icon END
	}
    if (!empty($this->product->customfieldsSorted['ontop'])) {
	$this->position = 'ontop';
	echo $this->loadTemplate('customfields');
    } // Product Custom ontop end
    ?>

  <div class="row-fluid">

	<div class="span4">
<?php
echo $this->loadTemplate('images');
?>
	</div>

    <div class="span8">
		<?php
		// Product Short Description
		if (!empty($this->product->product_s_desc)) {
		?>
			<div class="product-short-description">
			<?php
			/** @todo Test if content plugins modify the product description */
			echo nl2br($this->product->product_s_desc);
			?>
			</div>
		<?php
		} ?>
	    <div class="spacer-buy-area">

		<?php
		if ($this->showRating) {
		    $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);

		    if (empty($this->rating)) {
			?>
			<span class="vote"><?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . JText::_('COM_VIRTUEMART_UNRATED') ?></span>
			    <?php
			} else {
			    $ratingwidth = $this->rating->rating * 24; //I don't use round as percetntage with works perfect, as for me
			    ?>
			<span class="vote">
	<?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . round($this->rating->rating) . '/' . $maxrating; ?><br/>
			    <span title=" <?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE") . round($this->rating->rating) . '/' . $maxrating) ?>" class="ratingbox" style="display:inline-block;">
				<span class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>">
				</span>
			    </span>
			</span>
			<?php
		    }
		}
		if (is_array($this->productDisplayShipments)) {
		    foreach ($this->productDisplayShipments as $productDisplayShipment) {
			echo $productDisplayShipment . '<br />';
		    }
		}
		if (is_array($this->productDisplayPayments)) {
		    foreach ($this->productDisplayPayments as $productDisplayPayment) {
			echo $productDisplayPayment . '<br />';
		    }
		}
		// Product Price
		    // the test is done in show_prices
		//if ($this->show_prices and (empty($this->product->images[0]) or $this->product->images[0]->file_is_downloadable == 0)) {
		if (!empty($this->product->prices['costPrice']) && !empty($this->product->prices['salesPrice']) ){
		    echo $this->loadTemplate('showprices');
		}
		//}
		?>

		<?php
		// Add To Cart Button
// 			if (!empty($this->product->prices) and !empty($this->product->images[0]) and $this->product->images[0]->file_is_downloadable==0 ) {
//		if (!VmConfig::get('use_as_catalog', 0) and !empty($this->product->prices['salesPrice'])) {
		    echo $this->loadTemplate('addtocart');
//		}  // Add To Cart Button END
		?>

		<?php
		// Availability Image
		$stockhandle = VmConfig::get('stockhandle', 'none');
		if (($this->product->product_in_stock - $this->product->product_ordered) < 1) {
			if ($stockhandle == 'risetime' and VmConfig::get('rised_availability') and empty($this->product->product_availability)) {
			?>	<div class="availability">
			    <?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability'))) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . VmConfig::get('rised_availability', '7d.gif'), VmConfig::get('rised_availability', '7d.gif'), array('class' => 'availability')) : JText::_(VmConfig::get('rised_availability')); ?>
			</div>
		    <?php
			} else if (!empty($this->product->product_availability)) {
			?>
			<div class="availability">
			<?php echo (file_exists(JPATH_BASE . DS . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability)) ? JHTML::image(JURI::root() . VmConfig::get('assets_general_path') . 'images/availability/' . $this->product->product_availability, $this->product->product_availability, array('class' => 'availability')) : JText::_($this->product->product_availability); ?>
			</div>
			<?php
			}
		}

		// Ask a question about this product
		if (VmConfig::get('ask_question', 1) == 1) {
			?>
			<div class="ask-a-question">
				<a class="ask-a-question" href="<?php echo $this->askquestion_url ?>" ><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
				<!--<a class="ask-a-question modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $this->askquestion_url ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>-->
			</div>
			<?php }
		?>

		<?php

		if ($this->vendor) {
			$link = JRoute::_('index.php?option=com_virtuemart&view=vendor&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
			?>
			<div>
				<?php echo jText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL').' : 
				<a class="link" href="' . $link . '">'. $this->vendor->vendor_store_name .'</a>' ;
				// TODO add link to vendor product list ?
				?>
				<?php echo JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS',jText::_('JSHOW')); ?>
			</div>
			<?php
		}
		?>
		
	    </div>
	</div>
  </div>
<div class="clear"></div>


<?php // event onContentBeforeDisplay
echo $this->product->event->beforeDisplayContent; 
$active = ' class="active"';
?>

<ul class="nav nav-tabs" id="product-tabs">
	<?php if (!empty($this->product->product_desc)) { ?>
		<li class="active"><a href="#product-tab-desc" data-toggle="tab"><?php echo JText::_('JGLOBAL_DESCRIPTION') ?></a></li>
		<?php 
		$active = '';
	} if (!empty($this->product->customfieldsSorted['normal'])) { ?>
		<li <?php echo $active ?>><a href="#product-tab-customfield" data-toggle="tab"><?php echo JText::_('JDETAILS') ?></a></li>
		<?php
		$active = '';
	} if ($this->allowRating || $this->showReview) { ?>
		<li <?php echo $active ?>><a href="#product-tab-comment" data-toggle="tab"><?php echo JText::_('COM_VIRTUEMART_REVIEWS') ?></a></li>
		<?php
		$active = '';
	} if (!empty($this->product->customfieldsRelatedProducts)) { ?>
		<li <?php echo $active ?>><a href="#product-tab-RelatedProducts" data-toggle="tab"><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></a></li>
		<?php
		$active = '';
	} if (!empty($this->product->customfieldsRelatedCategories)) { ?>
		<li <?php echo $active ?>><a href="#product-tab-RelatedCategories" data-toggle="tab"><?php echo JText::_('COM_VIRTUEMART_RELATED_CATEGORIES'); ?></a></li>
<?php } ?>
</ul>
 
<div class="tab-content">
<?php $active = ' active';
	if (!empty($this->product->product_desc)) { ?>
		<div class="tab-pane active" id="product-tab-desc"><?php echo $this->product->product_desc; ?></div>
		<?php
		$active = '';
	} if (!empty($this->product->customfieldsSorted['normal'])) { ?>
		<div class="tab-pane<?php echo $active ?>" id="product-tab-customfield">
		<?php
		$active = '';
		$this->position = 'normal';
		echo $this->loadTemplate('customfields'); ?>
		</div>
	<?php } if ($this->allowRating || $this->showReview) { ?>
		<div class="tab-pane<?php echo $active ?>" id="product-tab-comment">
			<?php echo $this->loadTemplate('reviews'); ?>
		</div>
		<?php
		$active = '';
	} if (!empty($this->product->customfieldsRelatedProducts)) { ?>
		<div class="tab-pane<?php echo $active ?>" id="product-tab-RelatedProducts">
			<?php echo $this->loadTemplate('relatedproducts'); ?>
		</div>
		<?php
		$active = '';
	} if (!empty($this->product->customfieldsRelatedCategories)) { ?>
		<div class="tab-pane<?php echo $active ?>" id="product-tab-RelatedCategories">
			<?php echo $this->loadTemplate('relatedcategories'); ?>
		</div>
<?php } ?>
</div>
	<?php
    $product_packaging = '';
    if ($this->product->product_box) {
	?>
        <div class="product-box">
	    <?php
	        echo JText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
	    ?>
        </div>
    <?php } // Product Packaging END
    ?>

    <?php
    // Product Files
    // foreach ($this->product->images as $fkey => $file) {
    // Todo add downloadable files again
    // if( $file->filesize > 0.5) $filesize_display = ' ('. number_format($file->filesize, 2,',','.')." MB)";
    // else $filesize_display = ' ('. number_format($file->filesize*1024, 2,',','.')." KB)";

    /* Show pdf in a new Window, other file types will be offered as download */
    // $target = stristr($file->file_mimetype, "pdf") ? "_blank" : "_self";
    // $link = JRoute::_('index.php?view=productdetails&task=getfile&virtuemart_media_id='.$file->virtuemart_media_id.'&virtuemart_product_id='.$this->product->virtuemart_product_id);
    // echo JHTMl::_('link', $link, $file->file_title.$filesize_display, array('target' => $target));
    // }
/*     if (!empty($this->product->customfieldsRelatedProducts)) {
	echo $this->loadTemplate('relatedproducts');
    } // Product customfieldsRelatedProducts END

    if (!empty($this->product->customfieldsRelatedCategories)) {
	echo $this->loadTemplate('relatedcategories');
    } // Product customfieldsRelatedCategories END
 */    // Show child categories
    if (VmConfig::get('showCategory', 1)) {
	echo $this->loadTemplate('showcategory');
    }
    if (!empty($this->product->customfieldsSorted['onbot'])) {
    	$this->position='onbot';
    	echo $this->loadTemplate('customfields');
    } // Product Custom ontop end
    ?>

<?php // onContentAfterDisplay event
echo $this->product->event->afterDisplayContent; ?>


</div>
