<?php
/**
 * Generate orderdetails in Raw format for printing
 *
 * @package	VirtueMart
 * @subpackage Orders
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.raw.php 5522 2012-02-21 14:40:10Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

class VirtuemartViewOrders extends VmView {

	function display($tpl = null) {

		//Load helpers

		$this->loadHelper('currencydisplay');

		$this->loadHelper('html');

		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		$this->virtuemart_order_id = JRequest::getvar('virtuemart_order_id',null);
		$model = VmModel::getModel();

		if ($this->virtuemart_order_id === null) {
			// load ajax results
			$tpl ='results'; 
			$this->addStandardDefaultViewLists($model,'created_on');
			$this->lists['state_list'] = $this->renderOrderstatesList();
			$orderslist = $model->getOrdersList();

			$orderStatusModel=VmModel::getModel('orderstatus');
			$orderStates = $orderStatusModel->getOrderStatusList();
			$this->orderstatuses = $orderStates;

			if(!class_exists('CurrencyDisplay'))require(JPATH_VM_ADMINISTRATOR.'/helpers'.DS.'currencydisplay.php');

			/* Apply currency This must be done per order since it's vendor specific */
			$_currencies = array(); // Save the currency data during this loop for performance reasons

			if ($orderslist) {

			    foreach ($orderslist as $virtuemart_order_id => $order) {

				    if(!empty($order->order_currency)){
					    $currency = $order->order_currency;
				    } else if($order->virtuemart_vendor_id){
					    if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.'/models'.DS.'vendor.php');
					    $currObj = VirtueMartModelVendor::getVendorCurrency($order->virtuemart_vendor_id);
				        $currency = $currObj->virtuemart_currency_id;
				   }
				    //This is really interesting for multi-X, but I avoid to support it now already, lets stay it in the code
				    if (!array_key_exists('curr'.$currency, $_currencies)) {

					    $_currencies['curr'.$currency] = CurrencyDisplay::getInstance($currency,$order->virtuemart_vendor_id);
				    }

				    $order->order_total = $_currencies['curr'.$currency]->priceDisplay($order->order_total);
				    $order->invoiceNumber = $model->getInvoiceNumber($order->virtuemart_order_id);
			    }

			}

			/* Assign the data */
			$this->orderslist = $orderslist ;

			$this->pagination = $model->getPagination();
			parent::display('results');
			$scripts = 'jQuery("#results select").chosen();';
			echo $this->AjaxScripts($scripts);
			
		}
		else
		{
		// Note patrick Kohl. to finish  A big Spagetti to know what value is really needed
			// Load addl models
			$userFieldsModel = VmModel::getModel('userfields');
			$productModel = VmModel::getModel('product');

			/* Get the data */

			$order = $model->getOrder($this->virtuemart_order_id);
			//$order = $this->get('Order');
			$orderNumber = $order['details']['BT']->virtuemart_order_number;
			$orderbt = $order['details']['BT'];
			$orderst = (array_key_exists('ST', $order['details'])) ? $order['details']['ST'] : $orderbt;

			$currency = CurrencyDisplay::getInstance('',$order['details']['BT']->virtuemart_vendor_id);
			$this->currency = $currency;


			$_userFields = $userFieldsModel->getUserFields(
					 'registration'
					, array('captcha' => true, 'delimiters' => true) // Ignore these types
					, array('delimiter_userinfo','user_is_vendor' ,'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
			);
			$userfields = $userFieldsModel->getUserFieldsFilled(
					 $_userFields
					,$orderbt
			);
			$_userFields = $userFieldsModel->getUserFields(
					 'shipment'
					, array() // Default switches
					, array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
			);
			$shipmentfields = $userFieldsModel->getUserFieldsFilled(
					 $_userFields
					,$orderst
			);

			// Create an array to allow orderlinestatuses to be translated
			// We'll probably want to put this somewhere in ShopFunctions...
			$_orderStats = $this->get('OrderStatusList');
			$_orderStatusList = array();
			foreach ($_orderStats as $orderState) {
					$_orderStatusList[$orderState->order_status_code] = JText::_($orderState->order_status_name);
			}

			/*foreach($order['items'] as $_item) {
				if (!empty($_item->product_attribute)) {
					$_attribs = preg_split('/\s?<br\s*\/?>\s?/i', $_item->product_attribute);

					$product = $productModel->getProduct($_item->virtuemart_product_id);
					$_productAttributes = array();
					$_prodAttribs = explode(';', $product->attribute);
					foreach ($_prodAttribs as $_pAttr) {
						$_list = explode(',', $_pAttr);
						$_name = array_shift($_list);
						$_productAttributes[$_item->virtuemart_order_item_id][$_name] = array();
						foreach ($_list as $_opt) {
							$_optObj = new stdClass();
							$_optObj->option = $_opt;
							$_productAttributes[$_item->virtuemart_order_item_id][$_name][] = $_optObj;
						}
					}
				}
			}*/
			//$_shipmentInfo = ShopFunctions::getShipmentRateDetails($orderbt->virtuemart_shipmentmethod_id);

			/* Assign the data */
			$this->orderdetails = $order;
			$this->orderNumber = $orderNumber;
			$this->userfields = $userfields;
			$this->shipmentfields = $shipmentfields;
			$this->orderstatuslist = $_orderStatusList;
			$this->orderbt = $orderbt;
			$this->orderst = $orderst;
			$this->virtuemart_shipmentmethod_id = $orderbt->virtuemart_shipmentmethod_id;
//Note Patrick Kohl , why error reporting 0 in one case in all vm2 and here ???
			error_reporting(0);
			parent::display($tpl);
		}
		
	}
	public function renderOrderstatesList() {
		$orderstates = JRequest::getWord('order_status_code','');
		$query = 'SELECT `order_status_code` as value, `order_status_name` as text
			FROM `#__virtuemart_orderstates`
			WHERE published=1 ' ;
			$db = JFactory::getDBO();
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return VmHTML::select( 'order_status_code', $list,  $orderstates,'class="inputbox" onchange="this.form.submit();"');
    }
}

