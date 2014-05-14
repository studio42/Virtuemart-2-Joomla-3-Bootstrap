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
defined('_JEXEC') or die('Restricted access');
vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
vmJsApi::css('jquery.fancybox-1.3.4');
$document = JFactory::getDocument ();
$imageJS = '
jQuery(document).ready(function() {
	jQuery("a[rel=vm-additional-images]").fancybox({
		"titlePosition" 	: "inside",
		"transitionIn"	:	"elastic",
		"transitionOut"	:	"elastic"
	});
	jQuery(".additional-images a").click(function(e) {
		e.preventDefault();
		jQuery(".main-image img,.main-image a").attr("src",this.href );
		jQuery(".main-image img,.main-image a").attr("alt",this.title );
		return false;
	}); 
});
';
$document->addScriptDeclaration ($imageJS);

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
	$count_images = count ($this->product->images);
	if ($count_images > 1 ){//&& $this->document->_mime !== 'application/pdf') {
		?>

    <div class="additional-images thumbnails">
	<div class="row-fluid">
		<?php
		$col = 1;
		for ($i = 0; $i < $count_images; $i++) {
			$image = $this->product->images[$i];
			?>
            <div class="span4">
			<div class="thumbnail">
	            <?php
	                echo $image->displayMediaThumb('class="product-image" style="cursor: pointer"',true,"");
	            ?>
            </div>
            </div>
			<?php
			if($col === 3 && $i < $count_images) {
				$col=0;
				?>
				</div><div class="row-fluid">
				<?php }
			$col++;
		}
		?>
        <div class="clear"></div>
    </div>
    </div>
	<?php
	}
}
  // Showing The Additional Images END ?>