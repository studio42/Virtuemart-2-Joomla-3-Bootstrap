<?php

/**
 *
 * Category View
 *
 * @package	VirtueMart
 * @subpackage Category
 * @author RickG, jseros
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 6475 2012-09-21 11:54:21Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');
jimport('joomla.html.pane');

/**
 * HTML View class for maintaining the list of categories
 *
 * @package	VirtueMart
 * @subpackage Category
 * @author RickG, jseros
 */
class VirtuemartViewCategory extends VmView {

	function display($tpl = null) {

		$this->loadHelper('html');
		$this->loadHelper('permissions');
		if (!class_exists ('shopFunctionsF'))
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');

		$model = VmModel::getModel();
		$layoutName = $this->getLayout();

		$task = JRequest::getWord('task',$layoutName);
		$this->task = $task ;

		$this->perms = Permissions::getInstance();
		// to add in vmview ?
		$multivendor = Vmconfig::get('multix','none');
		$this->multiX = $multivendor !=='none' && $multivendor !='' ? true : false ;

		if ($layoutName == 'edit') {

			$this->loadHelper('shopfunctions');
			$category = $model->getCategory('',false);

			if (isset($category->category_name)) $name = $category->category_name; else $name ='';
			$this->SetViewTitle('CATEGORY',$name);

			$model->addImages($category);

			if ( $category->virtuemart_category_id) {
				$this->relationInfo = $model->getRelationInfo( $category->virtuemart_category_id );
			}
			$this->parent = $model->getParentCategory( $category->virtuemart_category_id );
			$this->jTemplateList = ShopFunctions::renderTemplateList(JText::_('COM_VIRTUEMART_CATEGORY_TEMPLATE_DEFAULT'));

			if(!class_exists('VirtueMartModelConfig'))require(JPATH_VM_ADMINISTRATOR.'/models'.DS.'config.php');
			$this->productLayouts = VirtueMartModelConfig::getLayoutList('productdetails',$category->category_product_layout,'category_product_layout');
			$this->categoryLayouts = VirtueMartModelConfig::getLayoutList('category',$category->category_layout,'categorylayout');
			// front autoset parent category
			if (!$category->virtuemart_category_id) $this->parent->virtuemart_category_id = jRequest::getInt('category_parent_id',0);
			//Nice fix by Joe, the 4. param prevents setting an category itself as child
			// Note Studio42, the fix is not suffisant, you can set the category in a children and get infinit loop in router for eg.
			$this->categorylist = ShopFunctions::categoryListTree(array($this->parent->virtuemart_category_id), 0, 0, (array) $category->virtuemart_category_id);

			if(Vmconfig::get('multix','none')!=='none'){
				$this->vendorList= ShopFunctions::renderVendorList($category->virtuemart_vendor_id,false);
			}
			$this->category = $category;
			$this->addStandardEditViewCommands($category->virtuemart_category_id,$category);
		}
		else {
			$category_id = JRequest::getInt('filter_category_id');
			if ( JRequest::getWord('format', '') === 'raw') {
				$tpl = 'results';
			}
			else 
			{
				$this->SetViewTitle('CATEGORY_S');
				$this->addStandardDefaultViewCommands();
				$this->categorylist = ShopFunctions::categoryListTreeLoop( (array)$category_id, 0, 0);
				if ($this->multiX && $this->adminVendor == 1) {
					JToolBarHelper::custom('toggle.shared.1', 'publish', 'yes', JText::_('COM_VIRTUEMART_SHARED'), true);
					JToolBarHelper::custom('toggle.shared.0', 'unpublish', 'no', JText::_('COM_VIRTUEMART_SHARED'), true);
				}
			}
			$this->catmodel = $model;
			$this->addStandardDefaultViewLists($model,'category_name');

			$this->categories = $model->getCategoryTree($category_id,0,false,$this->lists['search']);
			$this->pagination = $model->getPagination();

			//we need a function of the FE shopfunctions helper to cut the category descriptions
			jimport('joomla.filter.output');
		}
		parent::display($tpl);
		if ($tpl === 'results') echo $this->AjaxScripts();
	}

}

// pure php no closing tag
