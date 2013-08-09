<?php
/**
 *
 * @version $Id: migrator.php 7071 2013-07-12 19:33:22Z Milbo $
 * @package VirtueMart
 * @subpackage classes
 * @copyright Copyright (C) 2004-2007 soeren, 2009-2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.org
 */

if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

if(!class_exists('VmModel'))
require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');


class Migrator extends VmModel{

	private $_stop = false;

	public function __construct(){

// 		JTable::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'tables');

		$this->_app = JFactory::getApplication();
		$this->_db = JFactory::getDBO();
		$this->_oldToNew = new stdClass();
		$this->starttime = microtime(true);

		$max_execution_time = (int)ini_get('max_execution_time');
		$jrmax_execution_time= JRequest::getInt('max_execution_time');

		if(!empty($jrmax_execution_time)){
			// 			vmdebug('$jrmax_execution_time',$jrmax_execution_time);
			if($max_execution_time!=$jrmax_execution_time) @ini_set( 'max_execution_time', $jrmax_execution_time );
		} else if($max_execution_time<60) {
			@ini_set( 'max_execution_time', 60 );
		}

		$this->maxScriptTime = ini_get('max_execution_time')*0.80-1;	//Lets use 30% of the execution time as reserve to store the progress

		$jrmemory_limit= JRequest::getInt('memory_limit');
		if(!empty($jrmemory_limit)){
			@ini_set( 'memory_limit', $jrmemory_limit.'M' );
		} else {
			$memory_limit = (int) substr(ini_get('memory_limit'),0,-1);
			if($memory_limit<128)  @ini_set( 'memory_limit', '128M' );
		}

		$this->maxMemoryLimit = $this->return_bytes(ini_get('memory_limit')) - (14 * 1024 * 1024)  ;		//Lets use 11MB for joomla
		// 		vmdebug('$this->maxMemoryLimit',$this->maxMemoryLimit); //134217728
		//$this->maxMemoryLimit = $this -> return_bytes('20M');

		// 		ini_set('memory_limit','35M');
		$q = 'SELECT `id` FROM `#__virtuemart_migration_oldtonew_ids` ';
		$this->_db->setQuery($q);
		$res = $this->_db->loadResult();
		if(empty($res)){
			$q = 'INSERT INTO `#__virtuemart_migration_oldtonew_ids` (`id`) VALUES ("1")';
			$this->_db->setQuery($q);
			$this->_db->execute();
			$this->_app->enqueueMessage('Start with a new migration process and setup log maxScriptTime '.$this->maxScriptTime.' maxMemoryLimit '.$this->maxMemoryLimit/(1024*1024));
		} else {
			$this->_app->enqueueMessage('Found prior migration process, resume migration maxScriptTime '.$this->maxScriptTime.' maxMemoryLimit '.$this->maxMemoryLimit/(1024*1024));
		}

		$this->_keepOldProductIds = VmConfig::get('keepOldProductIds',FALSE);
	}

	private function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	function getMigrationProgress($group){

		$q = 'SELECT `'.$group.'` FROM `#__virtuemart_migration_oldtonew_ids` WHERE `id` = "1" ';

		$this->_db->setQuery($q);
		$result = $this->_db->loadResult();
		if(empty($result)){
			$result = array();
		} else {
			// 			vmdebug('getMigrationProgress '.$group,$result);
			$uresult = unserialize(trim($result));
			if(!$uresult){
				vmdebug('getMigrationProgress unserialize failed '.$group,$result);
				// 				vmWarn('getMigrationProgress '.$group.' array is created new and therefore empty $q '.$q.' '.print_r($uresult,1).' <pre>'.print_r($result,1).'</pre>');
				$result = array();
			} else {
				$result = $uresult;
			}
		}

		return $result;

	}

	function storeMigrationProgress($group,$array, $limit = ''){

		$q = 'UPDATE `#__virtuemart_migration_oldtonew_ids` SET `'.$group.'`="'.serialize($array).'" '.$limit.' WHERE `id` = "1"';

		$this->_db->setQuery($q);
		if(!$this->_db->execute()){
			$this->_app->enqueueMessage('storeMigrationProgress failed to update query '.$this->_db->getQuery());
			$this->_app->enqueueMessage('and ErrrorMsg '.$this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function migrateGeneral(){

		$result = $this->portMedia();
		$result1 = $this->portShoppergroups();
		$result2 = $this->portCategories();
		$result3 = $this->portManufacturerCategories();
		$result4 = $this->portManufacturers();
		// 		$result = $this->portOrderStatus();
        if(((int)$result + (int)$result1 + (int)$result2 + (int)$result3 + (int)$result4) ==5){
            $result = true;
        } else {
            $result = false;
        }

		$time = microtime(true) - $this->starttime;
		vmInfo('Worked on general migration for '.$time.' seconds');
		vmRamPeak('Migrate general vm1 info ended ');
		return $result;
	}

	function migrateUsers(){

		// 		$result = $this->portShoppergroups();
		$result = $this->portUsers();

		$time = microtime(true) - $this->starttime;
		vmInfo('Worked on user migration for '.$time.' seconds');
		vmRamPeak('Migrate shoppers ended ');
		return $result;
	}

	function migrateProducts(){

		// 		$result = $this->portMedia();

		// 		$result = $this->portCategories();
		// 		$result = $this->portManufacturerCategories();
		// 		$result = $this->portManufacturers();
		$result = $this->portProducts();

		$time = microtime(true) - $this->starttime;
		$this->_app->enqueueMessage('Worked on general migration for '.$time.' seconds');

		return $result;
	}

	function migrateOrders(){

		// 		$result = $this->portMedia();
		// 		$result = $this->portCategories();
		// 		$result = $this->portManufacturerCategories();
		// 		$result = $this->portManufacturers();
// 		$result = $this->portProducts();

		// 		$result = $this->portOrderStatus();
		$result = $this->portOrders();
		$time = microtime(true) - $this->starttime;
		vmInfo('Worked on migration for '.$time.' seconds');

		return $result;
	}

	function migrateAllInOne(){

		$result = $this->portMedia();

		$result = $this->portShoppergroups();
		$result = $this->portUsers();
		$result = $this->portVendor();

		$result = $this->portCategories();
		$result = $this->portManufacturerCategories();
		$result = $this->portManufacturers();
		$result = $this->portProducts();

		//$result = $this->portOrderStatus();
		$result = $this->portOrders();
		$time = microtime(true) - $this->starttime;
		$this->_app->enqueueMessage('Worked on migration for '.$time.' seconds');

		vmRamPeak('Migrate all ended ');
		return $result;
	}

	public function portMedia(){

		$ok = true;
		JRequest::setVar('synchronise',true);
		//Prevents search field from interfering with syncronization
		JRequest::setVar('searchMedia', '');

		//$imageExtensions = array('jpg','jpeg','gif','png');

		if(!class_exists('VirtueMartModelMedia'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'media.php');
		$this->mediaModel = VmModel::getModel('Media');
		//First lets read which files are already stored
		$this->storedMedias = $this->mediaModel->getFiles(false, true);

		//check for entries without file
		foreach($this->storedMedias as $media){

			if($media->file_is_forSale!=1){
				$media_path = JPATH_ROOT.DS.str_replace('/',DS,$media->file_url);
			} else {
				$media_path = $media->file_url;
			}

			if(!file_exists($media_path)){
				vmInfo('File for '.$media_path.' is missing');

				//The idea is here to test if the media with missing data is used somewhere and to display it
				//When it not used, the entry should be deleted then.
				/*				$q = 'SELECT * FROM `#__virtuemart_category_medias` as cm,
				`#__virtuemart_product_medias` as pm,
				`#__virtuemart_manufacturer_medias` as mm,
				`#__virtuemart_vendor_medias` as vm
				WHERE cm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
				OR pm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
				OR mm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
				OR vm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'" ';

				$this->_db->setQuery($q);
				$res = $this->_db->loadColumn();
				vmdebug('so',$res);
				if(count($res)>0){
				vmInfo('File for '.$media->file_url.' is missing, but used ');
				}
				*/
			}
		}


		$countTotal = 0;
		//We do it per type
		$url = VmConfig::get('media_product_path');
		$type = 'product';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
		}

		$url = VmConfig::get('media_category_path');
		$type = 'category';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
		}

		$url = VmConfig::get('media_manufacturer_path');
		$type = 'manufacturer';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
		}

		$url = VmConfig::get('media_vendor_path');
		$type = 'vendor';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		$url = VmConfig::get('forSale_path');
		$type = 'forSale';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));


		return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_FINISH', $countTotal);
	}

	private function _portMediaByType($url, $type){

		$knownNames = array();
		//create array of filenames for easier handling
		foreach($this->storedMedias as $media){
			if($media->file_type == $type){
				//Somehow we must use here the right char encoding, so that it works below
				// in line 320
				$knownNames[] = $media->file_url;
			}
		}

		$filesInDir = array();
		$foldersInDir = array();

		if($type!='forSale'){

			$path = str_replace('/', DS, $url);
			$foldersInDir = array(JPATH_ROOT . DS . $path);
		} else {
			$foldersInDir = array($url);
		}

		if (!is_dir($foldersInDir[0])) {
			vmError($type.' Path/Url is not set correct :'.$foldersInDir[0]);
			return 0;
		}

		while(!empty($foldersInDir)){
			foreach($foldersInDir as $dir){
				$subfoldersInDir = null;
				$subfoldersInDir = array();
				if($type!='forSale'){
					$relUrl = str_replace(DS, '/', substr($dir, strlen(JPATH_ROOT . DS)));
				} else {
// 					vmdebug('$dir',$dir);
					$relUrl = $dir;
				}
				if($handle = opendir($dir)){
					while(false !== ($file = readdir($handle))){

						//$file != "." && $file != ".." replaced by strpos
						if(!empty($file) && strpos($file,'.')!==0  && $file != 'index.html'){

							$filetype = filetype($dir . DS . $file);
							$relUrlName = '';
							$relUrlName = $relUrl.$file;
							// vmdebug('my relative url ',$relUrlName);

							//We port all type of media, regardless the extension
							if($filetype == 'file'){
								if(!in_array($relUrlName, $knownNames)){
									$filesInDir[] = array('filename' => $file, 'url' => $relUrl);
								}
							}else {
								if($filetype == 'dir' && $file != 'resized' && $file != 'invoices'){
									$subfoldersInDir[] = $dir.$file.DS;
									// 									vmdebug('my sub folder ',$dir.$file);
								}
							}
						}

						if((microtime(true)-$this->starttime) >= ($this->maxScriptTime*0.4)){
							break;
						}
					}
				}
				$foldersInDir = $subfoldersInDir;
				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime*0.4)){
					break;
				}
			}
			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime*0.4)){
				break;
			}
		}

		$i = 0;
		foreach($filesInDir as $file){

			$data = null;
			$data = array('file_title' => $file['filename'],
		    'virtuemart_vendor_id' => 1,
		    'file_description' => $file['filename'],
		    'file_meta' => $file['filename'],
		    'file_url' => $file['url'] . $file['filename'],
	    	 'media_published' => 1
			);

			if($type == 'product') $data['file_is_product_image'] = 1;
			if($type == 'forSale') $data['file_is_forSale'] = 1;

			$this->mediaModel->setId(0);
			$success = $this->mediaModel->store($data, $type);
			$errors = $this->mediaModel->getErrors();
			foreach($errors as $error){
				$this->_app->enqueueMessage('Migrator ' . $error);
			}
			$this->mediaModel->resetErrors();
			if($success) $i++;
			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				vmError('Attention script time too short, no time left to store the media, please rise script execution time');
				break;
			}
		}

		return $i;
	}

	private function portShoppergroups(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$query = 'SHOW TABLES LIKE "%vm_shopper_group%"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No Shoppergroup table found for migration');
			$this->_stop = true;
			return false;
		}

		$ok = true;

		$q = 'SELECT * FROM #__vm_shopper_group';
		$this->_db->setQuery($q);
		$oldShopperGroups = $this->_db->loadAssocList();
		if(empty($oldShopperGroups)) $oldShopperGroups = array();

		$oldtoNewShoppergroups = array();
		$alreadyKnownIds = $this->getMigrationProgress('shoppergroups');

		$starttime = microtime(true);
		$i = 0;
		foreach($oldShopperGroups as $oldgroup){

			if(!array_key_exists($oldgroup['shopper_group_id'],$alreadyKnownIds)){
				$sGroups = null;
				$sGroups = array();
				//$category['virtuemart_category_id'] = $oldcategory['category_id'];
				$sGroups['virtuemart_vendor_id'] = $oldgroup['vendor_id'];
				$sGroups['shopper_group_name'] = $oldgroup['shopper_group_name'];

				$sGroups['shopper_group_desc'] = $oldgroup['shopper_group_desc'];
				$sGroups['published'] = 1;
				$sGroups['default'] = $oldgroup['default'];

				$table = $this->getTable('shoppergroups');

				$table->bindChecknStore($sGroups);
				$errors = $table->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portShoppergroups '.$error);
					}
					break;
				}

				// 				$oldtoNewShoppergroups[$oldgroup['shopper_group_id']] = $sGroups['virtuemart_shoppergroup_id'];
				$alreadyKnownIds[$oldgroup['shopper_group_id']] = $sGroups['virtuemart_shoppergroup_id'];
				unset($sGroups['virtuemart_shoppergroup_id']);
				$i++;
			}
			// 			else {
			// 				$oldtoNewShoppergroups[$oldgroup['shopper_group_id']] = $alreadyKnownIds[$oldgroup['shopper_group_id']];
			// 			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}

		$time = microtime(true) - $starttime;
		$this->_app->enqueueMessage('Processed '.$i.' vm1 shoppergroups time: '.$time);

		$this->storeMigrationProgress('shoppergroups',$alreadyKnownIds);

	}

	private function portUsers(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$query = 'SHOW TABLES LIKE "%vm_user_info%"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No vm_user_info table found for migration');
			$this->_stop = true;
			return false;
		}
		//declaration _vm_userfield >> _virtuemart_userfields`

		// vendor_id >> virtuemart_vendor_id
		$this->_db->setQuery('select `name` FROM `#__virtuemart_userfields`');
		$vm2Fields = $this->_db->loadColumn ();
		$this->_db->setQuery('select * FROM `#__vm_userfield`');
		$oldfields = $this->_db->loadObjectList();
		$migratedfields ='';
		$userfields      = $this->getTable('userfields');
		$userinfo   = $this->getTable('userinfos');
		$orderinfo  = $this->getTable('order_userinfos');
		foreach ($oldfields as $field ) {
			if ($field->name =='country' or $field->name =='state') continue;
			if (!isset($field->shipment)) $field->shipment = 0 ;
			if ( !in_array( $field->name, $vm2Fields ) ) {
				$q = 'INSERT INTO `#__virtuemart_userfields` ( `name`, `title`, `description`, `type`, `maxlength`, `size`, `required`, `ordering`, `cols`, `rows`, `value`, `default`, `published`, `registration`, `shipment`, `account`, `readonly`, `calculated`, `sys`, `virtuemart_vendor_id`, `params`)
					VALUES ( "'.$field->name.'"," '.$field->title .'"," '.$field->description .'"," '.$field->type .'"," '.$field->maxlength .'"," '.$field->size .'"," '.$field->required .'"," '.$field->ordering .'"," '.$field->cols .'"," '.$field->rows .'"," '.$field->value .'"," '.$field->default .'"," '.$field->published .'"," '.$field->registration .'"," '.$field->shipment .'"," '.$field->account .'"," '.$field->readonly .'"," '.$field->calculated .'"," '.$field->sys .'"," '.$field->vendor_id .'"," '.$field->params .'" )';
				$this->_db->setQuery($q);
				$this->_db->execute();
				if ($this->_db->getErrorNum()) {
					vmError ($this->_db->getErrorMsg() );
				}
				$userfields->type = $field->type;
				$type = $userfields->formatFieldType($field);
				if (!$userinfo->_modifyColumn ('ADD', $field->name, $type)) {
					vmError($userinfo->getError());
					return false;
				}

				// Alter the order_userinfo table
				if (!$orderinfo->_modifyColumn ('ADD',$field->name, $type)) {
					vmError($orderinfo->getError());
					return false;
				}
				$migratedfields .= '['.$field->name.'] ';

			}
		}
		if ($migratedfields) vminfo('Userfield declaration '.$migratedfields.' Migrated');
		$oldToNewShoppergroups = $this->getMigrationProgress('shoppergroups');
		if(empty($oldToNewShoppergroups)){
			vmInfo('portUsers getMigrationProgress shoppergroups ' . $this->_db->getErrorMsg());
			return false;
		}

		if(!class_exists('VirtueMartModelUser')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
		$userModel = VmModel::getModel('user');

		$ok = true;
		$continue = true;

		//approximatly 110 users take a 1 MB
		$maxItems = $this->_getMaxItems('Users');


		// 		$maxItems = 10;
		$i=0;
		$startLimit = 0;
		$goForST = true;
		$jUserArray = array('id','username','name','password','block','sendEmail','registerDate',
	                    'lastvisitDate','activation','params','lastResetTime','resetCount');

		$JUserString = '`p`.`'.implode('`,`p`.`',$jUserArray).'`';
		//vmdebug('muhh',$JUserString);
		//$continue=false;

		$q = 'SELECT * FROM `#__vm_auth_group` ';
		$this->_db->setQuery($q);
		$groups = $this->_db->loadAssocList();

		while($continue){

			//Lets load all users from the joomla hmm or vm? VM1 users does NOT exist
			$q = 'SELECT `ui`.*,`svx`.*,'.$JUserString.',`vmu`.virtuemart_user_id FROM #__vm_user_info AS `ui`
				LEFT OUTER JOIN #__vm_shopper_vendor_xref AS `svx` ON `svx`.user_id = `ui`.user_id
				LEFT OUTER JOIN #__users AS `p` ON `p`.id = `ui`.user_id
				LEFT OUTER JOIN #__virtuemart_vmusers AS `vmu` ON `vmu`.virtuemart_user_id = `ui`.user_id
								WHERE (`vmu`.virtuemart_user_id) IS NULL  LIMIT '.$startLimit.','.$maxItems ;

			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port shoppers');
			$oldUsers = $res[0];
			$startLimit = $res[1];
			$continue = $res[2];

			$starttime = microtime(true);

			foreach($oldUsers as $user){

				$user['virtuemart_country_id'] = $this->getCountryIDByName($user['country']);
				$user['virtuemart_state_id'] = $this->getStateIDByName($user['state']);

				if(!empty($user['shopper_group_id'])){
					$user['virtuemart_shoppergroups_id'] = $oldToNewShoppergroups[$user['shopper_group_id']];
				}

				if(!empty($user['id'])){
					$user['virtuemart_user_id'] = $user['id'];
				}

				if(!empty($user['user_email'])){
					$user['email'] = $user['user_email'];
				}

				//$userModel->setUserId($user['id']);
				$userModel->setId($user['id']);		//Should work with setId, because only administrators are allowed todo the migration

				//Save the VM user stuff
				if(!$saveUserData=$userModel->saveUserData($user,false)){
					vmdebug('Error migration saveUserData ');
					// 					vmError(JText::_('COM_VIRTUEMART_NOT_ABLE_TO_SAVE_USER_DATA'));
				}


				$userfielddata = $userModel->_prepareUserFields($user, 'BT');

				$userinfo = $this->getTable('userinfos');
				if (!$userinfo->bindChecknStore($userfielddata)) {
					vmError('Migration storeAddress BT '.$userinfo->getError());
				}

				// 				$userinfo   = $this->getTable('userinfos');
				// 				if (!$userinfo->bindChecknStore($user)) {
				// 					vmError('Migrator portUsers '.$userinfo->getError());
				// 				}

				if(!empty($user['user_is_vendor']) && $user['user_is_vendor'] === 1){
					if (!$userModel->storeVendorData($user)){
						vmError('Migrator portUsers '.$userModel->getError());
					}
				}

				$i++;

				$errors = $userModel->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portUsers '.$error);
					}
					$userModel->resetErrors();
					$continue = false;
					//break;
				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					$goForST = false;
					break;
				}
			}
		}

		$time = microtime(true) - $starttime;
		vmInfo('Processed '.$i.' vm1 users time: '.$time);

		//adresses
		$starttime = microtime(true);
		$continue = $goForST;
		$startLimit = 0;
		$i = 0;
		while($continue){

			$q = 'SELECT `ui`.* FROM #__vm_user_info as `ui`
			LEFT OUTER JOIN #__virtuemart_userinfos as `vui` ON `vui`.`virtuemart_user_id` = `ui`.`user_id`
			WHERE `ui`.`address_type` = "ST" AND (`vui`.`virtuemart_user_id`) IS NULL LIMIT '.$startLimit.','.$maxItems;

			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port ST addresses');
			$oldUsersAddresses = $res[0];
			$startLimit = $res[1];
			$continue = $res[2];


			if(empty($oldUsersAddresses)) return $ok;

			//$alreadyKnownIds = $this->getMigrationProgress('staddress');
			$oldtonewST = array();

			foreach($oldUsersAddresses as $oldUsersAddi){

				// 			if(!array_key_exists($oldcategory['virtuemart_userinfo_id'],$alreadyKnownIds)){
				$oldUsersAddi['virtuemart_user_id'] = $oldUsersAddi['user_id'];

				$oldUsersAddi['virtuemart_country_id'] = $this->getCountryIDByName($oldUsersAddi['country']);
				$oldUsersAddi['virtuemart_state_id'] = $this->getStateIDByName($oldUsersAddi['state']);

				$userfielddata = $userModel->_prepareUserFields($oldUsersAddi, 'ST');

				$userinfo = $this->getTable('userinfos');
				if (!$userinfo->bindChecknStore($userfielddata)) {
					vmError('Migration storeAddress ST '.$userinfo->getError());
				}
				$i++;
				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					$continue = false;
					break;
				}

			}
		}

		$time = microtime(true) - $starttime;
		vmInfo('Processed '.$i.' vm1 users ST adresses time: '.$time);
		return $ok;
	}

	private function portVendor(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$query = 'SHOW TABLES LIKE "%_vm_vendor"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No vm_vendor table found for migration');
			$this->_stop = true;
			return false;
		}
		$this->_db->setQuery( 'SELECT *, vendor_id as virtuemart_vendor_id FROM `#__vm_vendor`' );
		$vendor = $this->_db->loadAssoc() ;
		$currency_code_3 = explode( ',', $vendor['vendor_accepted_currencies'] );//EUR,USD
		$this->_db->execute( 'SELECT virtuemart_currency_id FROM `#__virtuemart_currencies` WHERE `currency_code_3` IN ( "'.implode('","',$currency_code_3).'" ) ' );
		$vendor['vendor_accepted_currencies'] = $this->_db->loadColumn();

		$vendorModel = VmModel::getModel('vendor');
		$vendorId = $vendorModel->store($vendor);
		vmInfo('vendor '.$vendorId.' Stored');
		return true;
	}

	private function portCategories(){

		$query = 'SHOW TABLES LIKE "%vm_category%"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No vm_category table found for migration');
			$this->_stop = true;
			return false;
		}

		$catModel = VmModel::getModel('Category');

		$default_category_browse = JRequest::getString('migration_default_category_browse','');
		// 		vmdebug('migration_default_category_browse '.$default_category_browse);

		$default_category_fly = JRequest::getString('migration_default_category_fly','');

		$portFlypages = JRequest::getInt('portFlypages',0);

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}
		$ok = true;

		$q = 'SELECT * FROM #__vm_category';
		$this->_db->setQuery($q);
		$oldCategories = $this->_db->loadAssocList();

		$alreadyKnownIds = $this->getMigrationProgress('cats');
		// 		$oldtonewCats = array();

		$category = array();
		$i = 0;
		foreach($oldCategories as $oldcategory){

			if(!array_key_exists($oldcategory['category_id'],$alreadyKnownIds)){

				$category = array();
				//$category['virtuemart_category_id'] = $oldcategory['category_id'];
				$category['virtuemart_vendor_id'] = $oldcategory['vendor_id'];
				$category['category_name'] = stripslashes($oldcategory['category_name']);

				$category['category_description'] = $oldcategory['category_description'];
				$category['published'] = $oldcategory['category_publish'] == 'Y' ? 1 : 0;
// 				$category['created_on'] = $oldcategory['cdate'];
// 				$category['modified_on'] = $oldcategory['mdate'];
				$category['created_on'] = $this->_changeToStamp($oldcategory['cdate']);
				$category['modified_on'] = $this->_changeToStamp($oldcategory['mdate']);

				if($default_category_browse!=$oldcategory['category_browsepage']){
				 $browsepage = $oldcategory['category_browsepage'];
				if (strcmp($browsepage, 'managed') ==0 ) {
				$browsepage="browse_".$oldcategory['products_per_row'];
				}
				$category['category_layout'] = $browsepage;
				}
				if($portFlypages && $default_category_fly!=$oldcategory['category_flypage']){
				$category['category_product_layout'] = $oldcategory['category_flypage'];
				}

				//idea was to do it by the layout, but we store this information additionally for enhanced pagination
				$category['products_per_row'] = $oldcategory['products_per_row'];
				$category['ordering'] = $oldcategory['list_order'];

				if(!empty($oldcategory['category_full_image'])){
					$category['virtuemart_media_id'] = $this->_getMediaIdByName($oldcategory['category_full_image'],'category');
				}

				$catModel->setId(0);
				$category_id = $catModel->store($category);
				$errors = $catModel->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portCategories '.$error);
						$ok = false;
					}
					break;
				}

				$alreadyKnownIds[$oldcategory['category_id']] = $category_id;
				unset($category['virtuemart_category_id']);
				$i++;
			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}

		}

		// here all categories NEW/OLD are Know
		$this->storeMigrationProgress('cats',$alreadyKnownIds);
		if($ok)
		$msg = 'Looks everything worked correct, migrated ' . $i . ' categories ';
		else {
			$msg = 'Seems there was an error porting ' . $i . ' categories ';
			foreach($this->getErrors() as $error){
				$msg .= '<br />' . $error;
			}
		}
		$this->_app->enqueueMessage($msg);


		$q = 'SELECT * FROM #__vm_category_xref ';
		$this->_db->setQuery($q);
		$oldCategoriesX = $this->_db->loadAssocList();

		// $alreadyKnownIds = $this->getMigrationProgress('catsxref');

		$new_id = 0;
		$i = 0;
		$j = 0;
		$ok = true ;
		if(!empty($oldCategoriesX)){
			// 			vmdebug('$oldCategoriesX',$oldCategoriesX);
			foreach($oldCategoriesX as $oldcategoryX){
				$category = array();
				if(!empty($oldcategoryX['category_parent_id'])){
					if(array_key_exists($oldcategoryX['category_parent_id'],$alreadyKnownIds)){
						$category['category_parent_id'] = $alreadyKnownIds[$oldcategoryX['category_parent_id']];
					} else {
						vmError('Port Categories Xref unknow : ID '.$oldcategoryX['category_parent_id']);
						$ok = false ;
						$j++;
						continue ;
					}
				}

				if(array_key_exists($oldcategoryX['category_child_id'],$alreadyKnownIds)){
					$category['category_child_id'] = $alreadyKnownIds[$oldcategoryX['category_child_id']];
				} else {
					vmError('Port Categories Xref unknow : ID '.$oldcategoryX['category_child_id']);
					$ok = false ;
					$j++;
					continue ;
				}
				if ($ok == true) {
					$table = $this->getTable('category_categories');

					$table->bindChecknStore($category);
					$errors = $table->getErrors();
					if(!empty($errors)){
						foreach($errors as $error){
							vmError('Migrator portCategories ref '.$error);
							$ok = false;
						}
						break;
					}


					$i++;
				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					break;
				}
			}

			//$this->storeMigrationProgress('catsxref',$oldtonewCatsXref);
			if($ok)
			$msg = 'Looks everything worked correct, migrated ' . $i . ' categories xref ';
			else {
				$msg = 'Seems there was an error porting ' . $j . ' of '. $i.' categories xref ';
				foreach($this->getErrors() as $error){
					$msg .= '<br />' . $error;
				}
			}
			$this->_app->enqueueMessage($msg);

			return $ok;
		} else {
			$this->_app->enqueueMessage('No categories to import');
			return $ok;
		}
	}

	private function portManufacturerCategories(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}
		$ok = true;

		$q = 'SELECT * FROM #__vm_manufacturer_category';
		$this->_db->setQuery($q);
		$oldMfCategories = $this->_db->loadAssocList();

		if(!class_exists('TableManufacturercategories')) require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'manufacturercategories.php');

		$alreadyKnownIds = $this->getMigrationProgress('mfcats');
		// 		$oldtonewMfCats = array();

		$mfcategory = array();
		$i=0;
		foreach($oldMfCategories as $oldmfcategory){

			if(!array_key_exists($oldmfcategory['mf_category_id'],$alreadyKnownIds)){

				$mfcategory = null;
				$mfcategory = array();
				$mfcategory['mf_category_name'] = $oldmfcategory['mf_category_name'];
				$mfcategory['mf_category_desc'] = $oldmfcategory['mf_category_desc'];
				$mfcategory['published'] = 1;
				$table = $this->getTable('manufacturercategories');

				$table->bindChecknStore($mfcategory);
				$errors = $table->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portManufacturerCategories '.$error);
						$ok = false;
					}
					break;
				}

				$alreadyKnownIds[$oldmfcategory['mf_category_id']] = $mfcategory['virtuemart_manufacturercategories_id'];
				$i++;
			}

			unset($mfcategory['virtuemart_manufacturercategories_id']);

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}
		$this->storeMigrationProgress('mfcats',$alreadyKnownIds);

		if($ok)
		$msg = 'Looks everything worked correct, migrated ' .$i . ' manufacturer categories ';
		else {
			$msg = 'Seems there was an error porting ' . $i . ' manufacturer categories ';
			$msg .= $this->getErrors();
		}

		$this->_app->enqueueMessage($msg);

		return $ok;
	}

	private function portManufacturers(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}
		$ok = true;

		$q = 'SELECT * FROM #__vm_manufacturer ';
		$this->_db->setQuery($q);
		$oldManus = $this->_db->loadAssocList();

		// 		vmdebug('my old manus',$oldManus);
		// 		$oldtonewManus = array();
		$oldtoNewMfcats = $this->getMigrationProgress('mfcats');
		$alreadyKnownIds = $this->getMigrationProgress('manus');

		$i =0 ;
		foreach($oldManus as $oldmanu){
			if(!array_key_exists($oldmanu['manufacturer_id'],$alreadyKnownIds)){
				$manu = null;
				$manu = array();
				$manu['mf_name'] = $oldmanu['mf_name'];
				$manu['mf_email'] = $oldmanu['mf_email'];
				$manu['mf_desc'] = $oldmanu['mf_desc'];
				$manu['virtuemart_manufacturercategories_id'] = $oldtoNewMfcats[$oldmanu['mf_category_id']];
				$manu['mf_url'] = $oldmanu['mf_url'];
				$manu['published'] = 1;

				if(!class_exists('TableManufacturers'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'manufacturers.php');
				$table = $this->getTable('manufacturers');

				$table->bindChecknStore($manu);
				$errors = $table->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){

						vmError('Migrator portManufacturers '.$error);
						$ok = false;
					}
					break;
				}
				$alreadyKnownIds[$oldmanu['manufacturer_id']] = $manu['virtuemart_manufacturer_id'];
				//unset($manu['virtuemart_manufacturer_id']);
				$i++;
			}
			// 			else {
			// 				$oldtonewManus[$oldmanu['manufacturer_id']] = $alreadyKnownIds[$oldmanu['manufacturer_id']];
			// 			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}

		$this->storeMigrationProgress('manus',$alreadyKnownIds);

		if($ok)
		$msg = 'Looks everything worked correct, migrated ' .$i . ' manufacturers ';
		else {
			$msg = 'Seems there was an error porting ' . $i . ' manufacturers ';
			$msg .= $this->getErrors();
		}
		$this->_app->enqueueMessage($msg);
        return $ok;
	}

	private function portProducts(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return false;
		}

		$ok = true;
		$mediaIdFilename = array();

		//approximatly 100 products take a 1 MB
		$maxItems = $this->_getMaxItems('Products');
// 		$maxItems = 100;
		$startLimit = $this->_getStartLimit('products_start');;
		$j=0;


		$alreadyKnownIds = $this->getMigrationProgress('products');
		$oldToNewCats = $this->getMigrationProgress('cats');
		// 		$user = JFactory::getUser();

		//$oldtonewProducts = array();
		$oldtonewManus = $this->getMigrationProgress('manus');

		$userSgrpPrices = JRequest::getInt('userSgrpPrices',0);
		if($userSgrpPrices){
			$oldToNewShoppergroups = $this->getMigrationProgress('shoppergroups');
		}

		$productModel = VmModel::getModel('product');

		if(count($alreadyKnownIds)==($startLimit+$maxItems) ){
			$continue = false;
		} else {
			$continue = true;
		}

		while($continue){

			$q = 'SELECT *,`p`.product_id as product_id FROM `#__vm_product` AS `p`
			LEFT JOIN `#__vm_product_mf_xref` ON `#__vm_product_mf_xref`.`product_id` = `p`.`product_id`
			WHERE (`p`.product_id) IS NOT NULL
			GROUP BY `p`.product_id ORDER BY `p`.product_parent_id LIMIT '.$startLimit.','.$maxItems;

			$doneStart = $startLimit;
			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port Products');
			$oldProducts = $res[0];

			$startLimit = $res[1];
			$continue = $res[2];

 			//vmdebug('in product migrate $oldProducts ',$oldProducts);
			/* Not in VM1
			slug low_stock_notification intnotes metadesc metakey metarobot metaauthor layout published

			created_on created_by modified_on modified_by
			product_override_price override link

			Not in VM2
			product_thumb_image product_full_image attribute
			custom_attribute child_options quantity_options child_option_ids
			shopper_group_id    product_list
			*/

			//There are so many names the same, so we use the loaded array and manipulate it
// 					$oldProducts = array();
			foreach($oldProducts as $product){

				if(!empty($product['product_id']) and !array_key_exists($product['product_id'],$alreadyKnownIds)){

					$product['virtuemart_vendor_id'] = $product['vendor_id'];

					if(!empty($product['manufacturer_id'])){
						if(!empty($oldtonewManus[$product['manufacturer_id']])) {
							$product['virtuemart_manufacturer_id'] = $oldtonewManus[$product['manufacturer_id']];
						}
					}

					$q = 'SELECT `category_id` FROM #__vm_product_category_xref WHERE #__vm_product_category_xref.product_id = "'.$product['product_id'].'" ';
					$this->_db->setQuery($q);
					$productCats = $this->_db->loadColumn();

					$productcategories = array();
					if(!empty($productCats)){
						foreach($productCats as $cat){
							//product has category_id and categories?
							if(!empty($oldToNewCats[$cat])){
								// 								$product['virtuemart_category_id'] = $oldToNewCats[$cat];
								//This should be an array, or is it not in vm1? not cleared, may need extra foreach
								$productcategories[] = $oldToNewCats[$cat];
							} else {
								vmInfo('Coulndt find category for product, maybe just not in a category');
							}
						}
					}
					// if(!empty($alreadyKnownIds[$product['product_id']])){
					// $product_parent_id = $alreadyKnownIds[$product['product_id']];
					// }
					// Converting Attributes from parent product to customfields Cart variant
					// $q = 'SELECT * FROM `#__vm_product_attribute` WHERE `#__vm_product_attribute`.`product_id` ="'.$product['product_id'].'" ';
					// $this->_db->setQuery($q);
					// if(!empty($productAttributes = $this->_db->loadAssocList()) {

					// foreach($productAttributes as $attrib){
					// //custom select or create it
					// $q = 'SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` as c WHERE c.field_type ="V" and c.`custom_title` ="'.$attrib['attribute_name'].'" ';
					// $this->_db->setQuery($q);
					// if (!$virtuemart_custom_id = $this->_db->loadResult()) {
					// $customModel = VmModel::getModel('Custom');
					// $attrib['custom_title'] = $attrib['attribute_name'];
					// $attrib['custom_value'] = $attrib['attribute_value'];
					// $attrib['is_cart_attribute'] = '1';

					// $customModel->store($attrib);
					// }
					// }
					// }

					// Attributes End
					$product['categories'] = $productcategories;

					$product['published'] = $product['product_publish'] == 'Y' ? 1 : 0;

					$q = 'SELECT * FROM `#__vm_product_price` WHERE `product_id` = "'.$product['product_id'].'" ';
					$this->_db->setQuery($q);
					$entries = $this->_db->loadAssocList();
					if($entries){
						foreach($entries as $i=>$price){
							$product['mprices']['product_price_id'][$i] = 0;
							$product['mprices']['product_id'][$i] = $price['product_id'];
							$product['mprices']['product_price'][$i] = $price['product_price'];
							if($userSgrpPrices){
								$product['mprices']['virtuemart_shoppergroup_id'][$i] = $oldToNewShoppergroups[$price['shopper_group_id']];
							}
							$product['mprices']['product_currency'][$i] = $this->_ensureUsingCurrencyId($price['product_currency']);
							$product['mprices']['price_quantity_start'][$i] = $price['price_quantity_start'];
							$product['mprices']['price_quantity_end'][$i] = $price['price_quantity_end'];
							$product['mprices']['product_price_publish_up'][$i] = $price['product_price_vdate'];
							$product['mprices']['product_price_publish_down'][$i] = $price['product_price_edate'];
							$product['mprices']['created_on'][$i] = $this->_changeToStamp($price['cdate']);
							$product['mprices']['modified_on'][$i] = $this->_changeToStamp($price['mdate']);
						}
					}
				//	$product['price_quantity_start'] = $product['price_quantity_start'];
				//	$product['price_quantity_end'] = $product['price_quantity_end'];
		        //    $product['product_price_publish_up'] = $product['product_price_vdate'];
				//	$product['product_price_publish_down'] = $product['product_price_edate'];
					$product['created_on'] = $this->_changeToStamp($product['cdate']);
					$product['modified_on'] = $this->_changeToStamp($product['mdate']); //we could remove this to set modified_on today
					$product['product_available_date'] = $this->_changeToStamp($product['product_available_date']);

					if(!empty($product['product_weight_uom'])){
						$product['product_weight_uom'] = $this->parseWeightUom($product['product_weight_uom']);
					}

					if(!empty($product['product_lwh_uom'])){
						$product['product_lwh_uom'] = $this->parseDimensionUom($product['product_lwh_uom']);
					}
					//$product['created_by'] = $user->id;
					//$product['modified_by'] = $user->id;



					if(!empty($product['product_s_desc'])){
						$product['product_s_desc'] = stripslashes($product['product_s_desc']);
					}

					if(empty($product['product_name'] )){
						$product['product_name'] =  $product['product_sku'].':'.$product['product_id'].':'.$product['product_s_desc'];
					}

					// Here we  look for the url product_full_image and check which media has the same
					// full_image url
					if(!empty($product['product_full_image'])){
						$product['virtuemart_media_id'] = $this->_getMediaIdByName($product['product_full_image'],'product');
					}

					if(!empty($alreadyKnownIds[$product['product_parent_id']])){
						$product['product_parent_id'] = $alreadyKnownIds[$product['product_parent_id']];
						// 						vmInfo('new parent id : '. $product['product_parent_id']);
					} else {
						$product['product_parent_id'] = 0;
					}

					if($this->_keepOldProductIds){
						$product['virtuemart_product_id'] = $product['product_id'];
					}

					$product['virtuemart_product_id'] = $productModel->store($product);

					if(!empty($product['product_id']) and !empty($product['virtuemart_product_id'])){
						$alreadyKnownIds[$product['product_id']] = $product['virtuemart_product_id'];
					} else {
						vmdebug('$product["virtuemart_product_id"] or $product["product_id"] is EMPTY?',$product);
					}

					$errors = $productModel->getErrors();
					if(!empty($errors)){
						foreach($errors as $error){
							vmError('Migration: '.$i.' ' . $error);
						}
						vmdebug('Product add error',$product);
						$productModel->resetErrors();
						$continue = false;
						break;
					}
					$j++;
				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					vmdebug('Product import breaked, you may rise the execution time, this is not an error, just a hint');
					$continue = false;
					break;
				}

			}
			$limitStartToStore = ', products_start = "'.($doneStart+$j).'" ';
			$this->storeMigrationProgress('products',$alreadyKnownIds,$limitStartToStore);
			vmInfo('Migration: '.$i.' products processed ');
		}
		return $ok;
	}

	/**
	 * Finds the media id in the vm2 table for a given filename
	 *
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	var $mediaIdFilename = array();

	function _getMediaIdByName($filename,$type){
		if(!empty($this->mediaIdFilename[$type][$filename])){

			return $this->mediaIdFilename[$type][$filename];
		} else {
			$q = 'SELECT `virtuemart_media_id` FROM `#__virtuemart_medias`
										WHERE `file_title`="' .  $this->_db->escape($filename) . '"
										AND `file_type`="' . $this->_db->escape($type) . '"';
			$this->_db->setQuery($q);
			$virtuemart_media_id = $this->_db->loadResult();
			if($this->_db->getErrors()){
				vmError('Error in _getMediaIdByName',$this->_db->getErrorMsg());
			}
			if(!empty($virtuemart_media_id)){
				$this->mediaIdFilename[$type][$filename] = $virtuemart_media_id;
				return $virtuemart_media_id;
			} else {

				// 				vmdebug('No media found for '.$type.' '.$filename);
			}
		}
	}

	function portOrders(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		if(!class_exists('VirtueMartModelOrderstatus'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orderstatus.php');

		if (!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		$this->_db->setQuery('select `order_status_code` FROM `#__virtuemart_orderstates` `');
		$vm2Fields = $this->_db->loadColumn ();
		$this->_db->setQuery('select * FROM `#__vm_order_status`');
		$oldfields = $this->_db->loadObjectList();
		$migratedfields ='';
		foreach ($oldfields as $field ) {
			if ( !in_array( $field->order_status_code, $vm2Fields ) ) {
				$q = 'INSERT INTO `#__virtuemart_orderstates` ( `virtuemart_vendor_id`, `order_status_code`, `order_status_name`, `order_status_description`, `order_stock_handle`, `ordering`, `published`)
					VALUES ( "'.$field->vendor_id.'","'.$field->order_status_code .'","'.$field->order_status_name .'","'.$field->order_status_description .'","A","'.$field->list_order .'", 1 )';
				$this->_db->setQuery($q);
				$this->_db->execute();
				if ($this->_db->getErrorNum()) {
					vmError ($this->_db->getErrorMsg() );
				}
				$migratedfields .= '['.$field->order_status_code.'-'.$field->order_status_name.'] ';

			}
		}
		if ($migratedfields) vminfo('order states declaration '.$migratedfields.' Migrated');
		$oldtonewOrders = array();

		//Looks like there is a problem, when the data gets tooo big,
		//solved now with query directly ignoring already ported orders.
		$alreadyKnownIds = $this->getMigrationProgress('orders');
		$newproductIds = $this->getMigrationProgress('products');
		$orderCodeToId = $this->createOrderStatusAssoc();

		//approximatly 100 products take a 1 MB
		$maxItems = $this->_getMaxItems('Orders');


		$startLimit = $this->_getStartLimit('orders_start');
		vmdebug('portOrders $startLimit '.$startLimit);
		$i = 0;
		if(count($alreadyKnownIds)==($startLimit+$maxItems) ){
			$continue = false;
		} else {
			$continue = true;
		}

		$reWriteOrderNumber = JRequest::getInt('reWriteOrderNumber',0);
		$userOrderId = JRequest::getInt('userOrderId',0);

		while($continue){

			$q = 'SELECT `o`.*, `op`.*, `o`.`order_number` as `vm1_order_number`, `o2`.`order_number` as `nr2`,`o`.order_id FROM `#__vm_orders` as `o`
				LEFT OUTER JOIN `#__vm_order_payment` as `op` ON `op`.`order_id` = `o`.`order_id`
				LEFT JOIN `#__virtuemart_orders` as `o2` ON `o2`.`order_number` = `o`.`order_number`
				WHERE (o2.order_number) IS NULL ORDER BY o.order_id LIMIT '.$startLimit.','.$maxItems;

			$doneStart = $startLimit;
			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port Orders');
			$oldOrders = $res[0];
			$startLimit = $res[1];
			$continue = $res[2];

			foreach($oldOrders as $order){

				if(!array_key_exists($order['order_id'],$alreadyKnownIds)){
					$orderData = new stdClass();

					$orderData->virtuemart_order_id = null;
					$orderData->virtuemart_user_id = $order['user_id'];
					$orderData->virtuemart_vendor_id = $order['vendor_id'];

					if($reWriteOrderNumber==0){
						if($userOrderId==1){
							$orderData->order_number = $order['order_id'];
						} else {
							$orderData->order_number = $order['vm1_order_number'];
						}
					}

					$orderData->order_pass = 'p' . substr(md5((string)time() . $order['order_number']), 0, 5);
					//Note as long we do not have an extra table only storing addresses, the virtuemart_userinfo_id is not needed.
					//The virtuemart_userinfo_id is just the id of a stored address and is only necessary in the user maintance view or for choosing addresses.
					//the saved order should be an snapshot with plain data written in it.
					//		$orderData->virtuemart_userinfo_id = 'TODO'; // $_cart['BT']['virtuemart_userinfo_id']; // TODO; Add it in the cart... but where is this used? Obsolete?
					$orderData->order_total = $order['order_total'];
					$orderData->order_subtotal = $order['order_subtotal'];
					$orderData->order_tax = empty($order['order_tax'])? 0:$order['order_tax'];
					$orderData->order_shipment = empty($order['order_shipping'])? 0:$order['order_shipping'];
					$orderData->order_shipment_tax = empty($order['order_shipping_tax'])? 0:$order['order_shipping_tax'];
					if(!empty($order['coupon_code'])){
						$orderData->coupon_code = $order['coupon_code'];
						$orderData->coupon_discount = $order['coupon_discount'];
					}
					$orderData->order_discount = $order['order_discount'];

					$orderData->order_status = $order['order_status'];

					if(isset($order['order_currency'])){
						$orderData->user_currency_id = $this->getCurrencyIdByCode($order['order_currency']);
						//$orderData->user_currency_rate = $order['order_status'];
					}
					$orderData->virtuemart_paymentmethod_id = $order['payment_method_id'];
					$orderData->virtuemart_shipmentmethod_id = $order['ship_method_id'];
					//$orderData->order_status_id = $oldToNewOrderstates[$order['order_status']]


					$_filter = JFilterInput::getInstance(array('br', 'i', 'em', 'b', 'strong'), array(), 0, 0, 1);
					$orderData->customer_note = $_filter->clean($order['customer_note']);
					$orderData->ip_address = $order['ip_address'];

					$orderData->created_on = $this->_changeToStamp($order['cdate']);
					$orderData->modified_on = $this->_changeToStamp($order['mdate']); //we could remove this to set modified_on today

					$orderTable = $this->getTable('orders');
					$orderTable->bindChecknStore($orderData);
					$errors = $orderTable->getErrors();
					if(!empty($errors)){
						foreach($errors as $error){
							$this->_app->enqueueMessage('Migration orders: ' . $error);
						}
						$continue = false;
						break;
					}
					$i++;
					$newId = $alreadyKnownIds[$order['order_id']] = $orderTable->virtuemart_order_id;

					$q = 'SELECT * FROM `#__vm_order_item` WHERE `order_id` = "'.$order['order_id'].'" ';
					$this->_db->setQuery($q);
					$oldItems = $this->_db->loadAssocList();
					//$this->_app->enqueueMessage('Migration orderhistories: ' . $newId);
					foreach($oldItems as $item){
						$item['virtuemart_order_id'] = $newId;
						if(!empty($newproductIds[$item['product_id']])){
							$item['virtuemart_product_id'] = $newproductIds[$item['product_id']];
						} else {
							vmWarn('Attention, order is pointing to deleted product (not found in the array of old products)');
						}

						//$item['order_status'] = $orderCodeToId[$item['order_status']];
						$item['created_on'] = $this->_changeToStamp($item['cdate']);
						$item['modified_on'] = $this->_changeToStamp($item['mdate']); //we could remove this to set modified_on today
						$item['product_attribute'] = $this->_attributesToJson($item['product_attribute']); //we could remove this to set modified_on today

						$item['product_discountedPriceWithoutTax'] = $item['product_final_price']   -  $item['product_tax'];
						$item['product_subtotal_with_tax'] = $item['product_final_price']   *  $item['product_quantity'];
						$orderItemsTable = $this->getTable('order_items');
						$orderItemsTable->bindChecknStore($item);
						$errors = $orderItemsTable->getErrors();
						if(!empty($errors)){
							foreach($errors as $error){
								$this->_app->enqueueMessage('Migration orderitems: ' . $error);
							}
							$continue = false;
							break;
						}
					}

					$q = 'SELECT * FROM `#__vm_order_history` WHERE `order_id` = "'.$order['order_id'].'" ';
					$this->_db->setQuery($q);
					$oldItems = $this->_db->loadAssocList();

					foreach($oldItems as $item){
						$item['virtuemart_order_id'] = $newId;
						//$item['order_status_code'] = $orderCodeToId[$item['order_status_code']];


						$orderHistoriesTable = $this->getTable('order_histories');
						$orderHistoriesTable->bindChecknStore($item);
						$errors = $orderHistoriesTable->getErrors();
						if(!empty($errors)){
							foreach($errors as $error){
								$this->_app->enqueueMessage('Migration orderhistories: ' . $error);
							}
							$continue = false;
							break;
						}
					}

					$q = 'SELECT * FROM `#__vm_order_user_info` WHERE `order_id` = "'.$order['order_id'].'" ';
					$this->_db->setQuery($q);
					$oldItems = $this->_db->loadAssocList();
					if($oldItems){
						foreach($oldItems as $item){
							$item['virtuemart_order_id'] = $newId;
							$item['virtuemart_user_id'] = $item['user_id'];
							$item['virtuemart_country_id'] = $this->getCountryIDByName($item['country']);
							$item['virtuemart_state_id'] = $this->getStateIDByName($item['state']);

							$item['email'] = $item['user_email'];
							$orderUserinfoTable = $this->getTable('order_userinfos');
							$orderUserinfoTable->bindChecknStore($item);
							$errors = $orderUserinfoTable->getErrors();
							if(!empty($errors)){
								foreach($errors as $error){
									$this->_app->enqueueMessage('Migration orderuserinfo: ' . $error);
								}
								$continue = false;
								break;
							}
						}
					}
					//$this->_app->enqueueMessage('Migration: '.$i.' order processed new id '.$newId);
				}
// 				$this->storeMigrationProgress('orders',$alreadyKnownIds);
				// 				 else {
				// 					$oldtonewOrders[$order['order_id']] = $alreadyKnownIds[$order['order_id']];
				// 				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					$continue = false;

					break;
				}
			}
		}

		$limitStartToStore = ', orders_start = "'.($doneStart+$i).'" ';
		$this->storeMigrationProgress('orders',$alreadyKnownIds,$limitStartToStore);
		vmInfo('Migration: '.$i.' orders processed '.($doneStart+$i).' done.');
        return true;;
	}

	function portOrderStatus(){

		$q = 'SELECT * FROM `#__vm_order_status` ';

		$this->_db->setQuery($q);
		$oldOrderStatus = $this->_db->loadAssocList();

		$orderstatusModel = VmModel::getModel('Orderstatus');
		$oldtonewOrderstates = array();
		$alreadyKnownIds = $this->getMigrationProgress('orderstates');
		$i = 0;
		foreach($oldOrderStatus as $status){
			if(!array_key_exists($status['order_status_id'],$alreadyKnownIds)){
				$status['virtuemart_orderstate_id'] = 0;
				$status['virtuemart_vendor_id'] = $status['vendor_id'];
				$status['ordering'] = $status['list_order'];
				$status['published'] = 1;

				$newId = $orderstatusModel->store($status);
				$errors = $orderstatusModel->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						$this->_app->enqueueMessage('Migration: ' . $error);
					}
					$orderstatusModel->resetErrors();
					//break;
				}
				$oldtonewOrderstates[$status['order_status_id']] = $newId;
				$i++;
			} else {
				//$oldtonewOrderstates[$status['order_status_id']] = $alreadyKnownIds[$status['order_status_id']];
			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}

		$oldtonewOrderstates = array_merge($oldtonewOrderstates,$alreadyKnownIds);
		$oldtonewOrderstates = array_unique($oldtonewOrderstates);

		vmInfo('Migration: '.$i.' orderstates processed ');
		return;
	}

	private function _changeToStamp($dateIn){

		$date = JFactory::getDate($dateIn);
		return $date->toSql();
	}

	private function _ensureUsingCurrencyId($curr){

		$currInt = '';
		if(!empty($curr)){
			$this->_db = JFactory::getDBO();
			$q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies` WHERE `currency_code_3`="' . $this->_db->escape($curr) . '"';
			$this->_db->setQuery($q);
			$currInt = $this->_db->loadResult();
			if(empty($currInt)){
				JError::raiseWarning(E_WARNING, 'Attention, couldnt find currency id in the table for id = ' . $curr);
			}
		}

		return $currInt;
	}

	private function _getMaxItems($name){

		$maxItems = 50;
		$freeRam =  ($this->maxMemoryLimit - memory_get_usage(true))/(1024 * 1024) ;
		$maxItems = (int)$freeRam * 70;
		if($maxItems<=0){
			$maxItems = 50;
			vmWarn('Your system is low on RAM! Limit set: '.$this->maxMemoryLimit.' used '.memory_get_usage(true)/(1024 * 1024).' MB and php.ini '.ini_get('memory_limit'));
		} else if($maxItems>1000){
			$maxItems = 1000;
		}
		vmdebug('Migrating '.$name.', free ram left '.$freeRam.' so limit chunk to '.$maxItems);
		return $maxItems;
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _getStartLimit($name){

		$this->_db = JFactory::getDBO();

		$q = 'SELECT `'.$name.'` FROM `#__virtuemart_migration_oldtonew_ids` WHERE id="1" ';

		$this->_db->setQuery($q);

		$limit = $this->_db->loadResult();
		vmdebug('Migrator _getStartLimit '.$name,$limit);
		if(!empty($limit)) return $limit; else return 0;
	}

	/**
	 * Gets the virtuemart_country_id by a country 2 or 3 code
	 *
	 * @author Max Milbers
	 * @param string $name Country 3 or Country 2 code (example US for United States)
	 * return int virtuemart_country_id
	 */
	private $_countries = array();
	private $_states = array();

	private function getCountryIdByName($name){

		if(empty($this->_countries[$name])){
			$this->_countries[$name] = Shopfunctions::getCountryIDByName($name);
		}

		return $this->_countries[$name];
	}

	private function getStateIdByName($name){

		if(empty($this->_states[$name])){
			$this->_states[$name] = Shopfunctions::getStateIDByName($name);
		}

		return $this->_states[$name];
	}

/*	private function getCountryIdByCode($name){
		if(empty($name)){
			return 0;
		}

		if(strlen($name) == 2){
			$countryCode = 'country_2_code';
		}else {
			$countryCode = 'country_3_code';
		}

		$q = 'SELECT `virtuemart_country_id` FROM `#__virtuemart_countries`
				WHERE `' . $countryCode . '` = "' . $this->_db->escape($name) . '" ';
		$this->_db->setQuery($q);

		return $this->_db->loadResult();
	}

	/**
	 * Gets the virtuemart_country_id by a country 2 or 3 code
	 *
	 * @author Max Milbers
	 * @param string $name Country 3 or Country 2 code (example US for United States)
	 * return int virtuemart_country_id
	 */
/*	private function getStateIdByCode($name){
		if(empty($name)){
			return 0;
		}

		if(strlen($name) == 2){
			$code = 'country_2_code';
		}else {
			$code = 'country_3_code';
		}

		$q = 'SELECT `virtuemart_state_id` FROM `#__virtuemart_states`
				WHERE `' . $code . '` = "' . $this->_db->escape($name) . '" ';
		$this->_db->setQuery($q);

		return $this->_db->loadResult();
	}
*/
	private function getCurrencyIdByCode($name){
		if(empty($name)){
			return 0;
		}

		if(strlen($name) == 2){
			$code = 'currency_code_2';
		}else {
			$code = 'currency_code_3';
		}

		$q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies`
					WHERE `' . $code . '` = "' . $this->_db->escape($name) . '" ';
		$this->_db->setQuery($q);

		return $this->_db->loadResult();
	}

	/**
	 *
	 *
	 * @author Max Milbers
	 */
	private function createOrderStatusAssoc(){

		$q = 'SELECT * FROM `#__virtuemart_orderstates` ';
		$this->_db->setQuery($q);
		$orderstats = $this->_db->loadAssocList();
		$xref = array();
		foreach($orderstats as $status){

			$xref[$status['order_status_code']] = $status['virtuemart_orderstate_id'];
		}

		return $xref;
	}

	/**
	 * parse the entered string to a standard unit
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	private function parseWeightUom($weightUnit){

		$weightUnit = strtolower($weightUnit);
		$weightUnitMigrateValues = self::getWeightUnitMigrateValues();
		return $this->parseUom($weightUnit,$weightUnitMigrateValues );

	}

	/**
	 *
	 * parse the entered string to a standard unit
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	private function parseDimensionUom($dimensionUnit){

		$dimensionUnitMigrateValues = self::getDimensionUnitMigrateValues();
		$dimensionUnit = strtolower($dimensionUnit);
		return $this->parseUom($dimensionUnit,$dimensionUnitMigrateValues );

	}

	/**
	 *
	 * parse the entered string to a standard unit
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	private function parseUom($unit, $migrateValues){
		$new="";
		$unit = strtolower($unit);
		foreach ($migrateValues as $old => $new) {
			if (strpos($unit, $old) !== false) {
				return $new;
			}
		}
	}

	/**
	 *
	 * get new Length Standard Unit
	 * @author Valerie Isaksen
	 *
	 */
	function getDimensionUnitMigrateValues() {

		$dimensionUnitMigrate=array (
                  'mm' => 'MM'
		, 'cm' => 'CM'
		, 'm' => 'M'
		, 'yd' => 'YD'
		, 'foot' => 'FT'
		, 'ft' => 'FT'
		, 'inch' => 'IN'
		);
		return $dimensionUnitMigrate;
	}
	/**
	 *
	 * get new Weight Standard Unit
	 * @author Valerie Isaksen
	 *
	 */
	function getWeightUnitMigrateValues() {
		$weightUnitMigrate=array (
                  'kg' => 'KG'
		, 'kilos' => 'KG'
		, 'gr' => 'G'
		, 'pound' => 'LB'
		, 'livre' => 'LB'   //TODO ERROR HERE
		, 'once' => 'OZ'
		, 'ounce' => 'OZ'
		);
		return $weightUnitMigrate;
	}

	/**
	 * Helper function, was used to determine the difference of an loaded array (from vm19
	 * and a loaded object of vm2
	 */
	private function showVmDiff(){

		$productModel = VmModel::getModel('product');
		$product = $productModel->getProduct(0);

		$productK = array();
		$attribsImage = get_object_vars($product);

		foreach($attribsImage as $k => $v){
			$productK[] = $k;
		}

		$oldproductK = array();
		foreach($oldProducts[0] as $k => $v){
			$oldproductK[] = $k;
		}

		$notSame = array_diff($productK, $oldproductK);
		$names = '';
		foreach($notSame as $name){
			$names .= $name . ' ';
		}
		$this->_app->enqueueMessage('_productPorter  array_intersect ' . $names);

		$notSame = array_diff($oldproductK, $productK);
		$names = '';
		foreach($notSame as $name){
			$names .= $name . ' ';
		}
		$this->_app->enqueueMessage('_productPorter  ViceVERSA array_intersect ' . $names);
	}

	function loadCountListContinue($q,$startLimit,$maxItems,$msg){

		$continue = true;
		$this->_db->setQuery($q);
		if(!$this->_db->execute()){
			vmError($msg.' db error '. $this->_db->getErrorMsg());
			vmError($msg.' db error '. $this->_db->getQuery());
			$entries = array();
			$continue = false;
		} else {
			$entries = $this->_db->loadAssocList();
			$count = count($entries);
			vmInfo($msg. ' take '.$count.' vm1 entries for migration ');
			$startLimit += $maxItems;
			if($count<$maxItems){
				$continue = false;
			}
		}

		return array($entries,$startLimit,$continue);
	}

	function portCurrency(){

		$this->setRedirect($this->redirectPath);
		$db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_currency_id`,
		  `currency_name`,
		  `currency_code_2`,
		  `currency_code` AS currency_code_3,
		  `currency_numeric_code`,
		  `currency_exchange_rate`,
		  `currency_symbol`,
		`currency_display_style` AS `_display_style`
			FROM `#__virtuemart_currencia` ORDER BY virtuemart_currency_id';
		$db->setQuery($q);
		$result = $db->loadObjectList();

		foreach($result as $item){

			//			$item->virtuemart_currency_id = 0;
			$item->currency_exchange_rate = 0;
			$item->published = 1;
			$item->shared = 1;
			$item->virtuemart_vendor_id = 1;

			$style = explode('|', $item->_display_style);

			$item->currency_nbDecimal = $style[2];
			$item->currency_decimal_symbol = $style[3];
			$item->currency_thousands = $style[4];
			$item->currency_positive_style = $style[5];
			$item->currency_negative_style = $style[6];

			$db->insertObject('#__virtuemart_currencies', $item);
		}

		$this->setRedirect($this->redirectPath);
	}

	/**
	 * Method to restore all virtuemart tables in a database with a given prefix
	 *
	 * @access	public
	 * @param	string	Old table prefix
	 * @return	boolean	True on success.
	 */
	function restoreDatabase($prefix='bak_vm_') {
		// Initialise variables.
		$return = true;

		$this->_db = JFactory::getDBO();

		// Get the tables in the database.
		if ($tables = $this->_db->getTableList()) {
			foreach ($tables as $table) {
				// If the table uses the given prefix, back it up.
				if (strpos($table, $prefix) === 0) {
					// restore table name.
					$restoreTable = str_replace($prefix, '#__vm_', $table);

					// Drop the current active table.
					$this->_db->setQuery('DROP TABLE IF EXISTS '.$this->_db->nameQuote($restoreTable));
					$this->_db->execute();

					// Check for errors.
					if ($this->_db->getErrorNum()) {
						vmError('Migrator restoreDatabase '.$this->_db->getErrorMsg());
						$return = false;
					}

					// Rename the current table to the backup table.
					$this->_db->setQuery('RENAME TABLE '.$this->_db->nameQuote($table).' TO '.$this->_db->nameQuote($restoreTable));
					$this->_db->execute();

					// Check for errors.
					if ($this->_db->getErrorNum()) {
						vmError('Migrator restoreDatabase '.$this->_db->getErrorMsg());
						$return = false;
					}
				}
			}
		}

		return $return;
	}

	private function _attributesToJson($attributes){
		if ( !trim($attributes) ) return '';
		$attributesArray = explode(";", $attributes);
		foreach ($attributesArray as $valueKey) {
			// do the array
			$tmp = explode(":", $valueKey);
			if ( count($tmp) == 2 ) {
				if ($pos = strpos($tmp[1], '[')) $tmp[1] = substr($tmp[1], 0, $pos) ; // remove price
				$newAttributes['attributs'][$tmp[0]] = $tmp[1];
			}
		}
		return json_encode($newAttributes,JSON_FORCE_OBJECT);
	}

/**
	 * Roughly taken from the forum, a bit rewritten by Max Milbers to use the joomla database
	 * Thank you raycarter
	 *
	 * http://forum.virtuemart.net/index.php?topic=102083.0
	 * @author raycarter
	 */

	function portVm1Attributes(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$alreadyKnownIds = $this->getMigrationProgress('attributes');
		$i = 0;


		$prefix = '#_';
		$oldtable = '#__vm_product';
		$db = JFactory::getDbo();
		$db->setQuery("SELECT product_sku, attribute FROM " . $oldtable . " WHERE ( attribute IS NULL or attribute <> '') ");
		$rows = $db->loadObjectList();

		foreach ($rows as $product) {

			$db->setQuery("SELECT virtuemart_product_id FROM " . $prefix . "_virtuemart_products WHERE product_sku=" . $db->Quote($product->product_sku));
			$productid = (int)$db->loadResult();
			if(!in_array($productid,$alreadyKnownIds)){
				$ignore = JRequest::getVar('prodIdsToIgnore',array());
				if(!is_array($ignore)) $ignore = array($ignore);
				foreach($ignore as &$ig){
					$ig = (int)$ig;
				}
				$ign = false;
				if (count($ignore) && $productid) {
					foreach ($ignore as $ig) {
						if ($ig == $productid) {
							$ign = true;
							echo "ignoring product_id =" . $productid . "<br/>";
							break;
						}
					}
				}
				if (!$ign) {


					$attrStr = explode(";", $product->attribute);


					foreach ($attrStr as $attributes) {
						$result = "adding attributes for product_id :" . $productid . "<br/>";
						$attrData = array();
						$attrData = explode(",", $attributes);
						//its the parent, create it,it does not exist before
						$db->setQuery("SELECT virtuemart_custom_id FROM " . $prefix . "_virtuemart_customs WHERE custom_title =" . $db->Quote($attrData[0]));
						$parent = $db->loadResult();
						if ($parent) {
							$pid = $parent;
							$result.="found parent with id=" . $parent . "<br/>";
						} else {
							$query = 'INSERT INTO ' . $prefix . '_virtuemart_customs (custom_title,custom_tip,field_type,is_cart_attribute,published) VALUES
        (' . $db->Quote($attrData[0]) . ',"","V","1","1")';
							$db->setQuery($query);
							if (!$db->execute()) die($query);


							$pid = $db->insertid();
							$result.= "<p>inserted parent " . $attrData[0] . "</p>";
						}
						foreach ($attrData as $key => $attr) {
							if ($key != '0') {
								$priceset = explode("[", $attr);
								$price = 0;
								$warning='';
								if (count($priceset) > 1) {
									$price = substr($priceset[1], 0, -1); // remove ]
									if ('=' == substr ($price, 0,1)) {
										// Don't port, set the price to 0
										$price = 0;
										$warning='WARNING: Price for this attribute has been set to 0';
									} elseif  ("+" == substr($price, 0,1)) {
										$price =  substr($price, 1); // remove the +
									}
								}
								$cleaned = $priceset[0];
								//get ordering of the last element and add 1 to it
								$db->setQuery('SELECT MAX(ordering) from ' . $prefix . '_virtuemart_product_customfields');
								$ordering = $db->loadResult() + 1;
								$query = 'INSERT INTO ' . $prefix . '_virtuemart_product_customfields (virtuemart_product_id,virtuemart_custom_id,custom_value,custom_price,ordering) VALUES
                (' . $productid . ',' . $pid . ',' . $db->Quote($cleaned) . ',' . $price . ',' . $ordering . ')';
								$db->setQuery($query);
								if (!$db->execute()) {
									$result.="query failed for attribute :" . $cleaned . ", query :" . $query . "</br>";
									vmWarn('portVm1Attributes '.$result);
								};
								$result.="inserted attribute for parent :" . $attrData[0] . ", atttribute name :" . $cleaned . ' '.$warning. "<br/>";

							}
						}
					}
				}
				$alreadyKnownIds[] = $productid;
				$i++;
				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					break;
				}

			} else {

			}
		}

		$this->storeMigrationProgress('attributes',$alreadyKnownIds);
	}

	/**
	 * Roughly taken from the forum, a bit rewritten by Max Milbers to use the joomla database
	 * Thank you oneforallsoft
	 *
	 * http://forum.virtuemart.net/index.php?topic=116403.0
	 * http://www.oneforallsoft.com/related-products-missing-after-virtuemart-upgrade/
	 * @author oneforallsoft
	 */
	function portVm1RelatedProducts(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}
		vmSetStartTime('relatedproducts');

	    $maxItems = $this->_getMaxItems('relatedproducts');
		$startLimit = $this->_getStartLimit('relatedproducts_start');;
		$i=0;
		$continue = true;

		$alreadyKnownIds = $this->getMigrationProgress('relatedproducts');

		$out=array();
		$out2=array();

		while($continue){
			$q ='select * from #__vm_product_relations LIMIT '.$startLimit.','.$maxItems;

			$doneStart = $startLimit;
			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port Related products');
			$oldVm1relateds = $res[0];

			$startLimit = $res[1];
			$continue = $res[2];

			foreach($oldVm1relateds as $v){
				$pid=$v['product_id'];
				$ids=explode('|',$v['related_products']);
				$out=array_merge($ids,$out);
				$out[]=$pid;
				$out2[$pid]=$ids;
				$i++;
			}
			// GET SkuS for Products
			$skus=array();
			$q="select product_id,product_sku from #__vm_product where product_id in (".implode(',',$out).") ";
			$this->_db->setQuery($q );
			$product_skus = $this->_db->loadAssocList();
			if (empty($product_skus)) {
				vmError("Port Related products: The following SKUs were not found ".implode(',',$out) );
				break;
			}
			foreach ($product_skus as $v) {
				$skus[$v['product_id']]=$v['product_sku'];
			}

			foreach($out2 as $k=>$v){
				$tmp=array();
				foreach($v as $vv){
					if(isset($skus[$vv]))
						$tmp[]=$skus[$vv];
				}
				$out[$skus[$k]]=$tmp;
			}

			// GET virtuemart_product_id for those SKUs
			$q="select virtuemart_product_id,product_sku from #__virtuemart_products where product_sku in ('".implode("','",$skus)."') ";
			$this->_db->setQuery($q);
			$out3=array();
			$products = $this->_db->loadAssocList();
			if (empty($products)) {
				vmError("Port Related products: Some of those SKUs were not found ".implode(',',$skus) );
				break;
			}
			foreach ($products as $v) {
				$out3[$v['product_sku']]=$v["virtuemart_product_id"];
			}
			$now=date('Y-m-d H:i:s',time());
			$sql='';
			foreach($out as $k => $v){
				foreach($v as $vv){
					if(isset($out3[$k]) and isset($out3[$vv]))
						$sql.=",({$out3[$k]},1,{$out3[$vv]},'".$now."')";
				}
			}
			if (empty($sql)) {
				vmError("Port Related products: Error while inserting new related products " );
				break;
			}
			$q="INSERT INTO #__virtuemart_product_customfields (virtuemart_product_id,virtuemart_custom_id,custom_value,modified_on) values ".substr($sql,1);
			$this->_db->setQuery($q) ;
			$this->_db->execute();

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				vmdebug('Related products import breaked, you may rise the execution time, this is not an error, just a hint');
				$continue = false;
				break;
			}
		}
		if($out and count($out)==0){
			vmdebug ('no related products found');
			return;
		} else {
			vmdebug ('FOUND Related products ',count($out) );
		}

		$limitStartToStore = ', relatedproducts = "'.($doneStart+$i).'" ';
		$this->storeMigrationProgress('relatedproducts',$alreadyKnownIds,$limitStartToStore);
		vmInfo('Migration: '.$i.' Related products processed ');
	}



}

