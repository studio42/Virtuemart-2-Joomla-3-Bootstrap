<?php
/**
 *
 * Handle the orders view
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
 * @version $Id: view.html.php 5432 2012-02-14 02:20:35Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JLoader::register('VmView', JPATH_VM_SITE.'/helpers/VmView.php');
JLoader::register('VmImage', JPATH_VM_ADMINISTRATOR.'/helpers/image.php');


/**
 * Handle the orders view
 */
class VirtuemartViewInvoice extends VmView {

	var $format = 'html';
	var $doVendor = false;
	var $uselayout	= '';
	var $orderDetails = 0;
	var $invoiceNumber =0;
	var $doctype = 'invoice';
	var $showHeaderFooter = true;

	public function display($tpl = null)
	{

		$jinput = JFactory::getApplication()->input;
		$pdfTest = $jinput->get('print',0,'INT');

		if(empty($this->uselayout)){
			$layout = JRequest::getWord('layout','mail');
		} else {
			$layout = $this->uselayout;
		}
		switch ($layout) {
			case 'invoice':
				$this->doctype = $layout;
				$title = JText::_('COM_VIRTUEMART_INVOICE');
				break;
			case 'deliverynote':
				$this->doctype = $layout;
				$layout = 'invoice';
				$title = JText::_('COM_VIRTUEMART_DELIVERYNOTE');
				break;
			case 'confirmation':
				$this->doctype = $layout;
				$layout = 'confirmation';
				$title = JText::_('COM_VIRTUEMART_CONFIRMATION');
				break;
			case 'mail':
				if (VmConfig::get('order_mail_html')) {
					$layout = 'mail_html';
				} else {
					$layout = 'mail_raw';
				}
		}
		$this->setLayout($layout);
		$tmpl = $jinput->get('tmpl','','WORD');
		$this->print = false;
		if($tmpl){
			$this->print = true;
		}

		$this->format = $jinput->get('format','html','WORD');
		if($layout == 'invoice'){
			$this->document->setTitle( JText::_('COM_VIRTUEMART_INVOICE') );
		}
		$order_print=false;

		if ($this->print and $this->format=='html') {
			$order_print=true;
		}


		$orderModel = VmModel::getModel('orders');

		$orderDetails = $this->orderDetails;

		if($orderDetails==0){

			$orderDetails = $orderModel ->getMyOrderDetails();

			if(!$orderDetails or empty($orderDetails['details'])){
				echo JText::_('COM_VIRTUEMART_CART_ORDER_NOTFOUND');
				return;
			}


		}

		if(empty($orderDetails['details'])){
			echo JText::_('COM_VIRTUEMART_ORDER_NOTFOUND');
			return 0;
		}

        // if it is order print, invoice number should not be created, either it is there, either it has not been created
		if(empty($this->invoiceNumber) and !$order_print){
		    $invoiceNumberDate=array();
			if (  $orderModel->createInvoiceNumber($orderDetails['details']['BT'], $invoiceNumberDate)) {
                if (ShopFunctions::InvoiceNumberReserved( $invoiceNumberDate[0])) {
	                if  ($this->uselayout!='mail') {
		                $this->document->setTitle( JText::_('COM_VIRTUEMART_PAYMENT_INVOICE') );
                        return ;
	                }
                }
			    $this->invoiceNumber = $invoiceNumberDate[0];
			    $this->invoiceDate = $invoiceNumberDate[1];
			    if(!$this->invoiceNumber or empty($this->invoiceNumber)){
				    vmError('Cant create pdf, createInvoiceNumber failed');
				    if  ($this->uselayout!='mail') {
					    return ;
				    }
			    }
			} else {
				// Could OR should not create Invoice Number, createInvoiceNumber failed
				if  ($this->uselayout!='mail') {
					return ;
				}
			}
		}
		$company= empty($orderDetails['details']['BT']->company) ?"":$orderDetails['details']['BT']->company.", ";
		$shopperName =  $company. $orderDetails['details']['BT']->title.' '.$orderDetails['details']['BT']->first_name.' '.$orderDetails['details']['BT']->last_name;
		$this->shopperName = $shopperName;

		//Todo multix
		$vendorId=1;
		$emailCurrencyId=0;
		$exchangeRate=FALSE;
		JLoader::register('vmPSPlugin', JPATH_VM_PLUGINS.'/vmpsplugin.php');
		  JPluginHelper::importPlugin('vmpayment');
	    $dispatcher = JDispatcher::getInstance();
	    $dispatcher->trigger('plgVmgetEmailCurrency',array( $orderDetails['details']['BT']->virtuemart_paymentmethod_id, $orderDetails['details']['BT']->virtuemart_order_id, &$emailCurrencyId));
		JLoader::register('CurrencyDisplay', JPATH_VM_ADMINISTRATOR.'/helpers/currencydisplay.php');

		$currency = CurrencyDisplay::getInstance($emailCurrencyId,$vendorId);
			if ($emailCurrencyId) {
				$currency->exchangeRateShopper=$orderDetails['details']['BT']->user_currency_rate;
			}
		$this->currency = $currency;

		//Create BT address fields
		$userFieldsModel = VmModel::getModel('userfields');
		$_userFields = $userFieldsModel->getUserFields(
				 'account'
				, array('captcha' => true, 'delimiters' => true) // Ignore these types
				, array('delimiter_userinfo','user_is_vendor' ,'username','password', 'password2', 'agreed', 'address_type') // Skips
		);

		$userfields = $userFieldsModel->getUserFieldsFilled( $_userFields ,$orderDetails['details']['BT']);
		$this->userfields = $userfields;


		//Create ST address fields
		$orderst = (array_key_exists('ST', $orderDetails['details'])) ? $orderDetails['details']['ST'] : $orderDetails['details']['BT'];

		$shipmentFieldset = $userFieldsModel->getUserFields(
				 'shipment'
				, array() // Default switches
				, array('delimiter_userinfo', 'username', 'email', 'password', 'password2', 'agreed', 'address_type') // Skips
		);

		$this->shipmentfields = $userFieldsModel->getUserFieldsFilled( $shipmentFieldset ,$orderst );

		// Create an array to allow orderlinestatuses to be translated
		// We'll probably want to put this somewhere in ShopFunctions..
		$orderStatusModel = VmModel::getModel('orderstatus');
		$_orderstatuses = $orderStatusModel->getOrderStatusList();
		$orderstatuses = array();
		foreach ($_orderstatuses as $_ordstat) {
			$orderstatuses[$_ordstat->order_status_code] = JText::_($_ordstat->order_status_name);
		}
		$this->orderstatuslist = $orderstatuses;
		$this->orderstatuses = $orderstatuses;

		$_itemStatusUpdateFields = array();
		$_itemAttributesUpdateFields = array();
		foreach($orderDetails['items'] as $_item) {
// 			$_itemStatusUpdateFields[$_item->virtuemart_order_item_id] = JHTML::_('select.genericlist', $orderstatuses, "item_id[".$_item->virtuemart_order_item_id."][order_status]", 'class="selectItemStatusCode"', 'order_status_code', 'order_status_name', $_item->order_status, 'order_item_status'.$_item->virtuemart_order_item_id,true);
			$_itemStatusUpdateFields[$_item->virtuemart_order_item_id] =  $_item->order_status;

		}

		if (empty($orderDetails['shipmentName']) ) {
		    JLoader::register('vmPSPlugin', JPATH_VM_PLUGINS.'vmpsplugin.php');
		    JPluginHelper::importPlugin('vmshipment');
		    $dispatcher = JDispatcher::getInstance();
		    $returnValues = $dispatcher->trigger('plgVmOnShowOrderFEShipment',array(  $orderDetails['details']['BT']->virtuemart_order_id, $orderDetails['details']['BT']->virtuemart_shipmentmethod_id, &$orderDetails['shipmentName']));
		}

		if (empty($orderDetails['paymentName']) ) {
		    JLoader::register('vmPSPlugin', JPATH_VM_PLUGINS.'vmpsplugin.php');
		    JPluginHelper::importPlugin('vmpayment');
		    $dispatcher = JDispatcher::getInstance();
		    $returnValues = $dispatcher->trigger('plgVmOnShowOrderFEPayment',array( $orderDetails['details']['BT']->virtuemart_order_id, $orderDetails['details']['BT']->virtuemart_paymentmethod_id,  &$orderDetails['paymentName']));

		}

		$virtuemart_vendor_id=1;
		$vendorModel = VmModel::getModel('vendor');
		$this->vendor = $vendorModel->getVendor($virtuemart_vendor_id);
		$vendorModel->addImages($this->vendor);
		$this->vendor->vendorFields = $vendorModel->getVendorAddressFields();

		$tpl = null;
		if ($this->document->getType() ==="pdf") {
			$tpl = 'pdf';
			$this->document->Set('Creator','Invoice by VirtueMart 2 Bootstrap');
			$this->document->Set('Author', $this->vendor->vendor_name);

			$this->document->Set('Title',JText::_('COM_VIRTUEMART_INVOICE_TITLE').' '.$orderDetails['details']['BT']->order_number);
			$this->document->Set('Subject',JText::sprintf('COM_VIRTUEMART_INVOICE_SUBJ',$this->vendor->vendor_store_name,'',''));
			$this->document->Set('Keywords','Invoice by VirtueMart 2');
			$this->document->setGenerator('Virtuemart 2 Bootstrap');
			$this->document->setTitle(JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') . ' ' . $this->orderDetails['details']['BT']->order_number . ' ' . $this->vendor->vendor_store_name);
			$this->document->setName( JText::_('COM_VIRTUEMART_ACC_ORDER_INFO').' '.$this->orderDetails['details']['BT']->order_number);
			$this->document->setDescription( JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER').' '.$this->orderDetails['details']['BT']->order_number);


			if(empty($this->vendor->images[0])){
				vmError('Vendor image given path empty ');
			} else if(empty($this->vendor->images[0]->file_url_folder) or empty($this->vendor->images[0]->file_name) or empty($this->vendor->images[0]->file_extension) ){
				vmError('Vendor image given image is not complete '.$this->vendor->images[0]->file_url_folder.$this->vendor->images[0]->file_name.'.'.$this->vendor->images[0]->file_extension);
				vmdebug('Vendor image given image is not complete, the given media',$this->vendor->images[0]);
			} else if(!empty($this->vendor->images[0]->file_extension) and strtolower($this->vendor->images[0]->file_extension)=='png'){
				vmError('Warning extension of the image is a png, tpcdf has problems with that in the header, choose a jpg or gif');
			} else {
				$imagePath = DS. str_replace('/',DS, $this->vendor->images[0]->file_url_folder.$this->vendor->images[0]->file_name.'.'.$this->vendor->images[0]->file_extension);
				if(!file_exists(JPATH_ROOT.$imagePath)){
					vmError('Vendor image missing '.$imagePath);
				} else {
					$this->document->Set('HeaderData', $imagePath, 60, $this->vendor->vendor_store_name, $this->vendorAddress);
				}
			}
			// set header and footer fonts
			$this->document->Set('HeaderFont',Array('helvetica', '', 8));
			$this->document->Set('FooterFont',Array('helvetica', '', 10));

			//TODO include the right file (in libraries/tcpdf/config/lang set some language-dependent strings
			$l='';
			$this->document->Set('LanguageArray',$l);

			// set default font subsetting mode
			$this->document->Set('FontSubsetting',true);

			// Set font
			// dejavusans is a UTF-8 Unicode font, if you only need to
			// print standard ASCII chars, you can use core fonts like
			// helvetica or times to reduce file size.
			$this->document->Set('Font','helvetica', '', 8, '', true);
		}
// 		vmdebug('vendor', $vendor);
		if (strpos($layout,'mail') !== false) {
			$lineSeparator="<br />";
		} else {
			$lineSeparator="\n";
		}
		$this->headFooter = $this->showHeaderFooter;

		//Attention, this function will be removed, it wont be deleted, but it is obsoloete in any view.html.php
		JLoader::register('ShopFunctions', JPATH_VM_ADMINISTRATOR.'/helpers/shopfunctions.php');

		$this->vendorAddress= shopFunctions::renderVendorAddress($virtuemart_vendor_id, $lineSeparator);
		$vendorEmail = $vendorModel->getVendorEmail($virtuemart_vendor_id);
		$vars['vendorEmail'] = $vendorEmail;

		// this is no setting in BE to change the layout !
		//shopFunctionsF::setVmTemplate($this,0,0,$layoutName);

		//vmdebug('renderMailLayout invoice '.date('H:i:s'),$this->order);
		if (strpos($layout,'mail') !== false) {
		    if ($this->doVendor) {
		    	 //Old text key COM_VIRTUEMART_MAIL_SUBJ_VENDOR_C
			    $this->subject = JText::sprintf('COM_VIRTUEMART_MAIL_SUBJ_VENDOR_'.$orderDetails['details']['BT']->order_status, $this->shopperName, strip_tags($currency->priceDisplay($orderDetails['details']['BT']->order_total, $currency)), $orderDetails['details']['BT']->order_number);
			    $recipient = 'vendor';
		    } else {
			    $this->subject = JText::sprintf('COM_VIRTUEMART_MAIL_SUBJ_SHOPPER_'.$orderDetails['details']['BT']->order_status, $vendor->vendor_store_name, strip_tags($currency->priceDisplay($orderDetails['details']['BT']->order_total, $currency)), $orderDetails['details']['BT']->order_number );
			    $recipient = 'shopper';
		    }
		    $this->recipient = $recipient;
		}
		$this->orderDetails = $orderDetails;



		parent::display($tpl);
	}

	// FE public function renderMailLayout($doVendor=false)
	function renderMailLayout ($doVendor, $recipient) {

		$this->doVendor=$doVendor;
		$this->frompdf=false;
		$this->uselayout = 'mail';
		$this->display();

	}
	
	static function replaceVendorFields ($txt, $vendor) {
		// TODO: Implement more Placeholders (ordernr, invoicenr, etc.); 
		// REMEMBER TO CHANGE VmVendorPDF::replace_variables IN vmpdf.php, TOO!!!
		// Page nrs. for mails is always "1"
		$txt = str_replace('{vm:pagenum}', "1", $txt);
		$txt = str_replace('{vm:pagecount}', "1", $txt);
		$txt = str_replace('{vm:vendorname}', $vendor->vendor_store_name, $txt);
		$imgrepl='';
		if (!empty($vendor->images)) {
			$img = $vendor->images[0];
			$imgrepl = "<div class=\"vendor-image\">".$img->displayIt($img->file_url,'','',false, '', false, false)."</div>";
		}
		$txt = str_replace('{vm:vendorimage}', $imgrepl, $txt);
		$vendorAddress = shopFunctions::renderVendorAddress($vendor->virtuemart_vendor_id, "<br/>");
		// Trim the final <br/> from the address, which is inserted by renderVendorAddress automatically!
		if (substr($vendorAddress, -5, 5) == '<br/>') {
			$vendorAddress = substr($vendorAddress, 0, -5);
		}
		$txt = str_replace('{vm:vendoraddress}', $vendorAddress, $txt);
		$txt = str_replace('{vm:vendorlegalinfo}', $vendor->vendor_legal_info, $txt);
		$txt = str_replace('{vm:vendordescription}', $vendor->vendor_store_desc, $txt);
		$txt = str_replace('{vm:tos}', $vendor->vendor_terms_of_service, $txt);
		return "$txt";
	}


}
