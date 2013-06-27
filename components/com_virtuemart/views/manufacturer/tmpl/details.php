<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer
* @author Kohl Patrick, Eugen Stranz
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 2701 2011-02-11 15:16:49Z impleri $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<div class="manufacturer-details-view">
	<h1><?php echo $this->manufacturer->mf_name; ?></h1>

	<div class="spacer">

	<?php // Manufacturer Image
	if (!empty($this->manufacturerImage)) { ?>
		<div class="manufacturer-image">
		<?php echo $this->manufacturerImage; ?>
		</div>
	<?php } ?>

	<?php // Manufacturer Email
	if(!empty($this->manufacturer->mf_email)) { ?>
		<div class="manufacturer-email">
		<?php // TO DO Make The Email Visible Within The Lightbox
		echo JHtml::_('email.cloak', $this->manufacturer->mf_email,true,JText::_('COM_VIRTUEMART_EMAIL'),false) ?>
		</div>
	<?php } ?>

	<?php // Manufacturer URL
	if(!empty($this->manufacturer->mf_url)) { ?>
		<div class="manufacturer-url">
			<a target="_blank" href="<?php echo $this->manufacturer->mf_url ?>"><?php echo JText::_('COM_VIRTUEMART_MANUFACTURER_PAGE') ?></a>
		</div>
	<?php } ?>

	<?php // Manufacturer Description
	if(!empty($this->manufacturer->mf_desc)) { ?>
		<div class="manufacturer-description">
			<?php echo $this->manufacturer->mf_desc ?>
		</div>
	<?php } ?>

	<?php // Manufacturer Product Link
	$manufacturerProductsURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_manufacturer_id=' . $this->manufacturer->virtuemart_manufacturer_id);

	if(!empty($this->manufacturer->virtuemart_manufacturer_id)) { ?>
		<div class="manufacturer-product-link">
			<a target="_top" href="<?php echo $manufacturerProductsURL; ?>"><?php echo JText::sprintf('COM_VIRTUEMART_PRODUCT_FROM_MF',$this->manufacturer->mf_name); ?></a>
		</div>
	<?php } ?>

	<div class="clear"></div>
	</div>
</div>