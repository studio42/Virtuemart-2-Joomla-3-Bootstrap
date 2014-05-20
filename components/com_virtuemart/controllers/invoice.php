<?php
/**
 *
 * Controller for the front end Orderviews
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: orders.php 5432 2012-02-14 02:20:35Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JLoader::register('VmModel', JPATH_VM_ADMINISTRATOR.'/helpers/vmmodel.php');

/**
 * VirtueMart Component Controller
 *
 * @package		VirtueMart
 */
class VirtueMartControllerInvoice extends JControllerLegacy
{

	public function __construct()
	{
		parent::__construct();
		$this->useSSL = VmConfig::get('useSSL',0);
		$this->useXHTML = true;
		VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
		VmConfig::loadJLang('com_virtuemart_orders',TRUE);
	}

	public function getOrderDetails() {
		$orderModel = VmModel::getModel('orders');
		$orderDetails = 0;
		// If the user is not logged in, we will check the order number and order pass
		if ($orderPass = JRequest::getString('order_pass',false) and $orderNumber = JRequest::getString('order_number',false)){
			$orderId = $orderModel->getOrderIdByOrderPass($orderNumber,$orderPass);
			if(empty($orderId)){
				vmDebug ('Invalid order_number/password '.JText::_('COM_VIRTUEMART_RESTRICTED_ACCESS'));
				return 0;
			}
			$orderDetails = $orderModel->getOrder($orderId);
		}

		if($orderDetails==0) {

			$_currentUser = JFactory::getUser();
			$cuid = $_currentUser->get('id');

			// If the user is logged in, we will check if the order belongs to him
				$virtuemart_order_id = JRequest::getInt('virtuemart_order_id',0) ;
			if (!$virtuemart_order_id) {
				$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber(JRequest::getString('order_number'));
			}
			$orderDetails = $orderModel->getOrder($virtuemart_order_id);

			JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/permissions.php');
			if(!Permissions::getInstance()->check("admin")) {
				if(!empty($orderDetails['details']['BT']->virtuemart_user_id)){
					if ($orderDetails['details']['BT']->virtuemart_user_id != $cuid) {
						echo 'view '.JText::_('COM_VIRTUEMART_RESTRICTED_ACCESS');
						return ;
					}
				}
			}
		}
		return $orderDetails;
	}

	public function display($cachable = false, $urlparams = false)  {
	
		$jinput = JFactory::getApplication()->input;
		$format = $jinput->get('format','html','WORD');
		$layout = $jinput->get('layout','invoice','WORD');

		if ($format != 'pdf') {
			$document = JFactory::getDocument();
			$viewName='invoice';
			$view = $this->getView($viewName, $format);
			$view->headFooter = true;
			$view->document = $document;
			$view->display();
		} else {
			$viewName='invoice';
			$format="pdf";
			/* Create the invoice PDF file on disk and send that back */
			$orderModel = VmModel::getModel('orders');
			$orderDetails = $this->getOrderDetails();
			$fileLocation = $this->getInvoicePDF($orderDetails, $viewName, $layout, $format);
			$fileName = basename ($fileLocation);

			if (file_exists ($fileLocation)) {
			// if (file_exists ($fileName)) {
				$maxSpeed = 200;
				$range = 0;
				$size = filesize ($fileLocation);
				$contentType = 'application/pdf';
				header ("Cache-Control: public");
				header ("Content-Transfer-Encoding: binary\n");
				header ('Content-Type: application/pdf');

				$contentDisposition = 'attachment';

				$agent = strtolower ($_SERVER['HTTP_USER_AGENT']);

				if (strpos ($agent, 'msie') !== FALSE) {
					$fileName = preg_replace ('/\./', '%2e', $fileName, substr_count ($fileName, '.') - 1);
				}

				header ("Content-Disposition: $contentDisposition; filename=\"$fileName\"");

				header ("Accept-Ranges: bytes");

				if (isset($_SERVER['HTTP_RANGE'])) {
					list($a, $range) = explode ("=", $_SERVER['HTTP_RANGE']);
					str_replace ($range, "-", $range);
					$size2 = $size - 1;
					$new_length = $size - $range;
					header ("HTTP/1.1 206 Partial Content");
					header ("Content-Length: $new_length");
					header ("Content-Range: bytes $range$size2/$size");
				}
				else {
					$size2 = $size - 1;
					header ("Content-Range: bytes 0-$size2/$size");
					header ("Content-Length: " . $size);
				}

				if ($size == 0) {
					die('Zero byte file! Aborting download');
				}

				//$contents = file_get_contents ($fileName);
				//echo $contents;

				//	set_magic_quotes_runtime(0);
				$fp = fopen ("$fileLocation", "rb");
				fseek ($fp, $range);

				while (!feof ($fp) and (connection_status () == 0)) {
					set_time_limit (0);
					print(fread ($fp, 1024 * $maxSpeed));
					flush ();
					ob_flush ();
					sleep (1);
				}
				fclose ($fp);

				JFactory::getApplication()->close();

			} else {
				// TODO: Error message
				// vmError("File $fileName not found!");
			}
		}

	}
	public function samplePDF() {
		// if(!class_exists('VmVendorPDF')){
		$jlang =JFactory::getLanguage();
		$app = JApplication::getInstance('site', array(), 'J');
		$attributes = array('charset' => 'utf-8', 'lineend' => 'unix', 'tab' => '  ', 'language' => $jlang->getTag(),
			'direction' => $jlang->isRTL() ? 'rtl' : 'ltr');
		$document = JDocument::getInstance('pdf', $attributes);
		//$document->setDestination('F'); // render to file
		$viewName='invoice';
		// $viewLayout = JRequest::getCmd('layout', 'default');
		$view = $this->getView($viewName, 'html', '', array('base_path' => $this->basePath, 'layout' => 'samplepdf' ));
		$view->document = $document ;
		$view->display();
		//$pdf->PrintContents(JText::_('COM_VIRTUEMART_PDF_SAMPLEPAGE'));

	}

	function getInvoicePDF($orderDetails = 0, $viewName='invoice', $layout='invoice', $format='html', $force = false){
		JRequest::setVar('task','checkStoreInvoice');

		$force = true;

		//	@ini_set( 'max_execution_time', 5 );

		$path = VmConfig::get('forSale_path',0);
		if($path===0 ){
			vmError('No path set to store invoices');
			return false;
		} else {
			$path .= 'invoices/';
			if(!file_exists($path)){
				vmError('Path wrong to store invoices, folder invoices does not exist '.$path);
				return false;
			} else if(!is_writable( $path )){
				vmError('Cannot store pdf, directory not writeable '.$path);
				return false;
			}
		}

		$orderModel = VmModel::getModel('orders');
		$invoiceNumberDate=array();
		if (!  $orderModel->createInvoiceNumber($orderDetails['details']['BT'], $invoiceNumberDate)) {
		    return 0;
		}

		if(!empty($invoiceNumberDate[0])){
			$invoiceNumber = $invoiceNumberDate[0];
		} else {
			$invoiceNumber = FALSE;
		}

		if(!$invoiceNumber or empty($invoiceNumber)){
			vmError('Cant create pdf, createInvoiceNumber failed');
			return 0;
		}
		if (shopFunctions::InvoiceNumberReserved($invoiceNumber)) {
			return 0;
		}

		$path .= preg_replace('/[^A-Za-z0-9_\-\.]/', '_', 'vminvoice_'.$invoiceNumber.'.pdf');

		if(file_exists($path) and !$force){
			return $path;
		}

		// 			$app = JFactory::getApplication('site');

		//We come from the be, so we need to load the FE langauge
		$jlang =JFactory::getLanguage();
		$jlang->load('com_virtuemart', JPATH_SITE, 'en-GB', true);
		$jlang->load('com_virtuemart', JPATH_SITE, $jlang->getDefault(), true);
		$jlang->load('com_virtuemart', JPATH_SITE, null, true);

		$app = JApplication::getInstance('site', array(), 'J');
		// $viewLayout = JRequest::getCmd('layout', 'default');
		$viewName='invoice';
		$view = $this->getView($viewName, 'html', '', array('base_path' => $this->basePath, 'layout' => 'invoice' ));

		// attach the document to the view
		$attributes = array('charset' => 'utf-8', 'lineend' => 'unix', 'tab' => '  ', 'language' => $jlang->getTag(),
			'direction' => $jlang->isRTL() ? 'rtl' : 'ltr');
		$document = JDocument::getInstance('pdf', $attributes);
		$view->document = $document ;
		$view->document->setDestination('F'); // render to file
		$view->document->setPath($path);
		$viewType = $view->document->getType();

		$vmtemplate = VmConfig::get('vmtemplate',0);
		if($vmtemplate===0 or $vmtemplate == 'default'){
			$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';

			$db = JFactory::getDbo();
			$db->setQuery($q);
			$templateName = $db->loadResult();
		} else {
			$templateName = $vmtemplate;
		}

		$TemplateOverrideFolder = JPATH_SITE.'/templates'.$templateName.'/html/com_virtuemart/invoice';
		// if(file_exists($TemplateOverrideFolder)){
			$view->addTemplatePath( $TemplateOverrideFolder);
		// }

		$view->invoiceNumber = $invoiceNumberDate[0];
		$view->invoiceDate = $invoiceNumberDate[1];

		$view->orderDetails = $orderDetails;
		$view->uselayout = $layout;
		$view->showHeaderFooter = false;
		ob_start();
		$view->display();
		// test mode is print2
		$html = ob_get_clean();
		$view->document->setBuffer( $html );
		// $html must contain the path here
		$view->document->render();

		
		//var_dump( $this->basePath,$template,$format,$app,$view,$document,$this,$html); jexit();

		return $view->document->getPath() ;

	}
}

// No closing tag
