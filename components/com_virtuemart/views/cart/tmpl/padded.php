<?php
/**
*
* Layout for the add to cart popup
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2013 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

echo '<a class="continue" href="' . $this->continue_link . '" >' . JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
echo '<a class="showcart floatright" href="' . $this->cart_link . '">' . JText::_('COM_VIRTUEMART_CART_SHOW') . '</a>';
if($this->product){
	echo '<h4>'.JText::sprintf('COM_VIRTUEMART_CART_PRODUCT_ADDED',$this->product->product_name,$this->product->quantity).'</h4>';
}

if ($this->errorMsg) echo '<div>'.$this->errorMsg.'</div>';

if(VmConfig::get('popup_rel',1)){
	if($this->product and !empty($this->product->customfieldsRelatedProducts)){
		?>
		<div class="product-related-products">
				<h4><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h4>
		<?php
		foreach ($this->product->customfieldsRelatedProducts as $field) {
			if(!empty($field->display)) {
				?><div class="product-field product-field-type-<?php echo $field->field_type ?>">
				<span class="product-field-display"><?php echo $field->display ?></span>
				</div>
			<?php }
		} ?>
		</div>
	<?php
	}
}

?><br style="clear:both">
