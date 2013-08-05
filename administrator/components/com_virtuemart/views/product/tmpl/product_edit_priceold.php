<?php
/**
 *
 * Main product information
 *
 * @package    VirtueMart
 * @subpackage Product
 * @author Max Milbers
 * @todo Price update calculations
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product_edit_price.php 6669 2012-11-14 12:16:55Z alatak $
 * http://www.seomoves.org/blog/web-design-development/dynotable-a-jquery-plugin-by-bob-tantlinger-2683/
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die(); ?>

<table class="table table-striped productPriceTable ">

    <tr class="row<?php echo $rowColor?> form-horizontal">
        <td>
			
				<label class="control-label hasTip" 
				title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICE_COST_TIP'); ?>"
				style="font-weight: bold;">
					<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICE_COST') ?>
            	</label>
				<input
					type="text"
					class="inputbox input-mini"
					name="product_price[]"
					size="5"
					style="text-align:right;"
					value="<?php echo $this->calculatedPrices['costPrice']; ?>"/>

            <input type="hidden"
                   name="virtuemart_product_price_id[]" class="virtuemart_product_price_id"
                   value="<?php echo  $this->tempProduct->virtuemart_product_price_id; ?>"/>
        </td>
        <td>
			<?php echo $this->lists['currencies']; ?>

        </td>
        <td>
			<span class="hasTip" style="font-weight: bold;"
              title="<?php echo JText::_ ('COM_VIRTUEMART_SHOPPER_FORM_GROUP_PRICE_TIP'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_GROUP') ?>
			</span>
		</td>
        <td>
			<?php echo $this->lists['shoppergroups'];  ?>
        </td>
    </tr>
	<?php $rowColor = 1 - $rowColor; ?>
    <tr class="row<?php echo $rowColor?> form-horizontal">
		<td>
		
			<label style="font-weight: bold;">
				
				<span
						class="hasTip"
						title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICE_BASE_TIP'); ?>">
					<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICE_BASE') ?>
				</span>
				<div class="input-append">
					<input
						type="text"
						readonly
						class="inputbox readonly input-mini"
						name="basePrice[]"
						size="5"
						value="<?php echo $this->calculatedPrices['basePrice']; ?>"/>
					<span class="add-on"><?php echo $this->vendor_currency;   ?></span>
				</div>
			</label>
		</div>
        </td>
		<?php /*    <td width="17%"><div style="text-align: right; font-weight: bold;">
							<?php echo JText::_('COM_VIRTUEMART_RATE_FORM_VAT_ID') ?></div>
                        </td> */ ?>
        <td>
			<?php echo $this->lists['taxrates']; ?><br/>
        </td>
        <td>
            <span class="hasTip" title="<?php echo JText::_ ('COM_VIRTUEMART_RULES_EFFECTING_TIP') ?>">
				<?php echo JText::_ ('COM_VIRTUEMART_TAX_EFFECTING')  ?>
	         </span>
        </td>
        <td>
				<?php echo $this->taxRules ?>
        </td>
     <tr>
	<?php $rowColor = 1 - $rowColor; ?>
    <tr class="row<?php echo $rowColor?> form-horizontal">
        <td>
		
			<label style="font-weight: bold;">
				<span
                        class="hasTip"
                        title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICE_FINAL_TIP'); ?>">
					<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICE_FINAL') ?>
				</span>
				<div class="input-append">
					<input type="text"
						class="input-mini"
						name="salesPrice[]"
						size="5"
						style="text-align:right;"
						value="<?php echo $this->calculatedPrices['salesPriceTemp']; ?>"/>
					<span class="add-on"><?php echo $this->vendor_currency;   ?></span>
				</div>
			</label>
        </td>
		<?php /*  <td width="17%"><div style="text-align: right; font-weight: bold;">
							<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_DISCOUNT_TYPE') ?></div>
                        </td>*/ ?>
        <td>
			<?php echo $this->lists['discounts']; ?> <br/>
        </td>
        <td class="key">
	                    <span class="hasTip" title="<?php echo JText::_ ('COM_VIRTUEMART_RULES_EFFECTING_TIP') ?>">
						<?php if (!empty($this->DBTaxRules)) {
		                    echo JText::_ ('COM_VIRTUEMART_RULES_EFFECTING') . '</span><br />' . $this->DBTaxRules . '<br />';

	                    }
		                    if (!empty($this->DATaxRules)) {
			                    echo JText::_ ('COM_VIRTUEMART_RULES_EFFECTING') . '<br />' . $this->DATaxRules;
		                    }

		                    // 						vmdebug('my rules',$this->DBTaxRules,$this->DATaxRules); echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_DISCOUNT_EFFECTING').$this->DBTaxRules;  ?>
						</span>
        </td>
        <td  nowrap>
			<?php echo  vmJsApi::jDate ($this->tempProduct->product_price_publish_up, 'product_price_publish_up[]'); ?>
			<?php echo  vmJsApi::jDate ($this->tempProduct->product_price_publish_down, 'product_price_publish_down[]'); ?>
        </td>
    </tr>

<?php $rowColor = 1 - $rowColor; ?>
	<tr class="row<?php echo $rowColor?>">
		<td class="key" colspan="2">
			<strong>
				<span class="hasTip" title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_CALCULATE_PRICE_FINAL_TIP'); ?>">
					<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_CALCULATE_PRICE_FINAL'); ?>
				</span>
			</strong>
			<label class="btn toggle-hiden btn-small"><i class="icon-unpublish"></i><input type="hidden" name="use_desired_price[]" value="0"/></label>
		</td>
        <td >
			<div class="input-append form-inline">
				<label
                        class="hasTip"
                        title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_DISCOUNT_OVERRIDE_TIP'); ?>">
					<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_DISCOUNT_OVERRIDE') ?>
				
				<input type="text" size="5" class="input-mini"
					   style="text-align:right;" name="product_override_price[]"
					   value="<?php echo $this->tempProduct->product_override_price ?>"/>
				</label>
				<span class="add-on"><?php echo $this->vendor_currency;   ?></span>
			</div>
				<?php
					$options = array(0 => JText::_ ('COM_VIRTUEMART_DISABLED'), 1 => JText::_ ('COM_VIRTUEMART_OVERWRITE_FINAL'), -1 => JText::_ ('COM_VIRTUEMART_OVERWRITE_PRICE_TAX'));

					echo JHTML::_ ('Select.genericlist', $options, 'override[]','', 'value','text',$this->tempProduct->override,'');
				?>
			
        </td>
        <td>
            <div style="font-weight: bold;">
				<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_PRICE_QUANTITY_RANGE') ?>
            </div>
            <input type="text" size="5" class="input-mini"
                   style="text-align:right;" name="price_quantity_start[]"
                   value="<?php echo $this->tempProduct->price_quantity_start ?>"/>

            <input type="text" size="5" class="input-mini"
                   style="text-align:right;" name="price_quantity_end[]"
                   value="<?php echo $this->tempProduct->price_quantity_end  ?>"/>
        </td>
    </tr>
</table>



