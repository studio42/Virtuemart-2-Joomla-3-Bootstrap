<?php
/**
 *
 * List/add/edit/remove Order Status Types
 *
 * @package	VirtueMart
 * @subpackage OrderStatus
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 6307 2012-08-07 07:39:45Z alatak $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for maintaining the list of order types
 *
 * @package	VirtueMart
 * @subpackage OrderStatus
 * @author Oscar van Eijk
 */
class VirtuemartViewOrderstatus extends VmView {

	function display($tpl = null) {

		// Load the helper(s)


		$this->loadHelper('html');

		$model = VmModel::getModel();

		$this->stockHandelList = array(
			'A' => 'COM_VIRTUEMART_ORDER_STATUS_STOCK_AVAILABLE',
			'R' => 'COM_VIRTUEMART_ORDER_STATUS_STOCK_RESERVED',
			'O' => 'COM_VIRTUEMART_ORDER_STATUS_STOCK_OUT'
		);
		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model);
		$this->lists['vmCoreStatusCode'] = $model->getVMCoreStatusCode();
		$this->orderStatusList = $model->getOrderStatusList();
		$this->pagination = $model->getPagination();

		parent::display('results');
		echo $this->AjaxScripts();
	}
}

//No Closing Tag
