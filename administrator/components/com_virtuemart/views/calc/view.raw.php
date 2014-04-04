<?php
/**
*
* Calc View
*
* @package	VirtueMart
* @subpackage Calculation tool
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
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
 * Description
 *
 * @package		VirtueMart
 * @author
 */

class VirtuemartViewCalc extends VmView {

	function display($tpl = null) {

		// Load the helper(s)


		$this->loadHelper('html');

		$model = VmModel::getModel('calc');
		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.'/helpers'.DS.'permissions.php');
		$this->perms = Permissions::getInstance();

		//@todo should be depended by loggedVendor
		$this->vendorId=1;
		$db = JFactory::getDBO();
		JToolBarHelper::custom('toggle.calc_shopper_published.0', 'unpublish', 'no', JText::_('COM_VIRTUEMART_CALC_SHOPPER_PUBLISH_TOGGLE_OFF'), true);
		JToolBarHelper::custom('toggle.calc_shopper_published.1', 'publish', 'yes', JText::_('COM_VIRTUEMART_CALC_SHOPPER_PUBLISH_TOGGLE_ON'), true);
		JToolBarHelper::custom('toggle.calc_vendor_published.0', 'unpublish', 'no', JText::_('COM_VIRTUEMART_CALC_VENDOR_PUBLISH_TOGGLE_OFF'), true);
		JToolBarHelper::custom('toggle.calc_vendor_published.1', 'publish', 'yes', JText::_('COM_VIRTUEMART_CALC_VENDOR_PUBLISH_TOGGLE_ON'), true);

		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model);
		$this->calcs = $model->getCalcs(false, false, $this->lists['search']);
		$this->pagination = $model->getPagination();

		parent::display('results');
		echo $this->AjaxScripts();
	}

}
// pure php no closing tag