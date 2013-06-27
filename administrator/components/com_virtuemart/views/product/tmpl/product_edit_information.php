<?php
/**
 *
 * Main product information
 *
 * @package	VirtueMart
 * @subpackage Product
 * @author RolandD
 * @todo Price update calculations
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product_edit_information.php 6547 2012-10-16 10:55:06Z alatak $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
<?php echo $this->langList;
$i=0;
?>


<fieldset>
	<legend>
	<?php echo JText::_('COM_VIRTUEMART_PRODUCT_INFORMATION'); echo ' id: '.$this->product->virtuemart_product_id ?></legend>
    <table width="100%">
	    <tr>
        <td width="50%">
			<table width="100%" class="adminform">
				<tr class="row<?php echo $i?>">
					<td ><div style="text-align: right; font-weight: bold;">
					<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PUBLISH') ?></div>
					</td>
					<td >
						<?php echo  VmHTML::checkbox('published', $this->product->published); ?>
					</td>
                    <td ><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_SPECIAL') ?></div>
                    </td>
                    <td >
						<?php echo VmHTML::checkbox('product_special', $this->product->product_special); ?>
                    </td>
				</tr>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i?>">
					<td  >
						<div style="text-align:right;font-weight:bold;"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_SKU') ?></div>
					</td>
					<td  height="2" colspan="3" >
						<input type="text" class="inputbox" name="product_sku" id="product_sku" value="<?php echo $this->product->product_sku; ?>" size="32" maxlength="64" />
					</td>
				</tr>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i?>">
					<td  height="18"><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_NAME') ?></div>
					</td>
					<td height="18" colspan="3" >
						<input type="text" class="inputbox"  name="product_name" id="product_name" value="<?php echo htmlspecialchars($this->product->product_name); ?>" size="32" maxlength="255" />
					</td>
				</tr>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i?>">
					<td " height="18"><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_ALIAS') ?></div>
					</td>
					<td  height="18" colspan="3" >
						<input type="text" class="inputbox"  name="slug" id="slug" value="<?php echo $this->product->slug; ?>" size="32" maxlength="255" />
					</td>
				</tr>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i?>">
					<td ><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_URL') ?></div>
					</td>
					<td colspan="3">
						<input type="text" class="inputbox" name="product_url" value="<?php echo $this->product->product_url; ?>" size="32" maxlength="255" />
					</td>
				</tr>
            </table>
    </td>
	<td>
        <table width="100%" class="adminform">
						<?php $i = 1 - $i; ?>
			<?php	if(Vmconfig::get('multix','none')!=='none'){ ?>
				<tr class="row<?php echo $i?>">
					<td ><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_VENDOR') ?></div>
					</td>
				<td  colspan="3">
					<?php echo $this->lists['vendors'];?>
				</td>
				</tr>
				<?php $i = 1 - $i; ?>
				<?php } ?>


				<?php if(isset($this->lists['manufacturers'])){?>
				<tr class="row<?php echo $i?>">
					<td ><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_MANUFACTURER') ?></div>
					</td>
					<td colspan="3" >
						<?php echo $this->lists['manufacturers'];?>
					</td>
				</tr>
				<?php $i = 1 - $i; ?>
				<?php }?>
				<tr class="row<?php echo $i?>">
					<td  valign="top">
						<div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_CATEGORY_S') ?></div>
					</td>
					<td colspan="3">
						<select class="inputbox" id="categories" name="categories[]" multiple="multiple" size="10">
							<option value=""><?php echo JText::_('COM_VIRTUEMART_UNCATEGORIZED')  ?></option>
							<?php echo $this->category_tree; ?>
						</select>
					</td>
					<?php
					// It is important to have all product information in the form, since we do not preload the parent
					// I place the ordering here, maybe we make it editable later.
						if(!isset($this->product->ordering)) $this->product->ordering = 0;
					?>
					<input type="hidden" value="<?php echo $this->product->ordering ?>" name="ordering">
				</tr>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i?>">
					<td><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_GROUP') ?></div>
					</td>
					<td  colspan="3">
						<?php echo $this->shoppergroupList; ?>
					</td>
				</tr>
				<?php $i = 1 - $i; ?>
				<tr class="row<?php echo $i?>">
					<td><div style="text-align:right;font-weight:bold;">
						<?php echo JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_PAGE') ?></div>
					</td>
					<td colspan="3">
						<?php echo JHTML::_('Select.genericlist', $this->productLayouts, 'layout', 'size=1', 'value', 'text', $this->product->layout); ?>
					</td>
				</tr>
            </table>
		</td>
        </tr>
	</table>
</fieldset>

		<td valign="top">
			<!-- Product pricing -->
          <fieldset>
    <legend><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICES'); ?></legend>

	<?php
	//$product = $this->product;

	if (empty($this->product->prices)) {
		$this->product->prices[] = array();
	}
	$this->i = 0;
	$rowColor = 0;
	if (!class_exists ('calculationHelper')) {
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
	}
	$calculator = calculationHelper::getInstance ();
	$currency_model = VmModel::getModel ('currency');
	$currencies = $currency_model->getCurrencies ();
	$nbPrice = count ($this->product->prices);
	$this->priceCounter = 0;
	$this->product->prices[$nbPrice] = $this->product_empty_price;



	if (!class_exists ('calculationHelper')) {
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
	}
	$calculator = calculationHelper::getInstance ();
	?>
    <table border="0" width="100%" cellpadding="2" cellspacing="3" id="mainPriceTable" class="adminform">
        <tbody id="productPriceBody">
		<?php
		//vmdebug('grummel ',$this->product->prices);
		foreach ($this->product->prices as $sPrices) {

			if(count($sPrices) == 0) continue;
			if (empty($sPrices['virtuemart_product_price_id'])) {
				$sPrices['virtuemart_product_price_id'] = '';
			}
			//vmdebug('my $sPrices ',$sPrices);
			$sPrices = (array)$sPrices;
			$this->tempProduct = (object)array_merge ((array)$this->product, $sPrices);
			$this->calculatedPrices = $calculator->getProductPrices ($this->tempProduct);

			if((string)$sPrices['product_price']==='0' or (string)$sPrices['product_price']===''){
				$this->calculatedPrices['costPrice'] = '';
			}

			$currency_model = VmModel::getModel ('currency');
			$this->lists['currencies'] = JHTML::_ ('select.genericlist', $currencies, 'mprices[product_currency][' . $this->priceCounter . ']', '', 'virtuemart_currency_id', 'currency_name', $this->tempProduct->product_currency);

			$DBTax = ''; //JText::_('COM_VIRTUEMART_RULES_EFFECTING') ;
			foreach ($calculator->rules['DBTax'] as $rule) {
				$DBTax .= $rule['calc_name'] . '<br />';
			}
			$this->DBTaxRules = $DBTax;

			$tax = ''; //JText::_('COM_VIRTUEMART_TAX_EFFECTING').'<br />';
			foreach ($calculator->rules['Tax'] as $rule) {
				$tax .= $rule['calc_name'] . '<br />';
			}
			foreach ($calculator->rules['VatTax'] as $rule) {
				$tax .= $rule['calc_name'] . '<br />';
			}
			$this->taxRules = $tax;

			$DATax = ''; //JText::_('COM_VIRTUEMART_RULES_EFFECTING');
			foreach ($calculator->rules['DATax'] as $rule) {
				$DATax .= $rule['calc_name'] . '<br />';
			}
			$this->DATaxRules = $DATax;

			if (!isset($this->tempProduct->product_tax_id)) {
				$this->tempProduct->product_tax_id = 0;
			}
			$this->lists['taxrates'] = ShopFunctions::renderTaxList ($this->tempProduct->product_tax_id, 'mprices[product_tax_id][' . $this->priceCounter . ']');
			if (!isset($this->tempProduct->product_discount_id)) {
				$this->tempProduct->product_discount_id = 0;
			}
			$this->lists['discounts'] = $this->renderDiscountList ($this->tempProduct->product_discount_id, 'mprices[product_discount_id][' . $this->priceCounter . ']');

			$this->lists['shoppergroups'] = ShopFunctions::renderShopperGroupList ($this->tempProduct->virtuemart_shoppergroup_id, false, 'mprices[virtuemart_shoppergroup_id][' . $this->priceCounter . ']');

			if ($this->priceCounter == $nbPrice) {
				$tmpl = "productPriceRowTmpl";
			} else {
				$tmpl = "productPriceRowTmpl_" . $this->priceCounter;
			}

			?>
        <tr id="<?php echo $tmpl ?>" class="removable row<?php echo $rowColor?>">
	            <td width="100%">
		        <span class="vmicon vmicon-16-move price_ordering"></span>
		        <?php /* <span class="vmicon vmicon-16-new price-clone" ></span> */ ?>
                <span class="vmicon vmicon-16-remove price-remove"></span>
				<?php //echo JText::_ ('COM_VIRTUEMART_PRODUCT_PRICE_ORDER');
				echo $this->loadTemplate ('price'); ?>
			 </td>
        </tr>
			<?php
			$this->priceCounter++;
		}
		?>
        </tbody>
    </table>
    <div class="button2-left">
        <div class="blank">
            <a href="#" id="add_new_price" "><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_ADD_PRICE') ?> </a>
        </div>
    </div>

</fieldset>
</tr>
		</td>
	</tr>
	<tr>
	<td colspan="2" >
	<fieldset>
		<legend>
		<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_CHILD_PARENT'); ?></legend>
		<table class="adminform">
			<tr class="row<?php echo $i?>">
				<td width="50%">
				<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=product&task=createVariant&virtuemart_product_id='.$this->product->virtuemart_product_id.'&token='.JUtility::getToken() ); ?>

						<div class="button2-left">
							<div class="blank">
								<a href="<?php echo $link ?>">
								<?php echo Jtext::_('COM_VIRTUEMART_PRODUCT_ADD_CHILD'); ?>
								</a>
							</div>
						</div>
				</td>

				<td width="29%"><div style="text-align:right; font-weight: bold;">
					<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PARENT') ?>
				</td>
				<td width="71%"> <?php
				if ($this->product->product_parent_id) {


					$result = JText::_('COM_VIRTUEMART_EDIT').' ' . $this->product_parent->product_name;
					echo ' | '.JHTML::_('link', JRoute::_('index.php?view=product&task=edit&virtuemart_product_id='.$this->product->product_parent_id
						.'&option=com_virtuemart'), $this->product_parent->product_name, array('title' => $result)).' | '.$this->parentRelation;
				}
				?>
				</td>

			</tr>

			<?php $i = 1 - $i; ?>

			<tr class="row<?php echo $i?>" >
				<td width="79%" colspan = "3"><?php
                if (count($this->product_childs)>0 ) {

                	$customs = array();
                	if(!empty($this->product->customfields)){
                		foreach($this->product->customfields as $custom){
                			//vmdebug('my custom',$custom);
                			if($custom->field_type=='A'){
                				$customs[] = $custom;
                			}
                		}
                	}

//					vmdebug('ma $customs',$customs);
					?>

					<table class="adminlist">
						<tr>
							<th><?php echo JText::_('COM_VIRTUEMART_PRODUCT_CHILD') ?></th>
							<th><?php echo JText::_('COM_VIRTUEMART_PRODUCT_CHILD_NAME')?></th>
							<th><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_COST')?></th>
							<th><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_IN_STOCK')?></th>
							<th width="5%"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_ORDERED_STOCK')?></th>
							<?php foreach($customs as $custom){ ?>
								<th><?php echo JText::sprintf('COM_VIRTUEMART_PRODUCT_CUSTOM_FIELD_N',JText::_('COM_VIRTUEMART_'.$custom->custom_value))?></th>
							<?php } ?>
							<th><?php echo JText::_('COM_VIRTUEMART_ORDERING')?></th>
							<th><?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PUBLISH')?></th>
						</tr>
						<?php
						foreach ($this->product_childs as $child  ) {
							$i = 1 - $i;
							 ?>
							<tr class="row<?php echo $i ?>">
								<td><?php echo JHTML::_('link', JRoute::_('index.php?view=product&task=edit&product_parent_id='.$this->product->virtuemart_product_id.'&virtuemart_product_id='.$child->virtuemart_product_id.'&option=com_virtuemart'), $child->slug, array('title' => JText::_('COM_VIRTUEMART_EDIT').' '.$child->product_name)) ?></td>
								<td><input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][product_name]" size="32" value="<?php echo $child->product_name ?>" /></td>
								<td><input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][mprices][product_price][]" size="10" value="<?php echo $child->product_price ?>" /><input type="hidden" name="childs[<?php echo $child->virtuemart_product_id ?>][mprices][virtuemart_product_price_id][]" value="<?php echo $child->virtuemart_product_price_id?>"  ></td>
								<td><?php echo $child->product_in_stock ?></td>
								<td><?php echo $child->product_ordered ?></td>
								<?php foreach($customs as $custom){
									$attrib = $custom->custom_value;
									if(isset($child->$attrib)){
										$childAttrib = $child->$attrib;
									} else {
										vmdebug('unset? use Fallback product_name instead $attrib '.$attrib,$custom);
										$attrib = 'product_name';
										$childAttrib = $child->$attrib;

									}
									?>
									<td><input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][<?php echo $attrib ?>]" size="20" value="<?php echo $childAttrib ?>" /></td>
									<?php
								}
								?>
								<td>
                                    <input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][pordering]" size="2" value="<?php echo $child->pordering ?>" /></td>
								</td>
								<td>
									<?php echo VmHTML::checkbox('childs['.$child->virtuemart_product_id.'][published]', $child->published) ?></td>
							</tr>
							<?php
						} ?>
						</table>
					 <?php
					 }
					 ?>
				</td>
			</tr>
		</table>
	</fieldset>
	</tr>

	<tr>
		<td
			width="100%"
			valign="top"
			colspan="2">
			<fieldset>
				<legend>
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_PRINT_INTNOTES'); ?></legend>
				<textarea style="width: 100%;" class="inputbox" name="intnotes" id="intnotes" cols="35" rows="6">
					<?php echo $this->product->intnotes; ?></textarea>
			</fieldset>
		</td>
	</tr>

</table>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#mainPriceTable").dynoTable({
            removeClass:'.price-remove', //remove class name in  table
            cloneClass:'.price-clone', //Custom cloner class name in  table
            addRowTemplateId:'#productPriceRowTmpl', //Custom id for  row template
            addRowButtonId:'#add_new_price', //Click this to add a price
            lastRowRemovable:true, //let the table be empty.
            orderable:true, //prices can be rearranged
            dragHandleClass:".price_ordering", //class for the click and draggable drag handle
            onRowRemove:function () {
            },
            onRowClone:function () {
                //var myLastRow = jQuery('input [name="mprices[virtuemart_product_price_id][]"]',jQuery('#mainPriceTable tr:last') );
                //var myLastRow = jQuery('#mainPriceTable tr:last' );
	            /*jQuery.each(myLastRow,function(key, element1) {
                           jQuery.each(element1,function(key, element) {
                                alert('key: ' + key + '\n' + 'value: ' + element);
                           })
                });*/
            },
            onRowAdd:function () {
            },
            onTableEmpty:function () {
            },
            onRowReorder:function () {
            }
        });
    });

</script>

<script type="text/javascript">
var tax_rates = new Array();
<?php
if( property_exists($this, 'taxrates') && is_array( $this->taxrates )) {
	foreach( $this->taxrates as $key => $tax_rate ) {
		echo 'tax_rates["'.$tax_rate->tax_rate_id.'"] = '.$tax_rate->tax_rate."\n";
	}
}
?>

</script>


