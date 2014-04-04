<?php
/**
 *
 * Shopper group View
 *
 * @package	VirtueMart
 * @subpackage ShopperGroup
 * @author Markus �hler
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 6373 2012-08-24 10:41:03Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for maintaining the list of shopper groups
 *
 * @package	VirtueMart
 * @subpackage ShopperGroup
 * @author Markus �hler
 */
class VirtuemartViewShopperGroup extends VmView {

	function display($tpl = null) {
		// Load the helper(s)

		$this->loadHelper('html');
		$this->loadHelper('permissions');

		$model = VmModel::getModel();

		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model);
		$this->showVendors = Permissions::getInstance()->check('admin');
		$this->shoppergroups = $model->getShopperGroups(false, true);
		$this->pagination = $model->getPagination();

		parent::display('results');
		echo $this->AjaxScripts();
	}

} // pure php no closing tag
