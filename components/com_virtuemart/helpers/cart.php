<?php

/**
 *
 * Category model for the cart
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author RolandD
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 6557 2012-10-17 19:16:22Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


/**
 * Model class for the cart
 * Very important, use ALWAYS the getCart function, to get the cart from the session
 * @package	VirtueMart
 * @subpackage Cart
 * @author RolandD
 * @author Max Milbers
 */
class VirtueMartCart {

	//	var $productIds = array();
	var $products = array();
	var $_inCheckOut = false;
	var $_dataValidated = false;
	var $_confirmDone = false;
	var $_lastError = null; // Used to pass errmsg to the cart using addJS()
	//todo multivendor stuff must be set in the add function, first product determines ownership of cart, or a fixed vendor is used
	var $vendorId = 1;
	var $lastVisitedCategoryId = 0;
	var $virtuemart_shipmentmethod_id = 0;
	var $virtuemart_paymentmethod_id = 0;
	var $automaticSelectedShipment = false;
	var $automaticSelectedPayment  = false;
	var $BT = 0;
	var $ST = 0;
	var $tosAccepted = null;
	var $customer_comment = '';
	var $couponCode = '';
	var $cartData = null;
	var $lists = null;
	var $order_number=null; // added to solve emptying cart for payment notification
	// 	var $user = null;
// 	var $prices = null;
	var $pricesUnformatted = null;
	var $pricesCurrency = null;
	var $paymentCurrency = null;
	var $STsameAsBT = 0;
	var $productParentOrderable = TRUE;

	private static $_cart = null;
	private static $_triesValidateCoupon;
	var $useSSL = 1;
	// 	static $first = true;

	private function __construct() {
		$this->useSSL = VmConfig::get('useSSL',0);
		$this->useXHTML = true;
		self::$_triesValidateCoupon=0;
	}

	/**
	 * Get the cart from the session
	 *
	 * @author Max Milbers
	 * @access public
	 * @param array $cart the cart to store in the session
	 */
	public static function getCart($setCart=true, $options = array()) {

		//What does this here? for json stuff?
		if (!class_exists('JTable')
		)require(JPATH_VM_LIBRARIES . DS . 'joomla' . DS . 'database' . DS . 'table.php');
// 		JTable::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'tables');

		if(empty(self::$_cart)){
			$session = JFactory::getSession($options);
			$cartSession = $session->get('vmcart', 0, 'vm');

			if (!empty($cartSession)) {
				$sessionCart = unserialize( $cartSession );

				self::$_cart = new VirtueMartCart;

				self::$_cart->products = $sessionCart->products;
				self::$_cart->vendorId	 							= $sessionCart->vendorId;
				self::$_cart->lastVisitedCategoryId	 			= $sessionCart->lastVisitedCategoryId;
				self::$_cart->virtuemart_shipmentmethod_id	= $sessionCart->virtuemart_shipmentmethod_id;
				self::$_cart->virtuemart_paymentmethod_id 	= $sessionCart->virtuemart_paymentmethod_id;
				self::$_cart->automaticSelectedShipment 		= $sessionCart->automaticSelectedShipment;
				self::$_cart->automaticSelectedPayment 		= $sessionCart->automaticSelectedPayment;
				self::$_cart->BT 										= $sessionCart->BT;
				self::$_cart->ST 										= $sessionCart->ST;
				self::$_cart->tosAccepted 							= $sessionCart->tosAccepted;
				self::$_cart->customer_comment 					= base64_decode($sessionCart->customer_comment);
				self::$_cart->couponCode 							= $sessionCart->couponCode;
				self::$_cart->cartData 								= $sessionCart->cartData;
				self::$_cart->order_number							= $sessionCart->order_number;
				self::$_cart->lists 									= $sessionCart->lists;

				//self::$_cart->pricesUnformatted					= $sessionCart->pricesUnformatted;
				self::$_cart->pricesCurrency						= $sessionCart->pricesCurrency;
				self::$_cart->paymentCurrency						= $sessionCart->paymentCurrency;

				self::$_cart->_inCheckOut 							= $sessionCart->_inCheckOut;
				self::$_cart->_dataValidated						= $sessionCart->_dataValidated;
				self::$_cart->_confirmDone							= $sessionCart->_confirmDone;
				self::$_cart->STsameAsBT							= $sessionCart->STsameAsBT;

			}

		}

		if(empty(self::$_cart)){
			self::$_cart = new VirtueMartCart;
		}

		if ( $setCart == true ) {
			self::$_cart->setPreferred();
			self::$_cart->setCartIntoSession();
		}

		return self::$_cart;
	}

	/*
	 * Set non product info in object
	*/
	public function setPreferred() {

		$usermodel = VmModel::getModel('user');
		$user = $usermodel->getCurrentUser();

		if (empty($this->BT) || (!empty($this->BT) && count($this->BT) <=1) ) {
			foreach ($user->userInfo as $address) {
				if ($address->address_type == 'BT') {
					$this->saveAddressInCart((array) $address, $address->address_type,false);
				}
			}
		}

		if (empty($this->virtuemart_shipmentmethod_id) && !empty($user->virtuemart_shipmentmethod_id)) {
			$this->virtuemart_shipmentmethod_id = $user->virtuemart_shipmentmethod_id;
		}

		if (empty($this->virtuemart_paymentmethod_id) && !empty($user->virtuemart_paymentmethod_id)) {
			$this->virtuemart_paymentmethod_id = $user->virtuemart_paymentmethod_id;
		}

		//$this->tosAccepted is due session stuff always set to 0, so testing for null does not work
		if((!empty($user->agreed) || !empty($this->BT['agreed'])) && !VmConfig::get('agree_to_tos_onorder',0) ){
				$this->tosAccepted = 1;
		}
	}

	/**
	 * Set the cart in the session
	 *
	 * @author RolandD
	 *
	 * @access public
	 * @param array $cart the cart to store in the session
	 */
	public function setCartIntoSession() {

		$session = JFactory::getSession();

		$sessionCart = new stdClass();

		$products = array();
		if ($this->products) {
			foreach($this->products as $key =>$product){

				//Important DO NOT UNSET product_price
				//unset($product->product_price);
				//unset($product->prices);
				unset($product->pricesUnformatted);
				unset($product->mf_name);
				unset($product->mf_desc);
				unset($product->mf_url);

				unset($product->salesPrice);
				unset($product->basePriceWithTax);
				unset($product->subtotal);
				unset($product->subtotal_with_tax);
				unset($product->subtotal_tax_amount);
				unset($product->subtotal_discount);

				unset($product->product_price_vdate);
				unset($product->product_price_edate);
			}
		}
		// 		$sessionCart->products = $products;
		$sessionCart->products = $this->products;
		// 		echo '<pre>'.print_r($products,1).'</pre>';die;
		$sessionCart->vendorId	 							= $this->vendorId;
		$sessionCart->lastVisitedCategoryId	 			= $this->lastVisitedCategoryId;
		$sessionCart->virtuemart_shipmentmethod_id	= $this->virtuemart_shipmentmethod_id;
		$sessionCart->virtuemart_paymentmethod_id 	= $this->virtuemart_paymentmethod_id;
		$sessionCart->automaticSelectedShipment 		= $this->automaticSelectedShipment;
		$sessionCart->automaticSelectedPayment 		= $this->automaticSelectedPayment;
		$sessionCart->order_number 		            = $this->order_number;

		$sessionCart->BT 										= $this->BT;
		$sessionCart->ST 										= $this->ST;
		$sessionCart->tosAccepted 							= $this->tosAccepted;
		$sessionCart->customer_comment 					= base64_encode($this->customer_comment);
		$sessionCart->couponCode 							= $this->couponCode;
		$sessionCart->cartData 								= $this->cartData;
		$sessionCart->lists 									= $this->lists;
		// 		$sessionCart->user 									= $this->user;
// 		$sessionCart->prices 								= $this->prices;
		//$sessionCart->pricesUnformatted					= $this->pricesUnformatted;
		$sessionCart->pricesCurrency						= $this->pricesCurrency;
		$sessionCart->paymentCurrency						= $this->paymentCurrency;

		//private variables
		$sessionCart->_inCheckOut 							= $this->_inCheckOut;
		$sessionCart->_dataValidated						= $this->_dataValidated;
		$sessionCart->_confirmDone							= $this->_confirmDone;
		$sessionCart->STsameAsBT							= $this->STsameAsBT;

		if(!empty($sessionCart->pricesUnformatted)){
			foreach($sessionCart->pricesUnformatted as &$prices){
				if(is_array($prices)){
					foreach($prices as &$price){
						if(!is_array($price)){
							$price = (string)$price;
						}
					}
				} else {
					$prices = (string)$prices;
				}
			}
		}

// 		$pr = serialize($sessionCart->pricesUnformatted);
// 		vmdebug('$sessionCart',$sessionCart);
		$session->set('vmcart', serialize($sessionCart),'vm');

	}

	/**
	 * Remove the cart from the session
	 *
	 * @author Max Milbers
	 * @access public
	 */
	public function removeCartFromSession() {
		$session = JFactory::getSession();
		$session->set('vmcart', 0, 'vm');
	}

	public function setDataValidation($valid=false) {
		$this->_dataValidated = $valid;
		// 		$this->setCartIntoSession();
	}

	public function getDataValidated() {
		return $this->_dataValidated;
	}

	public function getInCheckOut() {
		return $this->_inCheckOut;
	}

	public function setOutOfCheckout(){
		$this->_inCheckOut = false;
		$this->setCartIntoSession();
	}

	/**
	 * Set the last error that occured.
	 * This is used on error to pass back to the cart when addJS() is invoked.
	 * @param string $txt Error message
	 * @author Oscar van Eijk
	 */
	private function setError($txt) {
		$this->_lastError = $txt;
	}

	/**
	 * Retrieve the last error message
	 * @return string The last error message that occured
	 * @author Oscar van Eijk
	 */
	public function getError() {
		return ($this->_lastError);
	}

	/**
	 * Add a product to the cart
	 *
	 * @author RolandD
	 * @author Max Milbers
	 * @access public
	 */
	public function add($virtuemart_product_ids=null,&$errorMsg='') {
		$mainframe = JFactory::getApplication();
		$success = false;
		$post = JRequest::get('default');

		if(empty($virtuemart_product_ids)){
			$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array'); //is sanitized then
		}

		if (empty($virtuemart_product_ids)) {
			$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_CART_ERROR_NO_PRODUCT_IDS', false));
			return false;
		}

		//Iterate through the prod_id's and perform an add to cart for each one
		foreach ($virtuemart_product_ids as $p_key => $virtuemart_product_id) {

			$quantityPost = (int) $post['quantity'][$p_key];

			if($quantityPost === 0) continue;

			$tmpProduct = $this->getProduct((int) $virtuemart_product_id, true, false,true,$quantityPost);

			// trying to save some space in the session table
			$product = new stdClass();
			$product -> virtuemart_manufacturer_id = $tmpProduct -> virtuemart_manufacturer_id;
// 			$product -> mf_name = $tmpProduct -> mf_name;
			$product -> slug = $tmpProduct -> slug;
// 			$product -> mf_desc = $tmpProduct -> mf_desc;
// 			$product -> mf_url = $tmpProduct -> mf_url;
			$product -> published = $tmpProduct -> published;

			$product -> virtuemart_product_price_id = $tmpProduct -> virtuemart_product_price_id;
			$product -> virtuemart_product_id = $tmpProduct -> virtuemart_product_id;
			$product -> virtuemart_shoppergroup_id = $tmpProduct -> virtuemart_shoppergroup_id;
			$product -> product_price = $tmpProduct -> product_price;
			$product -> override = $tmpProduct -> override;
			$product -> product_override_price = $tmpProduct -> product_override_price;

			$product -> product_tax_id = $tmpProduct -> product_tax_id;
			$product -> product_discount_id = $tmpProduct -> product_discount_id;
			$product -> product_currency = $tmpProduct -> product_currency;
// 			$product -> product_price_vdate = $tmpProduct -> product_price_vdate;
// 			$product -> product_price_edate = $tmpProduct -> product_price_edate;
			$product -> virtuemart_vendor_id = $tmpProduct -> virtuemart_vendor_id;
			$product -> product_parent_id = $tmpProduct -> product_parent_id;
			$product -> product_sku = $tmpProduct -> product_sku;
			$product -> product_name = $tmpProduct -> product_name;
			$product -> product_s_desc = $tmpProduct -> product_s_desc;

			$product -> product_weight = $tmpProduct -> product_weight;
			$product -> product_weight_uom = $tmpProduct -> product_weight_uom;
			$product -> product_length = $tmpProduct -> product_length;
			$product -> product_width = $tmpProduct -> product_width;
			$product -> product_height = $tmpProduct -> product_height;
			$product -> product_lwh_uom = $tmpProduct -> product_lwh_uom;

			$product -> product_in_stock = $tmpProduct -> product_in_stock;
			$product -> product_ordered = $tmpProduct -> product_ordered;

			$product -> product_sales = $tmpProduct -> product_sales;
			$product -> product_unit = $tmpProduct -> product_unit;
			$product -> product_packaging = $tmpProduct -> product_packaging;
			$product -> min_order_level = $tmpProduct -> min_order_level;
			$product -> max_order_level = $tmpProduct -> max_order_level;
			$product -> virtuemart_media_id = $tmpProduct -> virtuemart_media_id;
			$product -> step_order_level= $tmpProduct ->step_order_level;

			if(!empty($tmpProduct ->images)) $product->image =  $tmpProduct -> images[0];

			$product -> categories = $tmpProduct -> categories;
			$product -> virtuemart_category_id = $tmpProduct -> virtuemart_category_id;
			$product -> category_name = $tmpProduct -> category_name;

			$product -> link = $tmpProduct -> link;
			$product -> packaging = $tmpProduct -> packaging;
			//$product -> customfields = empty($tmpProduct -> customfields)? array():$tmpProduct -> customfields ;
			//$product -> customfieldsCart = empty($tmpProduct -> customfieldsCart)? array(): $tmpProduct -> customfieldsCart;
			if (!empty($tmpProduct -> customfieldsCart) ) $product -> customfieldsCart = true;
			//$product -> customsChilds = empty($tmpProduct -> customsChilds)? array(): $tmpProduct -> customsChilds;


			//Why reloading the product wiht same name $product ?
			// passed all from $tmpProduct and relaoding it second time ????
			// $tmpProduct = $this->getProduct((int) $virtuemart_product_id); seee before !!!
			// $product = $this->getProduct((int) $virtuemart_product_id);
			// Who ever noted that, yes that is exactly right that way, before we have a full object, with all functions
			// of all its parents, we only need the data of the product, so we create a dummy class which contains only the data
			// This is extremly important for performance reasons, else the sessions becomes too big.
			// Check if we have a product
			if ($product) {
				if(!empty( $post['virtuemart_category_id'][$p_key])){
					$virtuemart_category_idPost = (int) $post['virtuemart_category_id'][$p_key];
					$product->virtuemart_category_id = $virtuemart_category_idPost;
				}

				$productKey = $product->virtuemart_product_id;
				// INDEX NOT FOUND IN JSON HERE
				// changed name field you know exactly was this is
				if (isset($post['customPrice'])) {

					$product->customPrices = $post['customPrice'];
					if (isset($post['customPlugin'])){

						if(!class_exists('vmFilter'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmfilter.php');

						if(!is_array($post['customPlugin'])){
							$customPluginPost = (array)	$post['customPlugin'];
						} else {
							$customPluginPost = $post['customPlugin'];
						}
						VmConfig::$echoDebug=TRUE;

						foreach($customPluginPost as &$customPlugin){
							if(is_array($customPlugin)){
								foreach($customPlugin as &$customPlug){
									if(is_array($customPlug)){
										foreach($customPlug as &$customPl){
											$value = vmFilter::hl( $customPl,array('deny_attribute'=>'*'));
											//to strong
											/* $value = preg_replace('@<[\/\!]*?[^<>]*?>@si','',$value);//remove all html tags  */
											//lets use instead
											$value = JComponentHelper::filterText($value);
											$value = (string)preg_replace('#on[a-z](.+?)\)#si','',$value);//replace start of script onclick() onload()...
											$value = trim(str_replace('"', ' ', $value),"'") ;
											$customPl = (string)preg_replace('#^\'#si','',$value);
										}
									}
								}
							}
						}

						$product->customPlugin = json_encode($customPluginPost);
					}

					$productKey .= '::';

					foreach ($product->customPrices as $customPrice) {
						foreach ($customPrice as $customId => $custom_fieldId) {

							//MarkerVarMods
							if ( is_array($custom_fieldId) ) {
								foreach ($custom_fieldId as $userfieldId => $userfield) {
									//$productKey .= (int)$customId . ':' . (int)$userfieldId . ';';
									$productKey .= (int)$custom_fieldId . ':' .(int)$customId . ';';
								}
							} else {
								//TODO productCartId
								$productKey .= (int)$custom_fieldId . ':' .(int)$customId . ';';
							}

						}
					}

				}

				// Add in the quantity in case the customfield plugins need it
				$product->quantity = (int)$quantityPost;


				if(!class_exists('vmCustomPlugin')) require(JPATH_VM_PLUGINS.DS.'vmcustomplugin.php');
				JPluginHelper::importPlugin('vmcustom');
				$dispatcher = JDispatcher::getInstance();
				// on returning false the product have not to be added to cart
				$addToCartReturnValues = $dispatcher->trigger('plgVmOnAddToCart',array(&$product));
 			    foreach ($addToCartReturnValues as $returnValue) {
						if ( $returnValue === false )
							continue 2;
					}


				if (array_key_exists($productKey, $this->products) && (empty($product->customPlugin)) ) {

					$errorMsg = JText::_('COM_VIRTUEMART_CART_PRODUCT_UPDATED');
					$totalQuantity = $this->products[$productKey]->quantity+ $quantityPost;
					if ($this->checkForQuantities($product,$totalQuantity ,$errorMsg)) {
						$this->products[$productKey]->quantity = $totalQuantity;

					} else {

						continue;
					}
				}  else {
					if ( !empty($product->customPlugin)) {
						$productKey .= count($this->products);

					}
					if ($this->checkForQuantities($product, $quantityPost,$errorMsg)) {
						$this->products[$productKey] = $product;
						$product->quantity = $quantityPost;

						//$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_CART_PRODUCT_ADDED'));
					} else {
						// $errorMsg = JText::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
						continue;
					}
				}
				$success = true;
			} else {
				$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND', false));
				return false;
			}
		}
		if ($success== false) return false ;
		// End Iteration through Prod id's
		$this->setCartIntoSession();
		return true;
	}

	/**
	 * Remove a product from the cart
	 *
	 * @author RolandD
	 * @param array $cart_id the cart IDs to remove from the cart
	 * @access public
	 */
	public function removeProductCart($prod_id=0) {
		// Check for cart IDs
		if (empty($prod_id))
		$prod_id = JRequest::getVar('cart_virtuemart_product_id');
		unset($this->products[$prod_id]);

		$this->setCartIntoSession();
		return true;
	}

	/**
	 * Update a product in the cart
	 *
	 * @author Max Milbers
	 * @param array $cart_id the cart IDs to remove from the cart
	 * @access public
	 */
	public function updateProductCart($cart_virtuemart_product_id=0) {

		if (empty($cart_virtuemart_product_id))
		$cart_virtuemart_product_id = JRequest::getString('cart_virtuemart_product_id');
		if (empty($quantity))
		$quantity = JRequest::getInt('quantity');

		//		foreach($cart_virtuemart_product_ids as $cart_virtuemart_product_id){
		$updated = false;
		if (array_key_exists($cart_virtuemart_product_id, $this->products)) {
			if (!empty($quantity)) {
				if ($this->checkForQuantities($this->products[$cart_virtuemart_product_id], $quantity)) {
					$this->products[$cart_virtuemart_product_id]->quantity = $quantity;
					$updated = true;
				}
			} else {
				//Todo when quantity is 0,  the product should be removed, maybe necessary to gather in array and execute delete func
				unset($this->products[$cart_virtuemart_product_id]);
				$updated = true;
			}
			// Save the cart
			$this->setCartIntoSession();
		}


		if ($updated)
		return true;
		else
		return false;
	}

	/**
	 * Proxy function for getting a product object
	 *
	 * @author Max Milbers
	 * @todo Find out if the include path belongs here? For now it works.
	 * @param int $virtuemart_product_id The product ID to get the object for
	 * @return object The product details object
	 */
	private function getProduct($virtuemart_product_id, $quantity) {
		JModel::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'models');
		$model = JModel::getInstance('Product', 'VirtueMartModel');
		$product = $model->getProduct($virtuemart_product_id, true, false,true,$quantity);

		if ( VmConfig::get('oncheckout_show_images')){
			$model->addImages($product,1);

		}
		return $product;
	}


	/**
	* Get the category ID from a product ID
	*
	* @author RolandD, Patrick Kohl
	* @access public
	* @return mixed if found the category ID else null
	*/
	public function getCardCategoryId($virtuemart_product_id) {
		$db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id` = ' . (int) $virtuemart_product_id . ' LIMIT 1';
		$db->setQuery($q);
		return $db->loadResult();
	}

	/** Checks if the quantity is correct
	 *
	 * @author Max Milbers
	 */
	private function checkForQuantities($product, &$quantity=0,&$errorMsg ='') {

		$stockhandle = VmConfig::get('stockhandle','none');
		$mainframe = JFactory::getApplication();
		// Check for a valid quantity
		if (!is_numeric( $quantity)) {
			$errorMsg = JText::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			//			$this->_error[] = 'Quantity was not a number';
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}
		// Check for negative quantity
		if ($quantity < 1) {
			//			$this->_error[] = 'Quantity under zero';
			$errorMsg = JText::_('COM_VIRTUEMART_CART_ERROR_NO_VALID_QUANTITY', false);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}

		// Check to see if checking stock quantity
		if ($stockhandle!='none' && $stockhandle!='risetime') {

			$productsleft = $product->product_in_stock - $product->product_ordered;
			// TODO $productsleft = $product->product_in_stock - $product->product_ordered - $quantityincart ;
			if ($quantity > $productsleft ){
				if($productsleft>0 and $stockhandle=='disableadd'){
					$quantity = $productsleft;
					$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_QUANTITY',$quantity);
					$this->setError($errorMsg);
					vmInfo($errorMsg.' '.$product->product_name);
					// $mainframe->enqueueMessage($errorMsg);
				} else {
					$errorMsg = JText::_('COM_VIRTUEMART_CART_PRODUCT_OUT_OF_STOCK');
					$this->setError($errorMsg); // Private error retrieved with getError is used only by addJS, so only the latest is fine
					// todo better key string
					vmInfo($errorMsg. ' '.$product->product_name);
					// $mainframe->enqueueMessage($errorMsg);
					return false;
				}
			}
		}

		// Check for the minimum and maximum quantities
		$min = $product->min_order_level;
		if ($min != 0 && $quantity < $min) {
			$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_MIN_ORDER', $min);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}

		$max = $product->max_order_level;
		if ($max != 0 && $quantity > $max) {
			$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_MAX_ORDER', $max);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}

		$step = $product->step_order_level;
		if ($step != 0 && ($quantity%$step)!= 0) {
			$errorMsg = JText::sprintf('COM_VIRTUEMART_CART_STEP_ORDER', $step);
			$this->setError($errorMsg);
			vmInfo($errorMsg,$product->product_name);
			return false;
		}
		return true;
	}


	/**
	 * Validate the coupon code. If ok,. set it in the cart
	 * @param string $coupon_code Coupon code as entered by the user
	 * @author Oscar van Eijk
	 * TODO Change the coupon total/used in DB ?
	 * @access public
	 * @return string On error the message text, otherwise an empty string
	 */
	public function setCouponCode($coupon_code) {
		if (!class_exists('CouponHelper')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'coupon.php');
		}
		$prices = $this->getCartPrices();
		$msg = CouponHelper::ValidateCouponCode($coupon_code, $prices['salesPrice']);
		if (!empty($msg)) {
			$this->couponCode = '';
			$this->setCartIntoSession();
			return $msg;
		}
		$this->couponCode = $coupon_code;
		$this->setCartIntoSession();
		return JText::_('COM_VIRTUEMART_CART_COUPON_VALID');

	}

	/**
	 * Check the selected shipment data and store the info in the cart
	 * @param integer $shipment_id Shipment ID taken from the form data
	 * @author Oscar van Eijk
	 */
	public function setShipment($shipment_id) {

	    $this->virtuemart_shipmentmethod_id = $shipment_id;
	    $this->setCartIntoSession();

	}

	public function setPaymentMethod($virtuemart_paymentmethod_id) {
		$this->virtuemart_paymentmethod_id = $virtuemart_paymentmethod_id;
		$this->setCartIntoSession();
	}

	function confirmDone() {

		$this->checkoutData();
		if ($this->_dataValidated) {
			$this->_confirmDone = true;
			$this->confirmedOrder();
		} else {
			$mainframe = JFactory::getApplication();
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), JText::_('COM_VIRTUEMART_CART_CHECKOUT_DATA_NOT_VALID'));
		}
	}

	function checkout($redirect=true) {

		$this->checkoutData($redirect);
		if ($this->_dataValidated && $redirect) {
			$mainframe = JFactory::getApplication();
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), JText::_('COM_VIRTUEMART_CART_CHECKOUT_DONE_CONFIRM_ORDER'));
		}
	}

	private function redirecter($relUrl,$redirectMsg){

		$this->_dataValidated = false;
		$app = JFactory::getApplication();
		if($this->_redirect ){
			$this->setCartIntoSession();
			$app->redirect(JRoute::_($relUrl,$this->useXHTML,$this->useSSL), $redirectMsg);
		} else {
			$this->_inCheckOut = false;
			$this->setCartIntoSession();
			return false;
		}
	}

	private function checkoutData($redirect = true) {

		$this->_redirect = $redirect;
		$this->_inCheckOut = true;

		$this->tosAccepted = JRequest::getInt('tosAccepted', $this->tosAccepted);
		$this->STsameAsBT = JRequest::getInt('STsameAsBT', $this->STsameAsBT);
		$this->customer_comment = JRequest::getVar('customer_comment', $this->customer_comment);

		// no HTML TAGS but permit all alphabet
		$value =	preg_replace('@<[\/\!]*?[^<>]*?>@si','',$this->customer_comment);//remove all html tags
		$value =	(string)preg_replace('#on[a-z](.+?)\)#si','',$value);//replace start of script onclick() onload()...
		$value = trim(str_replace('"', ' ', $value),"'") ;
		$this->customer_comment=	(string)preg_replace('#^\'#si','',$value);//replace ' at start

		$this->cartData = $this->prepareCartData();
		$this->prepareCartPrice( ) ;

		if (empty($this->tosAccepted)) {

			$userFieldsModel = VmModel::getModel('Userfields');

			$required = $userFieldsModel->getIfRequired('agreed');
			if(!empty($required)){
				$redirectMsg = JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS');
				return $this->redirecter('index.php?option=com_virtuemart&view=cart' , $redirectMsg);
			}
		}

		if (($this->selected_shipto = JRequest::getVar('shipto', null)) !== null) {
			JModel::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'models');
			$userModel = JModel::getInstance('user', 'VirtueMartModel');
			$stData = $userModel->getUserAddressList(0, 'ST', $this->selected_shipto);
			$this->validateUserData('ST', $stData[0]);
		}

		if (count($this->products) == 0) {
			return $this->redirecter('index.php?option=com_virtuemart', JText::_('COM_VIRTUEMART_CART_NO_PRODUCT'));
		} else {
			foreach ($this->products as $product) {
				$redirectMsg = $this->checkForQuantities($product, $product->quantity);
				if (!$redirectMsg) {
					return $this->redirecter('index.php?option=com_virtuemart&view=cart', $redirectMsg);
				}
			}
		}

		// Check if a minimun purchase value is set
		if (($redirectMsg = $this->checkPurchaseValue()) != null) {
			return $this->redirecter('index.php?option=com_virtuemart&view=cart' , $redirectMsg);
		}

		//$this->prepareAddressDataInCart();
		//But we check the data again to be sure
		if (empty($this->BT)) {
			$redirectMsg = '';
			return $this->redirecter('index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=BT' , $redirectMsg);
		} else {
			$redirectMsg = self::validateUserData();
			if (!$redirectMsg) {
				return $this->redirecter('index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=BT' , '');
			}
		}

		if($this->STsameAsBT!==0){
			$this->ST = $this->BT;
		} else {
			//Only when there is an ST data, test if all necessary fields are filled
			if (!empty($this->ST)) {
				$redirectMsg = self::validateUserData('ST');
				if (!$redirectMsg) {
					return $this->redirecter('index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=ST' , '');
				}
			}
		}

		// Test Coupon
		if (!empty($this->couponCode)) {
			$prices = $this->getCartPrices();
			if (!class_exists('CouponHelper')) {
				require(JPATH_VM_SITE . DS . 'helpers' . DS . 'coupon.php');
			}
			if(self::$_triesValidateCoupon<8){
				$redirectMsg = CouponHelper::ValidateCouponCode($this->couponCode, $prices['salesPrice']);
			} else{
				$redirectMsg = JText::_('COM_VIRTUEMART_CART_COUPON_TOO_MANY_TRIES');
			}
			self::$_triesValidateCoupon++;// = self::$_triesValidateCoupon + 1;
			if (!empty($redirectMsg)) {

				$this->couponCode = '';
				return $this->redirecter('index.php?option=com_virtuemart&view=cart&task=edit_coupon' , $redirectMsg);
			}
		}
		$redirectMsg = '';

		//Test Shipment and show shipment plugin
		if (empty($this->virtuemart_shipmentmethod_id)) {
			return $this->redirecter('index.php?option=com_virtuemart&view=cart&task=edit_shipment' , $redirectMsg);
		} else {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			//Add a hook here for other shipment methods, checking the data of the choosed plugin
			$dispatcher = JDispatcher::getInstance();
			$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataShipment', array(  $this));
			//vmdebug('plgVmOnCheckoutCheckDataShipment CART', $retValues);
			foreach ($retValues as $retVal) {
				if ($retVal === true) {
					break; // Plugin completed succesfull; nothing else to do
				} elseif ($retVal === false) {
					// Missing data, ask for it (again)
					return $this->redirecter('index.php?option=com_virtuemart&view=cart&task=edit_shipment' , $redirectMsg);
					// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
				}
			}
		}
		//echo 'hier ';
		//Test Payment and show payment plugin
		if($this->pricesUnformatted['salesPrice']>0.0){
			if (empty($this->virtuemart_paymentmethod_id)) {
				return $this->redirecter('index.php?option=com_virtuemart&view=cart&task=editpayment' , $redirectMsg);
			} else {
				if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
				JPluginHelper::importPlugin('vmpayment');
				//Add a hook here for other payment methods, checking the data of the choosed plugin
				$dispatcher = JDispatcher::getInstance();
				$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataPayment', array( $this));

				foreach ($retValues as $retVal) {
					if ($retVal === true) {
						break; // Plugin completed succesful; nothing else to do
					} elseif ($retVal === false) {
						// Missing data, ask for it (again)
						return $this->redirecter('index.php?option=com_virtuemart&view=cart&task=editpayment' , $redirectMsg);
						// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
					}
				}
			}
		}

		if(VmConfig::get('oncheckout_only_registered',0)) {
			$currentUser = JFactory::getUser();
			if(empty($currentUser->id)){
				$redirectMsg = JText::_('COM_VIRTUEMART_CART_ONLY_REGISTERED');
				return $this->redirecter('index.php?option=com_virtuemart&view=user&task=editaddresscheckout&addrtype=BT' , $redirectMsg);
			}
		 }

		//Show cart and checkout data overview
		$this->_inCheckOut = false;
		$this->_dataValidated = true;

		$this->setCartIntoSession();

		return true;
	}

	/**
	 * Check if a minimum purchase value for this order has been set, and if so, if the current
	 * value is equal or hight than that value.
	 * @author Oscar van Eijk
	 * @return An error message when a minimum value was set that was not eached, null otherwise
	 */
	private function checkPurchaseValue() {

		$vendor = VmModel::getModel('vendor');
		$vendor->setId($this->vendorId);
		$store = $vendor->getVendor();
		if ($store->vendor_min_pov > 0) {
			$prices = $this->getCartPrices();
			if ($prices['salesPrice'] < $store->vendor_min_pov) {
				if (!class_exists('CurrencyDisplay'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				return JText::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE', $currency->priceDisplay($store->vendor_min_pov));
			}
		}
		return null;
	}

	/**
	 * Test userdata if valid
	 *
	 * @author Max Milbers
	 * @param String if BT or ST
	 * @param Object If given, an object with data address data that must be formatted to an array
	 * @return redirectMsg, if there is a redirectMsg, the redirect should be executed after
	 */
	private function validateUserData($type='BT', $obj = null) {

		if(empty($obj)){
			$obj = $this->{$type};
		}

		$usersModel = VmModel::getModel('user');
		return $usersModel->validateUserData($obj,$type);

	}

	/**
	 * This function is called, when the order is confirmed by the shopper.
	 *
	 * Here are the last checks done by payment plugins.
	 * The mails are created and send to vendor and shopper
	 * will show the orderdone page (thank you page)
	 *
	 */
	private function confirmedOrder() {

		//Just to prevent direct call
		if ($this->_dataValidated && $this->_confirmDone) {

			$orderModel = VmModel::getModel('orders');

			if (($orderID = $orderModel->createOrderFromCart($this)) === false) {
				$mainframe = JFactory::getApplication();
				JError::raiseWarning(500, 'No order created '.$orderModel->getError());
				$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart') );
			}
			$this->virtuemart_order_id = $orderID;
			$order= $orderModel->getOrder($orderID);

			$dispatcher = JDispatcher::getInstance();

			JPluginHelper::importPlugin('vmshipment');
			JPluginHelper::importPlugin('vmcustom');
			JPluginHelper::importPlugin('vmpayment');
			JPluginHelper::importPlugin('vmcalculation');
			$returnValues = $dispatcher->trigger('plgVmConfirmedOrder', array($this, $order));
			// may be redirect is done by the payment plugin (eg: paypal)
			// if payment plugin echos a form, false = nothing happen, true= echo form ,
			// 1 = cart should be emptied, 0 cart should not be emptied

		}


	}

	/**
	 * emptyCart: Used for payment handling.
	 *
	 * @author Valerie Cartan Isaksen
	 *
	 */
	public function emptyCart(){

		self::emptyCartValues($this);

		$this->setCartIntoSession();
	}


	/**
	 * emptyCart: Used for payment handling.
	 *
	 * @author Valerie Cartan Isaksen
	 *
	 */
	static public function emptyCartValues($cartData){

		//We delete the old stuff
		$cartData->products = array();
		$cartData->_inCheckOut = false;
		$cartData->_dataValidated = false;
		$cartData->_confirmDone = false;
		$cartData->customer_comment = '';
		$cartData->couponCode = '';
		$cartData->tosAccepted = null;
		$cartData->virtuemart_shipmentmethod_id = 0; //OSP 2012-03-14
		$cartData->virtuemart_paymentmethod_id = 0;
		$cartData->order_number=null;

	}

	function saveAddressInCart($data, $type, $putIntoSession = true) {

		//vmdebug('email $data',$data['email']);
		// VirtueMartModelUserfields::getUserFields() won't work
		if(!class_exists('VirtueMartModelUserfields')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'userfields.php' );
		$userFieldsModel = VmModel::getModel('userfields');
		$prefix = '';

		$prepareUserFields = $userFieldsModel->getUserFieldsFor('cart',$type);

		//STaddress may be obsolete
		if ($type == 'STaddress' || $type =='ST') {
			$prefix = 'shipto_';

		} else { // BT
			if(is_array($data)){
				if(!empty($data['agreed'])){
					$this->tosAccepted = $data['agreed'];
				}

				if(empty($data['email'])){
					$jUser = JFactory::getUser();
					$address['email'] = $jUser->email;
					//vmdebug('email was empty',$address['email']);
				}

			} else {
				if(!empty($data->agreed)){
					$this->tosAccepted = $data->agreed;
				}

				if(empty($data->email)){
					$address['email'] = JFactory::getUser()->email;
				}
			}
		}

		$address = array();
		if(is_array($data)){
			foreach ($prepareUserFields as $fld) {
				if(!empty($fld->name)){
					$name = $fld->name;
					/*if($fld->readonly){
						vmdebug(' saveAddressInCart ',$data[$prefix.$name]);
					}*/
					if($fld->required){
						//vmdebug('saveAddressInCart $prefix='.$prefix.' $name='.$name,$data);
						if(!empty($data[$prefix.$name])){
							$address[$name] = $data[$prefix.$name];
						} else {
							$address[$name] = $this->{$type}[$name];
						}
					} else {
						if(!empty($data[$prefix.$name])){
							$address[$name] = $data[$prefix.$name];
						} else {
							$address[$name] = '';
							//vmdebug('saveAddressInCart the $data[$prefix.$name] ($prefix.$name = '.$prefix.$name.' is empty');
						}
					}
				}
			}

		} else {
			foreach ($prepareUserFields as $fld) {
				if(!empty($fld->name)){
					$name = $fld->name;
					if($fld->required){
						if(!empty($data->{$prefix.$name})){
							$address[$name] = $data->{$prefix.$name};
						} else {
							$address[$name] = $this->{$type}[$name];
						}
					}else {
						$address[$name] = $data->{$prefix.$name};
					}
				}
			}

		}

		//dont store passwords in the session
		unset($address['password']);
		unset($address['password2']);

		$this->{$type} = $address;

		//vmdebug('saveAddressInCart '.$type,$address);

		if($putIntoSession){
			$this->setCartIntoSession();
		}

	}
	/*
	 * CheckAutomaticSelectedShipment
	* If only one shipment is available for this amount, then automatically select it
	*
	* @author Valérie Isaksen
	*/
	function CheckAutomaticSelectedShipment($cart_prices, $checkAutomaticSelected ) {

		if(count($this->products)==0 ) {
			return false;
		}
		$nbShipment = 0;
		$virtuemart_shipmentmethod_id=0;
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

		JPluginHelper::importPlugin('vmshipment');
		if (VmConfig::get('automatic_shipment',1) && $checkAutomaticSelected) {
		    $shipCounter=0;
			$dispatcher = JDispatcher::getInstance();
			$returnValues = $dispatcher->trigger('plgVmOnCheckAutomaticSelectedShipment', array(  $this,$cart_prices, &$shipCounter));
			foreach ($returnValues as $returnValue) {
				 if ( isset($returnValue )) {
					$nbShipment ++;
					if ($returnValue) $virtuemart_shipmentmethod_id = $returnValue;
				}
			}
			if ($nbShipment==1 && $virtuemart_shipmentmethod_id) {
				$this->virtuemart_shipmentmethod_id = $virtuemart_shipmentmethod_id;
				$this->automaticSelectedShipment=true;
				$this->setCartIntoSession();
				return true;
			} else {
				$this->automaticSelectedShipment=false;
				$this->setCartIntoSession();
				return false;
			}
		} else {
			return false;
		}


	}

	/*
	 * CheckAutomaticSelectedPayment
	* If only one payment is available for this amount, then automatically select it
	*
	* @author Valérie Isaksen
	*/
	function CheckAutomaticSelectedPayment($cart_prices,  $checkAutomaticSelected=true) {

		$nbPayment = 0;
		$virtuemart_paymentmethod_id=0;
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		if (VmConfig::get('automatic_payment',1) && $checkAutomaticSelected ) {
			$dispatcher = JDispatcher::getInstance();
			$paymentCounter=0;
			$returnValues = $dispatcher->trigger('plgVmOnCheckAutomaticSelectedPayment', array( $this, $cart_prices, &$paymentCounter));
			foreach ($returnValues as $returnValue) {
				  if ( isset($returnValue )) {
					 $nbPayment++;
					    if($returnValue) $virtuemart_paymentmethod_id = $returnValue;
				     }
			    }
			if ($nbPayment==1 && $virtuemart_paymentmethod_id) {
				$this->virtuemart_paymentmethod_id = $virtuemart_paymentmethod_id;
				$this->automaticSelectedPayment=true;
				$this->setCartIntoSession();
				return true;
			} else {
				$this->automaticSelectedPayment=false;
				$this->setCartIntoSession();
				return false;
			}
		} else {
			return false;
		}

	}

	/*
	 * CheckShipmentIsValid:
	* check if the selected shipment is still valid for this new cart
	*
	* @author Valerie Isaksen
	*/
	function CheckShipmentIsValid() {
		if ($this->virtuemart_shipmentmethod_id===0)
		return;
		$shipmentValid = false;
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnCheckShipmentIsValid', array( $this));
		foreach ($returnValues as $returnValue) {
			$shipmentValid += $returnValue;
		}
		if (!$shipmentValid) {
			$this->virtuemart_shipmentmethod_id = 0;
			$this->setCartIntoSession();
		}
	}



	/*
	 * Prepare the datas for cart/mail views
	* set product, price, user, adress and vendor as Object
	* @author Patrick Kohl
	* @author Valerie Isaksen
	*/
	function prepareCartViewData(){

		// Get the products for the cart
		$this->prepareCartPrice( ) ;

		$this->cartData = $this->prepareCartData();

		$this->prepareAddressDataInCart();
		$this->prepareVendor();

	}

	/**
	 * prepare display of cart
	 *
	 * @author RolandD
	 * @author Max Milbers
	 * @access public
	 */
	public function prepareCartData($checkAutomaticSelected=true){

		// Get the products for the cart
		$product_prices = $this->getCartPrices($checkAutomaticSelected);

		if (empty($product_prices)) return null;
		if(!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance();

		$calculator = calculationHelper::getInstance();

		$this->pricesCurrency = $currency->getCurrencyForDisplay();

		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmgetPaymentCurrency', array( $this->virtuemart_paymentmethod_id, &$this->paymentCurrency));
		$cartData = $calculator->getCartData();

		return $cartData ;
	}

	private function prepareCartPrice( ){

		$productM = VmModel::getModel('product');
		$usermodel = VmModel::getModel ('user');
		$currentVMuser = $usermodel->getCurrentUser ();
		if(!is_array($currentVMuser->shopper_groups)){
			$virtuemart_shoppergroup_ids = (array)$currentVMuser->shopper_groups;
		} else {
			$virtuemart_shoppergroup_ids = $currentVMuser->shopper_groups;
		}

		foreach ($this->products as $cart_item_id=>&$product){

			$product->virtuemart_category_id = $this->getCardCategoryId($product->virtuemart_product_id);
			//$product = $productM->getProduct($product->virtuemart_product_id,true, true, true, $product->quantity);
			$productM->getProductPrices($product,$product->quantity,$virtuemart_shoppergroup_ids,true,true);

			// No full link because Mail want absolute path and in shop is better relative path
			$product->url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$product->virtuemart_category_id);//JHTML::link($url, $product->product_name);
			if(!empty($product->customfieldsCart)){
				if(!class_exists('VirtueMartModelCustomfields'))require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'customfields.php');
				$product->customfields = VirtueMartModelCustomfields::CustomsFieldCartDisplay($cart_item_id,$product);
			} else {
				$product->customfields ='';
			}
			$product->cart_item_id = $cart_item_id ;
		}
	}

	/**
	 * Function Description
	 *
	 * @author Max Milbers
	 * @access public
	 * @param array $cart the cart to get the products for
	 * @return array of product objects
	 */
// 	$this->pricesUnformatted = $product_prices;

	public function getCartPrices($checkAutomaticSelected=true) {

		if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
		$calculator = calculationHelper::getInstance();

		$this->pricesUnformatted = $calculator->getCheckoutPrices($this, $checkAutomaticSelected);

		return $this->pricesUnformatted;
	}

	function prepareAddressDataInCart($type='BT',$new = false){

		$userFieldsModel =VmModel::getModel('Userfields');

		if($new){
			$data = null;
		} else {
			$data = (object)$this->$type;
		}

		if($type=='ST'){
			$preFix = 'shipto_';
		} else {
			$preFix = '';
		}

		$addresstype = $type.'address';
		$userFieldsBT = $userFieldsModel->getUserFieldsFor('cart',$type);
		$this->$addresstype = $userFieldsModel->getUserFieldsFilled(
		$userFieldsBT
		,$data
		,$preFix
		);

		if(!empty($this->ST) && $type!=='ST'){
			$data = (object)$this->ST;
			if($new){
				$data = null;
			}
			$userFieldsST = $userFieldsModel->getUserFieldsFor('cart','ST');
			$this->STaddress = $userFieldsModel->getUserFieldsFilled(
			$userFieldsST
			,$data
			,$preFix
			);
		}

	}

	function prepareAddressRadioSelection(){

		//Just in case
		$this->user = VmModel::getModel('user');

		$this->userDetails = $this->user->getUser();

		$_addressBT = array();

		// Shipment address(es)
		if($this->user){
			$_addressBT = $this->user->getUserAddressList($this->userDetails->JUser->get('id') , 'BT');

			// Overwrite the address name for display purposes
			if(empty($_addressBT[0])) $_addressBT[0] = new stdClass();
			$_addressBT[0]->address_type_name = JText::_('COM_VIRTUEMART_ACC_BILL_DEF');

			$_addressST = $this->user->getUserAddressList($this->userDetails->JUser->get('id') , 'ST');

		} else {

			$_addressBT[0]->address_type_name = '<a href="index.php'
			.'?option=com_virtuemart'
			.'&view=user'
			.'&task=editaddresscart'
			.'&addrtype=BT'
			. '">'.JText::_('COM_VIRTUEMART_ACC_BILL_DEF').'</a>'.'<br />';
			$_addressST = array();
		}

		$addressList = array_merge(
		array($_addressBT[0])// More BT addresses can exist for shopowners :-(
		, $_addressST );

		if($this->user){
			for ($_i = 0; $_i < count($addressList); $_i++) {
				$addressList[$_i]->address_type_name = '<a href="index.php'
				.'?option=com_virtuemart'
				.'&view=user'
				.'&task=editaddresscart'
				.'&addrtype='.(($_i == 0) ? 'BT' : 'ST')
				.'&virtuemart_userinfo_id='.(empty($addressList[$_i]->virtuemart_userinfo_id)? 0 : $addressList[$_i]->virtuemart_userinfo_id)
				. '">'.$addressList[$_i]->address_type_name.'</a>'.'<br />';
			}

			if(!empty($addressList[0]->virtuemart_userinfo_id)){
				$_selectedAddress = (
				empty($this->_cart->selected_shipto)
				? $addressList[0]->virtuemart_userinfo_id // Defaults to 1st BillTo
				: $this->_cart->selected_shipto
				);
				$this->lists['shipTo'] = JHTML::_('select.radiolist', $addressList, 'shipto', null, 'virtuemart_userinfo_id', 'address_type_name', $_selectedAddress);
			}else{
				$_selectedAddress = 0;
				$this->lists['shipTo'] = '';
			}


		} else {
			$_selectedAddress = 0;
			$this->lists['shipTo'] = '';
		}

		$this->lists['billTo'] = empty($addressList[0]->virtuemart_userinfo_id)? 0 : $addressList[0]->virtuemart_userinfo_id;

	}
	/**
	 * moved to shopfunctionf
	 * @deprecated
	 */
	function prepareMailData(){

		if(empty($this->vendor)) $this->prepareVendor();
		//TODO add orders, for the orderId
		//TODO add registering userdata
		// In general we need for every mail the shopperdata (with group), the vendor data, shopperemail, shopperusername, and so on
	}
/**
	 * moved to shopfunctionf
	 * @deprecated
	 */
	// add vendor for cart
	function prepareVendor(){

		$vendorModel = VmModel::getModel('vendor');
		$this->vendor = $vendorModel->getVendor(1);
		$vendorModel->addImages($this->vendor,1);
		return $this->vendor;
	}

	// Render the code for Ajax Cart
	function prepareAjaxData(){
		// Added for the zone shipment module
		//$vars["zone_qty"] = 0;
		$this->prepareCartData(false);
		$weight_total = 0;
		$weight_subtotal = 0;

		//of course, some may argue that the $this->data->products should be generated in the view.html.php, but
		//
		if(empty($this->data)){
			$this->data = new stdClass();
		}
		$this->data->products = array();
		$this->data->totalProduct = 0;
		$i=0;
		//OSP when prices removed needed to format billTotal for AJAX
		if (!class_exists('CurrencyDisplay'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance();

		foreach ($this->products as $priceKey=>$product){

			//$vars["zone_qty"] += $product["quantity"];
			$category_id = $this->getCardCategoryId($product->virtuemart_product_id);
			//Create product URL
			$url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.'&virtuemart_category_id='.$category_id);

			// @todo Add variants
			$this->data->products[$i]['product_name'] = JHTML::link($url, $product->product_name);

			// Add the variants
			if (!is_numeric($priceKey)) {
				if(!class_exists('VirtueMartModelCustomfields'))require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'customfields.php');
				//  custom product fields display for cart
				$this->data->products[$i]['product_attributes'] = VirtueMartModelCustomfields::CustomsFieldCartModDisplay($priceKey,$product);

			}
			$this->data->products[$i]['product_sku'] = $product->product_sku;

			//** @todo WEIGHT CALCULATION
			//$weight_subtotal = vmShipmentMethod::get_weight($product["virtuemart_product_id"]) * $product->quantity'];
			//$weight_total += $weight_subtotal;


			// product Price total for ajax cart
// 			$this->data->products[$i]['prices'] = $this->prices[$priceKey]['subtotal_with_tax'];
			$this->data->products[$i]['pricesUnformatted'] = $this->pricesUnformatted[$priceKey]['subtotal_with_tax'];
			$this->data->products[$i]['prices'] = $currency->priceDisplay( $this->pricesUnformatted[$priceKey]['subtotal_with_tax'] );

			// other possible option to use for display
			$this->data->products[$i]['subtotal'] = $this->pricesUnformatted[$priceKey]['subtotal'];
			$this->data->products[$i]['subtotal_tax_amount'] = $this->pricesUnformatted[$priceKey]['subtotal_tax_amount'];
			$this->data->products[$i]['subtotal_discount'] = $this->pricesUnformatted[$priceKey]['subtotal_discount'];
			$this->data->products[$i]['subtotal_with_tax'] = $this->pricesUnformatted[$priceKey]['subtotal_with_tax'];

			// UPDATE CART / DELETE FROM CART
			$this->data->products[$i]['quantity'] = $product->quantity;
			$this->data->totalProduct += $product->quantity ;

			$i++;
		}
		$this->data->billTotal = $currency->priceDisplay( $this->pricesUnformatted['billTotal'] );
		$this->data->dataValidated = $this->_dataValidated ;
		return $this->data ;
	}
}
