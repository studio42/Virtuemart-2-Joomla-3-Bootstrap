<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage ShopperGroup
 * @author Markus �hler
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: edit.php 6386 2012-08-29 11:29:26Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
		$js = '
	jQuery(document).ready(function( $ ) {
			if ( $("#custom_price_display").is(\':checked\') ) {
				$("#show_hide_prices").show();
			} else {
				$("#show_hide_prices").hide();
			}
		 $("#custom_price_display").click(function() {
			if ( $("#custom_price_display").is(\':checked\') ) {
				$("#show_hide_prices").show();
			} else {
				$("#show_hide_prices").hide();
			}
		});
	});
	';

$document = JFactory::getDocument();
$document->addScriptDeclaration($js);
AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start', 'COM_VIRTUEMART_SHOPPERGROUP_NAME');
?>


<form action="index.php" method="post" name="adminForm" id="adminForm">

    <div class="col50">
	<fieldset>
	    <legend><?php echo JText::_('COM_VIRTUEMART_SHOPPERGROUP_DETAILS'); ?></legend>
	    <table class="admintable">

		<?php echo VmHTML::row('input', 'COM_VIRTUEMART_SHOPPERGROUP_NAME', 'shopper_group_name', $this->shoppergroup->shopper_group_name); ?>
		<?php echo VmHTML::row('booleanlist', 'COM_VIRTUEMART_PUBLISHED', 'published', $this->shoppergroup->published); ?>
		<?php /*
		  <tr>
		  <td width="110" class="key">
		  <label for="virtuemart_vendor_id">
		  <?php echo JText::_('COM_VIRTUEMART_VENDOR'); ?>
		  </label>
		  </td>
		  <td>
		  <?php echo $this->vendorList; ?>
		  </td>
		  </tr>
		 *
		 */
		?>
		<?php
		if ($this->shoppergroup->default == 1) {
		    ?>
    		<tr>
    		    <td width="110" class="key">
    			<label for="default">
    			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_SHOPPERGROUP_DEFAULT_TIP'); ?>">
				    <?php echo JText::_('COM_VIRTUEMART_SHOPPERGROUP_DEFAULT'); ?>
    			    </span>
    			</label>
    		    </td>
    		    <td>
    			<img src="templates/khepri/images/menu/icon-16-default.png" alt="<?php echo JText::_('Default'); ?>" />
    		    </td>
    		</tr>
		    <?php } ?>
		<?php echo VmHTML::row('textarea', 'COM_VIRTUEMART_SHOPPERGROUP_DESCRIPTION', 'shopper_group_desc', $this->shoppergroup->shopper_group_desc); ?>
	    </table>
	</fieldset>

	<fieldset>
	    <legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES') ?></legend>

	    <table class="admintable">
		<tr>
		    <td>
<?php echo JText::_('COM_VIRTUEMART_SHOPPERGROUP_ENABLE_PRICE_DISPLAY'); ?>
		    </td>
		    <td>
<?php
			     $attributes='';
			    echo VmHTML::checkbox('custom_price_display', $this->shoppergroup->custom_price_display,1,0,$attributes) ?>
		    </td>
		</tr>
		</table>
		<table class="admintable" id="show_hide_prices">
		<tr>
		    <td>
			<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_PRICES_EXPLAIN'); ?>">
<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_PRICES'); ?>
		    </td>
		    <td>
<?php echo VmHTML::checkbox('show_prices', $this->shoppergroup->price_display->get('show_prices')); ?>
		    </td>
		</tr>

		    <tr>
			<th></th>
			<th><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_LABEL'); ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_TEXT'); ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_ROUNDING'); ?></th>
		    </tr>
<?php
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'basePrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'variantModification', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_VARMOD');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'basePriceVariant', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_VAR');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'basePriceWithTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_WTAX');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'discountedPriceWithoutTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_DISCPRICE_WOTAX');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'salesPriceWithDiscount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WD');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'salesPrice', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'priceWithoutTax', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WOTAX');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'discountAmount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_DISC_AMOUNT');
echo ShopFunctions::writePriceConfigLine($this->shoppergroup->price_display, 'taxAmount', 'COM_VIRTUEMART_ADMIN_CFG_PRICE_TAX_AMOUNT');
?>
		</table>

	</fieldset>
    </div>

    <input type="hidden" name="default" value="<?php echo $this->shoppergroup->default ?>" />
    <input type="hidden" name="virtuemart_shoppergroup_id" value="<?php echo $this->shoppergroup->virtuemart_shoppergroup_id; ?>" />
<?php echo $this->addStandardHiddenToForm(); ?>

</form>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea();
?>