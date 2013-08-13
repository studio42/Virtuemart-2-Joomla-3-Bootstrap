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

		// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
		vmJsApi::jPrice();

		$document = JFactory::getDocument();

		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

		if (!class_exists('VmImage'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'image.php');
		$categoryModel = VmModel::getModel('category');
		$productModel = VmModel::getModel('product');

		$search = JRequest::getvar('keyword', null);
		if ($search !==null) {
			$searchcustom = $this->getSearchCustom();
		}
		$this->assignRef('keyword', $keyword);
		$this->assignRef('search', $search);

		$categoryId = JRequest::getInt('virtuemart_category_id', ShopFunctionsF::getLastVisitedCategoryId());

		$virtuemart_manufacturer_id = JRequest::getInt('virtuemart_manufacturer_id',0 );

		$this->setCanonicalLink($tpl,$document,$categoryId);
		$vendorId = JRequest::getInt('virtuemart_vendor_id', 1);
		// Load the products in the given category
		$products = $productModel->getProductsInCategory($categoryId,$vendorId);
		$productModel->addImages($products,1);

		$this->assignRef('products', $products);

		if ($products) {
			$currency = CurrencyDisplay::getInstance( );
			$this->assignRef('currency', $currency);
			foreach($products as $product){
				$product->stock = $productModel->getStockIndicator($product);
			}
		}

		$ratingModel = VmModel::getModel('ratings');
		$showRating = $ratingModel->showRating();
		$this->assignRef('showRating', $showRating);

		$orderByList = $productModel->getOrderByList($categoryId);
		$this->assignRef('orderByList', $orderByList);

		// Add feed links
		if ($products  && VmConfig::get('feed_cat_published', 0)==1) {
			$link = '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$document->addHeadLink(JRoute::_($link . '&type=rss', FALSE), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$document->addHeadLink(JRoute::_($link . '&type=atom', FALSE), 'alternate', 'rel', $attribs);
		}

		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		$showBasePrice = Permissions::getInstance()->check('admin'); //todo add config settings
		$this->assignRef('showBasePrice', $showBasePrice);

		//set this after the $categoryId definition
		$paginationAction=JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$categoryId , FALSE);
		$this->assignRef('paginationAction', $paginationAction);

		shopFunctionsF::setLastVisitedCategoryId($categoryId);
		shopFunctionsF::setLastVisitedManuId($virtuemart_manufacturer_id);


		if($categoryId!==-1){
			$vendorId = JRequest::getInt('virtuemart_vendor_id', 1);
			$category = $categoryModel->getCategory($categoryId);
		}

		$perRow = empty($category->products_per_row)? VmConfig::get('products_per_row',3):$category->products_per_row;
		$this->assignRef('perRow', $perRow);

		$pagination = $productModel->getPagination($perRow);
		$this->assignRef('vmPagination', $pagination);

		if(!empty($category)){

			if ((!empty($categoryId) and $categoryId!==-1 ) and (empty($category->slug) or !$category->published)) {

				if(empty($category->slug)){
					vmInfo(JText::_('COM_VIRTUEMART_CAT_NOT_FOUND'));
				} else {
					if($category->virtuemart_id!==0 and !$category->published){
						vmInfo('COM_VIRTUEMART_CAT_NOT_PUBL',$category->category_name,$categoryId);
						//return false;
					}
				}

				$categoryLink = '';
				if ($category->category_parent_id) {
					$categoryLink = '&view=category&virtuemart_category_id=' .$category->category_parent_id;
				} else {
					$last_category_id = shopFunctionsF::getLastVisitedCategoryId();
					if (!$last_category_id or $categoryId == $last_category_id) {
						$last_category_id = JRequest::getInt('virtuemart_category_id', false);
					}
					if ($last_category_id and $categoryId != $last_category_id) {
						$categoryLink = '&view=category&virtuemart_category_id=' . $last_category_id;
					}
				}

				$app->redirect(JRoute::_('index.php?option=com_virtuemart' . $categoryLink . '&error=404', FALSE));

				return;
			}

			//No redirect here, category id = 0 means show ALL categories! note by Max Milbers
			/*		if(empty($category->virtuemart_vendor_id) && $search == null ) {
						$app -> enqueueMessage(JText::_('COM_VIRTUEMART_CATEGORY_NOT_FOUND'));
						$app -> redirect( 'index.php');
					}*/

			// Add the category name to the pathway
			if ($category->parents) {
				foreach ($category->parents as $c){
					$pathway->addItem(strip_tags($c->category_name),JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$c->virtuemart_category_id, FALSE));
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
				$params = new JRegistry;
				$category->event = new stdClass;
				$category->catid = $category->virtuemart_category_id;
				$category->id = null;
				$category->title = $category->category_name;
				$category->text = $category->category_description;
				$results = $dispatcher->trigger('onContentPrepare', array('com_virtuemart.category', &$category, &$params, 0));
				// More events for 3rd party content plugins
				// This do not disturb actual plugins, because we don't modify $product->text
				$res = $dispatcher->trigger('onContentAfterTitle', array('com_virtuemart.category', &$category, &$params, 0));
				$category->event->afterDisplayTitle = trim(implode("\n", $res));

				$res = $dispatcher->trigger('onContentBeforeDisplay', array('com_virtuemart.category', &$category, &$params, 0));
				$category->event->beforeDisplayContent = trim(implode("\n", $res));

				$res = $dispatcher->trigger('onContentAfterDisplay', array('com_virtuemart.category', &$category, &$params, 0));
				$category->event->afterDisplayContent = trim(implode("\n", $res));
				$category->category_description = $category->text;
				$category->category_name = $category->title;
			}



			if ($category->metadesc) {
				$document->setDescription( $category->metadesc );
			}
			if ($category->metakey) {
				$document->setMetaData('keywords', $category->metakey);
			}
			if ($category->metarobot) {
				$document->setMetaData('robots', $category->metarobot);
			}


			if ($app->getCfg('MetaAuthor') == '1') {
				$document->setMetaData('author', $category->metaauthor);
			}

			if(empty($category->category_template)){
				$category->category_template = VmConfig::get('categorytemplate');
			}

			shopFunctionsF::setVmTemplate($this,$category->category_template,0,$category->category_layout);
		} else {
			//Backward compatibility
			if(!isset($category)) {
				$category = new stdClass();
				$category->category_name = '';
				$category->category_description= '';
				$category->haschildren= false;
			}
		}

		$this->assignRef('category', $category);

	    // Set the titles
		if (!empty($category->customtitle)) {
        	$title = strip_tags($category->customtitle);
     	} elseif (!empty($category->category_name)) {
     		$title = strip_tags($category->category_name);
		} else {
			$title = $this->setTitleByJMenu($app);
		}

	  	if(JRequest::getInt('error')){
			$title .=' '.JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
		}
		if(!empty($keyword)){
			$title .=' ('.$keyword.')';
		}

		if ($virtuemart_manufacturer_id and !empty($products[0])) $title .=' '.$products[0]->mf_name ;
		$document->setTitle( $title );
		// Override Category name when viewing manufacturers products !IMPORTANT AFTER page title.
		if (JRequest::getInt('virtuemart_manufacturer_id' ) and !empty($products[0]) and isset($category->category_name)) $category->category_name =$products[0]->mf_name ;

		if ($app->getCfg('MetaTitle') == '1') {
			$document->setMetaData('title',  $title);
		}

		// set search and keyword
		if ($keyword = vmRequest::uword('keyword', '0', ' ,-,+,.,_')) {
			$pathway->addItem($keyword);
			//$title .=' ('.$keyword.')';
		}

		parent::display($tpl);
	}

	public function setTitleByJMenu($app){
		$menus	= $app->getMenu();
		$menu = $menus->getActive();
		$title = 'VirtueMart Category View';
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
		return $title;
	}

	public function setCanonicalLink($tpl,$document,$categoryId){
		// Set Canonic link
		if (!empty($tpl)) {
			$format = $tpl;
		} else {
			$format = JRequest::getWord('format', 'html');
		}
		if ($format == 'html') {

			$document->addHeadLink( JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$categoryId, FALSE) , 'canonical', 'rel', '' );

		}
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
			vmJsApi::chosenDropDowns();
			$this->searchCustomList = JText::_('COM_VIRTUEMART_SET_PRODUCT_TYPE').' '.JHTML::_('select.genericlist',$this->options, 'custom_parent_id', 'class="inputbox vm-chzn-select"', 'virtuemart_custom_id', 'custom_title', $this->custom_parent_id);
		} else {
			$this->searchCustomList = '';
		}

		$this->assignRef('searchcustom', $this->searchCustomList);
		$this->assignRef('searchcustomvalues', $this->searchCustomValues);
	}
}


//no closing tag