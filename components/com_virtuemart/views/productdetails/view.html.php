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
 * @version $Id: view.html.php 6477 2012-09-24 14:33:54Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JLoader::register('VmView', JPATH_VM_SITE.'/helpers/vmview.php');
JLoader::register('ShopFunctions', JPATH_VM_ADMINISTRATOR.'/helpers/shopfunctions.php');


/**
 * Product details
 *
 * @package VirtueMart
 * @author RolandD
 * @author Max Milbers
 */
class VirtueMartViewProductdetails extends VmView {

    /**
     * Collect all data to show on the template
     *
     * @author RolandD, Max Milbers
     */
    function display($tpl = null) {

	//TODO get plugins running
//		$dispatcher	= JDispatcher::getInstance();
//		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

	$this->show_prices = VmConfig::get('show_prices', 1);
	if ($this->show_prices == 1) {
		JLoader::register('calculationHelper', JPATH_VM_ADMINISTRATOR.'/helpers/calculationh.php');
	}

	$document = JFactory::getDocument();

	// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
	vmJsApi::jPrice();

	$app = JFactory::getApplication();
	$pathway = $app->getPathway();
	$task = JRequest::getCmd('task');

	JLoader::register('VmImage', JPATH_VM_ADMINISTRATOR.'/helpers/image.php');


	// Load the product
	//$product = $this->get('product');	//Why it is sensefull to use this construction? Imho it makes it just harder
	$this->product_model = VmModel::getModel('product');

	$virtuemart_product_idArray = JRequest::getVar('virtuemart_product_id', 0);
	if (is_array($virtuemart_product_idArray) and count($virtuemart_product_idArray) > 0) {
	    $virtuemart_product_id = (int)$virtuemart_product_idArray[0];
	} else {
	    $virtuemart_product_id = (int)$virtuemart_product_idArray;
	}

    $quantityArray = JRequest::getVar ('quantity', array()); //is sanitized then
    JArrayHelper::toInteger ($quantityArray);

    $quantity = 1;
    if (!empty($quantityArray[0])) {
	    $quantity = $quantityArray[0];
    }
	$onlyPublished = true;
	// set unpublished product when it's editable by its owner for preview
	if ($canEdit = ShopFunctions::can('edit','product')) {
		$onlyPublished = false;
	}
    $product = $this->product_model->getProduct($virtuemart_product_id,TRUE,TRUE,$onlyPublished,$quantity);
	if($product && $canEdit) {
		JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/permissions.php');
		$vendor = Permissions::getInstance()->isSuperVendor();
		if ($vendor > 1 && $product->virtuemart_vendor_id !== $vendor ) $product = null;
		elseif ( !$product->published ) $app->enqueueMessage(JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_STATUS').' : '.JText::_('COM_VIRTUEMART_UNPUBLISHED'),'warning');
	}

	$last_category_id = shopFunctionsF::getLastVisitedCategoryId();
	if (empty($product->slug)) {

	    //Todo this should be redesigned to fit better for SEO
	    $app->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND'));

	    $categoryLink = '';
	    if (!$last_category_id) {
		$last_category_id = JRequest::getInt('virtuemart_category_id', false);
	    }
	    if ($last_category_id) {
		$categoryLink = '&virtuemart_category_id=' . $last_category_id;
	    }

	    if (VmConfig::get('handle_404',1)) {
		    $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink . '&error=404', FALSE));
		} else {
			JError::raise(E_ERROR,'404','Not found');
		}

	    return;
	}

    if (!empty($product->customfields)) {
	    foreach ($product->customfields as $k => $custom) {
		    if (!empty($custom->layout_pos)) {
			    $product->customfieldsSorted[$custom->layout_pos][] = $custom;
			    unset($product->customfields[$k]);
		    }
	    }
	    $product->customfieldsSorted['normal'] = $product->customfields;
	    unset($product->customfields);
    }

	$product->event = new stdClass();
	$product->event->afterDisplayTitle = '';
	$product->event->beforeDisplayContent = '';
	$product->event->afterDisplayContent = '';
	if (VmConfig::get('enable_content_plugin', 0)) {
	   // add content plugin //
	   $dispatcher = & JDispatcher::getInstance();
	   JPluginHelper::importPlugin('content');
	   $product->text = $product->product_desc;
		// jimport( 'joomla.html.parameter' );
		// $params = new JParameter('');
		$params = new JRegistry;
		$product->event = new stdClass;
		$results = $dispatcher->trigger('onContentPrepare', array('com_virtuemart.productdetails', &$product, &$params, 0));
		// More events for 3rd party content plugins
		// This do not disturb actual plugins, because we don't modify $product->text
		$res = $dispatcher->trigger('onContentAfterTitle', array('com_virtuemart.productdetails', &$product, &$params, 0));
		$product->event->afterDisplayTitle = trim(implode("\n", $res));

		$res = $dispatcher->trigger('onContentBeforeDisplay', array('com_virtuemart.productdetails', &$product, &$params, 0));
		$product->event->beforeDisplayContent = trim(implode("\n", $res));

		$res = $dispatcher->trigger('onContentAfterDisplay', array('com_virtuemart.productdetails', &$product, &$params, 0));
		$product->event->afterDisplayContent = trim(implode("\n", $res));

		$product->product_desc = $product->text;
	}

	$this->product_model->addImages($product);

	if (isset($product->min_order_level) && (int) $product->min_order_level > 0) {
	    $this->min_order_level = $product->min_order_level;
	} else {
	    $this->min_order_level = 1;
	}

	if (isset($product->step_order_level) && (int) $product->step_order_level > 0) {
	    $this->step_order_level = $product->step_order_level;
	} else {
	    $this->step_order_level = 1;
	}

	// Load the neighbours
    if (VmConfig::get('product_navigation', 1)) {
	    $product->neighbours = $this->product_model->getNeighborProducts($product);
	}
	// Product vendor multiX
	if ($multix = Vmconfig::get('multix','none') === 'admin') {
		$vendor_model = VmModel::getModel('vendor');
		$this->vendor = $vendor_model->getVendor($product->virtuemart_vendor_id);
	} else $this->vendor = null;
	// echo 'multi'.$multix;
	// Load the category
	$category_model = VmModel::getModel('category');

	shopFunctionsF::setLastVisitedCategoryId($product->virtuemart_category_id);
	$catTitle = array();
	if ($category_model) {

		$category = $category_model->getCategory($product->virtuemart_category_id);

	    $category_model->addImages($category, 1);
	    $this->assignRef('category', $category);

		//Seems we dont need this anylonger, destroyed the breadcrumb
		if ($category->parents) {
			foreach ($category->parents as $c) {
				if(is_object($c) and isset($c->category_name)){
					$pathway->addItem(strip_tags($c->category_name), JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $c->virtuemart_category_id, FALSE));
					$catTitle[] = $c->category_name ;
				} else {
					vmdebug('Error, parent category has no name, breadcrumb maybe broken, category',$c);
				}
			}
		}

		$vendorId = JRequest::getInt('virtuemart_vendor_id', null);
		$category->children = $category_model->getChildCategoryList($vendorId, $product->virtuemart_category_id);
		$category_model->addImages($category->children, 1);
	}

	if (!empty($tpl)) {
	    $format = $tpl;
	} else {
	    $format = JRequest::getWord('format', 'html');
	}
	if ($format == 'html') {
	    // Set Canonic link
	    $document->addHeadLink($product->canonical, 'canonical', 'rel', '');
	}

	$uri = JURI::getInstance();
	//$pathway->addItem(JText::_('COM_VIRTUEMART_PRODUCT_DETAILS'), $uri->toString(array('path', 'query', 'fragment')));
	$pathway->addItem(strip_tags($product->product_name));
	// Set the titles
	// $document->setTitle should be after the additem pathway
	if ($product->customtitle) {
	    $document->setTitle(strip_tags($product->customtitle));
	} else {

	    $document->setTitle( ($catTitle ? implode(" / ", $catTitle) . ' / ' : '') . $product->product_name);
	}
	$ratingModel = VmModel::getModel('ratings');
	$this->allowReview = $ratingModel->allowReview($product->virtuemart_product_id);

	$this->showReview = $ratingModel->showReview($product->virtuemart_product_id);

	if ($this->showReview) {
	    $this->review = $ratingModel->getReviewByProduct($product->virtuemart_product_id);
	    $this->rating_reviews = $ratingModel->getReviews($product->virtuemart_product_id);
	}

	$this->showRating = $ratingModel->showRating($product->virtuemart_product_id);

	if ($this->showRating) {
	    $this->vote = $ratingModel->getVoteByProduct($product->virtuemart_product_id);
	    $this->rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
	}

	$this->allowRating = $ratingModel->allowRating($product->virtuemart_product_id);

	// todo: atm same form for "call for price" and "ask a question". Title of the form should be different
	$this->askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id . '&tmpl=component', FALSE);

	// Load the user details
	$this->user = JFactory::getUser();

	// More reviews link
	$uri = JURI::getInstance();
	$uri->setVar('showall', 1);
	$uristring = $uri->toString();
	$this->more_reviews = $uristring;

	if ($product->metadesc) {
	    $document->setDescription($product->metadesc);
	}
	if ($product->metakey) {
	    $document->setMetaData('keywords', $product->metakey);
	}

	if ($product->metarobot) {
	    $document->setMetaData('robots', $product->metarobot);
	}

	if ($app->getCfg('MetaTitle') == '1') {
	    $document->setMetaData('title', $product->product_name);  //Maybe better product_name
	}
	if ($app->getCfg('MetaAuthor') == '1') {
	    $document->setMetaData('author', $product->metaauthor);
	}


	$this->showBasePrice = Permissions::getInstance()->check('admin'); //todo add config settings
	$productDisplayShipments = array();
	$productDisplayPayments = array();

	JLoader::register('vmPSPlugin', JPATH_VM_PLUGINS.'vmpsplugin.php');
	JPluginHelper::importPlugin('vmshipment');
	JPluginHelper::importPlugin('vmpayment');
	$dispatcher = JDispatcher::getInstance();
	$returnValues = $dispatcher->trigger('plgVmOnProductDisplayShipment', array($product, &$productDisplayShipments));
	$returnValues = $dispatcher->trigger('plgVmOnProductDisplayPayment', array($product, &$productDisplayPayments));

	$this->productDisplayPayments = $productDisplayPayments;
	$this->productDisplayShipments = $productDisplayShipments;

	if (empty($category->category_template)) {
	    $category->category_template = VmConfig::get('categorytemplate');
	}
	$this->product = $product;
	shopFunctionsF::setVmTemplate($this, $category->category_template, $product->product_template, $category->category_product_layout, $product->layout);

	shopFunctionsF::addProductToRecent($virtuemart_product_id);

	$this->currency = CurrencyDisplay::getInstance();

	if(JRequest::getCmd( 'layout', 'default' )=='notify') $this->setLayout('notify'); //Added by Seyi Awofadeju to catch notify layout


	parent::display($tpl);
    }

	function renderMailLayout ($doVendor, $recipient) {
		$tpl = VmConfig::get('order_mail_html') ? 'mail_html_notify' : 'mail_raw_notify';

		$this->doVendor=$doVendor;
		$this->fromPdf=false;
		$this->uselayout = $tpl;
		$this->subject = !empty($this->subject) ? $this->subject : JText::_('COM_VIRTUEMART_CART_NOTIFY_MAIL_SUBJECT');
		$this->layoutName = $tpl;
		$this->setLayout($tpl);
		parent::display();
	}

    private function showLastCategory($tpl) {
		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
		$categoryLink = '';
		if ($virtuemart_category_id) {
		    $categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
		}
		$continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink, FALSE);

		$this->continue_link_html = '<a href="' . $continue_link . '" />' . JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';

		// Display it all
		parent::display($tpl);
    }


}

// pure php no closing tag