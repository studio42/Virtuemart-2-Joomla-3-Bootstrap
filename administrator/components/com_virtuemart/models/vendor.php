<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD, RickG
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: vendor.php 6425 2012-09-11 20:17:08Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

if (!class_exists ('VmModel')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

/**
 * Model for VirtueMart Vendors
 *
 * @package        VirtueMart
 */
class VirtueMartModelVendor extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 *
	 * @author Max Milbers
	 */
	function __construct () {

		parent::__construct ();

		//Todo multivendor nasty hack, to get vendor with id 1
		if (Vmconfig::get ('multix', 'none') == 'none') {
			$this->setId (1);
		}

		$this->setMainTable ('vendors');
	}

	/**
	 * name: getLoggedVendor
	 * Checks which $vendorId has the just logged in user.
	 *
	 * @author Max Milbers
	 * @param @param $ownerOnly returns only an id if the vendorOwner is logged in (dont get confused with storeowner)
	 * returns int $vendorId
	 */
	static function getLoggedVendor ($ownerOnly = TRUE) {

		$user = JFactory::getUser ();
		$userId = $user->id;
		if (isset($userId)) {
			$vendorId = self::getVendorId ('user', $userId, $ownerOnly);
			return $vendorId;
		} else {
			JError::raiseNotice (1, '$virtuemart_user_id empty, no user logged in');
			return 0;
		}

	}

	/**
	 * Retrieve the vendor details from the database.
	 *
	 * @author Max Milbers
	 * @return object Vendor details
	 */
	function getVendor ($vendor_id = NULL) {

		if ($vendor_id) {
			$this->_id = $vendor_id;
		}
		if (empty($this->_data)) {

			$this->_data = $this->getTable ('vendors');
			$this->_data->load ($this->_id);
// 			vmdebug('getVendor',$this->_id,$this->_data);
			// Convert ; separated string into array
			if ($this->_data->vendor_accepted_currencies) {
				$this->_data->vendor_accepted_currencies = explode (',', $this->_data->vendor_accepted_currencies);
			} else {
				$this->_data->vendor_accepted_currencies = array();
			}

			$xrefTable = $this->getTable ('vendor_medias');
			$this->_data->virtuemart_media_id = $xrefTable->load ($this->_id);

		}

		return $this->_data;
	}

	/**
	 * Retrieve a list of vendors
	 * todo only names are needed here, maybe it should be enhanced (loading object list is slow)
	 * todo add possibility to load without limit
	 *
	 * @author RickG
	 * @author Max Milbers
	 * @return object List of vendors
	 */
	public function getVendors () {

		$this->setId (0); //This is important ! notice by Max Milbers
		$query = 'SELECT * FROM `#__virtuemart_vendors_' . VMLANG . '` as l JOIN `#__virtuemart_vendors` as v using (`virtuemart_vendor_id`)';
		$query .= ' ORDER BY l.`virtuemart_vendor_id`';
		$this->_data = $this->_getList ($query, $this->getState ('limitstart'), $this->getState ('limit'));
		return $this->_data;
	}

	/**
	 * Find the user id given a vendor id
	 *
	 * @author Max Milbers
	 * @param int $virtuemart_vendor_id
	 * @return int $virtuemart_user_id
	 */
	static function getUserIdByVendorId ($vendorId) {

		//this function is used static, needs its own db

		if (empty($vendorId)) {
			return;
		} else {
			$db = JFactory::getDBO ();
			$query = 'SELECT `virtuemart_user_id` FROM `#__virtuemart_vmusers` WHERE `virtuemart_vendor_id`=' . (int)$vendorId;
			$db->setQuery ($query);
			$result = $db->loadResult ();
			$err = $db->getErrorMsg ();
			if (!empty($err)) {
				vmError ('getUserIdByVendorId ' . $err, 'Failed to retrieve user id by vendor');
			}
			return (isset($result) ? $result : 0);
		}
	}


	/**
	 * Bind the post data to the vendor table and save it
	 * This function DOES NOT safe information which is in the vmusers or vm_user_info table
	 * It only stores the stuff into the vendor table
	 *
	 * @author RickG
	 * @author Max Milbers
	 * @return boolean True is the save was successful, false otherwise.
	 */
	function store (&$data) {

		JPluginHelper::importPlugin ('vmvendor');
		$dispatcher = JDispatcher::getInstance ();
		$plg_datas = $dispatcher->trigger ('plgVmOnVendorStore', $data);
		foreach ($plg_datas as $plg_data) {
			$data = array_merge ($plg_data);
		}

		$oldVendorId = $data['virtuemart_vendor_id'];

		$table = $this->getTable ('vendors');

		/*	if(!$table->checkDataContainsTableFields($data)){
		 $app = JFactory::getApplication();
		 //$app->enqueueMessage('Data contains no Info for vendor, storing not needed');
		 return $this->_id;
	 }*/

		// Store multiple selectlist entries as a ; separated string
		if (key_exists ('vendor_accepted_currencies', $data) && is_array ($data['vendor_accepted_currencies'])) {
			$data['vendor_accepted_currencies'] = implode (',', $data['vendor_accepted_currencies']);
		}

		$table->bindChecknStore ($data);
		$errors = $table->getErrors ();
		foreach ($errors as $error) {
			$this->setError ($error);
			vmError ('store vendor', $error);
		}

		//set vendormodel id to the lastinserted one
// 	$dbv = $table->getDBO();
// 	if(empty($this->_id)) $this->_id = $dbv->insertid();
		if (empty($this->_id)) {
			$this->_id = $table->virtuemart_vendor_id;
		}

		if ($this->_id != $oldVendorId) {

			vmdebug('Developer notice, tried to update vendor xref should not appear in singlestore');

			//update user table
			$usertable = $this->getTable ('vmusers');
// 		$vendorsUserData =$usertable->load($this->_id);
// 		$vendorsUserData =$usertable->load($data['virtuemart_user_id']);
// 		$vendorsUserData->virtuemart_vendor_id = $virtuemart_vendor_id;
			//$vmusersData = array('virtuemart_user_id'=>$data['virtuemart_user_id'],'user_is_vendor'=>1,'virtuemart_vendor_id'=>$virtuemart_vendor_id,'customer_number'=>$data['customer_number'],'perms'=>$data['perms']);

			$usertable->bindChecknStore ($data, TRUE);

			$errors = $usertable->getErrors ();
			foreach ($errors as $error) {
				$this->setError ($error);
				vmError ('Store vendor ' . $error);
			}

		}

		// Process the images
		$mediaModel = VmModel::getModel ('Media');
		$mediaModel->storeMedia ($data, 'vendor');
		$errors = $mediaModel->getErrors ();
		foreach ($errors as $error) {
			vmError ($error);
		}

		$plg_datas = $dispatcher->trigger ('plgVmAfterVendorStore', $data);
		foreach ($plg_datas as $plg_data) {
			$data = array_merge ($plg_data);
		}

		return $this->_id;

	}

	/**
	 * Get the vendor specific currency
	 *
	 * @author Oscar van Eijk
	 * @param $_vendorId Vendor ID
	 * @return string Currency code
	 */

	static $_vendorCurrencies = array();
	static function getVendorCurrency ($_vendorId) {

		if(!isset(self::$_vendorCurrencies[$_vendorId])){
			$db = JFactory::getDBO ();

			$q = 'SELECT *  FROM `#__virtuemart_currencies` AS c
			, `#__virtuemart_vendors` AS v
			WHERE v.virtuemart_vendor_id = ' . (int)$_vendorId . '
			AND   v.vendor_currency = c.virtuemart_currency_id';
			$db->setQuery ($q);
			self::$_vendorCurrencies[$_vendorId] = $db->loadObject ();
		}

		return self::$_vendorCurrencies[$_vendorId];
	}

	/**
	 * Retrieve a lost of vendor objects
	 *
	 * @author Oscar van Eijk
	 * @return Array with all Vendor objects
	 */
	function getVendorCategories () {

		$_q = 'SELECT * FROM `#__vm_vendor_category`';
		$this->_db->setQuery ($_q);
		return $this->_db->loadObjectList ();
	}

	function getUserIdByOrderId ($virtuemart_order_id) {

		if (empty ($virtuemart_order_id)) {
			return;
		}
		$virtuemart_order_id = (int)$virtuemart_order_id;
		$q = "SELECT `virtuemart_user_id` FROM `#__virtuemart_orders` WHERE `virtuemart_order_id`='.$virtuemart_order_id'";
//		$db->query( $q );
		$this->_db->setQuery ($q);

//		if($db->next_record()){
		if ($this->_db->query ()) {
//			$virtuemart_user_id = $db->f('virtuemart_user_id');
			return $this->_db->loadResult ();
		} else {
			JError::raiseNotice (1, 'Error in DB $virtuemart_order_id ' . $virtuemart_order_id . ' dont have a virtuemart_user_id');
			return 0;
		}
	}


	/**
	 * Gets the vendorId by user Id mapped by table auth_user_vendor or by the order item
	 * Assigned users cannot change storeinformations
	 * ownerOnly = false should be used for users who are assigned to a vendor
	 * for administrative jobs like execution of orders or managing products
	 * Changing of vendorinformation should ONLY be possible by the Mainvendor who is in charge
	 *
	 * @author by Max Milbers
	 * @author RolandD
	 * @param string $type Where the vendor ID should be taken from
	 * @param mixed  $value Whatever value the vendor ID should be filtered on
	 * @return int Vendor ID
	 */
	static public function getVendorId ($type, $value, $ownerOnly = TRUE) {

		if (empty($value)) {
			return 0;
		}

		//sanitize input params
		$value = (int)$value;

		//static call used, so we need our own db instance
		$db = JFactory::getDBO ();
		switch ($type) {
			case 'order':
				$q = 'SELECT virtuemart_vendor_id FROM #__virtuemart_order_items WHERE virtuemart_order_id=' . $value;
				break;
			case 'user':
				if ($ownerOnly) {
					$q = 'SELECT `virtuemart_vendor_id`
						FROM `#__virtuemart_vmusers` `au`
						LEFT JOIN `#__virtuemart_userinfos` `u`
						ON (au.virtuemart_user_id = u.virtuemart_user_id)
						WHERE `u`.`virtuemart_user_id`=' . $value;
				} else {
					$q = 'SELECT `virtuemart_vendor_id` FROM `#__virtuemart_vmusers` WHERE `virtuemart_user_id`= "' . $value . '" ';
				}
				break;
			case 'product':
				$q = 'SELECT virtuemart_vendor_id FROM #__virtuemart_products WHERE virtuemart_product_id=' . $value;
				break;
		}
		$db->setQuery ($q);
		$virtuemart_vendor_id = $db->loadResult ();
		if ($virtuemart_vendor_id) {
			return $virtuemart_vendor_id;
		} else {
			return 0;
//			if($type!='user'){
//				return 0;
//			} else {
//				JError::raiseNotice(1, 'No virtuemart_vendor_id found for '.$value.' on '.$type.' check.');
//				return 0;
//			}
		}
	}

	/**
	 * This function gives back the storename for the given vendor.
	 *
	 * @author Max Milbers
	 */
	public function getVendorName ($virtuemart_vendor_id = 1) {

		$query = 'SELECT `vendor_store_name` FROM `#__virtuemart_vendors_' . VMLANG . '` WHERE `virtuemart_vendor_id` = "' . (int)$virtuemart_vendor_id . '" ';
		$this->_db->setQuery ($query);
		if ($this->_db->query ()) {
			return $this->_db->loadResult ();
		} else {
			return '';
		}
	}

	/**
	 * This function gives back the email for the given vendor.
	 *
	 * @author Max Milbers
	 */

	public function getVendorEmail ($virtuemart_vendor_id) {

		$virtuemart_user_id = self::getUserIdByVendorId ((int)$virtuemart_vendor_id);
		if (!empty($virtuemart_user_id)) {
			$query = 'SELECT `email` FROM `#__users` WHERE `id` = "' . $virtuemart_user_id . '" ';
			$this->_db->setQuery ($query);
			if ($this->_db->query ()) {
				return $this->_db->loadResult ();
			} else {
				return '';
			}
		}
		return '';
	}

	public function getVendorAdressBT ($virtuemart_vendor_id) {

		$userId = self::getUserIdByVendorId ($virtuemart_vendor_id);
		$usermodel = VmModel::getModel ('user');
// 		$usermodel->setId($userId);
		$virtuemart_userinfo_id = $usermodel->getBTuserinfo_id ($userId);
		$vendorAddressBt = $this->getTable ('userinfos');
		$vendorAddressBt->load ($virtuemart_userinfo_id);
		return $vendorAddressBt;
	}

	private $_vendorFields = FALSE;
	public function getVendorAddressFields(){

		if(!$this->_vendorFields){
			$userId = VirtueMartModelVendor::getUserIdByVendorId ($this->_id);
			$userModel = VmModel::getModel ('user');
			$virtuemart_userinfo_id = $userModel->getBTuserinfo_id ($userId);

			// this is needed to set the correct user id for the vendor when the user is logged
			$userModel->getVendor($this->_id,FALSE);

			$vendorFieldsArray = $userModel->getUserInfoInUserFields ('mail', 'BT', $virtuemart_userinfo_id, FALSE, TRUE);
			$this->_vendorFields = $vendorFieldsArray[$virtuemart_userinfo_id];
		}

		return $this->_vendorFields;
	}

}
