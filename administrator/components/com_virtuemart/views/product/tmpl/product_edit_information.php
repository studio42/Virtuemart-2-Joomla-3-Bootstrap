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
defined('_JEXEC') or die();
$i=0;
?>


<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_PRODUCT_INFORMATION').( $this->product->virtuemart_product_id ? ' id: '.$this->product->virtuemart_product_id : '') ?>
		<div class="pull-right"><?php echo $this->langList; ?></div>
	</legend>
    <div class="row-fluid"> 
		<table class="span6">
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PRODUCT_FORM_PUBLISH','published',$this->product->published); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_PRODUCT_FORM_SKU','product_sku',$this->product->product_sku); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_PRODUCT_FORM_NAME','product_name',$this->product->product_name); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_PRODUCT_FORM_ALIAS','slug',$this->product->slug); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_PRODUCT_FORM_URL','product_url',$this->product->product_url); ?>
		</table>
		<table class="span6">
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PRODUCT_FORM_SPECIAL','product_special',$this->product->product_special); ?>

			<?php	if(Vmconfig::get('multix','none')!=='none'){ ?>
				<?php echo VmHTML::row('raw','COM_VIRTUEMART_VENDOR', $this->lists['vendors'] ); ?>
			<?php } 
			if (isset($this->lists['manufacturers'])) { ?>
				<?php echo VmHTML::row('raw','COM_VIRTUEMART_MANUFACTURER', $this->lists['manufacturers'] ); ?>
			<?php }?>
			<?php echo VmHTML::row('raw', 'COM_VIRTUEMART_CATEGORY_S',
				'<select class="inputbox span12" id="categories" name="categories[]" multiple="multiple" size="10">
						<option value="">'. JText::_('COM_VIRTUEMART_UNCATEGORIZED') .'</option>
						'.$this->category_tree.'
					</select>' ); ?>

			<?php echo VmHTML::row('raw','COM_VIRTUEMART_SHOPPER_FORM_GROUP', $this->shoppergroupList ); ?>
			<?php echo VmHTML::row('raw','COM_VIRTUEMART_PRODUCT_DETAILS_PAGE', $this->productLayouts) ?>

		</table>
		<?php
		// It is important to have all product information in the form, since we do not preload the parent
		// I place the ordering here, maybe we make it editable later.
		// TODO : NOTE STUDIO42 must be fixed, ordering is by category and multiple, this trick can never work
		if(!isset($this->product->ordering)) $this->product->ordering = 0; {
		?>
		<input type="hidden" value="<?php echo $this->product->ordering ?>" name="ordering">
		<?php } ?>
	</div>
</fieldset>

<?php if (VmConfig::get('show_product_prices_tab',1) ) { ?>
<!-- Product pricing -->
  <fieldset>
    <legend><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_FORM_PRICES'); ?></legend>
	<div id="pricesort" data-lastRowUnremovable="true">
	<?php
	//$product = $this->product;

	if (empty($this->product->prices)) {
		$this->product->prices[] = $this->product_empty_price;
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
	// $this->product->prices[$nbPrice] = $this->product_empty_price;



	if (!class_exists ('calculationHelper')) {
		require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
	}
	$calculator = calculationHelper::getInstance ();

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
		$this->lists['currencies'] = JHTML::_ ('select.genericlist', $currencies, 'product_currency[]', '', 'virtuemart_currency_id', 'currency_name', $this->tempProduct->product_currency,'product_currency'.$sPrices['virtuemart_product_price_id']);

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
		$this->lists['taxrates'] = ShopFunctions::renderTaxList ($this->tempProduct->product_tax_id, 'product_tax_id[]','','product_tax_id'.$sPrices['virtuemart_product_price_id']);
		if (!isset($this->tempProduct->product_discount_id)) {
			$this->tempProduct->product_discount_id = 0;
		}
		$this->lists['discounts'] = $this->renderDiscountList ($this->tempProduct->product_discount_id, 'product_discount_id[]', 'product_discount_id'.$sPrices['virtuemart_product_price_id']);

		$this->lists['shoppergroups'] = ShopFunctions::renderShopperGroupList ($this->tempProduct->virtuemart_shoppergroup_id, false, 'price_shoppergroup_id[]','price_shoppergroup_id'.$sPrices['virtuemart_product_price_id']);
		?>
		<div class="price-container removable">
				<span class="icon-move price_ordering label"> </span> <span class="icon-remove price-remove  label pull-right"> </span>
			<?php //echo JText::_ ('COM_VIRTUEMART_PRODUCT_PRICE_ORDER');
			echo $this->loadTemplate ('price'); ?>
		</div>
		<?php
			$this->priceCounter++;
		}
		?>
	</div>
    <div class="button2-left">
        <div class="blank">
            <a href="#" id="add_new_price" class="btn"><i class="icon icon-new"></i> <?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_ADD_PRICE') ?> </a>
        </div>
    </div>

  </fieldset>
<?php } 
if (VmConfig::get('show_product_child_tab',1) ) { ?>
	<fieldset>
		<legend>
		<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_CHILD_PARENT'); ?></legend>
		<table class="adminform">
			<tr class="row<?php echo $i?>">
				<td width="50%">
				<?php 
				// $link=JROUTE::_('index.php?option=com_virtuemart&view=product&task=createVariant&virtuemart_product_id='.$this->product->virtuemart_product_id.'&'.JSession::getFormToken().'=1' ); 
				$link = $this->editLink($this->product->virtuemart_product_id,'<i class="icon icon-new"></i> '.jText::_('COM_VIRTUEMART_PRODUCT_ADD_CHILD'),null,'class="btn"', null,'createVariant&'.JSession::getFormToken().'=1');
				?>

						<div class="button2-left">
							<div class="blank">
								<?php echo $link ?>
							</div>
						</div>
				</td>

				<td width="29%"><div style="text-align:right; font-weight: bold;">
					<?php echo JText::_('COM_VIRTUEMART_PRODUCT_FORM_PARENT') ?>
					</div>
				</td>
				<td width="71%"> <?php
				if ($this->product->product_parent_id) {


					$result = JText::_('COM_VIRTUEMART_EDIT').' ' . $this->product_parent->product_name;
					echo ' | '.$this->editLink($this->product->product_parent_id
						, $this->product_parent->product_name, null, array('title' => $result)).' | '.$this->parentRelation;
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

					<table class="table table-striped">
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
							$editBtn = '<i class="icon icon-edit"></i> '.$child->slug;
							 ?>
							<tr>
								<td><?php echo $this->editLink($child->virtuemart_product_id.'&product_parent_id='.$this->product->virtuemart_product_id, $editBtn, null, array('title' => JText::_('COM_VIRTUEMART_EDIT').' '.$child->product_name, 'class' =>'btn')) ?></td>
								<td><input type="text" class="inputbox input-block-level" name="childs[<?php echo $child->virtuemart_product_id ?>][product_name]" size="32" value="<?php echo $child->product_name ?>" /></td>
								<td><input type="text" class="inputbox input-mini" name="childs[<?php echo $child->virtuemart_product_id ?>][product_price][]" size="10" value="<?php echo $child->product_price ?>" /><input type="hidden" name="childs[<?php echo $child->virtuemart_product_id ?>][virtuemart_product_price_id][]" value="<?php echo $child->virtuemart_product_price_id?>"  ></td>
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
                                    <input type="text" class="inputbox" style="width:30px"name="childs[<?php echo $child->virtuemart_product_id ?>][pordering]" size="2" value="<?php echo $child->pordering ?>" /></td>
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
<?php } ?>
	<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_PRODUCT_PRINT_INTNOTES'); ?></legend>
			<textarea style="width: 100%;" class="inputbox input-block-level" name="intnotes" id="intnotes" cols="35"><?php echo $this->product->intnotes; ?></textarea>
	</fieldset>

<?php if (VmConfig::get('show_product_prices_tab',1) ) { ?>
<script type="text/javascript">
    jQuery( function ($) {
		$('#add_new_price').click(function() {
			var div = $('.price-container').last().clone(true);
			div.find('.chzn-done').removeClass("chzn-done").removeAttr("id").next().remove();
			div.find('.virtuemart_product_price_id').val('');
			div.find('input.datepicker').removeClass('hasDatepicker').removeData('datepicker').unbind();
			div.appendTo('#pricesort');
			div.find('select').chosen();
			return false;
		});
		// icon exit but order is not saved in price table
		$('#pricesort').sortable({handle: ".icon-move"});
		// little hidden input switcher
		$('.productPriceTable .toggle-hiden').click(function() {
			var clicked = $(this), input = clicked.children('input'), on = input.val();
			clicked.find('i').toggleClass('icon-unpublish icon-publish');
			if (on == "1" ) on = "0" ; 
			else on = "1";
			input.val(on);
			return false;
		});
    });

var tax_rates = new Array();
<?php
if( property_exists($this, 'taxrates') && is_array( $this->taxrates )) {
	foreach( $this->taxrates as $key => $tax_rate ) {
		echo 'tax_rates["'.$tax_rate->tax_rate_id.'"] = '.$tax_rate->tax_rate."\n";
	}
}
?>

</script>

<?php } ?>
