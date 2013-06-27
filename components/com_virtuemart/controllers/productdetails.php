<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: productdetails.php 6425 2012-09-11 20:17:08Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport ('joomla.application.component.controller');

/**
 * VirtueMart Component Controller
 *
 * @package VirtueMart
 * @author RolandD
 */
class VirtueMartControllerProductdetails extends JController {

	public function __construct () {

		parent::__construct ();
		$this->registerTask ('recommend', 'MailForm');
		$this->registerTask ('askquestion', 'MailForm');
	}

	function display($cachable = false, $urlparams = false)  {

		$format = JRequest::getWord ('format', 'html');
		if ($format == 'pdf') {
			$viewName = 'Pdf';
		} else {
			$viewName = 'Productdetails';
		}

		$view = $this->getView ($viewName, $format);

		$view->display ();
	}

	/**
	 * Send the ask question email.
	 *
	 * @author Kohl Patrick, Christopher Roussel
	 */
	public function mailAskquestion () {

		// Display it all
		$view = $this->getView ('askquestion', 'html');
		if(!VmConfig::get('ask_question',false)){
			$view->display ();
		}
		JRequest::checkToken () or jexit ('Invalid Token');
		if (!class_exists ('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}
		$mainframe = JFactory::getApplication ();
		$vars = array();
		$min = VmConfig::get ('asks_minimum_comment_length', 50) + 1;
		$max = VmConfig::get ('asks_maximum_comment_length', 2000) - 1;
		$commentSize = mb_strlen (JRequest::getString ('comment'));
		$validMail = filter_var (JRequest::getVar ('email'), FILTER_VALIDATE_EMAIL);

		if ($commentSize < $min or $commentSize > $max or !$validMail) {
			$errmsg = JText::_ ('COM_VIRTUEMART_COMMENT_NOT_VALID_JS');
			if ($commentSize < $min) {
				vmdebug ('mailAskquestion', $min, $commentSize);
				$errmsg = JText::_ ('COM_VIRTUEMART_ASKQU_CS_MIN');
				;
			} else {
				if ($commentSize > $max) {
					$errmsg = JText::_ ('COM_VIRTUEMART_ASKQU_CS_MAX');
					;
				} else {
					if (!$validMail) {
						$errmsg = JText::_ ('COM_VIRTUEMART_ASKQU_INV_MAIL');
						;
					}
				}
			}

			$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&tmpl=component&view=productdetails&task=askquestion&virtuemart_product_id=' . JRequest::getInt ('virtuemart_product_id', 0)), $errmsg);
			return;
		}

		$virtuemart_product_idArray = JRequest::getInt ('virtuemart_product_id', 0);
		if (is_array ($virtuemart_product_idArray)) {
			$virtuemart_product_id = (int)$virtuemart_product_idArray[0];
		} else {
			$virtuemart_product_id = (int)$virtuemart_product_idArray;
		}
		$productModel = VmModel::getModel ('product');

		$vars['product'] = $productModel->getProduct ($virtuemart_product_id);

		$user = JFactory::getUser ();
		if (empty($user->id)) {
			$fromMail = JRequest::getVar ('email'); //is sanitized then
			$fromName = JRequest::getVar ('name', ''); //is sanitized then
			$fromMail = str_replace (array('\'', '"', ',', '%', '*', '/', '\\', '?', '^', '`', '{', '}', '|', '~'), array(''), $fromMail);
			$fromName = str_replace (array('\'', '"', ',', '%', '*', '/', '\\', '?', '^', '`', '{', '}', '|', '~'), array(''), $fromName);
		} else {
			$fromMail = $user->email;
			$fromName = $user->name;
		}
		$vars['user'] = array('name' => $fromName, 'email' => $fromMail);

		$vendorModel = VmModel::getModel ('vendor');
		$VendorEmail = $vendorModel->getVendorEmail ($vars['product']->virtuemart_vendor_id);
		$vars['vendor'] = array('vendor_store_name' => $fromName);

		if (shopFunctionsF::renderMail ('askquestion', $VendorEmail, $vars, 'productdetails')) {
			$string = 'COM_VIRTUEMART_MAIL_SEND_SUCCESSFULLY';
		} else {
			$string = 'COM_VIRTUEMART_MAIL_NOT_SEND_SUCCESSFULLY';
		}
		$mainframe->enqueueMessage (JText::_ ($string));


		$view->setLayout ('mail_confirmed');
		$view->display ();
	}

	/**
	 * Send the Recommend to a friend email.
	 *
	 * @author Kohl Patrick,
	 */
	public function mailRecommend () {

		JRequest::checkToken () or jexit ('Invalid Token');
		// Display it all
		$view = $this->getView ('recommend', 'html');

		if(!VmConfig::get('show_emailfriend',false)){
			$view->display ();
		}
		if (!class_exists ('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}
		if(!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');

		$mainframe = JFactory::getApplication ();
		$vars = array();

		$virtuemart_product_idArray = JRequest::getInt ('virtuemart_product_id', 0);
		if (is_array ($virtuemart_product_idArray)) {
			$virtuemart_product_id = (int)$virtuemart_product_idArray[0];
		} else {
			$virtuemart_product_id = (int)$virtuemart_product_idArray;
		}
		$productModel = VmModel::getModel ('product');

		$vars['product'] = $productModel->getProduct ($virtuemart_product_id);

		$user = JFactory::getUser ();
		$vars['user'] = array('name' => $user->name, 'email' =>  $user->email);

		$vars['vendorEmail'] = $user->email;
		$vendorModel = VmModel::getModel ('vendor');
		$vendor = $vendorModel->getVendor ($vars['product']->virtuemart_vendor_id);
		$vendorModel->addImages ($vars['vendor']);
		$vendor->vendorFields = $vendorModel->getVendorAddressFields();
		$vars['vendor'] = $vendor;
		$vars['vendorAddress']= shopFunctions::renderVendorAddress($vars['product']->virtuemart_vendor_id);

		$vars['vendorEmail']=  $user->email;
		$vars['vendor']->vendor_name =$user->name;


		$toMail = JRequest::getVar ('email'); //is sanitized then
		$toMail = str_replace (array('\'', '"', ',', '%', '*', '/', '\\', '?', '^', '`', '{', '}', '|', '~'), array(''), $toMail);

		if (shopFunctionsF::renderMail ('recommend', $toMail, $vars, 'productdetails', TRUE)) {
			$string = 'COM_VIRTUEMART_MAIL_SEND_SUCCESSFULLY';
		} else {
			$string = 'COM_VIRTUEMART_MAIL_NOT_SEND_SUCCESSFULLY';
		}
		$mainframe->enqueueMessage (JText::_ ($string));

// 		vmdebug('my email vars ',$vars,$TOMail);


		$view->setLayout ('mail_confirmed');
		$view->display ();
	}

	/**
	 *  Ask Question form
	 * Recommend form for Mail
	 */
	public function MailForm () {

		if (JRequest::getCmd ('task') == 'recommend') {

			/*OSP 2012-03-14 ...Track #375; allowed by setting */
			if (VmConfig::get ('recommend_unauth', 0) == '0') {
				$user = JFactory::getUser ();
				if (empty($user->id)) {
					VmInfo (JText::_ ('JGLOBAL_YOU_MUST_LOGIN_FIRST'));
					return;
				}
			}
			$view = $this->getView ('recommend', 'html');
		} else {
			$view = $this->getView ('askquestion', 'html');
		}

		/* Set the layout */
		$view->setLayout ('form');

		// Display it all
		$view->display ();
	}

	/* Add or edit a review
	 TODO  control and update in database the review */
	public function review () {

		$data = JRequest::get ('post');

		$model = VmModel::getModel ('ratings');
		$model->saveRating ($data);
		$errors = $model->getErrors ();
		if (empty($errors)) {
			$msg = JText::sprintf ('COM_VIRTUEMART_STRING_SAVED', JText::_ ('COM_VIRTUEMART_REVIEW'));
		}
		foreach ($errors as $error) {
			$msg = ($error) . '<br />';
		}

		$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . (int)$data['virtuemart_product_id']), $msg);

	}

	/**
	 * Json task for recalculation of prices
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 *
	 */
	public function recalculate () {

		//$post = JRequest::get('request');

//		echo '<pre>'.print_r($post,1).'</pre>';
		jimport ('joomla.utilities.arrayhelper');
		$virtuemart_product_idArray = JRequest::getVar ('virtuemart_product_id', array()); //is sanitized then
		if(is_array($virtuemart_product_idArray)){
			JArrayHelper::toInteger ($virtuemart_product_idArray);
			$virtuemart_product_id = $virtuemart_product_idArray[0];
		} else {
			$virtuemart_product_id = $virtuemart_product_idArray;
		}

		$customPrices = array();
		$customVariants = JRequest::getVar ('customPrice', array()); //is sanitized then
		//echo '<pre>'.print_r($customVariants,1).'</pre>';

		//MarkerVarMods
		foreach ($customVariants as $customVariant) {
			//foreach ($customVariant as $selected => $priceVariant) {
			//In this case it is NOT $selected => $variant, because we get it that way from the form
			foreach ($customVariant as $priceVariant => $selected) {
				//Important! sanitize array to int
				$selected = (int)$selected;
				$customPrices[$selected] = $priceVariant;
			}
		}

		$quantityArray = JRequest::getVar ('quantity', array()); //is sanitized then
		JArrayHelper::toInteger ($quantityArray);

		$quantity = 1;
		if (!empty($quantityArray[0])) {
			$quantity = $quantityArray[0];
		}

		$product_model = VmModel::getModel ('product');

		//VmConfig::$echoDebug = TRUE;
		$prices = $product_model->getPrice ($virtuemart_product_id, $customPrices, $quantity);

		$priceFormated = array();
		if (!class_exists ('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance ();
		foreach ($prices as $name => $product_price) {
// 		echo 'Price is '.print_r($name,1).'<br />';
			if ($name != 'costPrice') {
				$priceFormated[$name] = $currency->createPriceDiv ($name, '', $prices, TRUE);
			}
		}

		// Get the document object.
		$document = JFactory::getDocument ();
		// stAn: setName works in JDocumentHTML and not JDocumentRAW
		if (method_exists($document, 'setName')){
			$document->setName ('recalculate');
		}

		JResponse::setHeader ('Cache-Control', 'no-cache, must-revalidate');
		JResponse::setHeader ('Expires', 'Mon, 6 Jul 2000 10:00:00 GMT');
		// Set the MIME type for JSON output.
		$document->setMimeEncoding ('application/json');
		JResponse::setHeader ('Content-Disposition', 'attachment;filename="recalculate.json"', TRUE);
		JResponse::sendHeaders ();
		echo json_encode ($priceFormated);
		jexit ();
	}

	public function getJsonChild () {

		$view = $this->getView ('productdetails', 'json');

		$view->display (NULL);
	}

	/**
	 * Notify customer
	 *
	 * @author Seyi Awofadeju
	 *
	 */
	public function notifycustomer () {

		$data = JRequest::get ('post');

		$model = VmModel::getModel ('waitinglist');
		if (!$model->adduser ($data)) {
			$errors = $model->getErrors ();
			foreach ($errors as $error) {
				$msg = ($error) . '<br />';
			}
			$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $data['virtuemart_product_id']), $msg);
		} else {
			$msg = JText::sprintf ('COM_VIRTUEMART_STRING_SAVED', JText::_ ('COM_VIRTUEMART_CART_NOTIFY'));
			$this->setRedirect (JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $data['virtuemart_product_id']), $msg);
		}

	}
	/*
	 * Send an email to all shoppers who bought a product
	 */

	public function sentProductEmailToShoppers () {

		$model = VmModel::getModel ('product');
	    $model->sentProductEmailToShoppers ();

	}

}
// pure php no closing tag
