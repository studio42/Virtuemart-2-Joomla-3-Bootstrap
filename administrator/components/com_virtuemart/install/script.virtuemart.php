<?php
/**
 * VirtueMart script file
 *
 * This file is executed during install/upgrade and uninstall
 *
 * @author Max Milbers, RickG, impleri
 * @package VirtueMart
 */
defined('_JEXEC') or die('Restricted access');

//Maybe it is possible to set this within the xml file note by Max Milbers
$memory_limit = (int) substr(ini_get('memory_limit'),0,-1);
if($memory_limit<128)  @ini_set( 'memory_limit', '128M' );

$maxtime = (int) ini_get('max_execution_time');
if($maxtime < 140){
	@ini_set( 'max_execution_time', '140' );
}

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart');

if(!class_exists('JModel')) require JPATH_VM_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';

// hack to prevent defining these twice in 1.6 installation
if (!defined('_VM_SCRIPT_INCLUDED')) {
	define('_VM_SCRIPT_INCLUDED', true);


	/**
	 * VirtueMart custom installer class
	 */
	class com_virtuemartInstallerScript {


		/**
		 * method must be called after preflight
		 * Sets the paths and loads VMFramework config
		 */
		public function loadVm() {
// 			$this->path = JInstaller::getInstance()->getPath('extension_administrator');

			if(empty($this->path)){
				$this->path = JPATH_VM_ADMINISTRATOR;
			}
			require_once($this->path.DS.'helpers'.DS.'config.php');
			JTable::addIncludePath($this->path.DS.'tables');
			JModel::addIncludePath($this->path.DS.'models');

		}

		public function checkIfUpdate(){

			$update = false;
			$this->_db = JFactory::getDBO();
			$q = 'SHOW TABLES LIKE "%virtuemart_adminmenuentries%"'; //=>jos_virtuemart_shipment_plg_weight_countries
			$this->_db->setQuery($q);
			if($this->_db->loadResult()){

				$q = "SELECT count(id) AS idCount FROM `#__virtuemart_adminmenuentries`";
				$this->_db->setQuery($q);
				$result = $this->_db->loadResult();

				if (empty($result)) {
					$update = false;
				} else {
					$update = true;
				}
			} else {
				$update = false;
			}

			$this->update = $update;
			return $update;
		}


		/**
		 * Pre-process method (e.g. install/upgrade) and any header HTML
		 *
		 * @param string Process type (i.e. install, uninstall, update)
		 * @param object JInstallerComponent parent
		 * @return boolean True if VM exists, null otherwise
		 */
		public function preflight ($type, $parent=null) {

			if(version_compare(JVERSION,'1.6.0','ge')) {

				$q = 'DELETE FROM `#__menu` WHERE `menutype` = "main" AND
						(`link`="index.php?option=com_virtuemart" OR `alias`="virtuemart" )';
				$this->_db = JFactory::getDbo();
				$this->_db -> setQuery($q);
				$this->_db -> query();
				$error = $this->_db->getErrorMsg();
				if(!empty($error)){
					$app = JFactory::getApplication();
					$app ->enqueueMessage('Error deleting old vm admin menu (BE) '.$error);
				}
			}

		}


		/**
		 * Install script
		 * Triggers after database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function install ($loadVm = true) {

			if($loadVm) $this->loadVm();

			if($this->checkIfUpdate()){
				return $this->update($loadVm);
			}


			$this -> joomlaSessionDBToMediumText();

			// install essential and required data
			// should this be covered in install.sql (or 1.6's JInstaller::parseSchemaUpdates)?
			//			if(!class_exists('VirtueMartModelUpdatesMigration')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'updatesMigration.php');
			$params = JComponentHelper::getParams('com_languages');
			$lang = $params->get('site', 'en-GB');//use default joomla
			$lang = strtolower(strtr($lang,'-','_'));

			$model = JModel::getInstance('updatesmigration', 'VirtueMartModel');
			$model->execSQLFile($this->path.DS.'install'.DS.'install.sql',$lang);
			$model->execSQLFile($this->path.DS.'install'.DS.'install_essential_data.sql',$lang);
			$model->execSQLFile($this->path.DS.'install'.DS.'install_required_data.sql',$lang);

			//$id = $model->determineStoreOwner();
			$model->setStoreOwner();

			//copy sampel media
			$src = $this->path .DS. 'assets' .DS. 'images' .DS. 'vmsampleimages';
			// 			if(version_compare(JVERSION,'1.6.0','ge')) {

			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'shipment');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'payment');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'category');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'category'.DS.'resized');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'manufacturer');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'manufacturer'.DS.'resized');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'product');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'product'.DS.'resized');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'forSale');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'forSale'.DS.'invoices');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'forSale'.DS.'resized');
			$this->createIndexFolder(JPATH_ROOT .DS. 'images'.DS.'stories'.DS.'virtuemart'.DS.'typeless');


			$dst = JPATH_ROOT .DS. 'images' .DS. 'stories' .DS. 'virtuemart';

			$this->recurse_copy($src,$dst);

			$params = JComponentHelper::getParams('com_languages');
			$lang = $params->get('site', 'en-GB');//use default joomla
			$lang = strtolower(strtr($lang,'-','_'));
			if(!class_exists('GenericTableUpdater')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
			$updater = new GenericTableUpdater();
			$updater->createLanguageTables();

			$this->checkAddDefaultShoppergroups();

			$this->displayFinished(false);

			//include($this->path.DS.'install'.DS.'install.virtuemart.html.php');

			// perhaps a redirect to updatesMigration here rather than the html file?
			//			$parent->getParent()->setRedirectURL('index.php?option=com_virtuemart&view=updatesMigration');

			return true;
		}


		/**
		 * creates a folder with empty html file
		 *
		 * @author Max Milbers
		 *
		 */
		public function createIndexFolder($path){
			if(!class_exists('JFile')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'filesystem'.DS.'file.php');
			if(JFolder::create($path)) {
				if(!JFile::exists($path .DS. 'index.html')){
					JFile::copy(JPATH_ROOT.DS.'components'.DS.'index.html', $path .DS. 'index.html');
				}
				return true;
			}
			return false;
		}

		/**
		 * Update script
		 * Triggers after database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function update ($loadVm = true) {

			if($loadVm) $this->loadVm();

			if(!$this->checkIfUpdate()){
				return $this->install($loadVm);
			}

			$this->_db = JFactory::getDBO();

			if(empty($this->path)) $this->path = JPATH_VM_ADMINISTRATOR;

			$params = JComponentHelper::getParams('com_languages');
			$lang = $params->get('site', 'en-GB');//use default joomla
			$lang = strtolower(strtr($lang,'-','_'));

			if(!class_exists('VirtueMartModelUpdatesMigration')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'updatesmigration.php');
			$model = new VirtueMartModelUpdatesMigration(); //JModel::getInstance('updatesmigration', 'VirtueMartModel');
			$model->execSQLFile($this->path.DS.'install'.DS.'install.sql',$lang);
			// 			$this->displayFinished(true);
			//return false;

			$this -> joomlaSessionDBToMediumText();

// 			$this->portOverwritePrices();
/*			$table = '#__virtuemart_customs';
			$fieldname = 'field_type';
			$fieldvalue = 'G';
			$this->addToRequired($table,$fieldname,$fieldvalue,"INSERT INTO `#__virtuemart_customs`
					(`custom_parent_id`, `admin_only`, `custom_title`, `custom_tip`, `custom_value`, `custom_field_desc`,
					 `field_type`, `is_list`, `is_hidden`, `is_cart_attribute`, `published`) VALUES
						(0, 0, 'COM_VIRTUEMART_STOCKABLE_PRODUCT', 'COM_VIRTUEMART_STOCKABLE_PRODUCT_TIP', NULL,
					'COM_VIRTUEMART_STOCKABLE_PRODUCT_DESC', 'G', 0, 0, 0, 1 );");
*/

			$this->alterTable('#__virtuemart_product_prices',array(
				'product_price_vdate' => ' `product_price_publish_up` DATETIME NULL DEFAULT NULL AFTER `product_currency`',
				'product_price_edate' => ' `product_price_publish_down` DATETIME NULL DEFAULT NULL AFTER `product_price_publish_up`'
			));

			$this->alterTable('#__virtuemart_medias',array(
				'file_params' => '`file_params` varchar(17500)'
			));

			$this->deleteReCreatePrimaryKey('#__virtuemart_userinfos','virtuemart_userinfo_id');

			//$this->renameVdateToPublishDown();

			if(!class_exists('GenericTableUpdater')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'tableupdater.php');
			$updater = new GenericTableUpdater();

			$updater->updateMyVmTables();
			$result = $updater->createLanguageTables();

			$this->checkAddDefaultShoppergroups();

			$this->adjustDefaultOrderStates();

			$this->fixOrdersVendorId();
			if($loadVm) $this->displayFinished(true);

			return true;
		}

		private function fixOrdersVendorId(){

			$multix = Vmconfig::get('multix','none');

			if( $multix == 'none'){

				if(empty($this->_db)){
					$this->_db = JFactory::getDBO();
				}

				$q = 'SELECT `virtuemart_user_id` FROM #__virtuemart_orders WHERE virtuemart_vendor_id = "0" ';
				$this->_db->setQuery($q);
				$res = $this->_db->loadResult();

				if($res){
					//vmdebug('fixOrdersVendorId ',$res);
					$q = 'UPDATE #__virtuemart_orders SET `virtuemart_vendor_id`=1 WHERE virtuemart_vendor_id = "0" ';
					$this->_db->setQuery($q);
					$res = $this->_db->query();
					$err = $this->_db->getErrorMsg();
					if(!empty($err)){
						vmError('fixOrdersVendorId update orders '.$err);
					}
					$q = 'UPDATE #__virtuemart_order_items SET `virtuemart_vendor_id`=1 WHERE virtuemart_vendor_id = "0" ';
					$this->_db->setQuery($q);
					$res = $this->_db->query();
					$err = $this->_db->getErrorMsg();
					if(!empty($err)){
						vmError('fixOrdersVendorId update order_item '.$err);
					}
				}

			}



		}

		private function adjustDefaultOrderStates(){

			if(empty($this->_db)){
				$this->_db = JFactory::getDBO();
			}

			$order_stock_handles = array('P'=>'R', 'C'=>'R', 'X'=>'A', 'R'=>'A', 'S'=>'O');

			foreach($order_stock_handles as $k=>$v){

				$q = 'SELECT `order_stock_handle` FROM `#__virtuemart_orderstates`';
				$this->_db->setQuery($q);
				$res = $this->_db->query();
				$err = $this->_db->getErrorMsg();
				if(empty($res) and empty($err) ){
					$q = 'UPDATE `#__virtuemart_orderstates` SET `order_stock_handle`="'.$v.'" WHERE  `order_status_code`="'.$k.'" ;';
					$this->_db->setQuery($q);

					if(!$this->_db->query()){
						$app = JFactory::getApplication();
						$app->enqueueMessage('Error: Install alterTable '.$this->_db->getErrorMsg() );
						$ok = false;
					}
				}
			}

		}





		/**
		 * @author Max Milbers
		 * @param unknown_type $tablename
		 * @param unknown_type $fields
		 * @param unknown_type $command
		 */
		private function alterTable($tablename,$fields,$command='CHANGE'){

			$ok = true;

			if(empty($this->_db)){
				$this->_db = JFactory::getDBO();
			}

			$query = 'SHOW COLUMNS FROM `'.$tablename.'` ';
			$this->_db->setQuery($query);
			$columns = $this->_db->loadResultArray(0);

			foreach($fields as $fieldname => $alterCommand){
				if(in_array($fieldname,$columns)){
					$query = 'ALTER TABLE `'.$tablename.'` '.$command.' COLUMN `'.$fieldname.'` '.$alterCommand;

					$this->_db->setQuery($query);
					if(!$this->_db->query()){
						$app = JFactory::getApplication();
						$app->enqueueMessage('Error: Install alterTable '.$this->_db->getErrorMsg() );
						$ok = false;
					}
				}
			}

			return $ok;
		}

		/**
		 *
		 * @author Max Milbers
		 * @param unknown_type $table
		 * @param unknown_type $field
		 * @param unknown_type $action
		 * @return boolean This gives true back, WHEN it altered the table, you may use this information to decide for extra post actions
		 */
		private function checkAddFieldToTable($table,$field,$fieldType){

			$query = 'SHOW COLUMNS FROM `'.$table.'` ';
			$this->_db->setQuery($query);
			$columns = $this->_db->loadResultArray(0);

			if(!in_array($field,$columns)){


				$query = 'ALTER TABLE `'.$table.'` ADD '.$field.' '.$fieldType;
				$this->_db->setQuery($query);
				if(!$this->_db->query()){
					$app = JFactory::getApplication();
					$app->enqueueMessage('Error: Install checkAddFieldToTable '.$this->_db->getErrorMsg() );
					return false;
				} else {
					vmdebug('checkAddFieldToTable added '.$field);
					return true;
				}
			}
			return false;
		}

		private function addToRequired($table,$fieldname,$fieldvalue,$insert){
			if(empty($this->_db)){
				$this->_db = JFactory::getDBO();
			}

			$query = 'SELECT * FROM `'.$table.'` WHERE '.$fieldname.' = "'.$fieldvalue.'" ';
			$this->db->setQuery($query);
			$result = $this->db->loadResult();
			if(empty($result) || !$result ){
				$this->db->setQuery($insert);
				if(!$this->db->query()){
					$app = JFactory::getApplication();
					$app->enqueueMessage('Install addToRequired '.$this->db->getErrorMsg() );
				}
			}
		}

		private function deleteReCreatePrimaryKey($tablename,$fieldname){

			//Does not work, the keys must be regenerated
// 			$query = 'ALTER TABLE `#__virtuemart_userinfos`  CHANGE COLUMN `virtuemart_userinfo_id` `virtuemart_userinfo_id` INT(1) NOT NULL AUTO_INCREMENT FIRST';
// 			$this->_db->setQuery($query);
// 			if(!$this->_db->query()){

// 			} else {
// 				$query = 'ALTER TABLE `#__virtuemart_userinfos` AUTO_INCREMENT = 1';
// 				$this->_db->setQuery($query);
// 			}


			$query = 'SHOW FULL COLUMNS  FROM `'.$tablename.'` ';
			$this->_db->setQuery($query);
			$fullColumns = $this->_db->loadObjectList();

			$force = false;
			if($force or $fullColumns[0]->Field==$fieldname and strpos($fullColumns[0]->Type,'char')!==false){
				vmdebug('Old key found, recreate');

				// Yes, I know, it looks senselesss to create a field without autoincrement, to add a key and then the autoincrement and then they key again.
				// But seems the only method to drop and recreate primary, which has already data in it
				//First drop it
				$fields = array($fieldname => '');
				if($this->alterTable($tablename,$fields,'DROP')){

					//Now make the field, nothing must be entered
					$added = $this->checkAddFieldToTable($tablename,$fieldname,"INT(1) UNSIGNED NOT NULL FIRST");

					if($added){
						//Yes it should be primary, ohh it gets sorted, great
						$q = 'ALTER TABLE `'.$tablename.'` ADD KEY (`'.$fieldname.'`)';
						$this->_db->setQuery($q);
						if(!$this->_db->query()){
							$app = JFactory::getApplication();
							$app->enqueueMessage('Error: deleteReCreatePrimaryKey add KEY '.$this->_db->getErrorMsg() );
						}

						//ahh, now we can make it auto_increment
						$fields = array($fieldname => '`'.$fieldname.'` INT(1) UNSIGNED NOT NULL AUTO_INCREMENT FIRST');
						$this->alterTable($tablename,$fields);

						//Great, now it actually takes the attribute being a primary
						$q = 'ALTER TABLE `'.$tablename.'` ADD PRIMARY KEY (`'.$fieldname.'`)';
						$this->_db->setQuery($q);
						if(!$this->_db->query()){
							$app = JFactory::getApplication();
							$app->enqueueMessage('Error: deleteReCreatePrimaryKey final add Primary '.$this->_db->getErrorMsg() );
						} else {
							$q = 'ALTER TABLE `'.$tablename.'`  DROP INDEX `'.$fieldname.'`';
							$this->_db->setQuery($q);
							if(!$this->_db->query()){
								$app->enqueueMessage('Error: deleteReCreatePrimaryKey final add Primary '.$this->_db->getErrorMsg() );
							}
						}
 					}
				}

 			}

		}


		/**
		* Checks if both types of default shoppergroups are set
		* @author Max Milbers
		*/

		private function checkAddDefaultShoppergroups(){

			$q = 'SELECT `virtuemart_shoppergroup_id` FROM `#__virtuemart_shoppergroups` WHERE `default` = "1" ';

			$this->_db = JFactory::getDbo();
			$this->_db->setQuery($q);
			$res = $this->_db ->loadResult();

			if(empty($res)){
				$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `default`, `shared`) VALUES
								(NULL, 1, '-default-', 'This is the default shopper group.', 1, 1);";
				$this->_db->setQuery($q);
				$this->_db->query();
			}

			$q = 'SELECT `virtuemart_shoppergroup_id` FROM `#__virtuemart_shoppergroups` WHERE `default` = "2" ';

			$this->_db->setQuery($q);
			$res = $this->_db ->loadResult();

			if(empty($res)){
				$q = "INSERT INTO `#__virtuemart_shoppergroups` (`virtuemart_shoppergroup_id`, `virtuemart_vendor_id`, `shopper_group_name`, `shopper_group_desc`, `default`, `shared`) VALUES
								(NULL, 1, '-anonymous-', 'Shopper group for anonymous shoppers', 2, 1);";
				$this->_db->setQuery($q);
				$this->_db->query();
			}

		}

		private function changeShoppergroupDataSetAnonShopperToOne(){

			$q = 'SELECT * FROM `#__virtuemart_shoppergroups` WHERE virtuemart_shoppergroup_id = "1" ';
			$this->_db->setQuery($q);
			$sgroup = $this->_db->loadAssoc();

			if($sgroup['default']!=2){
				if(!class_exists('TableShoppergroups')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'shoppergroups.php');
				$db = JFactory::getDBO();
				$table = new TableShoppergroups($db);
				$stdgroup = null;
				$stdgroup = array('virtuemart_shoppergroup_id' => 1,
									'virtuemart_vendor_id'	=> 1,
									'shopper_group_name'		=> '-anonymous-',
									'shopper_group_desc'		=> 'Shopper group for anonymous shoppers',
									'default'					=> 2,
									'published'					=> 1,
									'shared'						=> 1
				);
				$table -> bindChecknStore($stdgroup);

				$sgroup['virtuemart_shoppergroup_id'] = 0;
				$table = new TableShoppergroups($this->_db);
				$table -> bindChecknStore($sgroup);
				vmdebug('changeShoppergroupDataSetAnonShopperToOne $table',$table);
			}
		}


		private function joomlaSessionDBToMediumText(){

			if(version_compare(JVERSION,'1.6.0','ge')) {
				$fields = array('data'=>'`data` mediumtext NULL AFTER `time`');
				$this->alterTable('#__session',$fields);
			}
		}

		/**
		 * Uninstall script
		 * Triggers before database processing
		 *
		 * @param object JInstallerComponent parent
		 * @return boolean True on success
		 */
		public function uninstall ($parent=null) {

			if(empty($this->path)){
				$this->path = JPATH_VM_ADMINISTRATOR;
			}
			//$this->loadVm();
			include($this->path.DS.'install'.DS.'uninstall.virtuemart.html.php');

			return true;
		}

		/**
		 * Post-process method (e.g. footer HTML, redirect, etc)
		 *
		 * @param string Process type (i.e. install, uninstall, update)
		 * @param object JInstallerComponent parent
		 */
		public function postflight ($type, $parent=null) {
			if ($type != 'uninstall') {

				$this->loadVm();
				// 				VmConfig::loadConfig(true);

				$this->_db = JFactory::getDBO();
				$q = 'SHOW TABLES LIKE "%virtuemart_configs%"'; //=>jos_virtuemart_shipment_plg_weight_countries
				$this->_db->setQuery($q);
				$res = $this->_db->loadResult();
				if(!empty($res)){
					JRequest::setVar(JUtility::getToken(), '1', 'post');
					$config = JModel::getInstance('config', 'VirtueMartModel');
					$config->setDangerousToolsOff();
				}

			}

			//Test if vm1.1 is installed and rename file to avoid conflicts
			if(JFile::exists(JPATH_VM_ADMINISTRATOR.DS.'toolbar.php')){
				JFile::move('toolbar.php','toolbar.vm1.php',JPATH_VM_ADMINISTRATOR);
			}

			//Prevents overwriting existing file.
			// 			if(!JFile::exists(JPATH_VM_ADMINISTRATOR.DS.'virtuemart_defaults.cfg')){
			// 				JFile::copy('virtuemart_defaults.cfg-dist','virtuemart_defaults.cfg',JPATH_VM_ADMINISTRATOR);
			// 			}

			return true;
		}

		/**
		 * copy all $src to $dst folder and remove it
		 *
		 * @author Max Milbers
		 * @param String $src path
		 * @param String $dst path
		 * @param String $type modules, plugins, languageBE, languageFE
		 */
		private function recurse_copy($src,$dst ) {

			$dir = opendir($src);
			$this->createIndexFolder($dst);

			if(is_resource($dir)){
				while(false !== ( $file = readdir($dir)) ) {
					if (( $file != '.' ) && ( $file != '..' )) {
						if ( is_dir($src .DS. $file) ) {
							$this->recurse_copy($src .DS. $file,$dst .DS. $file);
						}
						else {
							if(JFile::exists($dst .DS. $file)){
								if(!JFile::delete($dst .DS. $file)){
									$app = JFactory::getApplication();
									$app -> enqueueMessage('Couldnt delete '.$dst .DS. $file);
								}
							}
							if(!JFile::move($src .DS. $file,$dst .DS. $file)){
								$app = JFactory::getApplication();
								$app -> enqueueMessage('Couldnt move '.$src .DS. $file.' to '.$dst .DS. $file);
							}
						}
					}
				}
				closedir($dir);
				if (is_dir($src)) JFolder::delete($src);
			} else {
				$app = JFactory::getApplication();
				$app -> enqueueMessage('Couldnt read dir '.$dir.' source '.$src);
			}

		}

		public function displayFinished($update){

			$lang = JFactory::getLanguage();
			//Load first english files
			$lang->load('com_virtuemart.sys',JPATH_ADMINISTRATOR,'en_GB',true);
			$lang->load('com_virtuemart',JPATH_ADMINISTRATOR,'en_GB',true);

			//load specific language
			$lang->load('com_virtuemart.sys',JPATH_ADMINISTRATOR,null,true);
			$lang->load('com_virtuemart',JPATH_ADMINISTRATOR,null,true);
			?>
<link
	rel="stylesheet"
	href="components/com_virtuemart/assets/css/install.css"
	type="text/css" />
<link
	rel="stylesheet"
	href="components/com_virtuemart/assets/css/toolbar_images.css"
	type="text/css" />

<div align="center">
	<table
		width="100%"
		border="0">
		<tr>
			<td
				valign="top"
				align="center"><a
				href="http://virtuemart.net"
				target="_blank"> <img
					border="0"
					align="center"
					src="components/com_virtuemart/assets/images/vm_menulogo.png"
					alt="Cart" /> </a> <br /> <br />
				<h2>

				<?php echo JText::_('COM_VIRTUEMART_INSTALLATION_WELCOME') ?></h2>
			</td>
			<td>
				<h2>
				<?php
				if($update){
					echo JText::_('COM_VIRTUEMART_UPGRADE_SUCCESSFUL');
					/*					if($this->renewConfigManually){
						echo '<br />'.JText::_('When you got an error deleting the virtuemart.cfg file <br />
					Delete this file manually (administrator/components/com_virtuemart/virtuemart.cfg) and please use
					"renew config from file" in Tools => Updates/Migration');
					}*/
					echo '<br />'.JText::_('COM_VIRTUEMART_EXTENSION_UPGRADE_REMIND');

				} else {
					echo JText::_('COM_VIRTUEMART_INSTALLATION_SUCCESSFUL');
				}
				?>
				</h2> <br />

				<div id="cpanel">

				<?php
				if(!$update){
					?>
					<div class="icon">
						<a
							href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=installSampleData&token='.JUtility::getToken()) ?>">
							<span class="vmicon48 vm_install_48"></span> <br />
						<?php echo JText::_('COM_VIRTUEMART_INSTALL_SAMPLE_DATA'); ?>
							</a>
					</div>

		<?php } ?>

				<div class="icon">
				<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&task=disableDangerousTools&token='.JUtility::getToken() ) ?>">
					<span class="vmicon48 vm_frontpage_48"></span>
					<br /><?php echo JText::_('COM_VIRTUEMART_INSTALL_GO_SHOP') ?>
				</a>
				</div>
			</td>
		</tr>
	</table>
</div>






<?php
		}

	}

	/**
	 * Legacy j1.5 function to use the 1.6 class install/update
	 *
	 * @return boolean True on success
	 * @deprecated
	 */
	function com_install() {
		$vmInstall = new com_virtuemartInstallerScript();
		$upgrade = $vmInstall->checkIfUpdate();

		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6 code here
		} else {
			// Joomla! 1.5 code here
			$method = ($upgrade) ? 'update' : 'install';
			$vmInstall->$method();
			$vmInstall->postflight($method);
		}

		/*		if ((JVM_VERSION===1)) {
			$method = ($upgrade) ? 'update' : 'install';
		$vmInstall->$method();
		$vmInstall->postflight($method);
		}*/

		return true;
	}

	/**
	 * Legacy j1.5 function to use the 1.6 class uninstall
	 *
	 * @return boolean True on success
	 * @deprecated
	 */
	function com_uninstall() {
		$vmInstall = new com_virtuemartInstallerScript();
		// 		$vmInstall->preflight('uninstall');

		if(version_compare(JVERSION,'1.6.0','ge')) {
			// Joomla! 1.6 code here
		} else {
			$vmInstall->uninstall();
			$vmInstall->postflight('uninstall');
		}

		/*		if (JVM_VERSION===1) {
			$vmInstall->uninstall();
		$vmInstall->postflight('uninstall');
		}*/

		return true;
	}

} // if(defined)

// pure php no tag
