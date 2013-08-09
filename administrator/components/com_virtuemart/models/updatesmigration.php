<?php
/**
*
* Data module for updates and migrations
*
* @package	VirtueMart
* @subpackage updatesMigration
* @author Max Milbers, RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: updatesmigration.php 6350 2012-08-14 17:18:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the model framework
// j3 FIX if(!class_exists('JModelLegacy ')) require JPATH_VM_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';


/**
 * Model class for updates and migrations
 *
 * @package	VirtueMart
 * @subpackage updatesMigration
 * @author Max Milbers, RickG
 */
class VirtueMartModelUpdatesMigration extends JModelLegacy  {

    /**
     * Checks the VirtueMart Server for the latest available Version of VirtueMart
     *
     * @return string Example: 1.1.2
     */
    function getLatestVersion() {

    	if(!class_exists('VmConnector')) require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'connection.php');

		$url = "http://virtuemart.net/index2.php?option=com_versions&catid=1&myVersion={".VmConfig::getInstalledVersion()."}&task=latestversionastext";
		$result = VmConnector::handleCommunication($url);

		return $result;
    }


    /**
     * @author Max Milbers
     */
    function determineStoreOwner() {
		if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
		$virtuemart_user_id = VirtueMartModelVendor::getUserIdByVendorId(1);
		if (isset($virtuemart_user_id) && $virtuemart_user_id > 0) {
		    $this->_user = JFactory::getUser($virtuemart_user_id);
		}
		else {
		    $this->_user = JFactory::getUser();
		}
		return $this->_user->id;
    }


    /**
     * @author Max Milbers
     */
    function setStoreOwner($userId=-1) {

	    $allowInsert=FALSE;

	    if($userId===-1){
		    $allowInsert = TRUE;
		    $userId = 0;
	    }

		if (empty($userId)) {
		    $userId = $this->determineStoreOwner();
			vmdebug('setStoreOwner $userId = '.$userId.' by determineStoreOwner');
		}

		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM  `#__virtuemart_vmusers` WHERE `virtuemart_user_id`= "' . $userId . '" ');
		$oldUserId = $db->loadResult();

		if (!empty($oldUserId) and !empty($userId)) {
		    $db->setQuery( 'UPDATE `#__virtuemart_vmusers` SET `virtuemart_vendor_id` = "0", `user_is_vendor` = "0", `perms` = "" WHERE `virtuemart_vendor_id` ="1" ');
		    if ($db->execute() == false ) {
			    JError::raiseWarning(1, 'UPDATE __vmusers failed for virtuemart_user_id '.$userId);
			    return false;
		    }

			$db->setQuery( 'UPDATE `#__virtuemart_vmusers` SET `virtuemart_vendor_id` = "1", `user_is_vendor` = "1", `perms` = "admin" WHERE `virtuemart_user_id` ="'.$userId.'" ');
			if ($db->execute() === false ) {
				JError::raiseWarning(1, 'UPDATE __vmusers failed for virtuemart_user_id '.$userId);
				return false;
			} else {
				vmInfo('setStoreOwner VmUser updated new main vendor has user id  '.$userId);
			}
		} else if($allowInsert){
			$db->setQuery('INSERT `#__virtuemart_vmusers` (`virtuemart_user_id`, `user_is_vendor`, `virtuemart_vendor_id`, `perms`) VALUES ("' . $userId . '", "1","1","admin")');
			if ($db->execute() === false ) {
				JError::raiseWarning(1, 'setStoreOwner was not possible to execute INSERT __vmusers for virtuemart_user_id '.$userId);
				return false;
			} else {
				vmInfo('setStoreOwner VmUser inserted new main vendor has user id  '.$userId);
			}
		}

	    return $userId;
    }


    /**
     * Syncs user permission
     *
     * @param int virtuemart_user_id
     * @return bool true on success
     * @author Christopher Roussel
     */
    function setUserToPermissionGroup ($userId=0) {
		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');

		$usersTable = $this->getTable('vmusers');
		$usersTable->load((int)$userId);

		$perm = Permissions::getInstance();
		$usersTable->perms = $perm->getPermissions($userId);

		$result = $usersTable->check();
		if ($result) {
			$result = $usersTable->store();
		}

		if (!$result) {
			$errors = $usersTable->getErrors();
			foreach($errors as $error) {
				vmError(get_class( $this ).'::setUserToPermissionGroup user '.$error);
			}
			return false;
		}

		$xrefTable = $this->getTable('vmuser_shoppergroups');
		$data = $xrefTable->load((int)$userId);

		if (empty($data)) {
			$data = array('virtuemart_user_id'=>$userId, 'virtuemart_shoppergroup_id'=>'0');

			if (!$xrefTable->save($data)) {
				$errors = $xrefTable->getErrors();
				foreach($errors as $error){
					vmError(get_class( $this ).'::setUserToPermissionGroup xref '.$error);
				}
				return false;
			}
		}

		return true;
    }


    /**
     * Installs sample data to the current database.
     *
     * @author Max Milbers, RickG
     * @params $userId User Id to add the userinfo and vendor sample data to
     */
    function installSampleData($userId = null) {


	if ($userId == null) {
	    $userId = $this->determineStoreOwner();
	}


	$fields['username'] =  $this->_user->username;
	$fields['virtuemart_user_id'] =  $userId;
	$fields['address_type'] =  'BT';
	// Don't change this company name; it's used in install_sample_data.sql
	$fields['company'] =  "Washupito's the virtual mart";
	$fields['title'] =  'Sire';
	$fields['last_name'] =  'upito';
	$fields['first_name'] =  'Wash';
	$fields['middle_name'] =  'the cheapest';
	$fields['phone_1'] =  '555-555-555';
	$fields['address_1'] =  'vendorra road 8';
	$fields['city'] =  'Canangra';
	$fields['zip'] =  '055555';
	$fields['virtuemart_state_id'] =  '361';
	$fields['virtuemart_country_id'] =  '195';
// 	$fields['virtuemart_shoppergroup_id'] = '';
	//Dont change this, atm everything is mapped to mainvendor with id=1
	$fields['user_is_vendor'] =  '1';
	$fields['virtuemart_vendor_id'] = '1';
	$fields['vendor_name'] =  'Washupito';
	$fields['vendor_phone'] =  '555-555-1212';
	$fields['vendor_store_name'] =  "Washupito's Tiendita";
	$fields['vendor_store_desc'] =  ' <p>We have the best tools for do-it-yourselfers.  Check us out! </p> <p>We were established in 1969 in a time when getting good tools was expensive, but the quality was good.  Now that only a select few of those authentic tools survive, we have dedicated this store to bringing the experience alive for collectors and master mechanics everywhere.</p> 		<p>You can easily find products selecting the category you would like to browse above.</p>	';
	//$fields['virtuemart_media_id'] =  1;
	$fields['vendor_currency'] =  47;
	$fields['vendor_accepted_currencies'] = '52,26,47,144';
	$fields['vendor_terms_of_service'] =  '<h5>You haven&#39;t configured any terms of service yet. Click <a href="'.JURI::base(true).'/index.php?option=com_virtuemart&view=user&task=editshop">here</a> to change this text.</h5>';
	$fields['vendor_url'] = JURI::root();
	$fields['vendor_name'] =  'Washupito';
	$fields['perms']='admin';
	$fields['vendor_legal_info']="VAT-ID: XYZ-DEMO<br />Reg.Nr: DEMONUMBER";
	$fields['vendor_letter_css']='.vmdoc-header { }
.vmdoc-footer { }
';
	$fields['vendor_letter_header_html']='<h1>{vm:vendorname}</h1><p>{vm:vendoraddress}</p>';
	$fields['vendor_letter_header_image']='1';
	$fields['vendor_letter_footer_html']='{vm:vendorlegalinfo}<br /> Page {vm:pagenum}/{vm:pagecount}';
	if(!class_exists('VirtueMartModelUser')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'user.php');
	$usermodel = VmModel::getModel('user');
	$usermodel->setId($userId);

	//Save the VM user stuff
	if(!$usermodel->store($fields)){
		vmError(JText::_('COM_VIRTUEMART_NOT_ABLE_TO_SAVE_USER_DATA')  );
		JError::raiseWarning('', JText::_('COM_VIRTUEMART_RAISEWARNING_NOT_ABLE_TO_SAVE_USER_DATA'));
	}

// 	$params = JComponentHelper::getParams('com_languages');
// 	$lang = $params->get('site', 'en-GB');//use default joomla
// 	$this->installSampleSQL($lang);
	$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'install_sample_data.sql';
	    if(!defined('VMLANG')){
		    $params = JComponentHelper::getParams('com_languages');
		    $lang = $params->get('site', 'en-GB');//use default joomla
		    $lang = strtolower(strtr($lang,'-','_'));
	    } else {
		    $lang = VMLANG;
	    }
	if(!$this->execSQLFile($filename)){
		vmError(JText::_('Problems execution of SQL File '.$filename));
	} else {
		//update jplugin_id from shipment and payment
		$db = JFactory::getDBO();
		$q = 'SELECT `extension_id` FROM #__extensions WHERE element = "weight_countries" AND folder = "vmshipment"';
		$db->setQuery($q);
		$shipment_plg_id = $db->loadResult();
		if(!empty($shipment_plg_id)){
			$q = 'INSERT INTO `#__virtuemart_shipmentmethods` (`virtuemart_shipmentmethod_id`, `virtuemart_vendor_id`, `shipment_jplugin_id`, `shipment_element`, `shipment_params`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
			(1, 1, '.$shipment_plg_id.', "weight_countries", \'shipment_logos=""|countries=""|zip_start=""|zip_stop=""|weight_start=""|weight_stop=""|weight_unit="KG"|nbproducts_start=0|nbproducts_stop=0|orderamount_start=""|orderamount_stop=""|cost="0"|package_fee=""|tax_id="0"|free_shipment=""|\', 0, 0, 1, "0000-00-00 00:00:00", 0,  "0000-00-00 00:00:00", 0,  "0000-00-00 00:00:00", 0)';
			$db->setQuery($q);
			$db->execute();
 			$q = 'INSERT INTO `#__virtuemart_shipmentmethods_'.$lang.'` (`virtuemart_shipmentmethod_id`, `shipment_name`, `shipment_desc`, `slug`) VALUES (1, "Self pick-up", "", "Self-pick-up")';
			$db->setQuery($q);
			$db->execute();

			//Create table of the plugin
			$url = '/plugins/vmshipment';
			if (!class_exists ('plgVmShipmentWeight_countries')) require(JPATH_ROOT . DS . $url . DS . 'weight_countries.php');
			$this->installPluginTable('plgVmShipmentWeight_countries','#__virtuemart_shipment_plg_weight_countries','Shipment Weight Countries Table');
		}

		$q = 'SELECT `extension_id` FROM #__extensions WHERE element = "standard" AND folder = "vmpayment"';
		$db->setQuery($q);
		$payment_plg_id = $db->loadResult();
		if(!empty($payment_plg_id)){
			$q='INSERT INTO `#__virtuemart_paymentmethods` (`virtuemart_paymentmethod_id`, `virtuemart_vendor_id`, `payment_jplugin_id`,  `payment_element`, `payment_params`, `shared`, `ordering`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES
			(1, 1, '.$payment_plg_id.',  "standard", \'payment_logos=""|countries=""|payment_currency="0"|status_pending="U"|send_invoice_on_order_null="1"|min_amount=""|max_amount=""|cost_per_transaction=""|cost_percent_total=""|tax_id="0"|payment_info=""|\', 0, 0, 1,  "0000-00-00 00:00:00", 0,  "0000-00-00 00:00:00", 0,  "0000-00-00 00:00:00", 0)';
			$db->setQuery($q);
			$db->execute();

			$q="INSERT INTO `#__virtuemart_paymentmethods_".$lang."` (`virtuemart_paymentmethod_id`, `payment_name`, `payment_desc`, `slug`) VALUES	(1, 'Cash on delivery', '', 'Cash-on-delivery')";
			$db->setQuery($q);
			$db->execute();

			$url = '/plugins/vmpayment';
			if (!class_exists ('plgVmPaymentStandard')) require(JPATH_ROOT . DS . $url . DS . 'standard.php');
			$this->installPluginTable('plgVmPaymentStandard','#__virtuemart_payment_plg_standard','Payment Standard Table');
		}
		vmInfo(JText::_('COM_VIRTUEMART_SAMPLE_DATA_INSTALLED'));
	}

	return true;

    }

	function installPluginTable ($className,$tablename,$tableComment) {

		$query = "CREATE TABLE IF NOT EXISTS `" . $tablename . "` (";
		if(!empty($tablesFields)){
			foreach ($tablesFields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
		} else {
			$SQLfields = call_user_func($className."::getTableSQLFields");
			//$SQLfields = $className::getTableSQLFields ();
		//	$loggablefields = $className::getTableSQLLoggablefields ();
			$loggablefields = call_user_func($className."::getTableSQLLoggablefields");
			foreach ($SQLfields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
			foreach ($loggablefields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . ", ";
			}
		}

		$query .= "	      PRIMARY KEY (`id`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='" . $tableComment . "' AUTO_INCREMENT=1 ;";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		if (!$db->execute ()) {
			vmError ( $className.'::onStoreInstallPluginTable: ' . JText::_ ('COM_VIRTUEMART_SQL_ERROR') . ' ' . $db->stderr (TRUE));
		}

	}


    function restoreSystemDefaults() {

		JPluginHelper::importPlugin('vmextended');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onVmSqlRemove', $this);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'uninstall_essential_data.sql';
		$this->execSQLFile($filename);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'uninstall_required_data.sql';
		$this->execSQLFile($filename);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'install.sql';
		$this->execSQLFile($filename);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'install_essential_data.sql';
		$this->execSQLFile($filename);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'install_required_data.sql';
		$this->execSQLFile($filename);

			if(!class_exists('GenericTableUpdater')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
		$updater = new GenericTableUpdater();
		$updater->createLanguageTables();


		JPluginHelper::importPlugin('vmextended');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onVmSqlRestore', $this);
    }

    function restoreSystemTablesCompletly() {

		$this->removeAllVMTables();

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'install.sql';
		$this->execSQLFile($filename);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'install_essential_data.sql';
		$this->execSQLFile($filename);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'install_required_data.sql';
		$this->execSQLFile($filename);

		if(!class_exists('GenericTableUpdater')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
		$updater = new GenericTableUpdater();
		$updater->createLanguageTables();

		JPluginHelper::importPlugin('vmextended');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onVmSqlRestore', $this);
    }

    /**
     * Parse a sql file executing each sql statement found.
     *
     * @author Max Milbers
     */
    function execSQLFile($sqlfile ) {

		// Check that sql files exists before reading. Otherwise raise error for rollback
		if ( !file_exists($sqlfile) ) {
		    vmError('No SQL file provided!');
		    return false;
		}

		if(!defined('VMLANG')){
			$params = JComponentHelper::getParams('com_languages');
			$lang = $params->get('site', 'en-GB');//use default joomla
			$lang = strtolower(strtr($lang,'-','_'));
		} else {
			$lang = VMLANG;
		}

		// Create an array of queries from the sql file
		jimport('joomla.installer.helper');
		$queries = JInstallerHelper::splitSql(file_get_contents($sqlfile));

		if (count($queries) == 0) {
		    vmError('SQL file has no queries!');
		    return false;
		}
		$ok = true;
		$db = JFactory::getDBO();
		// Process each query in the $queries array (split out of sql file).
		foreach ($queries as $query) {
		    $query = trim($query);
		    if ($query != '' && $query{0} != '#') {
		    	if(strpos($query, 'CREATE' )!==false or strpos( $query, 'INSERT INTO')!==false){
		    		$query = str_replace('XLANG',$lang,$query);
		    	}
			$db->setQuery($query);
				if (!$db->execute()) {
				    JError::raiseWarning(1, 'JInstaller::install: '.$sqlfile.' '.JText::_('COM_VIRTUEMART_SQL_ERROR')." ".$db->stderr(true));
				    $ok = false;
				}
		    }
		}

		return $ok;
    }

    /**
     * Delete all Virtuemart tables.
     *
     * @return True if successful, false otherwise
     */
    function removeAllVMTables() {
		$db = JFactory::getDBO();
		$config = JFactory::getConfig();

		$prefix = $config->get('dbprefix').'virtuemart_%';
		$db->setQuery('SHOW TABLES LIKE "'.$prefix.'"');
		if (!$tables = $db->loadColumn()) {
		    vmError ($db->getErrorMsg());
		    return false;
		}

		$app = JFactory::getApplication();
		foreach ($tables as $table) {

		    $db->setQuery('DROP TABLE ' . $table);
		    if($db->execute()){
		    	$droppedTables[] = substr($table,strlen($prefix)-1);
		    } else {
		    	$errorTables[] = $table;
		    	$app->enqueueMessage('Error drop virtuemart table ' . $table);
		    }
		}


		if(!empty($droppedTables)){
			$app->enqueueMessage('Dropped virtuemart table ' . implode(', ',$droppedTables));
		}

	    if(!empty($errorTables)){
			$app->enqueueMessage('Error dropping virtuemart table ' . implode($errorTables,', '));
			return false;
		}

		return true;
    }


    /**
     * Remove all the data from all Virutmeart tables.
     *
     * @return boolean True if successful, false otherwise.
     */
    function removeAllVMData() {
		JPluginHelper::importPlugin('vmextended');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onVmSqlRemove', $this);

		$filename = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'install'.DS.'uninstall_data.sql';
		$this->execSQLFile($filename);
		$tables = array('products','categories','manufacturers','manufacturercategories');
		$prefix = $this->_db->getPrefix();
		foreach ($tables as $table) {
			$query = 'SHOW TABLES LIKE "'.$prefix.'virtuemart_'.$table.'_%"';
			$this->_db->setQuery($query);
			if($translatedTables= $this->_db->loadColumn()) {
				foreach ($translatedTables as $translatedTable) {
					$this->_db->setQuery('TRUNCATE TABLE `'.$translatedTable.'`');
					if($this->_db->execute()) vmInfo( $translatedTable.' empty');
					else vmError($translatedTable.' language table Cannot be deleted');
				}
			} else vmInfo('No '.$table.' language table found to delete '.$query);
		}
		//"TRUNCATE TABLE IS FASTER and reset the primary Keys;
		return true;
    }

	/**
	 * This function deletes all stored thumbs and deletes the entries for all thumbs, usually this is need for shops
	 * older than vm2.0.22. The new pattern is now not storing the url as long it is not overwritten.
	 * Of course the function deletes all overwrites, but you can now relativly easy change the thumbsize in your shop
	 * @author Max Milbers
	 */
	function resetThumbs(){

		$db = JFactory::getDbo();
		$q = 'UPDATE `#__virtuemart_medias` SET `file_url_thumb`=""';

		$db->setQuery($q);
		$db->execute();
		$err = $db->getErrorMsg();
		if(!empty($err)){
			vmError('resetThumbs Update entries failed ',$err);
		}
		jimport('joomla.filesystem.folder');
		$tmpimg_resize_enable = VmConfig::get('img_resize_enable',1);

		VmConfig::set('img_resize_enable',0);
		$this->deleteMediaThumbFolder('media_category_path');
		$this->deleteMediaThumbFolder('media_product_path');
		$this->deleteMediaThumbFolder('media_manufacturer_path');
		$this->deleteMediaThumbFolder('media_vendor_path');
		$this->deleteMediaThumbFolder('forSale_path_thumb','');

		VmConfig::set('img_resize_enable',$tmpimg_resize_enable);
		return true;

	}

	/**
	 * Delets a thumb folder and recreates it, contains small nasty hack for the thumbnail folder of the "file for sale"
	 * @author Max Milbers
	 * @param $type
	 * @param string $resized
	 * @return bool
	 */
	private function deleteMediaThumbFolder($type,$resized='resized'){

		if(!empty($resized)) $resized = DS.$resized;
		$typePath = VmConfig::get($type);
		if(!empty($typePath)){
			$path = JPATH_ROOT.DS.str_replace('/',DS,$typePath).$resized;
			$msg = JFolder::delete($path);
			if(!$msg){
				vmWarn('Problem deleting '.$type);
			}
			if(!class_exists('JFile')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'filesystem'.DS.'file.php');
			$msg = JFolder::create($path);
			return $msg;
		} else {

			return 'Config path for '.$type.' empty';
		}

	}

}

//pure php no tag
