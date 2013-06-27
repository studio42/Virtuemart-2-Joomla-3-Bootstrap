<?php

/**
 *
 * Product details view
 *
 * @package VirtueMart
 * @subpackage
 * @author RolandD
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 2796 2011-03-01 11:29:16Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

// Load the view framework
if (!class_exists ('VmView')) {
	require(JPATH_VM_SITE . DS . 'helpers' . DS . 'vmview.php');
}

/**
 * Product details
 *
 * @package VirtueMart
 * @author RolandD
 * @author Max Milbers
 */
class VirtueMartViewAskquestion extends VmView {

	/**
	 * Collect all data to show on the template
	 *
	 * @author RolandD, Max Milbers
	 */
	function display ($tpl = NULL) {

		$show_prices = VmConfig::get ('show_prices', 1);
		if ($show_prices == '1') {
			if (!class_exists ('calculationHelper')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
			}
		}
		$this->assignRef ('show_prices', $show_prices);
		$document = JFactory::getDocument ();

		/* add javascript for price and cart */
		vmJsApi::jPrice ();

		$mainframe = JFactory::getApplication ();
		$pathway = $mainframe->getPathway ();
		$task = JRequest::getCmd ('task');

		// Set the helper path
		$this->addHelperPath (JPATH_VM_ADMINISTRATOR . DS . 'helpers');

		$this->loadHelper ('image');
		$this->loadHelper ('addtocart');

		// Load the product
		$product_model = VmModel::getModel ('product');
		$category_model = VmModel::getModel ('Category');

		$virtuemart_product_idArray = JRequest::getInt ('virtuemart_product_id', 0);
		if (is_array ($virtuemart_product_idArray)) {
			$virtuemart_product_id = $virtuemart_product_idArray[0];
		} else {
			$virtuemart_product_id = $virtuemart_product_idArray;
		}

		if (empty($virtuemart_product_id)) {
			self::showLastCategory ($tpl);
			return;
		}

		if (!class_exists ('VirtueMartModelVendor')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
		}
		$product = $product_model->getProduct ($virtuemart_product_id);
		// Set Canonic link
		$format = JRequest::getWord('format', 'html');
		if ($format == 'html') {
			$document->addHeadLink ($product->link, 'canonical', 'rel', '');
		}


		// Set the titles
		$document->setTitle (JText::sprintf ('COM_VIRTUEMART_PRODUCT_DETAILS_TITLE', $product->product_name . ' - ' . JText::_ ('COM_VIRTUEMART_PRODUCT_ASK_QUESTION')));
		$uri = JURI::getInstance ();

		$this->assignRef ('product', $product);

		if (empty($product)) {
			self::showLastCategory ($tpl);
			return;
		}

		$product_model->addImages ($product, 1);

		/* Get the category ID */
		$virtuemart_category_id = JRequest::getInt ('virtuemart_category_id');
		if ($virtuemart_category_id == 0 && !empty($product)) {
			if (array_key_exists ('0', $product->categories)) {
				$virtuemart_category_id = $product->categories[0];
			}
		}

		shopFunctionsF::setLastVisitedCategoryId ($virtuemart_category_id);

		if ($category_model) {
			$category = $category_model->getCategory ($virtuemart_category_id);
			$this->assignRef ('category', $category);
			$pathway->addItem ($category->category_name, JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $virtuemart_category_id));
		}

		//$pathway->addItem(JText::_('COM_VIRTUEMART_PRODUCT_DETAILS'), $uri->toString(array('path', 'query', 'fragment')));
		$pathway->addItem ($product->product_name, JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $virtuemart_category_id . '&virtuemart_product_id=' . $product->virtuemart_product_id));

		// for askquestion
		$pathway->addItem (JText::_ ('COM_VIRTUEMART_PRODUCT_ASK_QUESTION'));

		$this->assignRef ('user', JFactory::getUser ());

		if ($product->metadesc) {
			$document->setDescription ($product->metadesc);
		}
		if ($product->metakey) {
			$document->setMetaData ('keywords', $product->metakey);
		}

		if ($product->metarobot) {
			$document->setMetaData ('robots', $product->metarobot);
		}

		if ($mainframe->getCfg ('MetaTitle') == '1') {
			$document->setMetaData ('title', $product->product_s_desc); //Maybe better product_name
		}
		if ($mainframe->getCfg ('MetaAuthor') == '1') {
			$document->setMetaData ('author', $product->metaauthor);
		}

		parent::display ($tpl);
	}

	function renderMailLayout () {

		$this->setLayout ('mail_html_question');
		$this->comment = JRequest::getString ('comment');

		$vendorModel = VmModel::getModel ('vendor');
		$this->vendor = $vendorModel->getVendor ();

		$this->subject = Jtext::_ ('COM_VIRTUEMART_QUESTION_ABOUT') . $this->product->product_name;
		$this->vendorEmail = $this->user['email'];
		// in this particular case, overwrite the value for fix the recipient name
		$this->vendor->vendor_name = $this->user['name'];
		//$this->vendorName= $this->user['email'];
		if (VmConfig::get ('order_mail_html')) {
			$tpl = 'mail_html_question';
		} else {
			$tpl = 'mail_raw_question';
		}
		$this->setLayout ($tpl);
		parent::display ();
	}

	private function showLastCategory ($tpl) {

		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId ();
		$categoryLink = '';
		if ($virtuemart_category_id) {
			$categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
		}
		$continue_link = JRoute::_ ('index.php?option=com_virtuemart&view=category' . $categoryLink);

		$continue_link_html = '<a href="' . $continue_link . '" />' . JText::_ ('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
		$this->assignRef ('continue_link_html', $continue_link_html);
		// Display it all
		parent::display ($tpl);
	}

}

// pure php no closing tag