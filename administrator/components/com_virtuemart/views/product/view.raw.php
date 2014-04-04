<?php
/**
 *
 * View class for the product
 *
 * @package	VirtueMart
 * @subpackage
 * @author
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 6543 2012-10-16 06:41:27Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author RolandD,Max Milbers
 */
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

class VirtuemartViewProduct extends VmView {

	function display($tpl = null) {

		// Get the task
		$this->task = JRequest::getWord('task',$this->getLayout());

		// Load helpers
		$this->loadHelper('currencydisplay');
		$this->loadHelper('html');
		$this->loadHelper('image');

		$model = VmModel::getModel();
		
		switch ($this->task) {
			case 'massxref_cats':
			case 'massxref_cats_exe':
				$this->addTemplatePath(JPATH_VM_ADMINISTRATOR.'/views'.DS.'category'.DS.'tmpl');
				$this->loadHelper('permissions');
				$this->perms = Permissions::getInstance();
				$this->showVendors = $this->perms->check('admin');

				$keyWord ='';
				$catmodel = VmModel::getModel('category');
				$this->catmodel = $catmodel;
				//$this->addStandardDefaultViewCommands();
				$this->addStandardDefaultViewLists($catmodel,'category_name');

				$this->categories = $catmodel->getCategoryTree(0,0,false,$this->lists['search']);
				$this->pagination = $catmodel->getPagination();

				break;
			case 'massxref_sgrps':
			case 'massxref_sgrps_exe':
				$this->addTemplatePath(JPATH_VM_ADMINISTRATOR.'/views'.DS.'shoppergroup'.DS.'tmpl');
				$this->loadHelper('permissions');
				$this->perms = Permissions::getInstance();
				$this->showVendors = $this->perms->check('admin');
				$sgrpmodel = VmModel::getModel('shoppergroup');
				$this->addStandardDefaultViewLists($sgrpmodel);

				$this->shoppergroups = $sgrpmodel->getShopperGroups(false, true);

				$this->pagination = $sgrpmodel->getPagination();
				break;

			default:
			if ($product_parent_id=JRequest::getInt('product_parent_id',false) ) {
				$product_parent= $model->getProductSingle($product_parent_id,false);

				if($product_parent){
					$title='PRODUCT_CHILDREN_LIST' ;
					$link_to_parent =  JHTML::_('link', JRoute::_('index.php?view=product&task=edit&virtuemart_product_id='.$product_parent->virtuemart_product_id.'&option=com_virtuemart'), $product_parent->product_name, array('title' => JText::_('COM_VIRTUEMART_EDIT_PARENT').' '.$product_parent->product_name));
					$msg= JText::_('COM_VIRTUEMART_PRODUCT_OF'). " ".$link_to_parent;
				} else {
					$title='PRODUCT_CHILDREN_LIST' ;
					$msg= 'Parent with product_parent_id '.$product_parent_id.' not found';
				}

			} else {
				$title='PRODUCT';
				$msg="";
			}
			$this->db = JFactory::getDBO();

			$this->SetViewTitle($title, $msg );

			$this->addStandardDefaultViewLists($model,'created_on');
			$vendor = Permissions::getInstance()->isSuperVendor();
			if ($vendor == 1 ) $vendor = null;
			/* Get the list of products */
			$productlist = $model->getProductListing(false,false,false,false,true,true,0,$vendor);

			//The pagination must now always set AFTER the model load the listing
			$this->pagination = $model->getPagination();

			/* Get the category tree */
			$categoryId = $model->virtuemart_category_id; //OSP switched to filter in model, was JRequest::getInt('virtuemart_category_id');
			$this->category_tree = ShopFunctions::categoryListTree(array($categoryId));

			/* Load the product price */
			$this->loadHelper('calculationh');

			$vendor_model = VmModel::getModel('vendor');
			$productreviews = VmModel::getModel('ratings');

			foreach ($productlist as $virtuemart_product_id => $product) {
				if (isset($product->virtuemart_media_id) )
					$product->mediaitems = count($product->virtuemart_media_id);
				else $product->mediaitems = 'none';
				$product->reviews = $productreviews->countReviewsForProduct($product->virtuemart_product_id);

				$vendor_model->setId($product->virtuemart_vendor_id);
				$vendor = $vendor_model->getVendor();

				$currencyDisplay = CurrencyDisplay::getInstance($vendor->vendor_currency,$vendor->virtuemart_vendor_id);

				if(!empty($product->product_price) && !empty($product->product_currency) ){
					$product->product_price_display = $currencyDisplay->priceDisplay($product->product_price,(int)$product->product_currency,1,true);
				}

				/* Write the first 5 categories in the list */
				$product->categoriesList = shopfunctions::renderGuiList('virtuemart_category_id','#__virtuemart_product_categories','virtuemart_product_id',$product->virtuemart_product_id,'category_name','#__virtuemart_categories','virtuemart_category_id','category');

			}

			$mf_model = VmModel::getModel('manufacturer');
			$this->manufacturers = $mf_model->getManufacturerDropdown();

			/* add Search filter in lists*/
			/* Search type */
			$options = array( '' => JText::_('COM_VIRTUEMART_LIST_EMPTY_OPTION'),
							'parent' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_PARENT_PRODUCT'),
							'product' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_PRODUCT'),
							'price' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_PRICE'),
							'withoutprice' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE_TYPE_WITHOUTPRICE')
			);
			$this->lists['search_type'] = VmHTML::selectList('search_type', JRequest::getVar('search_type'),$options,1,'','','input-medium');

			/* Search order */
			$options = array( 'bf' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE_BEFORE'),
								  'af' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE_AFTER')
			);
			$this->lists['search_order'] = VmHTML::selectList('search_order', JRequest::getVar('search_order'),$options,1,'','','input-small');

			$this->productlist = $productlist;
			$this->virtuemart_category_id = $categoryId;
			$this->model = $model;
			break;
		}
		$tpl = 'results';
		parent::display($tpl);
		echo $this->AjaxScripts();
	}

	function displayLinkToChildList($product_id, $product_name) {

		//$this->db = JFactory::getDBO();
		$this->db->setQuery(' SELECT COUNT( * ) FROM `#__virtuemart_products` WHERE `product_parent_id` ='.$product_id);
		if ($result = $this->db->loadResult()){
			$result = JText::sprintf('COM_VIRTUEMART_X_CHILD_PRODUCT', $result);
			if ($this->frontEdit) $front = "&tmpl=component";
			else $front="";
			echo JHTML::_('link', JRoute::_('index.php?view=product&product_parent_id='.$product_id.'&option=com_virtuemart'.$front), '<div class="small">'.$result.'</div>', array('class'=> 'hasTooltip', 'title' => JText::sprintf('COM_VIRTUEMART_PRODUCT_LIST_X_CHILDREN',$product_name) ));
		}
	}

	function displayLinkToParent($product_parent_id) {

		//$this->db = JFactory::getDBO();
		$this->db->setQuery(' SELECT `product_name` FROM `#__virtuemart_products_'.VMLANG.'` as l JOIN `#__virtuemart_products` using (`virtuemart_product_id`) WHERE `virtuemart_product_id` = '.$product_parent_id);
		if ($product_name = $this->db->loadResult()){
			$result = JText::sprintf('COM_VIRTUEMART_LIST_CHILDREN_FROM_PARENT', $product_name);
			if ($this->frontEdit) $front = "&tmpl=component";
			else $front="";
			echo JHTML::_('link', JRoute::_('index.php?view=product&product_parent_id='.$product_parent_id.'&option=com_virtuemart'.$front ), '<div class="small">'.$product_name.'</div>', array('class'=> 'hasTooltip', 'title' => $result));
		}
	}

}

//pure php no closing tag
