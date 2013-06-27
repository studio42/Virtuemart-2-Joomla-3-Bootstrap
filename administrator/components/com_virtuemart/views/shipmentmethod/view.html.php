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
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

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
		$this->addHelperPath(JPATH_VM_ADMINISTRATOR.DS.'helpers');

		$this->loadHelper('permissions');
		$this->loadHelper('vmpsplugin');

		$this->loadHelper('html');

		$model = VmModel::getModel();


		$layoutName = JRequest::getWord('layout', 'default');
		$this->SetViewTitle();


		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {
		        $shipment = $model->getShipment();
			$this->loadHelper('image');
			// $this->loadHelper('html');
			$this->loadHelper('parameterparser');
			// jimport('joomla.html.pane');
			 if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
			 $vendor_id = 1;
			 $currency=VirtueMartModelVendor::getVendorCurrency ($vendor_id);
			 $this->assignRef('vendor_currency', $currency->currency_symbol);

			 if(Vmconfig::get('multix','none')!=='none'){
					$vendorList= ShopFunctions::renderVendorList($shipment->virtuemart_vendor_id);
					$this->assignRef('vendorList', $vendorList);
			 }

			$this->assignRef('pluginList', self::renderInstalledShipmentPlugins($shipment->shipment_jplugin_id));
			$this->assignRef('shipment',	$shipment);
			$this->assignRef('shopperGroupList', ShopFunctions::renderShopperGroupList($shipment->virtuemart_shoppergroup_ids,true));

			$this->addStandardEditViewCommands($shipment->virtuemart_shipmentmethod_id);

		} else {
			JToolBarHelper::custom('cloneshipment', 'copy', 'copy', JText::_('COM_VIRTUEMART_SHIPMENT_CLONE'), true);

			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model);

			$shipments = $model->getShipments();
			$this->assignRef('shipments', $shipments);

			$pagination = $model->getPagination();
			$this->assignRef('pagination', $pagination);

		}

		parent::display($tpl);
	}

	function renderInstalledShipmentPlugins($selected)
	{
		$db = JFactory::getDBO();

		if (JVM_VERSION===1) {
			$table = '#__plugins';
			$enable = 'published';
			$ext_id = 'id';
		}
		else {
			$table = '#__extensions';
			$enable = 'enabled';
			$ext_id = 'extension_id';
		}
		$q = 'SELECT * FROM `'.$table.'` WHERE `folder` = "vmshipment" AND `'.$enable.'`="1" ';
		$db->setQuery($q);
		$result = $db->loadAssocList($ext_id);
		if(empty($result)){
			$app = JFactory::getApplication();
			$app -> enqueueMessage(JText::_('COM_VIRTUEMART_NO_SHIPMENT_PLUGINS_INSTALLED'));
		}

		foreach ($result as &$sh) {
			$sh['name'] = JText::_($sh['name']);
		}
		return JHtml::_('select.genericlist', $result, 'shipment_jplugin_id', null, $ext_id, 'name', $selected);
	}

}
// pure php no closing tag
