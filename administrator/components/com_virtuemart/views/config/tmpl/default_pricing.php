<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_pricing.php 6566 2012-10-19 16:33:47Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
		$js = '
	jQuery(document).ready(function( $ ) {
			if ( $("#show_prices").is(\':checked\') ) {
				$("#show_hide_prices").show();
			} else {
				$("#show_hide_prices").hide();
			}
		 $("#show_prices").click(function() {
			if ( $("#show_prices").is(\':checked\') ) {
				$("#show_hide_prices").show();
			} else {
				$("#show_hide_prices").hide();
			}
		});
	});
	';
$document = JFactory::getDocument();
$document->addScriptDeclaration($js);
?>
<br />
<table>
    <tr><td valign="top">

	    <fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_CONFIGURATION') ?></legend>
		<table class="admintable">
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_SHOW_TAX_TIP'); ?>">
			    <label for="show_tax"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_SHOW_TAX'); ?>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_tax', VmConfig::get('show_tax',1)); ?>
			</td>
		    </tr>
		   <tr>
            <td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_ASKPRICE_TIP'); ?>">
			    <label for="show_tax"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_ASKPRICE'); ?>
			    </span>
            </td>
            <td>
				<?php echo VmHTML::checkbox('askprice', VmConfig::get('askprice',0)); ?>
            </td>
        </tr>
            <tr>
                <td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_RAPPENRUNDUNG_TIP'); ?>">
			    <label for="show_tax"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_RAPPENRUNDUNG'); ?>
			    </span>
                </td>
                <td>
					<?php echo VmHTML::checkbox('rappenrundung', VmConfig::get('rappenrundung',0)); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_ROUNDINDIG_TIP'); ?>">
			    <label for="show_tax"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_ROUNDINDIG'); ?>
			    </span>
                </td>
                <td>
					<?php echo VmHTML::checkbox('roundindig', VmConfig::get('roundindig',FALSE)); ?>
                </td>
            </tr>
            <tr>
                <td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_CVARSWT_TIP'); ?>">
			    <label for="show_tax"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICE_CVARSWT'); ?>
			    </span>
                </td>
                <td>
					<?php echo VmHTML::checkbox('cVarswT', VmConfig::get('cVarswT',1)); ?>
                </td>
            </tr>
		</table>
	    </fieldset>

	</td><td valign="top">

	    <fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES') ?></legend>
		<table class="admintable">
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_PRICES_EXPLAIN'); ?>">
			    <label for="show_prices"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_PRICES') ?></label>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_prices', VmConfig::get('show_prices',1)); ?>
			</td>
			</tr>
			</table>
		    <table class="admintable" id="show_hide_prices">
			<tr>
				<th></th>
				<th><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_LABEL'); ?></th>
				<th><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_TEXT'); ?></th>
				<th><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRICES_ROUNDING'); ?></th>
			</tr>
			<?php

			echo ShopFunctions::writePriceConfigLine($this->config,'basePrice','COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE');
			echo ShopFunctions::writePriceConfigLine($this->config,'variantModification','COM_VIRTUEMART_ADMIN_CFG_PRICE_VARMOD');
			echo ShopFunctions::writePriceConfigLine($this->config,'basePriceVariant','COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_VAR');
			echo ShopFunctions::writePriceConfigLine($this->config,'discountedPriceWithoutTax','COM_VIRTUEMART_ADMIN_CFG_PRICE_DISCPRICE_WOTAX',0);
			echo ShopFunctions::writePriceConfigLine($this->config,'priceWithoutTax','COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WOTAX',0);
			echo ShopFunctions::writePriceConfigLine($this->config,'taxAmount','COM_VIRTUEMART_ADMIN_CFG_PRICE_TAX_AMOUNT',0);
			echo ShopFunctions::writePriceConfigLine($this->config,'basePriceWithTax','COM_VIRTUEMART_ADMIN_CFG_PRICE_BASEPRICE_WTAX');
			echo ShopFunctions::writePriceConfigLine($this->config,'salesPrice','COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE');
			echo ShopFunctions::writePriceConfigLine($this->config,'salesPriceWithDiscount','COM_VIRTUEMART_ADMIN_CFG_PRICE_SALESPRICE_WD');
			echo ShopFunctions::writePriceConfigLine($this->config,'discountAmount','COM_VIRTUEMART_ADMIN_CFG_PRICE_DISC_AMOUNT');
			echo ShopFunctions::writePriceConfigLine($this->config,'unitPrice','COM_VIRTUEMART_ADMIN_CFG_PRICE_UNITPRICE');
			?>
		</table>
	    </fieldset>
	</td></tr>
</table>