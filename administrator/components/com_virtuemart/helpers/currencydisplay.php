<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );



/**
 *
 * @version $Id: currencydisplay.php 6566 2012-10-19 16:33:47Z Milbo $
 * @package VirtueMart
 * @subpackage classes
 *
 * @author Max Milbers
 * @copyright Copyright (C) 2004-2008 Soeren Eberhardt-Biermann - All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

class CurrencyDisplay {

	static $_instance;
	private $_currencyConverter;

	private $_currency_id   = '0';		// string ID related with the currency (ex : language)
	private $_symbol    		= 'udef';	// Printable symbol
	private $_nbDecimal 		= 2;	// Number of decimals past colon (or other)
	private $_decimal   		= ',';	// Decimal symbol ('.', ',', ...)
	private $_thousands 		= ' '; 	// Thousands separator ('', ' ', ',')
	private $_positivePos	= '{number}{symbol}';	// Currency symbol position with Positive values :
	private $_negativePos	= '{sign}{number}{symbol}';	// Currency symbol position with Negative values :
	var $_priceConfig	= array();	//holds arrays of 0 and 1 first is if price should be shown, second is rounding
	var $exchangeRateShopper = 1.0;

	private function __construct ($vendorId = 0){

		$this->_app = JFactory::getApplication();
		if(empty($vendorId)) $vendorId = 1;

		$this->_db = JFactory::getDBO();
		$q = 'SELECT `vendor_currency`,`currency_code_3`,`currency_numeric_code` FROM `#__virtuemart_vendors` AS v
		LEFT JOIN `#__virtuemart_currencies` AS c ON virtuemart_currency_id = vendor_currency
		WHERE v.`virtuemart_vendor_id`="'.(int)$vendorId.'"';

		$this->_db->setQuery($q);
		$row = $this->_db->loadRow();
		$this->_vendorCurrency = $row[0];
		$this->_vendorCurrency_code_3 = $row[1];
		$this->_vendorCurrency_numeric = (int)$row[2];

		//vmdebug('$row ',$row);
		$converterFile  = VmConfig::get('currency_converter_module','convertECB.php');

		if (file_exists( JPATH_VM_ADMINISTRATOR.DS.'plugins'.DS.'currency_converter'.DS.$converterFile ) and !is_dir(JPATH_VM_ADMINISTRATOR.DS.'plugins'.DS.'currency_converter'.DS.$converterFile)) {
			$module_filename=substr($converterFile, 0, -4);
			require_once(JPATH_VM_ADMINISTRATOR.DS.'plugins'.DS.'currency_converter'.DS.$converterFile);
			if( class_exists( $module_filename )) {
				$this->_currencyConverter = new $module_filename();
			}
		} else {

			if(!class_exists('convertECB')) require(JPATH_VM_ADMINISTRATOR.DS.'plugins'.DS.'currency_converter'.DS.'convertECB.php');
			$this->_currencyConverter = new convertECB();

		}

	}

	/**
	 *
	 * Gives back the format of the currency, gets $style if none is set, with the currency Id, when nothing is found it tries the vendorId.
	 * When no param is set, you get the format of the mainvendor
	 *
	 * @author Max Milbers
	 * @param int 		$currencyId Id of the currency
	 * @param int 		$vendorId Id of the vendor
	 * @param string 	$style The vendor_currency_display_code
	 *   FORMAT:
	 1: id,
	 2: CurrencySymbol,
	 3: NumberOfDecimalsAfterDecimalSymbol,
	 4: DecimalSymbol,
	 5: Thousands separator
	 6: Currency symbol position with Positive values :
	 7: Currency symbol position with Negative values :

	 EXAMPLE: ||&euro;|2|,||1|8
	 * @return string
	 */
	static public function getInstance($currencyId=0,$vendorId=0){

		// 		vmdebug('hmmmmm getInstance given $currencyId '.$currencyId,self::$_instance->_currency_id);
		// 		if(empty(self::$_instance) || empty(self::$_instance->_currency_id) || ($currencyId!=self::$_instance->_currency_id && !empty($currencyId)) ){

		if(empty(self::$_instance)  || (!empty($currencyId) and $currencyId!=self::$_instance->_currency_id) ){

			self::$_instance = new CurrencyDisplay($vendorId);

			if(empty($currencyId)){

				if(self::$_instance->_app->isSite()){
					self::$_instance->_currency_id = self::$_instance->_app->getUserStateFromRequest( "virtuemart_currency_id", 'virtuemart_currency_id',JRequest::getInt('virtuemart_currency_id', 0));
				}
				if(empty(self::$_instance->_currency_id)){
					self::$_instance->_currency_id = self::$_instance->_vendorCurrency;
				}

			} else {
				self::$_instance->_currency_id = $currencyId;
			}


			$q = 'SELECT * FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="'.(int)self::$_instance->_currency_id.'"';
			self::$_instance->_db->setQuery($q);
			$style = self::$_instance->_db->loadObject();

			if(!empty($style)){
				self::$_instance->setCurrencyDisplayToStyleStr($style);
			} else {
				$uri = JFactory::getURI();

				if(empty(self::$_instance->_currency_id)){
					$link = $uri->root().'administrator/index.php?option=com_virtuemart&view=user&task=editshop';
					JError::raiseWarning('1', JText::sprintf('COM_VIRTUEMART_CONF_WARN_NO_CURRENCY_DEFINED','<a href="'.$link.'">'.$link.'</a>'));
				} else{
					if(JRequest::getWord('view')!='currency'){
						$link = $uri->root().'administrator/index.php?option=com_virtuemart&view=currency&task=edit&cid[]='.self::$_instance->_currency_id;
						JError::raiseWarning('1', JText::sprintf('COM_VIRTUEMART_CONF_WARN_NO_FORMAT_DEFINED','<a href="'.$link.'">'.$link.'</a>'));
					}
				}

				//				self::$_instance->setCurrencyDisplayToStyleStr($currencyId);
				//would be nice to automatically unpublish the product/currency or so
			}
		}
		self::$_instance->setPriceArray();

		return self::$_instance;
	}

	/**
	 * Parse the given currency display string into the currency diplsy values.
	 *
	 * This function takes the currency style string as saved in the vendor
	 * record and parses it into its appropriate values.  An example style
	 * string would be 1|&euro;|2|,|.|0|0
	 *
	 * @author Max Milbers
	 * @param String $currencyStyle String containing the currency display settings
	 */
	private function setCurrencyDisplayToStyleStr($style) {
		//vmdebug('setCurrencyDisplayToStyleStr ',$style);
		$this->_currency_id = $style->virtuemart_currency_id;
		$this->_symbol = $style->currency_symbol;
		$this->_nbDecimal = $style->currency_decimal_place;
		$this->_decimal = $style->currency_decimal_symbol;
		$this->_numeric_code = (int)$style->currency_numeric_code;
		$this->_thousands = $style->currency_thousands;
		$this->_positivePos = $style->currency_positive_style;
		$this->_negativePos = $style->currency_negative_style;

	}

	/**
	 * This function sets an array, which holds the information if
	 * a price is to be shown and the number of rounding digits
	 *
	 * @author Max Milbers
	 */
	function setPriceArray(){

		if(count($this->_priceConfig)>0)return true;

		if(!class_exists('JParameter')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter.php' );

		$user = JFactory::getUser();

		$result = false;
		if(!empty($user->id)){
			$q = 'SELECT `vx`.`virtuemart_shoppergroup_id` FROM `#__virtuemart_vmusers` as `u`
									LEFT OUTER JOIN `#__virtuemart_vmuser_shoppergroups` AS `vx` ON `u`.`virtuemart_user_id`  = `vx`.`virtuemart_user_id`
									LEFT OUTER JOIN `#__virtuemart_shoppergroups` AS `sg` ON `vx`.`virtuemart_shoppergroup_id` = `sg`.`virtuemart_shoppergroup_id`
									WHERE `u`.`virtuemart_user_id` = "'.$user->id.'" ';
			$this->_db->setQuery($q);
			$result = $this->_db->loadResult();
		}

		if(!$result){
			$q = 'SELECT `price_display`,`custom_price_display` FROM `#__virtuemart_shoppergroups` AS `sg`
							WHERE `sg`.`default` = "'.($user->guest+1).'" ';

			$this->_db->setQuery($q);
			$result = $this->_db->loadRow();
		} else {
			$q = 'SELECT `price_display`,`custom_price_display` FROM `#__virtuemart_shoppergroups` AS `sg`
										WHERE `sg`.`virtuemart_shoppergroup_id` = "'.$result.'" ';

			$this->_db->setQuery($q);
			$result = $this->_db->loadRow();
		}

		if(!empty($result[0])){
			$result[0] = unserialize($result[0]);
		}

		$custom_price_display = 0;
		if(!empty($result[1])){
			$custom_price_display = $result[1];
		}

		if($custom_price_display && !empty($result[0])){
			$show_prices = $result[0]->get('show_prices',VmConfig::get('show_prices', 1));
			// 			vmdebug('$result[0]',$result[0],$show_prices);
		} else {
			$show_prices = VmConfig::get('show_prices', 1);
		}



		$priceFields = array('basePrice','variantModification','basePriceVariant',
											'basePriceWithTax','discountedPriceWithoutTax',
											'salesPrice','priceWithoutTax',
											'salesPriceWithDiscount','discountAmount','taxAmount','unitPrice');

		if($show_prices==1){
			foreach($priceFields as $name){
				$show = 0;
				$round = 0;
				$text = 0;

				//Here we check special settings of the shoppergroup
				// 				$result = unserialize($result);
				if($custom_price_display==1){
					$show = (int)$result[0]->get($name);
					$round = (int)$result[0]->get($name.'Rounding');
					$text = $result[0]->get($name.'Text');
					// 					vmdebug('$custom_price_display');
				} else {
					$show = VmConfig::get($name,0);
					$round = VmConfig::get($name.'Rounding',2);
					$text = VmConfig::get($name.'Text',0);
					// 					vmdebug('$config_price_display');
				}

				//Map to currency
				if($round==-1){
					$round = $this->_nbDecimal;
					//vmdebug('Use currency rounding '.$round);
				}
				$this->_priceConfig[$name] = array($show,$round,$text);
			}
		} else {
			foreach($priceFields as $name){
				$this->_priceConfig[$name] = array(0,0,0);
			}
		}

		// 		vmdebug('$this->_priceConfig',$this->_priceConfig);
	}

	/**
	 * getCurrencyForDisplay: get The actual displayed Currency
	 * Use this only in a view, plugin or modul, never in a model
	 *
	 * @param integer $currencyId
	 * return integer $currencyId: displayed Currency
	 *
	 */
	public function getCurrencyForDisplay( $currencyId=0 ){

		if(empty($currencyId)){
			$currencyId = (int)$this->_app->getUserStateFromRequest( 'virtuemart_currency_id', 'virtuemart_currency_id',$this->_vendorCurrency );
			if(empty($currencyId)){
				$currencyId = $this->_vendorCurrency;
			}
		}

		return $currencyId;
	}

	/**
	 * This function is for the gui only!
	 * Use this only in a view, plugin or modul, never in a model
	 * TODO for vm2.2 remove quantity option
	 * @param float $price
	 * @param integer $currencyId
	 * return string formatted price
	 */
	public function priceDisplay($price, $currencyId=0,$quantity = 1.0,$inToShopCurrency = false,$nb= -1){

		$currencyId = $this->getCurrencyForDisplay($currencyId);

		if($nb==-1){
			$nb = $this->_nbDecimal;
		}

		//vmdebug('priceDisplay',$quantity);
	/*	if($this->_vendorCurrency_numeric===756){ // and $this->_numeric_code!==$this->_vendorCurrency_numeric){
			$price = round((float)$price * 2,1) * 0.5 * (float)$quantity;
		} else {*/
			$price = round((float)$price,$nb) * (float)$quantity;
		//}
		$price = $this->convertCurrencyTo($currencyId,$price,$inToShopCurrency);

		if($this->_numeric_code===756 and VmConfig::get('rappenrundung',FALSE)=="1"){
			$price = round((float)$price * 2,1) * 0.5;
		}//*/
		return $this->getFormattedCurrency($price,$nb);
	}

	/**
	 * Format, Round and Display Value
	 * @author Max Milbers
	 * @param val number
	 */
	private function getFormattedCurrency( $nb, $nbDecimal=-1){

		//TODO $this->_nbDecimal is the config of the currency and $nbDecimal is the config of the price type.
		if($nbDecimal==-1) $nbDecimal = $this->_nbDecimal;
		if($nb>=0){
			$format = $this->_positivePos;
			$sign = '+';
		} else {
			$format = $this->_negativePos;
			$sign = '-';
			$nb = abs($nb);
		}

		//$res = $this->formatNumber($nb, $nbDecimal, $this->_thousands, $this->_decimal);
		$res = number_format((float)$nb,(int)$nbDecimal,$this->_decimal,$this->_thousands);
		$search = array('{sign}', '{number}', '{symbol}');
		$replace = array($sign, $res, $this->_symbol);
		$formattedRounded = str_replace ($search,$replace,$format);

		return $formattedRounded;
	}

	/**
	 * function to create a div to show the prices, is necessary for JS
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 * @param string name of the price
	 * @param String description key
	 * @param array the prices of the product
	 * return a div for prices which is visible according to config and have all ids and class set
	 */
	public function createPriceDiv($name,$description,$product_price,$priceOnly=false,$switchSequel=false,$quantity = 1.0,$forceNoLabel=false){

		// 		vmdebug('createPriceDiv '.$name,$product_price[$name]);
		if(empty($product_price) and $name != 'billTotal') return '';

		//The fallback, when this price is not configured
		if(empty($this->_priceConfig[$name])){
			$this->_priceConfig[$name] = $this->_priceConfig['salesPrice'];
		}

		//This is a fallback because we removed the "salesPriceWithDiscount" ;
		if(is_array($product_price)){
			$price = $product_price[$name] ;
		} else {
			$price = $product_price;
		}

		//This could be easily extended by product specific settings
		if(!empty($this->_priceConfig[$name][0])){
			if(!empty($price) or $name == 'billTotal'){
				$vis = "block";
				$priceFormatted = $this->priceDisplay($price,0,(float)$quantity,false,$this->_priceConfig[$name][1],$name );
			} else {
				$priceFormatted = '';
				$vis = "none";
			}
			if($priceOnly){
				return $priceFormatted;
			}
			if($forceNoLabel) {
				return '<div class="Price'.$name.'" style="display : '.$vis.';" ><span class="Price'.$name.'" >'.$priceFormatted.'</span></div>';
			}
			$descr = '';
			if($this->_priceConfig[$name][2]) $descr = JText::_($description);
			// 			vmdebug('createPriceDiv $name '.$name.' '.$product_price[$name]);
			if(!$switchSequel){
				return '<div class="Price'.$name.'" style="display : '.$vis.';" >'.$descr.'<span class="Price'.$name.'" >'.$priceFormatted.'</span></div>';
			} else {
				return '<div class="Price'.$name.'" style="display : '.$vis.';" ><span class="Price'.$name.'" >'.$priceFormatted.'</span>'.$descr.'</div>';
			}
		}

	}

	/**
	 *
	 * @author Max Milbers
	 * @param unknown_type $currency
	 * @param unknown_type $price
	 * @param unknown_type $shop
	 */
	function convertCurrencyTo($currency,$price,$shop=true){


		if(empty($currency)){
			// 			vmdebug('empty  $currency ',$price);
			return $price;
		}

		// If both currency codes match, do nothing
		if( (is_Object($currency) and $currency->_currency_id == $this->_vendorCurrency)  or (!is_Object($currency) and $currency == $this->_vendorCurrency)) {
			// 			vmdebug('  $currency == $this->_vendorCurrency ',$price);
			return $price;
		}

		if(is_Object($currency)){
			$exchangeRate = (float)$currency->exchangeRateShopper;
			vmdebug('convertCurrencyTo OBJECT '.$exchangeRate);
		}
		else {
			//				$this->_db = JFactory::getDBO();
			$q = 'SELECT `currency_exchange_rate` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` ="'.(int)$currency.'" ';
			$this->_db->setQuery($q);
			$exch = (float)$this->_db->loadResult();
			// 				vmdebug('begin convertCurrencyTo '.$exch);
			if(!empty($exch)){
				$exchangeRate = $exch;
			} else {
				$exchangeRate = 0;
			}
		}

		if(!empty($exchangeRate) ){

			if($shop){
				$price = $price / $exchangeRate;
			} else {
				$price = $price * $exchangeRate;
			}

		} else {
			$currencyCode = self::ensureUsingCurrencyCode($currency);
			$vendorCurrencyCode = self::ensureUsingCurrencyCode($this->_vendorCurrency);
			$globalCurrencyConverter=JRequest::getVar('globalCurrencyConverter');
			if($shop){
				$price = $this ->_currencyConverter->convert( $price, $currencyCode, $vendorCurrencyCode);
				//vmdebug('convertCurrencyTo Use dynamic rate in shop '.$oldprice .' => '.$price);
			} else {
				//vmdebug('convertCurrencyTo Use dynamic rate to shopper currency '.$price);
				$price = $this ->_currencyConverter->convert( $price , $vendorCurrencyCode, $currencyCode);
			}
			// 			vmdebug('convertCurrencyTo my currency ',$this->exchangeRateShopper);
		}

		return $price;
	}


	/**
	 * Changes the virtuemart_currency_id into the right currency_code
	 * For exampel 47 => EUR
	 *
	 * @author Max Milbers
	 * @author Frederic Bidon
	 */
	function ensureUsingCurrencyCode($curr){

		if(is_numeric($curr) and $curr!=0){
			$this->_db = JFactory::getDBO();
			$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="'.(int)$curr.'"';
			$this->_db->setQuery($q);
			$currInt = $this->_db->loadResult();
			if(empty($currInt)){
				JError::raiseWarning(E_WARNING,'Attention, could not find currency code in the table for id = '.$curr);
			}
			return $currInt;
		}
		return $curr;
	}

	/**
	 * Changes the currency_code into the right virtuemart_currency_id
	 * For exampel 'currency_code_3' : EUR => 47
	 *
	 * @author Max Milbers
	 * @author Kohl Patrick
	 */
	function getCurrencyIdByField($value=0,$fieldName ='currency_code_3'){

		if(is_string($value) ){
			$this->_db = JFactory::getDBO();
			$q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies` WHERE `'.$fieldName.'`="'.$value.'"';
			$this->_db->setQuery($q);
			$currency_id = $this->_db->loadResult();
			if(empty($currency_id)){
				JError::raiseWarning(E_WARNING,'Attention, couldnt find currency_id in the table for '.$fieldName.' = '.$value);
			}
			return $currency_id;
		}
		return $value;
	}



	/**
	 *
	 * @author Horvath, Sandor [HU] http://de.php.net/manual/de/function.number-format.php
	 * @author Max Milbers
	 * @param double $number
	 * @param int $decimals
	 * @param string $thousand_separator
	 * @param string $decimal_point
	 */
	function formatNumber($number, $decimals = 2, $decimal_point = '.', $thousand_separator = '&nbsp;' ){

		//    	$tmp1 = round((float) $number, $decimals);

		return number_format($number,$decimals,$decimal_point,$thousand_separator);
		//		while (($tmp2 = preg_replace('/(\d+)(\d\d\d)/', '\1 \2', $tmp1)) != $tmp1){
		//			$tmp1 = $tmp2;
		//		}
		//
		//		return strtr($tmp1, array(' ' => $thousand_separator, '.' => $decimal_point));
	}

	/**
	 * Return the currency symbol
	 */
	public function getSymbol() {
		return($this->_symbol);
	}

	/**
	 * Return the currency ID
	 */
	public function getId() {
		return($this->_currency_id);
	}

	/**
	 * Return the number of decimal places
	 *
	 * @author RickG
	 * @return int Number of decimal places
	 */
	public function getNbrDecimals() {
		return($this->_nbDecimal);
	}

	/**
	 * Return the decimal symbol
	 *
	 * @author RickG
	 * @return string Decimal place symbol
	 */
	public function getDecimalSymbol() {
		return($this->_decimal);
	}

	/**
	 * Return the decimal symbol
	 *
	 * @author RickG
	 * @return string Decimal place symbol
	 */
	public function getThousandsSeperator() {
		return($this->_thousands);
	}

	/**
	 * Return the positive format
	 *
	 * @author RickG
	 * @return string Positive number format
	 */
	public function getPositiveFormat() {
		return($this->_positivePos);
	}

	/**
	 * Return the negative format
	 *
	 * @author RickG
	 * @return string Negative number format
	 */
	public function getNegativeFormat() {
		return($this->_negativePos);
	}



}
// pure php no closing tag
