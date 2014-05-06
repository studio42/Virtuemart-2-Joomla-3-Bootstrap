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
		$task = JRequest::getWord('task',$this->getLayout());
		vmdebug('VirtuemartViewProduct '.$task);
		$this->task = $task;

		// Load helpers
		$this->loadHelper('currencydisplay');
		$this->loadHelper('html');
		$this->loadHelper('image');

		$model = VmModel::getModel();
		// Handle any publish/unpublish
		switch ($task) {
			case 'add':
			case 'edit':

				VmConfig::loadJLang('com_virtuemart_orders',TRUE);
				VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
				$this->jsonPath = JFactory::getApplication ()->isSite () ? juri::root() :'';
				$virtuemart_product_id = JRequest::getVar('virtuemart_product_id', array());

				if(is_array($virtuemart_product_id) && count($virtuemart_product_id) > 0){
					$virtuemart_product_id = (int)$virtuemart_product_id[0];
				} else {
					$virtuemart_product_id = (int)$virtuemart_product_id;
				}

				$product = $model->getProductSingle($virtuemart_product_id,false);
				$product_parent= $model->getProductParent($product->product_parent_id);

				$mf_model = VmModel::getModel('manufacturer');
				$manufacturers = $mf_model->getManufacturerDropdown($product->virtuemart_manufacturer_id);
				$this->manufacturers = $manufacturers;

				// set category in front edit link
				if ($task == 'add') {
					if ($category_id = jRequest::getInt('virtuemart_category_id',0) )
						$product->categories = array($category_id);
				}
				// Get the category tree
				if (isset($product->categories)) $this->category_tree = ShopFunctions::categoryListTree($product->categories);
				else $this->category_tree = ShopFunctions::categoryListTree();

				//Get the shoppergoup list - Cleanshooter Custom Shopper Visibility
				if (isset($product->shoppergroups)) $this->shoppergroupList = ShopFunctions::renderShopperGroupList($product->shoppergroups);
				else $this->shoppergroupList = '';

				// Load the product price
				$this->loadHelper('calculationh');

				$product_childIds = $model->getProductChildIds($virtuemart_product_id);

				$product_childs = array();
				foreach($product_childIds as $id){
					$product_childs[] = $model->getProductSingle($id,false);
				}
				$this->assignRef('product_childs', $product_childs);

				if(!class_exists('VirtueMartModelConfig')) require(JPATH_VM_ADMINISTRATOR.'/models'.DS.'config.php');
				$this->productLayouts = VirtueMartModelConfig::getLayoutList('productdetails',$product->layout);

				// Load Images
				$model->addImages($product);

				if(is_Dir(VmConfig::get('vmtemplate').DS.'images'.DS.'availability'.DS)){
					$imagePath = VmConfig::get('vmtemplate').'/images/availability/';
				} else {
					$imagePath = '/components/com_virtuemart/assets/images/availability/';
				}
				$this->imagePath = $imagePath;

				// Load the vendors
				$vendor_model = VmModel::getModel('vendor');

				if(Vmconfig::get('multix','none')!=='none'){
					if ($task =='add') $vendor_id = $this->adminVendor;
					else $vendor_id = $product->virtuemart_vendor_id ;
					$lists['vendors'] = Shopfunctions::renderVendorList($vendor_id);
				}
				// Load the currencies
				$currency_model = VmModel::getModel('currency');
				$this->loadHelper('permissions');
				$vendor_model->setId(Permissions::getInstance()->isSuperVendor());
				$vendor = $vendor_model->getVendor();
				
				if(empty($product->product_currency)){
					$product->product_currency = $vendor->vendor_currency;
				}
				//STUDIO42  fix for currency, old method set 2 time same currency symbol
				// TODO verify all others
				$currencyModel = VmModel::getModel('currency');
				$currencyModel->setId($vendor->vendor_currency);
				$currency = $currencyModel->getData();
				$this->vendor_currency = $currency->currency_symbol;

				$currencyModel->setId($product->product_currency);
				$currency = $currencyModel->getData();
				$this->product_currency = $currency->currency_symbol;

				if(count($manufacturers)>0 ){
					$lists['manufacturers'] = JHTML::_('select.genericlist', $manufacturers, 'virtuemart_manufacturer_id', 'class="inputbox"', 'value', 'text', $product->virtuemart_manufacturer_id );
				}

				$lists['product_weight_uom'] = ShopFunctions::renderWeightUnitList('product_weight_uom',$task=='add'? VmConfig::get('weight_unit_default'): $product->product_weight_uom);
				$lists['product_iso_uom'] = ShopFunctions::renderUnitIsoList('product_unit',$task=='add'? VmConfig::get('weight_unit_default'): $product->product_unit);
				$lists['product_lwh_uom'] = ShopFunctions::renderLWHUnitList('product_lwh_uom', $task=='add'?VmConfig::get('lwh_unit_default') : $product->product_lwh_uom);

				if( empty( $product->product_available_date )) {
					$product->product_available_date = date("Y-m-d") ;
				}
				$waitinglistmodel = VmModel::getModel('waitinglist');
				/* Load waiting list */
				if ($product->virtuemart_product_id) {
					//$waitinglist = $this->get('waitingusers', 'waitinglist');
					$waitinglist = $waitinglistmodel->getWaitingusers($product->virtuemart_product_id);
					$this->assignRef('waitinglist', $waitinglist);
				}
				$productShoppers = $model->getProductShoppersByStatus($product->virtuemart_product_id,array('S') );
				$this->assignRef('productShoppers', $productShoppers);
				$orderstatusModel = VmModel::getModel('orderstatus');
				$lists['OrderStatus'] = $orderstatusModel->renderOSList(array(),'order_status',TRUE);
				$field_model = VmModel::getModel('customfields');
				$fieldTypes = $field_model->getField_types();
				$this->assignRef('fieldTypes', $fieldTypes);

				/* Load product types lists */
				if ($customsList = $field_model->getCustomsList()) {
					$emptyOption = JHTML::_ ('select.option', '', '- '.JText::_ ('COM_VIRTUEMART_LIST_EMPTY_OPTION').' :', 'value', 'text');
					array_unshift ($customsList, $emptyOption);
					$customlist = JHTML::_('select.genericlist', $customsList,'customlist');
					if ($task="add") {
						if ($customfieldsDefault = $this->getBLankCustomfields()) {
						$product->customfields = $customfieldsDefault ;
						// var_dump( $customfieldsDefault);jexit();
						}
					}
				}
				$this->assignRef('customsList', $customlist);

				$ChildCustomRelation = $field_model->getProductChildCustomRelation();
				$this->assignRef('ChildCustomRelation',$ChildCustomRelation);


				if ($product->product_parent_id > 0) {

					$parentRelation= $field_model->getProductParentRelation($product->virtuemart_product_id);
					$this->assignRef('parentRelation',$parentRelation);

					// Set up labels
					$info_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_ITEM_INFO_LBL');
					$status_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_ITEM_STATUS_LBL');
					$dim_weight_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_ITEM_DIM_WEIGHT_LBL');
					$images_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_ITEM_IMAGES_LBL');
					$delete_message = JText::_('COM_VIRTUEMART_PRODUCT_FORM_DELETE_ITEM_MSG');
				}
				else {
					if ($task == 'add') $action = JText::_('COM_VIRTUEMART_PRODUCT_FORM_NEW_PRODUCT_LBL');
					else $action = JText::_('COM_VIRTUEMART_PRODUCT_FORM_UPDATE_ITEM_LBL');

					$info_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRODUCT_INFO_LBL');
					$status_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRODUCT_STATUS_LBL');
					$dim_weight_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRODUCT_DIM_WEIGHT_LBL');
					$images_label = JText::_('COM_VIRTUEMART_PRODUCT_FORM_PRODUCT_IMAGES_LBL');
					$delete_message = JText::_('COM_VIRTUEMART_PRODUCT_FORM_DELETE_PRODUCT_MSG');
				}


				$this->assignRef('product', $product);
				$product_empty_price = array(
					'virtuemart_product_price_id' => 0
				, 'virtuemart_product_id'         => $virtuemart_product_id
				, 'virtuemart_shoppergroup_id'    => NULL
				, 'product_price'                 => NULL
				, 'override'                      => NULL
				, 'product_override_price'        => NULL
				, 'product_tax_id'                => NULL
				, 'product_discount_id'           => NULL
				, 'product_currency'              => $vendor->vendor_currency
				, 'product_price_publish_up'      => NULL
				, 'product_price_publish_down'    => NULL
				, 'price_quantity_start'          => NULL
				, 'price_quantity_end'            => NULL
				);
				$this->assignRef ('product_empty_price', $product_empty_price);

				$this->assignRef('product_parent', $product_parent);
				/* Assign label values */
				$this->assignRef('action', $action);
				$this->assignRef('info_label', $info_label);
				$this->assignRef('status_label', $status_label);
				$this->assignRef('dim_weight_label', $dim_weight_label);
				$this->assignRef('images_label', $images_label);
				$this->assignRef('delete_message', $delete_message);
				$this->assignRef('lists', $lists);
				// Toolbar
				if ($product->product_sku) $sku=' ('.$product->product_sku.')'; else $sku="";
				if (!empty($product->canonCatLink)) $canonLink = '&virtuemart_category_id=' . $product->canonCatLink; else $canonLink = '';
				if(!empty($product->virtuemart_product_id)){
				$text = '<a href="'.juri::root().'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product->virtuemart_product_id.$canonLink.'" target="_blank" >'. $product->product_name.$sku.'<span class="vm2-modallink"></span></a>';
				} else {
					$text = $product->product_name.$sku;
				}
				$this->SetViewTitle('PRODUCT',$text);
				//JToolBarHelper::custom('sentproductemailtocustomer', 'email_32', 'email_32',  'COM_VIRTUEMART_PRODUCT_EMAILTOSHOPPER' ,false);
				$this->addStandardEditViewCommands ($product->virtuemart_product_id);
				break;

			case 'massxref_cats':
			case 'massxref_cats_exe':
//TODO test if path is ok addpath is now in the constructor
				$this->addTemplatePath(JPATH_VM_ADMINISTRATOR.'/views'.DS.'category'.DS.'tmpl');
				$this->SetViewTitle('PRODUCT_MASSXREF');
				// $this->setLayout('massxref');
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

				// restore icon is good but not the word
				JToolBarHelper::custom('display','restore', 'restore', JText::_('JTOOLBAR_BACK'), false);
				JToolBarHelper::custom('massxref_cats_exe', 'assign', 'assign', JText::_('COM_VIRTUEMART_MASS_REPLACE'), false);
				JToolBarHelper::custom('massxref_cats_add', 'new', 'new', JText::_('COM_VIRTUEMART_MASS_ADD'), true);
				break;

			case 'massxref_sgrps':
			case 'massxref_sgrps_exe':
				$this->SetViewTitle('PRODUCT_MASSXREF');
//TODO test if path is ok addpath is now in the constructor
				$this->addTemplatePath(JPATH_VM_ADMINISTRATOR.'/views'.DS.'shoppergroup'.DS.'tmpl');
				$this->loadHelper('permissions');
				$this->perms = Permissions::getInstance();
				$this->showVendors = $this->perms->check('admin');
				$sgrpmodel = VmModel::getModel('shoppergroup');
				$this->addStandardDefaultViewLists($sgrpmodel);

				$this->shoppergroups = $sgrpmodel->getShopperGroups(false, true);

				$this->pagination = $sgrpmodel->getPagination();

				JToolBarHelper::custom('massxref_sgrps_exe', 'groups-add', 'new', JText::_('COM_VIRTUEMART_PRODUCT_XREF_SGRPS_EXE'), false);

				break;

		default:
			if ($product_parent_id=JRequest::getInt('product_parent_id',false) ) {
				$product_parent= $model->getProductSingle($product_parent_id,false);

				if($product_parent){
					$title='PRODUCT_CHILDREN_LIST' ;
					$link_to_parent =  JHTML::_('link', JRoute::_('index.php?view=product&task=edit&virtuemart_product_id='.$product_parent->virtuemart_product_id.'&option=com_virtuemart'), $product_parent->product_name, 
						array('class'=> 'hasTooltip', 'title' => JText::_('COM_VIRTUEMART_EDIT_PARENT').' '.$product_parent->product_name));
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
			$this->loadHelper('permissions');
			$this->SetViewTitle($title, $msg );

			$this->addStandardDefaultViewLists($model,'created_on', 'DESC', 'filter_product');
			$vendor_id = $this->adminVendor;
			if ($vendor_id == 1 ) $vendor_id = null;
			// fix ???
			$catid = JRequest::getInt('uctlist = ',0);
			/* Get the list of products */
			$productlist = $model->getProductListing(false,false,false,false,true,true,$catid,$vendor_id);

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
			$this->lists['search_type'] = VmHTML::selectList('search_type', JRequest::getVar('search_type'),$options,1,'','onchange="Joomla.ajaxSearch(this); return false;"','input-medium');

			/* Search order */
			$options = array( 'bf' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE_BEFORE'),
								  'af' => JText::_('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE_AFTER')
			);
			$this->lists['search_order'] = VmHTML::selectList('search_order', JRequest::getVar('search_order'),$options,1,'','','input-small');

			// Toolbar

			//JToolBarHelper::save('sentproductemailtoshoppers', JText::_('COM_VIRTUEMART_PRODUCT_EMAILTOSHOPPERS'));
			JToolBarHelper::custom('massxref_cats', 'new', 'new', JText::_('COM_VIRTUEMART_PRODUCT_XREF_CAT'), true);
			JToolBarHelper::custom('massxref_sgrps', 'new', 'new', JText::_('COM_VIRTUEMART_PRODUCT_XREF_SGRPS'), true);
			JToolBarHelper::custom('createchild', 'new', 'new', JText::_('COM_VIRTUEMART_PRODUCT_CHILD'), true);
			JToolBarHelper::custom('cloneproduct', 'copy', 'copy', JText::_('COM_VIRTUEMART_PRODUCT_CLONE'), true);
			JToolBarHelper::custom('addrating', 'star-2', '', JText::_('COM_VIRTUEMART_ADD_RATING'), true);
			$this->addStandardDefaultViewCommands();


			$this->productlist = $productlist;
			$this->virtuemart_category_id = $categoryId;
			$this->model = $model;

			break;
		}

		parent::display($tpl);
	}

	/**
	 * This is wrong
	 *@deprecated
	 */
	function renderMail() {
		$this->setLayout('mail_html_waitlist');
		$this->subject = JText::sprintf('COM_VIRTUEMART_PRODUCT_WAITING_LIST_EMAIL_SUBJECT', $this->productName);
		$notice_body = JText::sprintf('COM_VIRTUEMART_PRODUCT_WAITING_LIST_EMAIL_BODY', $this->productName, $this->url);

		parent::display();
	}


	/**
	 * Renders the list for the discount rules
	 *
	 * @author Max Milbers
	 */
	function renderDiscountList($selected,$name='product_discount_id',$id=null){
		if ($id ===null) $id = $name ;
		if(!class_exists('VirtueMartModelCalc')) require(JPATH_VM_ADMINISTRATOR.'/models'.DS.'calc.php');
		$discounts = VirtueMartModelCalc::getDiscounts();

		$discountrates = array();
		$discountrates[] = JHTML::_('select.option', '-1', JText::_('COM_VIRTUEMART_PRODUCT_DISCOUNT_NONE'), 'product_discount_id' );
		$discountrates[] = JHTML::_('select.option', '0', JText::_('COM_VIRTUEMART_PRODUCT_DISCOUNT_NO_SPECIAL'), 'product_discount_id' );
		//		$discountrates[] = JHTML::_('select.option', 'override', JText::_('COM_VIRTUEMART_PRODUCT_DISCOUNT_OVERRIDE'), 'product_discount_id');
		foreach($discounts as $discount){
			$discountrates[] = JHTML::_('select.option', $discount->virtuemart_calc_id, $discount->calc_name, 'product_discount_id');
		}
		$listHTML = JHTML::_('Select.genericlist', $discountrates, $name, '', 'product_discount_id', 'text', $selected, $id);
		return $listHTML;

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
	// add the default customfields by parent_id and admin_only setting
	function getBLankCustomfields(){
		$db = JFactory::getDBO();
		$model = VmModel::getModel('Customfields') ;
		$vendor = $this->adminVendor;
		$query = 'SELECT *,custom_value as value  FROM `#__virtuemart_customs` WHERE `custom_parent_id` in 
			(
				SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` WHERE `field_type` = "P" AND `admin_only` = 1
			)';
		if ($vendor > 1) {
			$query .= " AND shared = 1";
		}
		$query .= ' order by custom_parent_id asc';
		$db->setQuery($query);
		$fields = $db->loadObjectlist();
		$row = 0;
		foreach ($fields as &$field) {
			$field->display = $model->displayProductCustomfieldBE($field,0,$row);
			$row++;
		}
		return $fields;
	}
}

//pure php no closing tag
