<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_images.php 6188 2012-06-29 09:38:30Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//Count images for slideshows 
$count_images = count ($this->product->images);
$imgagesPerRow = 3 ; // use here a divider of 12 : 1,2,3,4,6,12 only
if ($count_images > $imgagesPerRow) {
	$isSlide = true;
	
} else {
	$isSlide = false;
	
}
vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
vmJsApi::css('jquery.fancybox-1.3.4');
$document = JFactory::getDocument ();

$imageJS = '
jQuery(function($) {
	$("a[rel=vm-additional-images]").fancybox({
		"titlePosition" 	: "inside",
		"transitionIn"	:	"elastic",
		"transitionOut"	:	"elastic"
	});
	$(".additional-images a").click(function(e) {
		// e.preventDefault();
		var href = this.href ,
			main = jQuery(".main-image"),
			ext = href.substring(href.length-4),
			isImg ;
		ext = ext.toLowerCase();
		isImg = (ext ==".png" || ext ==".jpg" || ext =="jpeg" || ext ==".gif");
		if (isImg) {
			main.find("img").attr("src",this.href ).attr("alt",this.title );
			main.find("a").attr("href",this.href ).attr("alt",this.title );
			$(this).parent().addClass("active").siblings().removeClass("active");
			return false;
		}
	}); 

';
if ($isSlide) {
	vmJsApi::js( 'jquery.lightSlider.min');
	vmJsApi::css('lightSlider');
	$imageJS .='
		var $slideBar = $(".additional-images .row-fluid");
		var lightSlider =
			$slideBar.lightSlider({
				  minSlide:'.$imgagesPerRow.',
				  maxSlide:'.$imgagesPerRow.',
				  slideMove:'.$imgagesPerRow.',
				  pager:false,
				  gallery:true,
				  prevHtml:\'<span class="icon-previous"></span>\',
				  nextHtml:\'<span class="icon-next"></span>\',
				  onAfterSlide: function() {
					$slideBar.children(".active").children().trigger("click");
					// console.log($slideBar.children(".active"),this);
				  }
			});
	';

//todo find a better working horizontal slider( without tone of hacks)
// http://www.smoothdivscroll.com/mixedContentTouch.html
}

$document->addScriptDeclaration ($imageJS.'});');

if (!empty($this->product->images)) {
	$image = $this->product->images[0];
	?>
<div class="main-image">

	<?php
		echo $image->displayMediaFull("",true,"rel='vm-additional-images'");
	?>

	 <div class="clear"></div>
</div>
<?php

	if ($count_images > 1 ){//&& $this->document->_mime !== 'application/pdf') {
		$span = 12 / $imgagesPerRow ;
		?>

    <div class="additional-images thumbnails">
	<div class="row-fluid gallery">
		<?php
		$col = 1;
		for ($i = 0; $i < $count_images; $i++) {
			$image = $this->product->images[$i];
			?>
            <div class="span<?php echo $span ?> text-center">
	            <?php
	                echo $image->displayMediaThumb('class="product-image" style="cursor: pointer"',true,"");
	            ?>

            </div>
			<?php
 /* 			if($col === 3 && $i < $count_images) {
				$col=0;
				?>
				</div><div class="row-fluid">
				<?php }
			$col++; */
		}
		?>

    </div>
    </div>

	<?php
	}
}
  // Showing The Additional Images END ?>