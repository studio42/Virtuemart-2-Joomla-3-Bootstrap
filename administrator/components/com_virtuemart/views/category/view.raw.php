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

/**
 * HTML View class for maintaining the list of categories
 *
 * @package	VirtueMart
 * @subpackage Category
 * @author RickG, jseros
 */
class VirtuemartViewCategory extends VmView {

	function display($tpl = null) {
		//Template path and helper fix for Front-end editing
		$this->addTemplatePath(JPATH_VM_ADMINISTRATOR.'/views'.DS.'category'.DS.'tmpl');
		$this->addHelperPath(JPATH_VM_ADMINISTRATOR.'/helpers');
		$this->loadHelper('html');
		if (!class_exists ('shopFunctionsF'))
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		$model = VmModel::getModel();

		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.'/helpers'.DS.'permissions.php');
		$this->perms = Permissions::getInstance();
		$this->catmodel = $model;
		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model,'category_name');
		$this->task = JRequest::getWord('task','');
		$this->categories = $model->getCategoryTree(0,0,false,$this->lists['search']);
		$this->pagination = $model->getPagination();
		//we need a function of the FE shopfunctions helper to cut the category descriptions
		jimport('joomla.filter.output');

		parent::display('results');
		echo $this->AjaxScripts();
	}

}

// pure php no closing tag
