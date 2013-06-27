<?php

/**
 * Calculation helper class
 *
 * This class provides the functions for the calculations
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class calculationHelper {

	private $_db;
	private $_shopperGroupId;
	var $_cats;
	private $_now;
	private $_nullDate;
	//	private $_currency;
	private $_debug;
	private $_manufacturerId;
	private $_deliveryCountry;
	private $_deliveryState;
	private $_currencyDisplay;
	var $_cart = null;
	private $_cartPrices = false;
	var $productPrices;
	var $_cartData;

	public $_amount;

// 	public $override = 0;
	public $productVendorId;
	public $productCurrency;
	public $product_tax_id = 0;
	public $product_discount_id = 0;
	public $product_marge_id = 0;
	public $vendorCurrency = 0;
	public $inCart = FALSE;
	private $exchangeRateVendor = 0;
	private $exchangeRateShopper = 0;
	private $_internalDigits = 8;
	private $_revert = false;
	static $_instance;

	//	public $basePrice;		//simular to costprice, basePrice is calculated in the shopcurrency
	//	public $salesPrice;		//end Price in the product currency
	//	public $discountedPrice;  //amount of effecting discount
	//	public $salesPriceCurrency;
	//	public $discountAmount;

	/** Constructor,... sets the actual date and current currency
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @author Geraint
	 */
	private function __construct() {
		$this->_db = JFactory::getDBO();
		$this->_app = JFactory::getApplication();

		//We store in UTC and use here of course also UTC
		$jnow = JFactory::getDate();
		$this->_now = $jnow->toMySQL();
		$this->_nullDate = $this->_db->getNullDate();

		//Attention, this is set to the mainvendor atm.
		//This means also that atm for multivendor, every vendor must use the shopcurrency as default
		//         $this->vendorCurrency = 1;
		$this->productVendorId = 1;

		if (!class_exists('CurrencyDisplay')
		)require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$this->_currencyDisplay = CurrencyDisplay::getInstance();
		$this->_debug = false;

		if(!empty($this->_currencyDisplay->_vendorCurrency)){
			$this->vendorCurrency = $this->_currencyDisplay->_vendorCurrency;
			$this->vendorCurrency_code_3 = $this->_currencyDisplay->_vendorCurrency_code_3;
			$this->vendorCurrency_numeric = $this->_currencyDisplay->_vendorCurrency_numeric;
		}
	/*	else if(VmConfig::get('multix','none')!='none'){
			$this->_db->setQuery('SELECT `vendor_currency` FROM #__virtuemart_vendors  WHERE `virtuemart_vendor_id`="1" ');
			$single = $this->_db->loadResult();
			$this->vendorCurrency = $single;
		}*/

		$this->setShopperGroupIds();

		$this->setVendorId($this->productVendorId);

		$this->rules['Marge'] = array();
		$this->rules['Tax'] 	= array();
		$this->rules['VatTax'] 	= array();
		$this->rules['DBTax'] = array();
		$this->rules['DATax'] = array();

		//round only with internal digits
		$this->_roundindig = VmConfig::get('roundindig',FALSE);
	}

	static public function getInstance() {
		if (!is_object(self::$_instance)) {
			self::$_instance = new calculationHelper();
		} else {
			//We store in UTC and use here of course also UTC
			$jnow = JFactory::getDate();
			self::$_instance->_now = $jnow->toMySQL();
		}
		return self::$_instance;
	}

	public function setVendorCurrency($id) {
		$this->vendorCurrency = $id;
	}

	//static $allrules= array();
	var $allrules= array();
	public function setVendorId($id){

		$this->productVendorId = $id;
		//vmdebug('setVendorId $allrules '.$this->productVendorId,count($this->allrules));
		if(empty($this->allrules[$this->productVendorId])){
			$epoints = array("'Marge'","'Tax'","'VatTax'","'DBTax'","'DATax'");
			$this->allrules[$this->productVendorId]['Marge'] = array();
			$this->allrules[$this->productVendorId]['Tax'] 	= array();
			$this->allrules[$this->productVendorId]['VatTax'] 	= array();
			$this->allrules[$this->productVendorId]['DBTax'] = array();
			$this->allrules[$this->productVendorId]['DATax'] = array();
			$q = 'SELECT * FROM #__virtuemart_calcs WHERE
		                    `calc_kind` IN (' . implode(",",$epoints). ' )
		                     AND `published`="1"
		                     AND (`virtuemart_vendor_id`="' . $this->productVendorId . '" OR `shared`="1" )
		                     AND ( ( publish_up = "' . $this->_db->getEscaped($this->_nullDate) . '" OR publish_up <= "' . $this->_db->getEscaped($this->_now) . '" )
		                        AND ( publish_down = "' . $this->_db->getEscaped($this->_nullDate) . '" OR publish_down >= "' . $this->_db->getEscaped($this->_now) . '" )
										OR `for_override` = "1" )';
			$this->_db->setQuery($q);
			$allrules = $this->_db->loadAssocList();

			foreach ($allrules as $rule){
				$this->allrules[$this->productVendorId][$rule["calc_kind"]][] = $rule;
			}
		}

	}

	public function getCartPrices() {
		return $this->_cartPrices;
	}

	public function setCartPrices($cartPrices) {
		$this->_cartPrices = $cartPrices;
	}

	public function getCartData() {
		return $this->_cartData;
	}

	private function setShopperGroupIds($shopperGroupIds=0, $vendorId=1) {

		if (!empty($shopperGroupIds)) {
			$this->_shopperGroupId = $shopperGroupIds;
		} else {
			$user = JFactory::getUser();
			if (!empty($user->id)) {
				$this->_db->setQuery('SELECT `usgr`.`virtuemart_shoppergroup_id` FROM #__virtuemart_vmuser_shoppergroups as `usgr`
 										JOIN `#__virtuemart_shoppergroups` as `sg` ON (`usgr`.`virtuemart_shoppergroup_id`=`sg`.`virtuemart_shoppergroup_id`)
 										WHERE `usgr`.`virtuemart_user_id`="' . $user->id . '" AND `sg`.`virtuemart_vendor_id`="' . (int) $vendorId . '" ');
				$this->_shopperGroupId = $this->_db->loadResultArray();
				if (empty($this->_shopperGroupId)) {

					$this->_db->setQuery('SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_shoppergroups
								WHERE `default`="'.($user->guest+1).'" AND `virtuemart_vendor_id`="' . (int) $vendorId . '"');
					$this->_shopperGroupId = $this->_db->loadResultArray();
				}
			}
			else if (empty($this->_shopperGroupId)) {

				$shoppergroupmodel = VmModel::getModel('ShopperGroup');
				$site = JFactory::getApplication ()->isSite ();
				$this->_shopperGroupId = array();
				$shoppergroupmodel->appendShopperGroups($this->_shopperGroupId,$user,$site,$vendorId);

			}
		}
	}

	private function setCountryState($cart=0) {

		if ($this->_app->isAdmin())
			return;

		if (empty($cart)) {
			if (!class_exists('VirtueMartCart')) require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
			$cart = VirtueMartCart::getCart();
		}
		$this->_cart = $cart;

		$stBased = VmConfig::get('taxSTbased',TRUE);
		if ($stBased and !empty($this->_cart->ST['virtuemart_country_id'])) {
			$this->_deliveryCountry = (int)$this->_cart->ST['virtuemart_country_id'];
		} else if (!empty($this->_cart->BT['virtuemart_country_id'])) {
			$this->_deliveryCountry = (int)$this->_cart->BT['virtuemart_country_id'];
		}

		if ($stBased and !empty($this->_cart->ST['virtuemart_state_id'])) {
			$this->_deliveryState = (int)$this->_cart->ST['virtuemart_state_id'];
		} else if (!empty($cart->BT['virtuemart_state_id'])) {
			$this->_deliveryState = (int)$this->_cart->BT['virtuemart_state_id'];
		}
		//vmdebug('setCountryState state '.$this->_deliveryState,$this->_cart->BT);
	}

	/** function to start the calculation, here it is for the product
	 *
	 * The function first gathers the information of the product (maybe better done with using the model)
	 * After that the function gatherEffectingRulesForProductPrice writes the queries and gets the ids of the rules which affect the product
	 * The function executeCalculation makes the actual calculation according to the rules
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @param int $product 	    The product
	 * @param int $catIds 		When the category is already determined, then it makes sense to pass it, if not the function does it for you
	 * @return int $prices		An array of the prices
	 * 							'basePrice'  		basePrice calculated in the shopcurrency
	 * 							'basePriceWithTax'	basePrice with Tax
	 * 							'discountedPrice'	before Tax
	 * 							'priceWithoutTax'	price Without Tax but with calculated discounts AFTER Tax. So it just shows how much the shopper saves, regardless which kind of tax
	 * 							'discountAmount'	the "you save X money"
	 * 							'salesPrice'		The final price, with all kind of discounts and Tax, except stuff that is only in the checkout
	 *
	 */
	public function getProductPrices($product, $variant=0.0, $amount=0, $ignoreAmount=true, $currencydisplay=true) {


		$costPrice = 0;

		//We already have the productobject, no need for extra sql
		if (is_object($product)) {
			$costPrice = isset($product->product_price)? $product->product_price:0;
			$this->productCurrency = isset($product->product_currency)? $product->product_currency:0;
			$override = isset($product->override)? $product->override:0;
			$product_override_price = isset($product->product_override_price)? $product->product_override_price:0;
			$this->product_tax_id = isset($product->product_tax_id)? $product->product_tax_id:0;
			$this->product_discount_id = isset($product->product_discount_id)? $product->product_discount_id:0;
			$productVendorId = !empty($product->virtuemart_vendor_id)? $product->virtuemart_vendor_id:1;
			$this->setVendorId($productVendorId);

			$this->_cats = $product->categories;
			$this->_product = $product;
			$this->_product->amount = $amount;
			$this->productPrices = array();
			if(!isset($this->_product->quantity)) $this->_product->quantity = 1;

			$this->_manufacturerId = !empty($product->virtuemart_manufacturer_id) ? $product->virtuemart_manufacturer_id:0;
		} //Use it as productId
		else {
			vmError('getProductPrices no object given query time','getProductPrices no object given query time');
		}

		if(VmConfig::get('multix','none')!='none' and (empty($this->vendorCurrency) or $this->vendorCurrency!=$this->productVendorId)){
			$this->_db->setQuery('SELECT `vendor_currency` FROM #__virtuemart_vendors  WHERE `virtuemart_vendor_id`="' . $this->productVendorId . '" ');
			$single = $this->_db->loadResult();
			$this->vendorCurrency = $single;
		}

		if (!empty($amount)) {
			$this->_amount = $amount;
		}

		$this->setCountryState($this->_cart);

		//For Profit, margin, and so on
		$this->rules['Marge'] = $this->gatherEffectingRulesForProductPrice('Marge', $this->product_marge_id);

		$this->productPrices['costPrice'] = $costPrice;
		$basePriceShopCurrency = $this->roundInternal($this->_currencyDisplay->convertCurrencyTo((int) $this->productCurrency, $costPrice,true));
		//vmdebug('my pure $basePriceShopCurrency',$costPrice,$this->productCurrency,$basePriceShopCurrency);
		$basePriceMargin = $this->roundInternal($this->executeCalculation($this->rules['Marge'], $basePriceShopCurrency));
		$this->basePrice = $basePriceShopCurrency = $this->productPrices['basePrice'] = !empty($basePriceMargin) ? $basePriceMargin : $basePriceShopCurrency;

		$this->rules['Tax'] = $this->gatherEffectingRulesForProductPrice('Tax', $this->product_tax_id);
		$this->rules['VatTax'] = $this->gatherEffectingRulesForProductPrice('VatTax', $this->product_tax_id);

		$this->rules['DBTax'] = $this->gatherEffectingRulesForProductPrice('DBTax', $this->product_discount_id);
		$this->rules['DATax'] = $this->gatherEffectingRulesForProductPrice('DATax', $this->product_discount_id);

		if (!empty($variant)) {
			$basePriceShopCurrency = $basePriceShopCurrency + doubleval($variant);
			$this->productPrices['basePrice'] = $this->productPrices['basePriceVariant'] = $basePriceShopCurrency;
		}
		if (empty($this->productPrices['basePrice'])) {
			return $this->fillVoidPrices($this->productPrices);
		}
		if (empty($this->productPrices['basePriceVariant'])) {
			$this->productPrices['basePriceVariant'] = $this->productPrices['basePrice'];
		}


		$this->productPrices['basePriceWithTax'] = $this->roundInternal($this->executeCalculation($this->rules['Tax'], $this->productPrices['basePrice'], true),'basePriceWithTax');
		if(!empty($this->rules['VatTax'])){
			$price = !empty($this->productPrices['basePriceWithTax']) ? $this->productPrices['basePriceWithTax'] : $this->productPrices['basePrice'];
			$this->productPrices['basePriceWithTax'] = $this->roundInternal($this->executeCalculation($this->rules['VatTax'], $price,true),'basePriceWithTax');
		}

		$this->productPrices['discountedPriceWithoutTax'] = $this->roundInternal($this->executeCalculation($this->rules['DBTax'], $this->productPrices['basePrice']),'discountedPriceWithoutTax');

		if ($override==-1) {
			$this->productPrices['discountedPriceWithoutTax'] = $product_override_price;
		}

		$priceBeforeTax = !empty($this->productPrices['discountedPriceWithoutTax']) ? $this->productPrices['discountedPriceWithoutTax'] : $this->productPrices['basePrice'];

		$this->productPrices['priceBeforeTax'] = $priceBeforeTax;
		$this->productPrices['salesPrice'] = $this->roundInternal($this->executeCalculation($this->rules['Tax'], $priceBeforeTax, true),'salesPrice');

		$salesPrice = !empty($this->productPrices['salesPrice']) ? $this->productPrices['salesPrice'] : $priceBeforeTax;

		$this->productPrices['taxAmount'] = $this->roundInternal($salesPrice - $priceBeforeTax);

		if(!empty($this->rules['VatTax'])){
			$this->productPrices['salesPrice'] = $this->roundInternal($this->executeCalculation($this->rules['VatTax'], $salesPrice),'salesPrice');
			$salesPrice = !empty($this->productPrices['salesPrice']) ? $this->productPrices['salesPrice'] : $salesPrice;
		}

		$this->productPrices['salesPriceWithDiscount'] = $this->roundInternal($this->executeCalculation($this->rules['DATax'], $salesPrice),'salesPriceWithDiscount');

// 		vmdebug('$$override salesPriceWithDiscount',$override,$this->productPrices['salesPriceWithDiscount'],$salesPrice);
		$this->productPrices['salesPrice'] = !empty($this->productPrices['salesPriceWithDiscount']) ? $this->productPrices['salesPriceWithDiscount'] : $salesPrice;

		$this->productPrices['salesPriceTemp'] = $this->productPrices['salesPrice'];
		//Okey, this may not the best place, but atm we handle the override price as salesPrice
		if ($override==1) {
			$this->productPrices['salesPrice'] = $product_override_price;
// 			$this->productPrices['discountedPriceWithoutTax'] = $this->product_override_price;
// 			$this->productPrices['salesPriceWithDiscount'] = $this->product_override_price;
		} else {

		}

		if(!empty($product->product_packaging) and $product->product_packaging!='0.0000'){
			$this->productPrices['unitPrice'] = $this->productPrices['salesPrice']/$product->product_packaging;
		} else {
			$this->productPrices['unitPrice'] = 0.0;
		}


		if(!empty($this->rules['VatTax'])){
			$this->_revert = true;
			$this->productPrices['priceWithoutTax'] = $this->productPrices['salesPrice'] - $this->productPrices['taxAmount'];
			$afterTax = $this->roundInternal($this->executeCalculation($this->rules['VatTax'], $this->productPrices['salesPrice']),'salesPrice');

			if(!empty($afterTax)){
				$this->productPrices['taxAmount'] = $this->productPrices['salesPrice'] - $afterTax;
			}
			$this->_revert = false;
		}

// 		vmdebug('getProductPrices',$this->productPrices['salesPrice'],$this->product_override_price);
		//The whole discount Amount
		//		$this->productPrices['discountAmount'] = $this->roundInternal($this->productPrices['basePrice'] + $this->productPrices['taxAmount'] - $this->productPrices['salesPrice']);
		$basePriceWithTax = !empty($this->productPrices['basePriceWithTax']) ? $this->productPrices['basePriceWithTax'] : $this->productPrices['basePrice'];

		//changed
		//		$this->productPrices['discountAmount'] = $this->roundInternal($basePriceWithTax - $salesPrice);
		$this->productPrices['discountAmount'] = $this->roundInternal($basePriceWithTax - $this->productPrices['salesPrice']);

		//price Without Tax but with calculated discounts AFTER Tax. So it just shows how much the shopper saves, regardless which kind of tax
		//		$this->productPrices['priceWithoutTax'] = $this->roundInternal($salesPrice - ($salesPrice - $discountedPrice));
// 		$this->productPrices['priceWithoutTax'] = $this->productPrices['salesPrice'] - $this->productPrices['taxAmount'];
		$this->productPrices['priceWithoutTax'] = $salesPrice - $this->productPrices['taxAmount'];

		$this->productPrices['variantModification'] = $variant;

		$this->productPrices['DBTax'] = array();
		foreach($this->rules['DBTax'] as $dbtax){
			$this->productPrices['DBTax'][$dbtax['virtuemart_calc_id']] = array($dbtax['calc_name'],$dbtax['calc_value'],$dbtax['calc_value_mathop'],$dbtax['calc_shopper_published'],$dbtax['calc_currency'],$dbtax['calc_params'], $dbtax['virtuemart_vendor_id'], $dbtax['virtuemart_calc_id']);
		}

		$this->productPrices['Tax'] = array();
		foreach($this->rules['Tax'] as $tax){
			$this->productPrices['Tax'][$tax['virtuemart_calc_id']] =  array($tax['calc_name'],$tax['calc_value'],$tax['calc_value_mathop'],$tax['calc_shopper_published'],$tax['calc_currency'],$tax['calc_params'], $tax['virtuemart_vendor_id'], $tax['virtuemart_calc_id']);
		}

		$this->productPrices['VatTax'] = array();
		foreach($this->rules['VatTax'] as $tax){
			$this->productPrices['VatTax'][$tax['virtuemart_calc_id']] =  array($tax['calc_name'],$tax['calc_value'],$tax['calc_value_mathop'],$tax['calc_shopper_published'],$tax['calc_currency'],$tax['calc_params'], $tax['virtuemart_vendor_id'], $tax['virtuemart_calc_id'],);
		}

		$this->productPrices['DATax'] = array();
		foreach($this->rules['DATax'] as $datax){
			$this->productPrices['DATax'][$datax['virtuemart_calc_id']] =  array($datax['calc_name'],$datax['calc_value'],$datax['calc_value_mathop'],$datax['calc_shopper_published'],$datax['calc_currency'],$datax['calc_params'], $datax['virtuemart_vendor_id'], $datax['virtuemart_calc_id']);
		}

		if(!empty($this->rules['VatTax'])){
			//vmdebug('!empty($this->rules["VatTax"]',$this->rules['VatTax']);
			if(empty($this->_cartData['VatTax'])){
				$this->_cartData['VatTax'] = array();
			}

			foreach($this->rules['VatTax'] as &$rule){
				if(isset($this->_cartData['VatTax'][$rule['virtuemart_calc_id']])){
					if(!isset($this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['taxAmount'])) {
						$this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['taxAmount'] = 0.0;
						$this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['subTotal'] = 0.0;
					}
					$this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['taxAmount'] += $this->productPrices['taxAmount'] * $this->_product->quantity;
					$this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['subTotal']  += $this->productPrices['salesPrice'] * $this->_product->quantity;

				} else {
					$this->_cartData['VatTax'][$rule['virtuemart_calc_id']] = $rule;
					if(!isset($this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['taxAmount'])) $this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['taxAmount'] = $this->productPrices['taxAmount'] * $this->_product->quantity;
					if(!isset($this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['subTotal'])) $this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['subTotal'] = $this->productPrices['salesPrice'] * $this->_product->quantity;
				}
				vmdebug('subtotal vattax id '.$rule['virtuemart_calc_id'].' =  '.$this->_cartData['VatTax'][$rule['virtuemart_calc_id']]['taxAmount']);
			}
		}

// 		vmdebug('getProductPrices',$this->productPrices);
		return $this->productPrices;
	}

	public function calculateCostprice($productId,$data){

		$this->_revert = true;
		//vmdebug('calculationh.php calculateCostprice ',$data);
		//vmSetStartTime('calculateCostprice');

		if(empty($data['product_currency'])){
			$this->_db->setQuery('SELECT * FROM #__virtuemart_product_prices  WHERE `virtuemart_product_id`="' . $productId . '" ');
			$row = $this->_db->loadAssoc();
			if ($row) {
				if (!empty($row['product_price'])) {

					$this->productCurrency = $row['product_currency'];
					$this->product_tax_id = $row['product_tax_id'];
					$this->product_discount_id = $row['product_discount_id'];
				} else {
					$app = Jfactory::getApplication();
					$app->enqueueMessage('cost Price empty, if child, everything okey, this is just a dev note');
					return false;
				}
			}
		} else {
			$this->productCurrency = $data['product_currency'];
			$this->product_tax_id = $data['product_tax_id'];
			$this->product_discount_id = $data['product_discount_id'];

		}

		$this->_db->setQuery('SELECT `virtuemart_vendor_id` FROM #__virtuemart_products  WHERE `virtuemart_product_id`="' . $productId . '" ');
		$single = $this->_db->loadResult();
		$this->productVendorId = $single;
		if (empty($this->productVendorId)) {
			$this->productVendorId = 1;
		}

		$this->_db->setQuery('SELECT `virtuemart_category_id` FROM #__virtuemart_product_categories  WHERE `virtuemart_product_id`="' . $productId . '" ');
		$this->_cats = $this->_db->loadResultArray();

// 		vmTime('getProductPrices no object given query time','getProductCalcs');

		if(VmConfig::get('multix','none')!='none' and empty($this->vendorCurrency )){
			$this->_db->setQuery('SELECT `vendor_currency` FROM #__virtuemart_vendors  WHERE `virtuemart_vendor_id`="' . $this->productVendorId . '" ');
			$single = $this->_db->loadResult();
			$this->vendorCurrency = $single;
		}

		if (!empty($amount)) {
			$this->_amount = $amount;
		}

		//$this->setCountryState($this->_cart);
		$this->rules['Marge'] = $this->gatherEffectingRulesForProductPrice('Marge', $this->product_marge_id);
		$this->rules['Tax'] = $this->gatherEffectingRulesForProductPrice('Tax', $this->product_tax_id);
		$this->rules['VatTax'] = $this->gatherEffectingRulesForProductPrice('VatTax', $this->product_tax_id);
		$this->rules['DBTax'] = $this->gatherEffectingRulesForProductPrice('DBTax', $this->product_discount_id);
		$this->rules['DATax'] = $this->gatherEffectingRulesForProductPrice('DATax', $this->product_discount_id);

		$salesPrice = $data['salesPrice'];

		$withoutVatTax = $this->roundInternal($this->executeCalculation($this->rules['VatTax'], $salesPrice));
		$withoutVatTax = !empty($withoutVatTax) ? $withoutVatTax : $salesPrice;
		vmdebug('calculateCostprice',$salesPrice,$withoutVatTax, $data);

		$withDiscount = $this->roundInternal($this->executeCalculation($this->rules['DATax'], $withoutVatTax));
		$withDiscount = !empty($withDiscount) ? $withDiscount : $withoutVatTax;
// 		vmdebug('Entered final price '.$salesPrice.' discount '.$withDiscount);
		$withTax = $this->roundInternal($this->executeCalculation($this->rules['Tax'], $withDiscount));
		$withTax = !empty($withTax) ? $withTax : $withDiscount;

		$basePriceP = $this->roundInternal($this->executeCalculation($this->rules['DBTax'], $withTax));
		$basePriceP = !empty($basePriceP) ? $basePriceP : $withTax;

		$basePrice = $this->roundInternal($this->executeCalculation($this->rules['Marge'], $basePriceP));
		$basePrice = !empty($basePrice) ? $basePrice : $basePriceP;

		$productCurrency = CurrencyDisplay::getInstance();
		$costprice = $productCurrency->convertCurrencyTo( $this->productCurrency, $basePrice,false);
		$this->_revert = false;

		//vmdebug('calculateCostprice',$salesPrice,$costprice, $data);
		return $costprice;
	}


	public function setRevert($revert){
		$this->_revert = $revert;
	}

	private function fillVoidPrices(&$prices) {

		if (!isset($prices['basePrice']))
			$prices['basePrice'] = null;
		if (!isset($prices['basePriceVariant']))
			$prices['basePriceVariant'] = null;
		if (!isset($prices['basePriceWithTax']))
			$prices['basePriceWithTax'] = null;
		if (!isset($prices['discountedPriceWithoutTax']))
			$prices['discountedPriceWithoutTax'] = null;
		if (!isset($prices['priceBeforeTax']))
			$prices['priceBeforeTax'] = null;
		if (!isset($prices['taxAmount']))
			$prices['taxAmount'] = null;
		if (!isset($prices['salesPriceWithDiscount']))
			$prices['salesPriceWithDiscount'] = null;
		if (!isset($prices['salesPriceTemp']))
			$prices['salesPriceTemp'] = null;
		if (!isset($prices['salesPrice']))
			$prices['salesPrice'] = null;
		if (!isset($prices['discountAmount']))
			$prices['discountAmount'] = null;
		if (!isset($prices['priceWithoutTax']))
			$prices['priceWithoutTax'] = null;
		if (!isset($prices['variantModification']))
			$prices['variantModification'] = null;
		if (!isset($prices['unitPrice']))
			$prices['unitPrice'] = null;
		return $prices;
	}

	/** function to start the calculation, here it is for the invoice in the checkout
	 * This function is partly implemented !
	 *
	 * The function calls getProductPrices for every product except it is already known (maybe changed and adjusted with product amount value
	 * The single prices gets added in an array and already summed up.
	 *
	 * Then simular to getProductPrices first the effecting rules are determined and calculated.
	 * Ah function to determine the coupon that effects the calculation is already implemented. But not completly in the calculation.
	 *
	 * 		Subtotal + Tax + Discount =	Total
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @param int $productIds 	The Ids of the products
	 * @param int $cartVendorId The Owner of the cart, this can be ignored in vm1.5
	 * @return int $prices		An array of the prices
	 * 							'resultWithOutTax'	The summed up baseprice of all products
	 * 							'resultWithTax'  	The final price of all products with their tax, discount and so on
	 * 							'discountBeforeTax'	discounted price without tax which affects only the checkout (the tax of the products is in it)
	 * 							'discountWithTax'	discounted price taxed
	 * 							'discountAfterTax'	final result
	 *
	 */
	//	function getCheckoutPrices($productIds,$variantMods=array(), $cartVendorId=1,$couponId=0,$shipId=0,$paymId=0){
	public function getCheckoutPrices($cart, $checkAutomaticSelected=true) {

		if(isset($this->_cartPrices) and is_array($this->_cartPrices) and count($this->_cartPrices)>0 and isset($this->_cartData['totalProduct']) and $this->_cartData['totalProduct']==count($cart->products)){
			return $this->_cartPrices;
		}

		$this->_cart = $cart;
		$this->inCart = TRUE;
		$pricesPerId = array();
		$this->_cartPrices = array();
		$this->_cartData = array();
		$resultWithTax = 0.0;
		$resultWithOutTax = 0.0;
		$this->_cartData['VatTax'] = array();
		$this->_cartPrices['basePrice'] = 0;
		$this->_cartPrices['basePriceWithTax'] = 0;
		$this->_cartPrices['discountedPriceWithoutTax'] = 0;
		$this->_cartPrices['salesPrice'] = 0;
		$this->_cartPrices['taxAmount'] = 0;
		$this->_cartPrices['salesPriceWithDiscount'] = 0;
		$this->_cartPrices['discountAmount'] = 0;
		$this->_cartPrices['priceWithoutTax'] = 0;
		$this->_cartPrices['subTotalProducts'] = 0;
		$this->_cartData['duty'] = 1;

		$this->_cartData['payment'] = 0; //could be automatically set to a default set in the globalconfig
		$this->_cartData['paymentName'] = '';
		$cartpaymentTax = 0;

		$this->setCountryState($cart);

		$this->_amountCart = 0;
		$this->_cartData['totalProduct'] = count($cart->products);
		foreach ($cart->products as $name => $product) {
			//$product = $productModel->getProduct($product->virtuemart_product_id,false,false,true);
			$productId = $product->virtuemart_product_id;
			if (empty($product->quantity) || empty($product->virtuemart_product_id)) {
				JError::raiseWarning(710, 'Error the quantity of the product for calculation is 0, please notify the shopowner, the product id ' . $product->virtuemart_product_id);
				continue;
			}

			$variantmods = $this->parseModifier($name);
			$variantmod = $this->calculateModificators($product, $variantmods);

			$cartproductkey = $name; //$product->virtuemart_product_id.$variantmod;

			$product->prices = $pricesPerId[$cartproductkey] = $this->getProductPrices($product, $variantmod, $product->quantity, true, false);
			$this->_amountCart += $product->quantity;

			$this->_cartPrices[$cartproductkey] = $product->prices;

			if($this->_currencyDisplay->_priceConfig['basePrice']) $this->_cartPrices['basePrice'] += self::roundInternal($product->prices['basePrice'],'basePrice') * $product->quantity;
			//				$this->_cartPrices['basePriceVariant'] = $this->_cartPrices['basePriceVariant'] + $pricesPerId[$product->virtuemart_product_id]['basePriceVariant']*$product->quantity;
			if($this->_currencyDisplay->_priceConfig['basePriceWithTax']) $this->_cartPrices['basePriceWithTax'] += self::roundInternal($product->prices['basePriceWithTax']) * $product->quantity;
			if($this->_currencyDisplay->_priceConfig['discountedPriceWithoutTax']) $this->_cartPrices['discountedPriceWithoutTax'] += self::roundInternal($product->prices['discountedPriceWithoutTax'],'discountedPriceWithoutTax') * $product->quantity;
			if($this->_currencyDisplay->_priceConfig['salesPrice']){
				$this->_cartPrices[$cartproductkey]['subtotal_with_tax'] = self::roundInternal($product->prices['salesPrice'],'salesPrice') * $product->quantity;
				$this->_cartPrices['salesPrice'] += $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
			}

			if($this->_currencyDisplay->_priceConfig['taxAmount']){
				$this->_cartPrices[$cartproductkey]['subtotal_tax_amount'] = self::roundInternal($product->prices['taxAmount'],'taxAmount') * $product->quantity;
				$this->_cartPrices['taxAmount'] += $this->_cartPrices[$cartproductkey]['subtotal_tax_amount'];
			}

			if($this->_currencyDisplay->_priceConfig['salesPriceWithDiscount']) $this->_cartPrices['salesPriceWithDiscount'] += self::roundInternal($product->prices['salesPriceWithDiscount'],'salesPriceWithDiscount') * $product->quantity;
			if($this->_currencyDisplay->_priceConfig['discountAmount']){
				$this->_cartPrices[$cartproductkey]['subtotal_discount'] = self::roundInternal($product->prices['discountAmount'],'discountAmount') * $product->quantity;
				$this->_cartPrices['discountAmount'] += $this->_cartPrices[$cartproductkey]['subtotal_discount'];
			}
			if($this->_currencyDisplay->_priceConfig['priceWithoutTax']) {
				$this->_cartPrices[$cartproductkey]['subtotal'] = self::roundInternal($product->prices['priceWithoutTax'],'priceWithoutTax') * $product->quantity;
				$this->_cartPrices['priceWithoutTax'] += $this->_cartPrices[$cartproductkey]['subtotal'];
			}
		}

		$this->_product = null;
		$this->_cartData['DBTaxRulesBill'] = $this->gatherEffectingRulesForBill('DBTaxBill');
		$this->_cartData['taxRulesBill'] = $this->gatherEffectingRulesForBill('TaxBill');
		$this->_cartData['DATaxRulesBill'] = $this->gatherEffectingRulesForBill('DATaxBill');

		$this->_cartPrices['salesPriceDBT'] = array();
		$this->_cartPrices['taxRulesBill'] = array();
		$this->_cartPrices['DATaxRulesBill'] = array();

		foreach ($cart->products as $cartproductkey => $product) {
			//for Rules with Categories
			foreach($this->_cartData['DBTaxRulesBill'] as $k=>&$dbrule){
				if(!empty($dbrule['calc_categories'])){
					if(!isset($dbrule['subTotal'])) $dbrule['subTotal'] = 0.0;
					$set = array_intersect($dbrule['calc_categories'],$product->categories);
					if(count($set)>0){
						//foreach($set as $s){
							$dbrule['subTotal'] += $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
							vmdebug('DB Rule '.$dbrule['calc_name'].' is per category subTotal '.$dbrule['subTotal']);
							// subarray with subTotal for each taxID necessary to calculate tax correct if there are more than one VatTaxes
							$dbrule['subTotalPerTaxID'] = array();
							if(!isset($dbrule['subTotalPerTaxID'][$product->product_tax_id])) $dbrule['subTotalPerTaxID'][$product->product_tax_id] = 0.0;
							$dbrule['subTotalPerTaxID'][$product->product_tax_id] += $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
						//}
					}
				}
				
			}
			// subTotal for each taxID necessary, equal if calc_categories exists ore not
			if(!empty($this->_cartData['taxRulesBill'])) {
				foreach($this->_cartData['taxRulesBill'] as $k=>&$trule){
					if(!isset($trule['subTotal'])) $trule['subTotal'] = 0.0;
					if($product->product_tax_id != 0) {
						if($product->product_tax_id == $k) {
							$trule['subTotal']+= $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
						}
					}
					elseif(!empty($trule['calc_categories'])){
						$set = array_intersect($trule['calc_categories'],$product->categories);
						if(count($set)>0){
							$trule['subTotal'] += $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
							vmdebug('DB Rule '.$trule['calc_name'].' is per category subTotal '.$trule['subTotal']);
						}
					}
					else {
						$trule['subTotal'] += $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
					}
				}
			}
			/*
			foreach($this->_cartData['taxRulesBill'] as $k=>&$trule){
				if(!empty($trule['calc_categories'])){
					if(!isset($trule['subTotal'])) $trule['subTotal'] = 0.0;
					$set = array_intersect($trule['calc_categories'],$product->categories);
					if(count($set)>0){
						//foreach($set as $s){
							$trule['subTotal'] += $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
							vmdebug('DB Rule '.$trule['calc_name'].' is per category subTotal '.$trule['subTotal']);
						//}
					}
				}
			}
			*/
			
			foreach($this->_cartData['DATaxRulesBill'] as &$darule){
				if(!empty($darule['calc_categories'])){
					if(!isset($darule['subTotal'])) $darule['subTotal'] = 0.0;
					$set = array_intersect($darule['calc_categories'],$product->categories);
					if(count($set)>0){
						if(!isset($darule['subTotal'])) $darule['subTotal'] = 0.0;
						//foreach($set as $s){
							$darule['subTotal'] += $this->_cartPrices[$cartproductkey]['subtotal_with_tax'];
						//}
					}
				}
			}
		}

		// Calculate the discount from all rules before tax to calculate billTotal
		$cartdiscountBeforeTax = $this->roundInternal($this->cartRuleCalculation($this->_cartData['DBTaxRulesBill'], $this->_cartPrices['salesPrice']));		
		
		// We need the discount per category for each taxID to reduce the total discount before calculate percentage from hole cart discounts
		$categorydiscountBeforeTax = 0;
		foreach ($this->_cartData['DBTaxRulesBill'] as &$rule) {
			if (!empty($rule['subTotalPerTaxID'])) {
				foreach ($rule['subTotalPerTaxID'] as $k=>$DBTax) {
					$this->roundInternal($this->cartRuleCalculation($this->_cartData['DBTaxRulesBill'], $this->_cartPrices['salesPrice'], $k, true));
					if (!empty($this->_cartData['VatTax'][$k]['DBTax'][$rule['virtuemart_calc_id'] . 'DBTax'])) {
						$categorydiscountBeforeTax += $this->_cartData['VatTax'][$k]['DBTax'][$rule['virtuemart_calc_id'] . 'DBTax'];
					}
					//vmdebug('$categorydiscountBeforeTax',$categorydiscountBeforeTax);
				}
			}
		}
		// combine the discounts before tax for each taxID
		foreach ($this->_cartData['VatTax'] as &$rule) {
			if (!empty($rule['DBTax'])) {
				$sum = 0;
				foreach ($rule['DBTax'] as $key=>$val) {
					$sum += $val;
				}
				$rule['DBTax'] = $sum;
			}
		}
		
		// calculate the new subTotal with discounts before tax, necessary for billTotal
		$toTax = $this->_cartPrices['salesPrice'] - abs($cartdiscountBeforeTax);

		//Avalara wants to calculate the tax of the shipment. Only disadvantage to set shipping here is that the discounts per bill respectivly the tax per bill
		// is not considered.
		$shipment_id = empty($cart->virtuemart_shipmentmethod_id) ? 0 : $cart->virtuemart_shipmentmethod_id;
		$this->calculateShipmentPrice($cart,  $shipment_id, $checkAutomaticSelected);

		// next step is handling a coupon, if given
		$this->_cartPrices['salesPriceCoupon'] = 0.0;
		if (!empty($cart->couponCode)) {
			$this->couponHandler($cart->couponCode);
		}

		// now calculate the discount for hole cart and reduce subTotal for each taxRulesBill, to calculate correct tax, also if there are more than one tax rules	
		$totalDiscountBeforeTax =  abs($cartdiscountBeforeTax) - abs($categorydiscountBeforeTax) + $this->_cartPrices['salesPriceCoupon'];
		foreach ($this->_cartData['taxRulesBill'] as $k=>&$rule) {
			
			if(!empty($rule['subTotal'])) {
				$rule['percentage'] = $rule['subTotal'] / $this->_cartPrices['salesPrice'];
				if (isset($this->_cartData['VatTax'][$k]['DBTax'])) {
					$rule['subTotal'] -= abs($this->_cartData['VatTax'][$k]['DBTax']);
				}
				$rule['subTotal'] -= $totalDiscountBeforeTax * $rule['percentage'];
			}
		}

		// now each taxRule subTotal is reduced with DBTax and we can calculate the cartTax 
		$cartTax = $this->roundInternal($this->cartRuleCalculation($this->_cartData['taxRulesBill'], $toTax));

		// toDisc is new subTotal after tax, now it comes discount afterTax and we can calculate the final cart price with tax.
		$toDisc = $toTax + abs($cartTax);
		$cartdiscountAfterTax = $this->roundInternal($this->cartRuleCalculation($this->_cartData['DATaxRulesBill'], $toDisc));
		$this->_cartPrices['withTax'] = $toDisc - abs($cartdiscountAfterTax);

		
		$paymentId = empty($cart->virtuemart_paymentmethod_id) ? 0 : $cart->virtuemart_paymentmethod_id;

		$this->calculatePaymentPrice($cart, $paymentId, $checkAutomaticSelected);

		//		$sub =!empty($this->_cartPrices['discountedPriceWithoutTax'])? $this->_cartPrices['discountedPriceWithoutTax']:$this->_cartPrices['basePrice'];
		if($this->_currencyDisplay->_priceConfig['salesPrice']) $this->_cartPrices['billSub'] = $this->_cartPrices['basePrice'] + $this->_cartPrices['shipmentValue'] + $this->_cartPrices['paymentValue'];
		//		$this->_cartPrices['billSub']  = $sub + $this->_cartPrices['shipmentValue'] + $this->_cartPrices['paymentValue'];
		if($this->_currencyDisplay->_priceConfig['discountAmount']) $this->_cartPrices['billDiscountAmount'] = -$this->_cartPrices['discountAmount'] + $cartdiscountBeforeTax + $cartdiscountAfterTax;// + $this->_cartPrices['shipmentValue'] + $this->_cartPrices['paymentValue'] ;
		if($this->_currencyDisplay->_priceConfig['taxAmount']) $this->_cartPrices['billTaxAmount'] = $this->_cartPrices['taxAmount'] + $this->_cartPrices['shipmentTax'] + $this->_cartPrices['paymentTax'] + $cartTax; //+ $this->_cartPrices['withTax'] - $toTax

		//The coupon handling is only necessary if a salesPrice is displayed, otherwise we have a kind of catalogue mode
		if($this->_currencyDisplay->_priceConfig['salesPrice']){
			$this->_cartPrices['billTotal'] = $this->_cartPrices['salesPriceShipment'] + $this->_cartPrices['salesPricePayment'] + $this->_cartPrices['withTax'] - $this->_cartPrices['salesPriceCoupon'];

			if($this->_cartPrices['billTotal'] < 0){
				$this->_cartPrices['billTotal'] = 0.0;
			}
			
			$this->_cartData['vmVat'] = TRUE;

			if($this->_cartData['vmVat'] and (!empty($cartdiscountBeforeTax) and isset($this->_cartData['VatTax']) and count($this->_cartData['VatTax'])>0) or !empty($cart->couponCode)){
				//$this->_revert = true;
				
				$allTotalTax = 0.0;
				$totalDiscount =  abs($cartdiscountBeforeTax) - abs($categorydiscountBeforeTax) + $this->_cartPrices['salesPriceCoupon'];

			//	vmdebug(' salesPriceCoupon = '. $this->_cartPrices['salesPriceCoupon'].'     billDiscountAmount = '.$this->_cartPrices['billDiscountAmount']);
				foreach($this->_cartData['VatTax'] as &$vattax){

					//$vattax['DBTax'] = var_dump(array_sum($vattax['DBTax']));
					if (isset($vattax['subTotal'])) {
						$vattax['percentage'] = $vattax['subTotal'] / $this->_cartPrices['salesPrice'];
					}
					$vattax['DBTax'] = isset($vattax['DBTax']) ? $vattax['DBTax'] : 0;
					if (isset($vattax['calc_value'])) {
						$vattax['discountTaxAmount'] = round(($totalDiscount * $vattax['percentage'] + abs($vattax['DBTax'])) / (100 + $vattax['calc_value']) * $vattax['calc_value'],$this->_currencyDisplay->_priceConfig['taxAmount'][1]);
					}
					//$vattax['subTotal'] = $vattax['subTotal'] - $vattax['percentage'] * $totalDiscount;

					if (isset($vattax['discountTaxAmount'])) $this->_cartPrices['billTaxAmount'] -= $vattax['discountTaxAmount'];
					$allTotalTax += $totalDiscount;
					//$this->_cartPrices['billTaxAmount'] += $vattax['subTotal'];
					//vmdebug('my vattax recalc data the percentage = '.$vattax['percentage'].'  salesPrice = '.$this->_cartPrices['salesPrice'].'  $totalDiscount = '. $totalDiscount.'  subtotal = '.$vattax['subTotal']);

				}

			}
		}

		//Calculate VatTax result
		if ($this->_cartPrices['shipment_calc_id']) $this->_cartData['VatTax'][$this->_cartPrices['shipment_calc_id']]['shipmentTax'] = $this->_cartPrices['shipmentTax'];
		if ($this->_cartPrices['payment_calc_id']) $this->_cartData['VatTax'][$this->_cartPrices['payment_calc_id']]['paymentTax'] = $this->_cartPrices['paymentTax'];
		foreach($this->_cartData['VatTax'] as $k=>&$vattax){
			$vattax['result'] = isset($vattax['taxAmount']) ? $vattax['taxAmount'] : 0;
			if (isset($vattax['discountTaxAmount'])) $vattax['result'] -= $vattax['discountTaxAmount'];
			if (isset($vattax['shipmentTax'])) $vattax['result'] += $vattax['shipmentTax'];
			if (isset($vattax['paymentTax'])) $vattax['result'] += $vattax['paymentTax'];
			if (!isset($vattax['virtuemart_calc_id'])) $vattax['virtuemart_calc_id'] = $this->getCalcRuleData($k)->virtuemart_calc_id;
			if (!isset($vattax['calc_name'])) $vattax['calc_name'] = $this->getCalcRuleData($k)->calc_name;
			if (!isset($vattax['calc_value'])) $vattax['calc_value'] = $this->getCalcRuleData($k)->calc_value;
		}
		foreach ($this->_cartData['taxRulesBill'] as $k=>&$rule) {
			$this->_cartData['VatTax'][$k]['result'] = isset($this->_cartData['VatTax'][$k]['result']) ? $this->_cartData['VatTax'][$k]['result'] : 0;
			$this->_cartData['VatTax'][$k]['result'] += round($this->_cartPrices[$rule['virtuemart_calc_id'] . 'Diff'],$this->_currencyDisplay->_priceConfig['salesPrice'][1]);
			if(!isset($this->_cartData['VatTax'][$k]['virtuemart_calc_id'])) $this->_cartData['VatTax'][$k]['virtuemart_calc_id'] = $rule['virtuemart_calc_id'];
			if(!isset($this->_cartData['VatTax'][$k]['calc_name'])) $this->_cartData['VatTax'][$k]['calc_name'] = $rule['calc_name'];
			if(!isset($this->_cartData['VatTax'][$k]['calc_value'])) $this->_cartData['VatTax'][$k]['calc_value'] = $rule['calc_value'];
		}

		//$this->_cartData['taxRulesBill'] = array_merge($this->_cartData['taxRulesBill'],$this->_cartData['VatTax']);
		//vmdebug('$this->_cartData',$this->_cartData);
		//vmdebug('$this->_cartPrices',$this->_cartPrices);

		return $this->_cartPrices;
	}


	/**
	 * Get the data of the CalcRule ID if it is not there
	 * @author Maik Kuennemann
	 * @param $VatTaxID ID of the taxe rule
	 */
	private function getCalcRuleData($calcRuleID) {
		$q = 'SELECT * FROM #__virtuemart_calcs WHERE `virtuemart_calc_id`="' . $calcRuleID . '"';
		$this->_db->setQuery($q);
		$calcRule = $this->_db->loadObject();
		return $calcRule;
	}

	/**
	 * Get coupon details and calculate the value
	 * @author Oscar van Eijk
	 * @param $_code Coupon code
	 */
	private function couponHandler($_code) {

		JPluginHelper::importPlugin('vmcoupon');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmCouponHandler', array($_code,&$this->_cartData, &$this->_cartPrices));
		if(!empty($returnValues)){
			foreach ($returnValues as $returnValue) {
				if ($returnValue !== null  ) {
					return $returnValue;
				}
			}
		}

		if (!class_exists('CouponHelper'))
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'coupon.php');
		if (!($_data = CouponHelper::getCouponDetails($_code))) {
			return; // TODO give some error here
		}
		$_value_is_total = ($_data->percent_or_total == 'total');
		$this->_cartData['couponCode'] = $_code;
		$this->_cartData['couponDescr'] = ($_value_is_total ? '' : (round($_data->coupon_value) . '%')
		);
		$this->_cartPrices['salesPriceCoupon'] = ($_value_is_total ? $_data->coupon_value : ($this->_cartPrices['salesPrice'] * ($_data->coupon_value / 100))
		);
		// TODO Calculate the tax
		$this->_cartPrices['couponTax'] = 0;
		$this->_cartPrices['couponValue'] = $this->_cartPrices['salesPriceCoupon'] - $this->_cartPrices['couponTax'];
		//$this->_cartPrices['billTotal'] -= $this->_cartPrices['salesPriceCoupon'];
		//if($this->_cartPrices['billTotal'] < 0){
		//	$this->_cartPrices['billTotal'] = 0.0;
		//}
	}

	/**
	 * Function to calculate discount/tax of cart rules.
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers, Maik Künnemann
	 * 
	 * @return int 	$price  	the discount/tax
	 */
	function cartRuleCalculation($rules, $baseprice, $TaxID = 0, $DBTax = false) {

		if (empty($rules))return 0;

		$rulesEffSorted = $this->record_sort($rules, 'ordering',$this->_revert);

		if (isset($rulesEffSorted)) {

			$discount = 0;

			foreach ($rulesEffSorted as &$rule) {

				if(isset($rule['subTotal'])) {
					$cIn = $rule['subTotal'];
				} else {
					$cIn = $baseprice;
				}

				$cOut = $this->interpreteMathOp($rule, $cIn);

				$this->_cartPrices[$rule['virtuemart_calc_id'] . 'Diff'] = $this->roundInternal($this->roundInternal($cOut) - $cIn);

				$discount += round($this->_cartPrices[$rule['virtuemart_calc_id'] . 'Diff'],$this->_currencyDisplay->_priceConfig['salesPrice'][1]);

				if(isset($rule['subTotal']) and $TaxID != 0 and $DBTax = true) {
					$cIn = $rule['subTotalPerTaxID'][$TaxID];
					$cOut = $this->interpreteMathOp($rule, $cIn);
					$this->_cartData['VatTax'][$TaxID]['DBTax'][$rule['virtuemart_calc_id'] . 'DBTax'] = round($this->roundInternal($this->roundInternal($cOut) - $cIn),$this->_currencyDisplay->_priceConfig['salesPrice'][1]);;
				}
			}
		}

		return $discount;
	}
	
	/**
	 * Function to execute the calculation of the gathered rules Ids.
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @param 		$rules 		The Ids of the products
	 * @param 		$price 		The input price, if no rule is affecting, 0 gets returned
	 * @return int 	$price  	the endprice
	 */
	function executeCalculation($rules, $baseprice, $relateToBaseAmount=false,$setCartPrices = true) {

		if (empty($rules))return 0;

		$rulesEffSorted = $this->record_sort($rules, 'ordering',$this->_revert);

		$price = $baseprice;
		$finalprice = $baseprice;
		if (isset($rulesEffSorted)) {

			foreach ($rulesEffSorted as $rule) {

				if(isset($rule['subTotal'])){
					$cIn = $rule['subTotal'];
					vmdebug('executeCalculation use subTotal of rule '.$rule['subTotal']);
				}
				else if ($relateToBaseAmount) {
					$cIn = $baseprice;
				} else {
					$cIn = $price;
				}

				$cOut = $this->interpreteMathOp($rule, $cIn);

				$tmp = $this->roundInternal($this->roundInternal($cOut) - $cIn);


				if($setCartPrices){
					$this->_cartPrices[$rule['virtuemart_calc_id'] . 'Diff'] = $tmp;
				}
 				//vmdebug('executeCalculation id : '.$rule['virtuemart_calc_id'].' = '.$tmp);
				//okey, this is a bit flawless logic, but should work
				if ($relateToBaseAmount) {
					$finalprice = $finalprice + $tmp;
				} else {
					$price = $cOut;
				}
			}
		}

		//okey done with it
		if (!$relateToBaseAmount) {
			$finalprice = $price;
		}

		return $finalprice;
	}

	/**
	 * Gatheres the rules which affects the product.
	 *
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @param	$entrypoint The entrypoint how it should behave. Valid values should be
	 * 						Profit (Commission is a profit rule that is shared, maybe we remove shared and make a new entrypoint called profit)
	 * 						DBTax (Discount for wares, coupons)
	 * 						Tax
	 * 						DATax (Discount on money)
	 * 						Duty
	 * @return	$rules The rules that effects the product as Ids
	 */
	function gatherEffectingRulesForProductPrice($entrypoint, $id) {

		$testedRules = array();
		if ($id === -1) return $testedRules;
		//virtuemart_calc_id 	virtuemart_vendor_id	calc_shopper_published	calc_vendor_published	published 	shared calc_amount_cond
		$countries = '';
		$states = '';
		$shopperGroup = '';
		$entrypoint = (string) $entrypoint;
		if(empty($this->allrules[$this->productVendorId][$entrypoint])){
			return $testedRules;
		}
		$allRules = $this->allrules[$this->productVendorId][$entrypoint];

		//Cant be done with Leftjoin afaik, because both conditions could be arrays.
		foreach ($allRules as $i => $rule) {

			if(!empty($id)){
				if($rule['virtuemart_calc_id']==$id){
					$testedRules[] = $rule;
				}
				continue;
			}
			if(!empty($this->allrules[$this->productVendorId][$entrypoint][$i]['for_override'])){
				continue;
			}
			if(!isset($this->allrules[$this->productVendorId][$entrypoint][$i]['cats'])){

				$q = 'SELECT `virtuemart_category_id` FROM #__virtuemart_calc_categories WHERE `virtuemart_calc_id`="' . $rule['virtuemart_calc_id'] . '"';
				$this->_db->setQuery($q);
				$this->allrules[$this->productVendorId][$entrypoint][$i]['cats'] = $this->_db->loadResultArray();

			}

			$hitsCategory = true;
			if (isset($this->_cats)) {
				$hitsCategory = $this->testRulePartEffecting($this->allrules[$this->productVendorId][$entrypoint][$i]['cats'], $this->_cats);
			}

			if(!isset($this->allrules[$this->productVendorId][$entrypoint][$i]['shoppergrps'])){
				$q = 'SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_calc_shoppergroups WHERE `virtuemart_calc_id`="' . $rule['virtuemart_calc_id'] . '"';
				$this->_db->setQuery($q);
				$this->allrules[$this->productVendorId][$entrypoint][$i]['shoppergrps'] = $this->_db->loadResultArray();
			}

			$hitsShopper = true;
			if (isset($this->_shopperGroupId)) {
				$hitsShopper = $this->testRulePartEffecting($this->allrules[$this->productVendorId][$entrypoint][$i]['shoppergrps'], $this->_shopperGroupId);
			}

			if(!isset($this->allrules[$this->productVendorId][$entrypoint][$i]['countries'])){
				$q = 'SELECT `virtuemart_country_id` FROM #__virtuemart_calc_countries WHERE `virtuemart_calc_id`="' . $rule["virtuemart_calc_id"] . '"';
				$this->_db->setQuery($q);
				$this->allrules[$this->productVendorId][$entrypoint][$i]['countries'] = $this->_db->loadResultArray();
			}

			if(!isset($this->allrules[$this->productVendorId][$entrypoint][$i]['states'])){
				$q = 'SELECT `virtuemart_state_id` FROM #__virtuemart_calc_states WHERE `virtuemart_calc_id`="' . $rule["virtuemart_calc_id"] . '"';
				$this->_db->setQuery($q);
				$this->allrules[$this->productVendorId][$entrypoint][$i]['states'] = $this->_db->loadResultArray();
			}

			$hitsDeliveryArea = true;
			if(!empty($this->allrules[$this->productVendorId][$entrypoint][$i]['states'])){
				if (!empty($this->_deliveryState)){
					$hitsDeliveryArea = $this->testRulePartEffecting($this->allrules[$this->productVendorId][$entrypoint][$i]['states'], $this->_deliveryState);
				} else {
					$hitsDeliveryArea = false;
				}
			} else if(!empty($this->allrules[$this->productVendorId][$entrypoint][$i]['countries'])){
				if (!empty($this->_deliveryCountry)){
					$hitsDeliveryArea = $this->testRulePartEffecting($this->allrules[$this->productVendorId][$entrypoint][$i]['countries'], $this->_deliveryCountry);
				} else {
					$hitsDeliveryArea = false;
				}
			}

			if(!isset($this->allrules[$this->productVendorId][$entrypoint][$i]['manufacturers'])){
				$q = 'SELECT `virtuemart_manufacturer_id` FROM #__virtuemart_calc_manufacturers WHERE `virtuemart_calc_id`="' . $rule['virtuemart_calc_id'] . '"';
				$this->_db->setQuery($q);
				$this->allrules[$this->productVendorId][$entrypoint][$i]['manufacturers'] = $this->_db->loadResultArray();
			}

			$hitsManufacturer = true;
			if (isset($this->_manufacturerId)) {
				$hitsManufacturer = $this->testRulePartEffecting($this->allrules[$this->productVendorId][$entrypoint][$i]['manufacturers'], $this->_manufacturerId);
			}

			if ($hitsCategory and $hitsShopper and $hitsDeliveryArea and $hitsManufacturer) {
				if ($this->_debug)
					echo '<br/ >Add rule ForProductPrice ' . $rule["virtuemart_calc_id"];

				$testedRules[] = $rule;
			}
		}

		//Test rules in plugins
		if(!empty($testedRules)){
			JPluginHelper::importPlugin('vmcalculation');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('plgVmInGatherEffectRulesProduct',array(&$this,&$testedRules));
		}

		return $testedRules;
	}

	/**
	 * Gathers the effecting rules for the calculation of the bill
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @param	$entrypoint
	 * @param	$cartVendorId
	 * @return $rules The rules that effects the Bill as Ids
	 */
	function gatherEffectingRulesForBill($entrypoint, $cartVendorId=1) {

		//Test if calculation affects the current entry point
		//shared rules counting for every vendor seems to be not necessary
		$q = 'SELECT * FROM #__virtuemart_calcs WHERE
                `calc_kind`="' . $entrypoint . '"
                AND `published`="1"
                AND (`virtuemart_vendor_id`="' . $cartVendorId . '" OR `shared`="1" )
				AND ( publish_up = "' . $this->_db->getEscaped($this->_nullDate) . '" OR publish_up <= "' . $this->_db->getEscaped($this->_now) . '" )
				AND ( publish_down = "' . $this->_db->getEscaped($this->_nullDate) . '" OR publish_down >= "' . $this->_db->getEscaped($this->_now) . '" ) ';
		//			$shoppergrps .  $countries . $states ;
		$this->_db->setQuery($q);
		$rules = $this->_db->loadAssocList();
		$testedRules = array();

		foreach ($rules as $rule) {

			$q = 'SELECT `virtuemart_country_id` FROM #__virtuemart_calc_countries WHERE `virtuemart_calc_id`="' . $rule["virtuemart_calc_id"] . '"';
			$this->_db->setQuery($q);
			$countries = $this->_db->loadResultArray();

			$q = 'SELECT `virtuemart_state_id` FROM #__virtuemart_calc_states WHERE `virtuemart_calc_id`="' . $rule["virtuemart_calc_id"] . '"';
			$this->_db->setQuery($q);
			$states = $this->_db->loadResultArray();

			$hitsDeliveryArea = true;
			//vmdebug('gatherEffectingRulesForBill $hitsDeliveryArea $countries and states  ',$countries,$states,$q);
			if (!empty($countries) && empty($states)) {
				$hitsDeliveryArea = $this->testRulePartEffecting($countries, $this->_deliveryCountry);
			} else if (!empty($states) ) {
				$hitsDeliveryArea = $this->testRulePartEffecting($states, $this->_deliveryState);
				vmdebug('gatherEffectingRulesForBill $hitsDeliveryArea '.(int)$hitsDeliveryArea.' '.$this->_deliveryState,$states);
			}


			$q = 'SELECT `virtuemart_category_id` FROM #__virtuemart_calc_categories WHERE `virtuemart_calc_id`="' . $rule['virtuemart_calc_id'] . '"';
			$this->_db->setQuery($q);
			$rule['calc_categories'] = $this->_db->loadResultArray();


			$q = 'SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_calc_shoppergroups WHERE `virtuemart_calc_id`="' . $rule["virtuemart_calc_id"] . '"';
			$this->_db->setQuery($q);
			$shoppergrps = $this->_db->loadResultArray();

			$hitsShopper = true;
			if (isset($this->_shopperGroupId)) {
				$hitsShopper = $this->testRulePartEffecting($shoppergrps, $this->_shopperGroupId);
			}

			if ($hitsDeliveryArea && $hitsShopper) {
				if ($this->_debug)
					echo '<br/ >Add Checkout rule ' . $rule["virtuemart_calc_id"] . '<br/ >';
				$testedRules[$rule['virtuemart_calc_id']] = $rule;
			}
		}

		//Test rules in plugins
		if(!empty($testedRules)){
			JPluginHelper::importPlugin('vmcalculation');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('plgVmInGatherEffectRulesBill', array(&$this, &$testedRules));
		}

		return $testedRules;
	}

	/**
	 * Calculates the effecting Shipment prices for the calculation
	 * @todo
	 * @copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 * @param 	$code 	The Id of the coupon
	 * @return 	$rules 	ids of the coupons
	 */
	function calculateShipmentPrice(  $cart, $ship_id, $checkAutomaticSelected=true) {

		$this->_cartData['shipmentName'] = JText::_('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
		$this->_cartPrices['shipmentValue'] = 0; //could be automatically set to a default set in the globalconfig
		$this->_cartPrices['shipmentTax'] = 0;
		$this->_cartPrices['salesPriceShipment'] = 0;
		$this->_cartPrices['shipment_calc_id'] = 0;
		// check if there is only one possible shipment method

		$automaticSelectedShipment =   $cart->CheckAutomaticSelectedShipment($this->_cartPrices, $checkAutomaticSelected);
		if ($automaticSelectedShipment) $ship_id=$cart->virtuemart_shipmentmethod_id;
		if (empty($ship_id)) return;

		// Handling shipment plugins
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmonSelectedCalculatePriceShipment',array(  $cart, &$this->_cartPrices, &$this->_cartData['shipmentName']  ));

		/*
		   * Plugin return true if shipment rate is still valid
		   * false if not any more
		   */
		$shipmentValid=0;
		foreach ($returnValues as $returnValue) {
			$shipmentValid += $returnValue;
		}
		if (!$shipmentValid) {
			$cart->virtuemart_shipmentmethod_id = 0;
			$cart->setCartIntoSession();
		}


		return $this->_cartPrices;
	}

	/**
	 * Calculates the effecting Payment prices for the calculation
	 * @todo
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 * @param 	$code 	The Id of the paymentmethod
	 * @param	$value	amount of the money to transfere
	 * @param	$value	$cartVendorId
	 * @return 	$paymentCosts 	The amount of money the customer has to pay. Calculated in shop currency
	 */
	function calculatePaymentPrice($cart,   $payment_id , $checkAutomaticSelected=true) {
		//		if (empty($code)) return 0.0;
		//		$code=4;
		$this->_cartData['paymentName'] = JText::_('COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED');
		$this->_cartPrices['paymentValue'] = 0; //could be automatically set to a default set in the globalconfig
		$this->_cartPrices['paymentTax'] = 0;
		$this->_cartPrices['paymentTotal'] = 0;
		$this->_cartPrices['salesPricePayment'] = 0;
		$this->_cartPrices['payment_calc_id'] = 0;
		// check if there is only one possible payment method
		$cart->automaticSelectedPayment =   $cart->CheckAutomaticSelectedPayment( $this->_cartPrices, $checkAutomaticSelected);
		if ($cart->automaticSelectedPayment) $payment_id=$cart->virtuemart_paymentmethod_id;
		if (empty($payment_id)) return;

		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmonSelectedCalculatePricePayment',array( $cart, &$this->_cartPrices, &$this->_cartData['paymentName']  ));

		/*
		   * Plugin return true if payment plugin is  valid
		   * false if not  valid anymore
		   * only one value is returned
		   */
		$paymentValid=0;
		foreach ($returnValues as $returnValue) {
			$paymentValid += $returnValue;
		}
		if (!$paymentValid) {
			$cart->virtuemart_paymentmethod_id = 0;
			$cart->setCartIntoSession();
		}
		return $this->_cartPrices;
	}

	function calculateCustomPriceWithTax($price, $override_id=0) {

		if(VmConfig::get('cVarswT',1)){
			$taxRules = $this->gatherEffectingRulesForProductPrice('Tax', $override_id);
			$vattaxRules = $this->gatherEffectingRulesForProductPrice('VatTax', $override_id);
			$rules = array_merge($taxRules,$vattaxRules);
			if(!empty($rules)){
				$price = $this->executeCalculation($rules, $price, true);
			}

			$price = $this->roundInternal($price);
		}


		return $price;
	}

	/**
	 * This function just writes the query for gatherEffectingRulesForProductPrice
	 * When a condition is not set, it is handled like a set condition that affects it. So the users have only to add a value
	 * for the conditions they want to (You dont need to enter a start or end date when the rule should count everytime).
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @param $data		the ids of the rule, for exampel the ids of the categories that affect the rule
	 * @param $field	the name of the field in the db, for exampel calc_categories to write a rule that asks for the field calc_categories
	 * @return $q		The query
	 */
	function writeRulePartEffectingQuery($data, $field, $setAnd=0) {
		$q = '';
		if (!empty($data)) {
			if ($setAnd) {
				$q = ' AND (';
			} else {
				$q = ' (';
			}
			foreach ($data as $id) {
				$q = $q . '`' . $field . '`="' . $id . '" OR';
			}
			$q = $q . '`' . $field . '`="0" )';
		}
		return $q;
	}

	/**
	 * This functions interprets the String that is entered in the calc_value_mathop field
	 * The first char is the signum of the function. The more this function can be enhanced
	 * maybe with function that works like operators, the easier it will be to make more complex disount/commission/profit formulas
	 * progressive, nonprogressive and so on.
	 *
	 * @copyright Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author Max Milbers
	 * @param 	$mathop 	String reprasentation of the mathematical operation, valid ('+','-','+%','-%')
	 * @param	$value 	float	The value that affects the price
	 * @param 	$currency int	the currency which should be used
	 * @param	$price 	float	The price to calculate
	 */
	function interpreteMathOp($rule, $price) {

		$mathop = $rule['calc_value_mathop'];
		$value = (float)$rule['calc_value'];
		$currency = $rule['calc_currency'];
		//$mathop, $value, $price, $currency='')

		$coreMathOp = array('+','-','+%','-%');

		if(!$this->_revert){
			$plus = '+';
			$minus = '-';
		} else {
			$plus = '-';
			$minus = '+';
		}

		if(in_array($mathop,$coreMathOp)){
			$sign = substr($mathop, 0, 1);

			$calculated = false;
			if (strlen($mathop) == 2) {
				$cmd = substr($mathop, 1, 2);
				if ($cmd == '%') {
					if(!$this->_revert){
						$calculated = $price * $value / 100.0;
					} else {
						if(!empty($value)){
							if($sign == $plus){
								$calculated =  abs($price /(1 -  (100.0 / $value)));
							} else {
								$calculated = abs($price /(1 +  (100.0 / $value)));
							}
						} else {
							vmdebug('interpreteMathOp $value is empty '.$rule['calc_name']);
						}
// 							vmdebug('interpreteMathOp $price'.$price.' $value '.$value.' $sign '.$sign.' '.$plus.' $calculated '.$calculated);
					}
				}
			} else if (strlen($mathop) == 1){
				$calculated = $this->_currencyDisplay->convertCurrencyTo($currency, $value);
			}
// 				vmdebug('interpreteMathOp',$price,$calculated,$plus);
			if($sign == $plus){
				return $price + (float)$calculated;
			} else if($sign == $minus){
				return $price - (float)$calculated;
			} else {
				VmWarn('Unrecognised mathop '.$mathop.' in calculation rule found');
				return $price;
			}
		} else {

			JPluginHelper::importPlugin('vmcalculation');
			$dispatcher = JDispatcher::getInstance();
			//$calculated = $dispatcher->trigger('interpreteMathOp', array($this, $mathop, $value, $price, $currency,$this->_revert));
			$calculated = $dispatcher->trigger('plgVmInterpreteMathOp', array($this, $rule, $price,$this->_revert));
			//vmdebug('result of plgVmInterpreteMathOp',$calculated);
			if($calculated){
				foreach($calculated as $calc){
					if($calc) return $calc;
				}
			} else {
				VmWarn('Unrecognised mathop '.$mathop.' in calculation rule found, seems you created this rule with plugin not longer accesible (deactivated, uninstalled?)');
				return $price;
			}
		}

	}

	/**
	 * Standard round function, we round every number with 6 fractionnumbers
	 * We need at least 4 to calculate something like 9.25% => 0.0925
	 * 2 digits
	 * Should be setable via config (just for the crazy case)
	 */
	function roundInternal($value,$name = 0) {

		if(!$this->_roundindig and $name!==0){
			if(isset($this->_currencyDisplay->_priceConfig[$name][1])){
				//vmdebug('roundInternal rounding use '.$this->_currencyDisplay->_priceConfig[$name][1].' digits');
				return round($value,$this->_currencyDisplay->_priceConfig[$name][1]);
			} else {
				vmdebug('roundInternal rounding not found for '.$name,$this->_currencyDisplay->_priceConfig[$name]);
				return round($value, $this->_internalDigits);
			}
		} else {
			return round($value, $this->_internalDigits);
		}

	}

	/**
	 * Round function for display with 6 fractionnumbers.
	 * For more information please read http://en.wikipedia.org/wiki/Propagation_of_uncertainty
	 * and http://www.php.net/manual/en/language.types.float.php
	 * So in case of € or $ it is rounded in cents
	 * Should be setable via config
	 * @deprecated
	 */
/*		function roundDisplay($value) {
		   return round($value, 4);
	   }*/

	/**
	 * Can test the tablefields Category, Country, State
	 *  If the the data is 0 false is returned
	 */
	function testRulePartEffecting($rule, $data) {

		if (!isset($rule))
			return true;
		if (!isset($data))
			return false;

		if (is_array($rule)) {
			if (count($rule) == 0)
				return true;
		} else {
			$rule = array($rule);
		}
		if (!is_array($data))
			$data = array($data);

		$intersect = array_intersect($rule, $data);
		if ($intersect) {
			return true;
		} else {
			return false;
		}
	}

	/** Sorts indexed 2D array by a specified sub array key
	 *
	 * Copyright richard at happymango dot me dot uk
	 * @author Max Milbers
	 */
	function record_sort($records, $field, $reverse=false) {
		if (is_array($records)) {
			$hash = array();

			foreach ($records as $record) {

				$keyToUse = $record[$field];
				while (array_key_exists($keyToUse, $hash)) {
					$keyToUse = $keyToUse + 1;
				}
				$hash[$keyToUse] = $record;
			}
			($reverse) ? krsort($hash) : ksort($hash);
			$records = array();
			foreach ($hash as $record) {
				$records [] = $record;
			}
		}
		return $records;
	}

	/**
	 * Calculate a pricemodification for a variant
	 *
	 * Variant values can be in the following format:
	 * Array ( [Size] => Array ( [XL] => +1 [M] => [S] => -2 ) [Power] => Array ( [strong] => [middle] => [poor] => =24 ) )
	 *
	 * In the post is the data for the chosen variant, when there is a hit, it gets calculated
	 *
	 * Returns all variant modifications summed up or the highest price set with '='
	 *
	 * @todo could be slimmed a bit down, using smaller array for variantnames, this could be done by using the parseModifiers method, needs to adjust the post
	 * @author Max Milbers
	 * @param int $virtuemart_product_id the product ID the attribute price should be calculated for
	 * @param array $variantnames the value of the variant
	 * @return array The adjusted price modificator
	 */
	public function calculateModificators(&$product, $variants) {

		$modificatorSum = 0.0;
		//MarkerVarMods
		foreach ($variants as $selected => $variant) {
			if (!empty($selected)) {

				$query = 'SELECT  C.* , field.*
						FROM `#__virtuemart_customs` AS C
						LEFT JOIN `#__virtuemart_product_customfields` AS field ON C.`virtuemart_custom_id` = field.`virtuemart_custom_id`
						WHERE field.`virtuemart_customfield_id`=' .(int) $selected;
				$this->_db->setQuery($query);
				$productCustomsPrice = $this->_db->loadObject();
				//	echo 'calculateModificators '.$selected.' <pre>'.print_r($productCustomsPrice,1).'</pre>';
// 					vmdebug('calculateModificators',$productCustomsPrice);
				if (!empty($productCustomsPrice) and $productCustomsPrice->field_type =='E') {
					if(!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS.DS.'vmcustomplugin.php');
					JPluginHelper::importPlugin('vmcustom');
					$dispatcher = JDispatcher::getInstance();
					$dispatcher->trigger('plgVmCalculateCustomVariant',array(&$product, &$productCustomsPrice,$selected,$modificatorSum));
				}

				//$app = JFactory::getApplication();
				if (!empty($productCustomsPrice->custom_price)) {
					//TODO adding % and more We should use here $this->interpreteMathOp
					$modificatorSum = $modificatorSum + $productCustomsPrice->custom_price;
				}
			}
		}
// 			echo ' $modificatorSum ',$modificatorSum;
		return $modificatorSum;
	}

	public function parseModifier($name) {

		$variants = array();
		if ($index = strpos($name, '::')) {
			$virtuemart_product_id = substr($name, 0, $index);
			$allItems = substr($name, $index + 2);
			$items = explode(';', $allItems);

			foreach ($items as $item) {
				if (!empty($item)) {
					//vmdebug('parseModifier $item',$item);
					$index2 = strpos($item, ':');
					if($index2!=false){
						$selected = substr($item, 0, $index2);
						$variant = substr($item, $index2 + 1);
						//	echo 'My selected '.$selected;
						//	echo ' My $variant '.$variant.' ';
						//TODO productCartId
						//MarkerVarMods
						$variants[$selected] = $variant; //this works atm not for the cart
						//$variants[$variant] = $selected; //but then the orders are broken
					}
				}
			}
		}
		//vmdebug('parseModifier $variants',$variants);
		return $variants;
	}

}
