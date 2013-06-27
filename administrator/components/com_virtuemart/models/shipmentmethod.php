<?php
/**
 *
 * Data module for shipment
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
 * @version $Id: shipmentmethod.php 6350 2012-08-14 17:18:08Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model class for shop shipment
 *
 * @package	VirtueMart
 * @subpackage Shipment
 * @author RickG
 */
class VirtueMartModelShipmentmethod extends VmModel {

	//    /** @var integer Primary key */
	//    var $_id;
	/** @var integer Joomla plugin ID */
	var $jplugin_id;
	/** @var integer Vendor ID */
	var $virtuemart_vendor_id;

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('shipmentmethods');
		$this->_selectedOrdering = 'ordering';
	}

	/**
	 * Retrieve the detail record for the current $id if the data has not already been loaded.
	 *
	 * @author RickG
	 */
	function getShipment() {

		if (empty($this->_data)) {
			$this->_data = $this->getTable('shipmentmethods');
			$this->_data->load((int)$this->_id);

			if($this->_data->shipment_jplugin_id){
				JPluginHelper::importPlugin('vmshipment');
				$dispatcher = JDispatcher::getInstance();
				$retValue = $dispatcher->trigger('plgVmDeclarePluginParamsShipment',array( $this->_data->shipment_element,$this->_data->shipment_jplugin_id,&$this->_data));

			}
// 			vmdebug('$$this->_data getShipment',$this->_data);

			if(empty($this->_data->virtuemart_vendor_id)){
				if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
				$this->_data->virtuemart_vendor_id = VirtueMartModelVendor::getLoggedVendor();;
			}
 		//if(!empty($this->_id)){
				/* Add the shipmentcarreir shoppergroups */
				$q = 'SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_shipmentmethod_shoppergroups WHERE `virtuemart_shipmentmethod_id` = "'.$this->_id.'"';
				$this->_db->setQuery($q);
				$this->_data->virtuemart_shoppergroup_ids = $this->_db->loadResultArray();#
				if(empty($this->_data->virtuemart_shoppergroup_ids)) $this->_data->virtuemart_shoppergroup_ids = 0;


		//}

		}

		return $this->_data;
	}

	/**
	 * Retireve a list of shipment from the database.
	 *
	 * @author RickG
	 * @return object List of shipment  objects
	 */
	public function getShipments() {
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
		$query = ' `#__virtuemart_shipmentmethods`.* ,  `'.$table.'`.`name` as shipmentmethod_name FROM `#__virtuemart_shipmentmethods` ';
		$query .= 'JOIN `'.$table.'`   ON  `'.$table.'`.`'.$ext_id.'` = `#__virtuemart_shipmentmethods`.`shipment_jplugin_id` ';

		$whereString = '';
		$select = ' * FROM `#__virtuemart_shipmentmethods_'.VMLANG.'` as l ';
		$joinedTables = ' JOIN `#__virtuemart_shipmentmethods`   USING (`virtuemart_shipmentmethod_id`) ';
		$this->_data =$this->exeSortSearchListQuery(0,$select,$joinedTables,$whereString,' ',$this->_getOrdering() );
		//$this->_data = $this->exeSortSearchListQuery(0,'',$query,$whereString,'',$this->_getOrdering('ordering'));

		if(isset($this->_data)){

			if(!class_exists('shopfunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');
			foreach ($this->_data as $data){
				/* Add the shipment shoppergroups */
				$q = 'SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_shipmentmethod_shoppergroups WHERE `virtuemart_shipmentmethod_id` = "'.$data->virtuemart_shipmentmethod_id.'"';
				$this->_db->setQuery($q);
				$data->virtuemart_shoppergroup_ids = $this->_db->loadResultArray();

				/* Write the first 5 shoppergroups in the list */
				$data->shipmentShoppersList = shopfunctions::renderGuiList('virtuemart_shoppergroup_id','#__virtuemart_shipmentmethod_shoppergroups','virtuemart_shipmentmethod_id',$data->virtuemart_shipmentmethod_id,'shopper_group_name','#__virtuemart_shoppergroups','virtuemart_shoppergroup_id','shoppergroup',4,0);


			}

		}
		return $this->_data;
	}



	/**
	 * Bind the post data to the shipment tables and save it
	 *
	 * @author Max Milbers
	 * @return boolean True is the save was successful, false otherwise.
	 */
	public function store(&$data)
	{
		//$data = JRequest::get('post');


		if ($data) {
			$data = (array)$data;
		}
// 		vmdebug('store',$data);
			if(!empty($data['params'])){
				foreach($data['params'] as $k=>$v){
					$data[$k] = $v;
				}
			}

		if(empty($data['virtuemart_vendor_id'])){
			if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
			$data['virtuemart_vendor_id'] = VirtueMartModelVendor::getLoggedVendor();
		} else {
			$data['virtuemart_vendor_id'] = (int) $data['virtuemart_vendor_id'];
		}

		// missing string FIX, Bad way ?
		if (JVM_VERSION===1) {
			$tb = '#__plugins';
			$ext_id = 'id';
		} else {
			$tb = '#__extensions';
			$ext_id = 'extension_id';
		}
		$q = 'SELECT `element` FROM `' . $tb . '` WHERE `' . $ext_id . '` = "'.$data['shipment_jplugin_id'].'"';
		$this->_db->setQuery($q);
		$data['shipment_element'] = $this->_db->loadResult();

		$table = $this->getTable('shipmentmethods');

		if(isset($data['shipment_jplugin_id'])){

			JPluginHelper::importPlugin('vmshipment');
			$dispatcher = JDispatcher::getInstance();
			//bad trigger, we should just give it data, so that the plugins itself can check the data to be stored
			//so this trigger is now deprecated and will be deleted in vm2.2
			$retValue = $dispatcher->trigger('plgVmSetOnTablePluginParamsShipment',array( $data['shipment_element'],$data['shipment_jplugin_id'],&$table));

			$retValue = $dispatcher->trigger('plgVmSetOnTablePluginShipment',array( &$data,&$table));
			vmdebug('my data after store ',$data);
		}

		$table->bindChecknStore($data);
		$errors = $table->getErrors();
		foreach($errors as $error){
			vmError($error);
		}
		$xrefTable = $this->getTable('shipmentmethod_shoppergroups');
		$xrefTable->bindChecknStore($data);
		$errors = $xrefTable->getErrors();
		foreach($errors as $error){
			vmError($error);
		}

		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		//Add a hook here for other shipment methods, checking the data of the choosed plugin
		$dispatcher = JDispatcher::getInstance();
		$retValues = $dispatcher->trigger('plgVmOnStoreInstallShipmentPluginTable', array(  $data['shipment_jplugin_id']));

		return $table->virtuemart_shipmentmethod_id;
	}
	/**
	 * Creates a clone of a given shipmentmethod id
	 *
	 * @author Valérie Isaksen
	 * @param int $virtuemart_shipmentmethod_id
	 */

	public function createClone ($id) {

		//	if (is_array($cids)) $cids = array($cids);
		$this->setId ($id);
		$shipment = $this->getShipment ();
		$shipment->virtuemart_shipmentmethod_id = 0;
		$shipment->shipment_name = $shipment->shipment_name.' Copy';
		if (!$clone = $this->store($shipment)) {
			JError::raiseError(500, 'createClone '. $shipment->getError() );
		}
		return $clone;
	}
}

//no closing tag
