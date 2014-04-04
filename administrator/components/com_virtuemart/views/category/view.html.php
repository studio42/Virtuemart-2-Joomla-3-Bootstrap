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
			$this->categoryLayouts = VirtueMartModelConfig::getLayoutList('category');
			$this->productLayouts = VirtueMartModelConfig::getLayoutList('productdetails');

			//Nice fix by Joe, the 4. param prevents setting an category itself as child
			$this->categorylist = ShopFunctions::categoryListTree(array($this->parent->virtuemart_category_id), 0, 0, (array) $category->virtuemart_category_id);

			if(Vmconfig::get('multix','none')!=='none'){
				$this->vendorList= ShopFunctions::renderVendorList($category->virtuemart_vendor_id,false);
			}
			$this->category = $category;
			$this->addStandardEditViewCommands($category->virtuemart_category_id,$category);
		}
		else {
			$this->SetViewTitle('CATEGORY_S');

			$this->catmodel = $model;
			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model,'category_name');

			$this->categories = $model->getCategoryTree(0,0,false,$this->lists['search']);
			$this->pagination = $model->getPagination();

			//we need a function of the FE shopfunctions helper to cut the category descriptions
			jimport('joomla.filter.output');
		}

		parent::display($tpl);
	}

}

// pure php no closing tag
