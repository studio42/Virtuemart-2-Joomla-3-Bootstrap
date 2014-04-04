<?php
/**
 *
 * List/add/edit/remove Users
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Oscar van Eijk
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

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');
jimport('joomla.version');

/**
 * HTML View class for maintaining the list of users
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Oscar van Eijk
 */
class VirtuemartViewUser extends VmView {

	function display($tpl = null) {

		// Load the helper(s)
		$this->loadHelper('html');
		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.'/helpers'.DS.'permissions.php');
		$perm = Permissions::getInstance();
		$this->perm = $perm ;

		$model = VmModel::getModel();
		$this->addStandardDefaultViewLists($model,'ju.id');
		$this->userList = $model->getUserList();
		$this->pagination = $model->getPagination();
		$shoppergroupmodel = VmModel::getModel('shopperGroup');
		$this->defaultShopperGroup = $shoppergroupmodel->getDefault(0)->shopper_group_name;

		parent::display('results');
		echo $this->AjaxScripts();
	}

}

//No Closing Tag
