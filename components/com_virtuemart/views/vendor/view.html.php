<?php

/**
 *
 * List/add/edit/remove Vendors
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 5133 2011-12-19 12:02:41Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JLoader::register('VmView', JPATH_VM_SITE.'/helpers/VmView.php');

// Set to '0' to use tabs i.s.o. sliders
// Might be a config option later on, now just here for testing.
define('__VM_USER_USE_SLIDERS', 0);

/**
 * HTML View class for maintaining the list of users
 *
 * @package	VirtueMart
 * @subpackage Vendor
 * @author Max Milbers
 */
class VirtuemartViewVendor extends VmView {

	/**
	 * Displays the view, collects needed data for the different layouts
	 *
	 * Okey I try now a completly new idea.
	 * We make a function for every tab and the display is getting the right tabs by an own function
	 * putting that in an array and after that we call the preparedataforlayoutBlub
	 *
	 * @author Max Milbers
	 */
	function display($tpl = null) {

		$document = JFactory::getDocument();
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		$layoutName = $this->getLayout();

		$model = VmModel::getModel();

		$virtuemart_vendor_id = JRequest::getInt('virtuemart_vendor_id');

// 		if ($layoutName=='default') {
		if (empty($virtuemart_vendor_id)) {
			$document->setTitle( JText::_('COM_VIRTUEMART_VENDOR_LIST') );
			$pathway->addItem(JText::_('COM_VIRTUEMART_VENDOR_LIST'));

			$this->vendors = $model->getVendors();
			$model->addImages($this->vendors);

		} else {

			$vendor = $model->getVendor($virtuemart_vendor_id);
			$model->addImages($vendor);
			if (VmConfig::get ('enable_content_plugin', 0)) {
				// add content plugin //
				$dispatcher = & JDispatcher::getInstance ();
				JPluginHelper::importPlugin ('content');
				$vendor->text = $vendor->vendor_store_desc;
				$params = new JRegistry;
				if (!isset($vendor->event)) {
					$vendor->event = new stdClass();
				}
				$results = $dispatcher->trigger ('onContentPrepare', array('com_virtuemart.vendor', &$vendor, &$params, 0));
				// More events for 3rd party content plugins
				// This do not disturb actual plugins, because we don't modify $vendor->text
				$res = $dispatcher->trigger ('onContentAfterTitle', array('com_virtuemart.vendor', &$vendor, &$params, 0));
				$vendor->event->afterDisplayTitle = trim (implode ("\n", $res));

				$res = $dispatcher->trigger ('onContentBeforeDisplay', array('com_virtuemart.vendor', &$vendor, &$params, 0));
				$vendor->event->beforeDisplayContent = trim (implode ("\n", $res));

				$res = $dispatcher->trigger ('onContentAfterDisplay', array('com_virtuemart.vendor', &$vendor, &$params, 0));
				$vendor->event->afterDisplayContent = trim (implode ("\n", $res));

				$vendor->vendor_store_desc = $vendor->text;
			}
			$this->vendor = $vendor;

			if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.'/models/vendor.php');
			$userId = VirtueMartModelVendor::getUserIdByVendorId($virtuemart_vendor_id);

			//$usermodel = VmModel::getModel('user');

			//$virtuemart_userinfo_id = $usermodel->getBTuserinfo_id($userId);
			//$usermodel->getVendor($virtuemart_vendor_id);
			//$userFields = $usermodel->getUserInfoInUserFields($layoutName, 'BT', $virtuemart_userinfo_id,true,true);
			//$this->assignRef('userFields', $userFields);

			if ($layoutName=='tos') {
				$document->setTitle( JText::_('COM_VIRTUEMART_VENDOR_TOS') );
				$pathway->addItem(JText::_('COM_VIRTUEMART_VENDOR_TOS'));
			}
			elseif ($layoutName=='contact') {
				$user = JFactory::getUser();
				$document->setTitle( JText::_('COM_VIRTUEMART_VENDOR_CONTACT') );
				$pathway->addItem(JText::_('COM_VIRTUEMART_VENDOR_CONTACT'));
				$this->user = $user;

			} else {
				$document->setTitle( JText::_('COM_VIRTUEMART_VENDOR_DETAILS').' '.$this->vendor->vendor_store_name );
				$pathway->addItem($this->vendor->vendor_store_name);//JText::_('COM_VIRTUEMART_VENDOR_DETAILS'));
				$this->setLayout('details');
				$productModel = VmModel::getModel('product');
				$this->products = $productModel->getProductsInCategory(0,$virtuemart_vendor_id);
				if ($this->products) {
					$productModel->addImages($this->products,1);
					$currency = CurrencyDisplay::getInstance( );
					$this->currency = $currency;
					foreach($this->products as $product){
						$product->stock = $productModel->getStockIndicator($product);
					}
					if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.'/helpers/permissions.php');
					$this->showBasePrice = Permissions::getInstance()->check('admin'); //todo add config settings
					$this->perRow = VmConfig::get('products_per_row',3);
					$this->pagination = $productModel->getPagination($this->perRow);
				}
			}

			$this->linkdetails = '<a href="'.JRoute::_('index.php?option=com_virtuemart&view=vendor&layout=details&virtuemart_vendor_id=' .
				$virtuemart_vendor_id, FALSE).'">'.JText::_('COM_VIRTUEMART_VENDOR_DETAILS').'</a>';
			$this->linkcontact = '<a href="'.JRoute::_('index.php?option=com_virtuemart&view=vendor&layout=contact&virtuemart_vendor_id=' . $virtuemart_vendor_id, FALSE).'">'.JText::_('COM_VIRTUEMART_VENDOR_CONTACT').'</a>';
			$this->linktos = '<a href="'.JRoute::_('index.php?option=com_virtuemart&view=vendor&layout=tos&virtuemart_vendor_id=' . $virtuemart_vendor_id, FALSE).'">'.JText::_('COM_VIRTUEMART_VENDOR_TOS').'</a>';

		}

		parent::display($tpl);

	}


	function renderMailLayout($doVendor, $recipient) {

		$this->setLayout('mail_html_question');
		$this->comment = JRequest::getString('comment');
		$virtuemart_vendor_id = JRequest::getInt('virtuemart_vendor_id');
		$this->doVendor=$doVendor;
		//$this->doVendor=TRUE;
		$vendorModel = VmModel::getModel('vendor');
		$this->vendor = $vendorModel->getVendor($virtuemart_vendor_id);
		// in this particular case, overwrite the value for fix the recipient name
		$this->vendor->vendor_name= $this->user['name'];
		$this->subject = JText::_('COM_VIRTUEMART_VENDOR_CONTACT') .' '.$this->user['name'];
		$this->vendorEmail= $this->user['email'];
		//$this->vendorName= $this->user['email'];
		if (VmConfig::get('order_mail_html')) {
			$tpl = 'mail_html_question';
		} else {
			$tpl = 'mail_raw_question';
		}
		$this->setLayout($tpl);
		parent::display( );
	}

}

//No Closing Tag
