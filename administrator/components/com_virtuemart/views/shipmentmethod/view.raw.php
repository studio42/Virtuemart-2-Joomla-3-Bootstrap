<?php
/**
*
* Shipment  View
*
* @package	VirtueMart
* @subpackage Shipment
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6326 2012-08-08 14:14:28Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for maintaining the list of shipment
 *
 * @package	VirtueMart
 * @subpackage Shipment
 * @author RickG
 */
class VirtuemartViewShipmentmethod extends VmView {

	function display($tpl = null) {

		// Load the helper(s)
		$this->addHelperPath(JPATH_VM_ADMINISTRATOR.'/helpers');
		$this->loadHelper('permissions');
		$this->loadHelper('vmpsplugin');
		$this->loadHelper('html');
		$model = VmModel::getModel();

		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model);
		$this->shipments = $model->getShipments();
		$this->pagination = $model->getPagination();
		$this->installedShipments = $this->shipmentsPlgList();

		parent::display('results');
		echo $this->AjaxScripts();
	}

	// list all payement(enabled or disabeld
	function shipmentsPlgList(){
		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `#__extensions` WHERE `folder` = "vmshipment"';// AND `enabled`="1" ';
		$db->setQuery($q);
		return $db->loadObjectList('extension_id');
	}
}
// pure php no closing tag
