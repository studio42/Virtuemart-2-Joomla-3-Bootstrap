<?php
/**
*
* Handle the category view
*
* @package	VirtueMart
* @subpackage
* @author RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6504 2012-10-05 09:40:59Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php');

/**
* Handle the category view
*
* @package VirtueMart
* @author RolandD
* @todo set meta data
* @todo add full path to breadcrumb
*/
class VirtuemartViewCategory extends VmView {

	public function display($tpl = null) {


		$show_prices  = VmConfig::get('show_prices',1);
		if($show_prices == '1'){
			if(!class_exists('calculationHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'calculationh.php');
		}
		$this->assignRef('show_prices', $show_prices);

		$document = JFactory::getDocument();
		// add javascript for price and cart
		vmJsApi::jPrice();

		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		/* Set the helper path */
		$this->addHelperPath(JPATH_VM_ADMINISTRATOR.DS.'helpers');

		//Load helpers
		$this->loadHelper('image');
		$categoryModel = VmModel::getModel('category');
		$productModel = VmModel::getModel('product');


		$categoryId = JRequest::getInt('virtuemart_category_id', false);
		$vendorId = 1;

		$category = $categoryModel->getCategory($categoryId);
		if(!$category->published){
			vmInfo('COM_VIRTUEMART_CAT_NOT_PUBL',$category->category_name,$categoryId);
			return false;
		}
		$categoryModel->addImages($category,1);
		$perRow = empty($category->products_per_row)? VmConfig::get('products_per_row',3):$category->products_per_row;
// 		$categoryModel->setPerRow($perRow);
		$this->assignRef('perRow', $perRow);


		//No redirect here, category id = 0 means show ALL categories! note by Max Milbers
/*		if(empty($category->virtuemart_vendor_id) && $search == null ) {
	    	$app -> enqueueMessage(JText::_('COM_VIRTUEMART_CATEGORY_NOT_FOUND'));
	    	$app -> redirect( 'index.php');
	    }*/

	    // Add the category name to the pathway
		if ($category->parents) {
			foreach ($category->parents as $c){
				$pathway->addItem(strip_tags($c->category_name),JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$c->virtuemart_category_id));
			}
		}

		$categoryModel->addImages($category,1);
		$cache = JFactory::getCache('com_virtuemart','callback');
		$category->children = $cache->call( array( 'VirtueMartModelCategory', 'getChildCategoryList' ),$vendorId, $categoryId );

		$categoryModel->addImages($category->children,1);

		if (VmConfig::get('enable_content_plugin', 0)) {
			// add content plugin //
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$category->text = $category->category_description;
			if(!class_exists('JParameter')) require(JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter.php');

			$params = new JParameter('');

			if(JVM_VERSION === 2 ) {
				$results = $dispatcher->trigger('onContentPrepare', array('com_virtuemart.category', &$category, &$params, 0));
				// More events for 3rd party content plugins
				// This do not disturb actual plugins, because we don't modify $product->text
				$res = $dispatcher->trigger('onContentAfterTitle', array('com_virtuemart.category', &$category, &$params, 0));
				$category->event->afterDisplayTitle = trim(implode("\n", $res));

				$res = $dispatcher->trigger('onContentBeforeDisplay', array('com_virtuemart.category', &$category, &$params, 0));
				$category->event->beforeDisplayContent = trim(implode("\n", $res));

				$res = $dispatcher->trigger('onContentAfterDisplay', array('com_virtuemart.category', &$category, &$params, 0));
				$category->event->afterDisplayContent = trim(implode("\n", $res));
			} else {
				$results = $dispatcher->trigger('onPrepareContent', array(& $category, & $params, 0));
			}
			$category->category_description = $category->text;
		}


	   $this->assignRef('category', $category);

		// Set Canonic link
		if (!empty($tpl)) {
			$format = $tpl;
		} else {
			$format = JRequest::getWord('format', 'html');
		}
		if ($format == 'html') {
			$document->addHeadLink( JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$categoryId) , 'canonical', 'rel', '' );
		}

	    // Set the titles
		if ($category->customtitle) {
        	 $title = strip_tags($category->customtitle);
     	} elseif ($category->category_name) {
     		 $title = strip_tags($category->category_name);
     		 }
		else {
			$menus	= $app->getMenu();
			$menu = $menus->getActive();
			if ($menu) $title = $menu->title;
			// $title = $this->params->get('page_title', '');
			// Check for empty title and add site name if param is set
			if (empty($title)) {
				$title = $app->getCfg('sitename');
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}
		}

	  	if(JRequest::getInt('error')){
			$title .=' '.JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
		}

		// set search and keyword
		if ($keyword = vmRequest::uword('keyword', '0', ' ,-,+')) {
			$pathway->addItem($keyword);
			$title .=' ('.$keyword.')';
		}
		$search = JRequest::getvar('keyword', null);
		if ($search !==null) {
			$searchcustom = $this->getSearchCustom();
		}
		$this->assignRef('keyword', $keyword);
		$this->assignRef('search', $search);

	    // Load the products in the given category
	    $products = $productModel->getProductsInCategory($categoryId);
	    $productModel->addImages($products,1);

	    $this->assignRef('products', $products);
		foreach($products as $product){
              $product->stock = $productModel->getStockIndicator($product);
         }

		$ratingModel = VmModel::getModel('ratings');
		$showRating = $ratingModel->showRating();
		$this->assignRef('showRating', $showRating);

		$virtuemart_manufacturer_id = JRequest::getInt('virtuemart_manufacturer_id',0 );
		if ($virtuemart_manufacturer_id and !empty($products[0])) $title .=' '.$products[0]->mf_name ;
		$document->setTitle( $title );
		// Override Category name when viewing manufacturers products !IMPORTANT AFTER page title.
		if (JRequest::getInt('virtuemart_manufacturer_id' ) and !empty($products[0])) $category->category_name =$products[0]->mf_name ;

	    $pagination = $productModel->getPagination($perRow);
	    $this->assignRef('vmPagination', $pagination);

	    $orderByList = $productModel->getOrderByList($categoryId);
	    $this->assignRef('orderByList', $orderByList);

	   if ($category->metadesc) {
			$document->setDescription( $category->metadesc );
		}
		if ($category->metakey) {
			$document->setMetaData('keywords', $category->metakey);
		}
		if ($category->metarobot) {
			$document->setMetaData('robots', $category->metarobot);
		}

		if ($app->getCfg('MetaTitle') == '1') {
			$document->setMetaData('title',  $title);

		}
		if ($app->getCfg('MetaAuthor') == '1') {
			$document->setMetaData('author', $category->metaauthor);
		}
		if ($products) {
		$currency = CurrencyDisplay::getInstance( );
		$this->assignRef('currency', $currency);
		}

		// Add feed links
		if ($products  && VmConfig::get('feed_cat_published', 0)==1) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link . '&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link . '&type=atom'), 'alternate', 'rel', $attribs);
		}
		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		$showBasePrice = Permissions::getInstance()->check('admin'); //todo add config settings
		$this->assignRef('showBasePrice', $showBasePrice);

		//set this after the $categoryId definition
		$paginationAction=JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$categoryId );
		$this->assignRef('paginationAction', $paginationAction);

	    shopFunctionsF::setLastVisitedCategoryId($categoryId);
		shopFunctionsF::setLastVisitedManuId($virtuemart_manufacturer_id);

	    if(empty($category->category_template)){
	    	$category->category_template = VmConfig::get('categorytemplate');
	    }

	    shopFunctionsF::setVmTemplate($this,$category->category_template,0,$category->category_layout);

		parent::display($tpl);
	}
	/*
	 * generate custom fields list to display as search in FE
	 */
	public function getSearchCustom() {

		$emptyOption  = array('virtuemart_custom_id' =>0, 'custom_title' => JText::_('COM_VIRTUEMART_LIST_EMPTY_OPTION'));
		$this->_db =JFactory::getDBO();
		$this->_db->setQuery('SELECT `virtuemart_custom_id`, `custom_title` FROM `#__virtuemart_customs` WHERE `field_type` ="P"');
		$this->options = $this->_db->loadAssocList();

		if ($this->custom_parent_id = JRequest::getInt('custom_parent_id', 0)) {
			$this->_db->setQuery('SELECT `virtuemart_custom_id`, `custom_title` FROM `#__virtuemart_customs` WHERE custom_parent_id='.$this->custom_parent_id);
			$this->selected = $this->_db->loadObjectList();
			$this->searchCustomValues ='';
			foreach ($this->selected as $selected) {
				$this->_db->setQuery('SELECT `custom_value` as virtuemart_custom_id,`custom_value` as custom_title FROM `#__virtuemart_product_customfields` WHERE virtuemart_custom_id='.$selected->virtuemart_custom_id);
				 $valueOptions= $this->_db->loadAssocList();
				 $valueOptions = array_merge(array($emptyOption), $valueOptions);
				$this->searchCustomValues .= JText::_($selected->custom_title).' '.JHTML::_('select.genericlist', $valueOptions, 'customfields['.$selected->virtuemart_custom_id.']', 'class="inputbox"', 'virtuemart_custom_id', 'custom_title', 0);
			}
		}

		// add search for declared plugins
		JPluginHelper::importPlugin('vmcustom');
		$dispatcher = JDispatcher::getInstance();
		$plgDisplay = $dispatcher->trigger('plgVmSelectSearchableCustom',array( &$this->options,&$this->searchCustomValues,$this->custom_parent_id ) );

		if(!empty($this->options)){
			$this->options = array_merge(array($emptyOption), $this->options);
			// render List of available groups
			$this->searchCustomList = JText::_('COM_VIRTUEMART_SET_PRODUCT_TYPE').' '.JHTML::_('select.genericlist',$this->options, 'custom_parent_id', 'class="inputbox"', 'virtuemart_custom_id', 'custom_title', $this->custom_parent_id);
		} else {
			$this->searchCustomList = '';
		}

		$this->assignRef('searchcustom', $this->searchCustomList);
		$this->assignRef('searchcustomvalues', $this->searchCustomValues);
	}
}


//no closing tag