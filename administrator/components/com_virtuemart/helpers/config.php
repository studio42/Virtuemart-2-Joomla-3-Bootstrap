<?php
/**
 * Configuration helper class
 *
 * This class provides some functions that are used throughout the VirtueMart shop to access confgiuration values.
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author RickG
 * @author Max Milbers
 * @copyright Copyright (c) 2004-2008 Soeren Eberhardt-Biermann, 2009 VirtueMart Team. All rights reserved.
 */
defined('_JEXEC') or die('Restricted access');

/**
 *
 * We need this extra paths to have always the correct path undependent by loaded application, module or plugin
 * Plugin, module developers must always include this config at start of their application
 *   $vmConfig = VmConfig::loadConfig(); // load the config and create an instance
 *  $vmConfig -> jQuery(); // for use of jQuery
 *  Then always use the defined paths below to ensure future stability
 */
define( 'JPATH_VM_SITE', JPATH_ROOT.DS.'components'.DS.'com_virtuemart' );
defined('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart');
// define( 'JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart' );
define( 'JPATH_VM_PLUGINS', JPATH_VM_ADMINISTRATOR.DS.'plugins' );

if(version_compare(JVERSION,'1.7.0','ge')) {
	defined('JPATH_VM_LIBRARIES') or define ('JPATH_VM_LIBRARIES', JPATH_PLATFORM);
	defined('JVM_VERSION') or define ('JVM_VERSION', 2);
}
else {
	if (version_compare (JVERSION, '1.6.0', 'ge')) {
		defined ('JPATH_VM_LIBRARIES') or define ('JPATH_VM_LIBRARIES', JPATH_LIBRARIES);
		defined ('JVM_VERSION') or define ('JVM_VERSION', 2);
	}
	else {
		defined ('JPATH_VM_LIBRARIES') or define ('JPATH_VM_LIBRARIES', JPATH_LIBRARIES);
		defined ('JVM_VERSION') or define ('JVM_VERSION', 1);
	}
}

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

//This number is for obstruction, similar to the prefix jos_ of joomla it should be avoided
//to use the standard 7, choose something else between 1 and 99, it is added to the ordernumber as counter
// and must not be lowered.
defined('VM_ORDER_OFFSET') or define('VM_ORDER_OFFSET',3);


require(JPATH_VM_ADMINISTRATOR.DS.'version.php');

JTable::addIncludePath(JPATH_VM_ADMINISTRATOR.DS.'tables');

if (!class_exists ('VmModel')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

/**
 * This function shows an info message, the messages gets translated with JText::,
 * you can overload the function, so that automatically sprintf is taken, when needed.
 * So this works vmInfo('COM_VIRTUEMART_MEDIA_NO_PATH_TYPE',$type,$link )
 * and also vmInfo('COM_VIRTUEMART_MEDIA_NO_PATH_TYPE');
 *
 * @author Max Milbers
 * @param unknown_type $publicdescr
 * @param unknown_type $value
 */

function vmInfo($publicdescr,$value=NULL){

	VmConfig::$maxMessageCount++;
	$app = JFactory::getApplication();

	if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
		$lang = JFactory::getLanguage();
		if($value!==NULL){

			$args = func_get_args();
			if (count($args) > 0) {
				$args[0] = $lang->_($args[0]);
				$app ->enqueueMessage(call_user_func_array('sprintf', $args),'info');
			}
		}	else {
			// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
			$publicdescr = $lang->_($publicdescr);
			$app ->enqueueMessage('Info: '.JText::_($publicdescr),'info');
			// 		debug_print_backtrace();
		}
	}
	else {
		if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
			$app->enqueueMessage ('Max messages reached', 'info');
		}
	}

}

/**
 * Informations for the vendors or the administrators of the store, but not for developers like vmdebug
 * @param      $publicdescr
 * @param null $value
 */
function vmAdminInfo($publicdescr,$value=NULL){

	if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
	if(Permissions::getInstance()->isSuperVendor()){
		VmConfig::$maxMessageCount++;
		$app = JFactory::getApplication();

		if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
			$lang = JFactory::getLanguage();
			if($value!==NULL){

				$args = func_get_args();
				if (count($args) > 0) {
					$args[0] = $lang->_($args[0]);
					$app ->enqueueMessage(call_user_func_array('sprintf', $args),'info');
				}
			}	else {
				// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
				$publicdescr = $lang->_($publicdescr);
				$app ->enqueueMessage('Info: '.JText::_($publicdescr),'info');
				// 		debug_print_backtrace();
			}
		}
		else {
			if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
				$app->enqueueMessage ('Max messages reached', 'info');
			}
		}
	}

}

function vmWarn($publicdescr,$value=NULL){

	VmConfig::$maxMessageCount++;
	$app = JFactory::getApplication();

	if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
		$lang = JFactory::getLanguage();
		if($value!==NULL){

			$args = func_get_args();
			if (count($args) > 0) {
				$args[0] = $lang->_($args[0]);
				$app ->enqueueMessage(call_user_func_array('sprintf', $args),'warning');
			}
		}	else {
			// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
			$publicdescr = $lang->_($publicdescr);
			$app ->enqueueMessage('Info: '.$publicdescr,'warning');
			// 		debug_print_backtrace();
		}
	}
	else {
		if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
			$app->enqueueMessage ('Max messages reached', 'info');
		}
	}

}

/**
 * Shows an error message, sensible information should be only in the first one, the second one is for non BE users
 * @author Max Milbers
 */
function vmError($descr,$publicdescr=''){

	VmConfig::$maxMessageCount++;
	$app = JFactory::getApplication();

	if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
		if (empty($descr)) {
			vmTrace ('vmError message empty');
		}
		$lang = JFactory::getLanguage();
		if (!class_exists ('Permissions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		}
		if(Permissions::getInstance()->check('admin')){
			$app = JFactory::getApplication();
			$descr = $lang->_($descr);
			$app ->enqueueMessage('vmError: '.$descr,'error');
		} else {
			if(!empty($publicdescr)){
				$app = JFactory::getApplication();

				$publicdescr = $lang->_($publicdescr);
				$app ->enqueueMessage($publicdescr,'error');
			}
		}
	}
	else {
		if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
			$app->enqueueMessage ('Max messages reached', 'info');
		}
	}


}

/**
 * A debug dumper for VM, it is only shown to backend users.
 *
 * @author Max Milbers
 * @param unknown_type $descr
 * @param unknown_type $values
 */
function vmdebug($debugdescr,$debugvalues=NULL){

	if(VMConfig::showDebug()  ){

		VmConfig::$maxMessageCount++;
		$app = JFactory::getApplication();

		if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
			if($debugvalues!==NULL){
				// 			$debugdescr .=' <pre>'.print_r($debugvalues,1).'<br />'.print_r(get_class_methods($debugvalues),1).'</pre>';

				$args = func_get_args();
				if (count($args) > 1) {
					// 				foreach($args as $debugvalue){
					for($i=1;$i<count($args);$i++){
						if(isset($args[$i])){
							$debugdescr .=' Var'.$i.': <pre>'.print_r($args[$i],1).'<br />'.print_r(get_class_methods($args[$i]),1).'</pre>';
						}
					}

				}
			}

			if(!VmConfig::$echoDebug){
				$app = JFactory::getApplication();
				$app ->enqueueMessage('<span class="vmdebug" >vmdebug '.$debugdescr.'</span>');
			} else {
				echo $debugdescr;
			}

		}
		else {
			if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
				$app->enqueueMessage ('Max messages reached', 'info');
			}
		}

	}

}

function vmTrace($notice,$force=FALSE){

	if($force || (VMConfig::showDebug() ) ){
		//$app = JFactory::getApplication();
		//
		ob_start();
		echo '<pre>';
		debug_print_backtrace();
		echo '</pre>';
		$body = ob_get_contents();
		ob_end_clean();
		if(!VmConfig::$echoDebug){
			$app = JFactory::getApplication();
			$app ->enqueueMessage($notice.' '.$body.' ');
		} else {
			echo $notice.' <pre>'.$body.'</pre>';
		}

	}

}

function vmRam($notice,$value=NULL){
	vmdebug($notice.' used Ram '.round(memory_get_usage(TRUE)/(1024*1024),2).'M ',$value);
}

function vmRamPeak($notice,$value=NULL){
	vmdebug($notice.' memory peak '.round(memory_get_peak_usage(TRUE)/(1024*1024),2).'M ',$value);
}


function vmSetStartTime($name='current'){

	VmConfig::setStartTime($name, microtime(TRUE));
}

function vmTime($descr,$name='current'){

	if (empty($descr)) {
		$descr = $name;
	}
	$starttime = VmConfig::$_starttime ;
	if(empty($starttime[$name])){
		vmdebug('vmTime: '.$descr.' starting '.microtime(TRUE));
		VmConfig::$_starttime[$name] = microtime(TRUE);
	}
	else {
		if ($name == 'current') {
			vmdebug ('vmTime: ' . $descr . ' time consumed ' . (microtime (TRUE) - $starttime[$name]));
			VmConfig::$_starttime[$name] = microtime (TRUE);
		}
		else {
			if (empty($descr)) {
				$descr = $name;
			}
			$tmp = 'vmTime: ' . $descr . ': ' . (microtime (TRUE) - $starttime[$name]);
			vmdebug ($tmp);
		}
	}

}

/**
* The time how long the config in the session is valid.
* While configuring the store, you should lower the time to 10 seconds.
* Later in a big store it maybe useful to rise this time up to 1 hr.
* That would mean that changing something in the config can take up to 1 hour until this change is effecting the shoppers.
*/

/**
 * We use this Class STATIC not dynamically !
 */
class VmConfig {

	// instance of class
	private static $_jpConfig = NULL;
	private static $_debug = NULL;
	public static $_starttime = array();
	public static $loaded = FALSE;

	public static $maxMessageCount = 0;
	public static $maxMessage = 100;
	public static $echoDebug = FALSE;

	var $lang = FALSE;

	var $_params = array();
	var $_raw = array();


	private function __construct() {

		if(function_exists('mb_ereg_replace')){
			mb_regex_encoding('UTF-8');
		}


		//todo
		/*	if(strpos(JVERSION,'1.5') === false){
			$jlang = JFactory::getLanguage();
			$jlang->load('virtuemart', null, 'en-GB', true); // Load English (British)
			$jlang->load('virtuemart', null, $jlang->getDefault(), true); // Load the site's default language
			$jlang->load('virtuemart', null, null, true); // Load the currently selected language
		}*/


	}

	static function getStartTime(){
		return self::$_starttime;
	}

	static function setStartTime($name,$value){
		self::$_starttime[$name] = $value;
	}

	static function showDebug(){

		//return self::$_debug = true;	//this is only needed, when you want to debug THIS file
		if(self::$_debug===NULL){

			$debug = VmConfig::get('debug_enable','none');
			// 			$app = JFactory::getApplication();
			// 			$app ->enqueueMessage($debug);

			// 1 show debug only to admins
			if($debug === 'admin' ){
				if (!class_exists ('Permissions')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
				}
				if(Permissions::getInstance()->check('admin')){
					self::$_debug = TRUE;
				} else {
					self::$_debug = FALSE;
				}
			}
			// 2 show debug to anyone
			else {
				if ($debug === 'all') {
					self::$_debug = TRUE;
				}
				// else dont show debug
				else {
					self::$_debug = FALSE;
				}
			}

		}

		return self::$_debug;
	}


	/**
	 * loads a language file, the trick for us is that always the config option enableEnglish is tested
	 * and the path are already set and the correct order is used
	 * We use first the english language, then the default
	 *
	 * @author Max Milbers
	 * @static
	 * @param $name
	 * @return bool
	 */
	static public function loadJLang($name,$site=false,$loadCore=false){

		$path = JPATH_ADMINISTRATOR;
		if($site){
			$path = JPATH_SITE;
		}
		$jlang =JFactory::getLanguage();
		$tag = $jlang->getTag();
		if(VmConfig::get('enableEnglish', 1) and $tag!='en-GB'){
			$jlang->load($name, $path, 'en-GB');
		}

		$jlang->load($name, $path,$tag,true);

 	}

	/**
	 * Loads the configuration and works as singleton therefore called static. The call using the program cache
	 * is 10 times faster then taking from the session. The session is still approx. 30 times faster then using the file.
	 * The db is 10 times slower then the session.
	 *
	 * Performance:
	 *
	 * Fastest is
	 * Program Cache: 1.5974044799805E-5
	 * Session Cache: 0.00016094612121582
	 *
	 * First config db load: 0.00052118301391602
	 * Parsed and in session: 0.001554012298584
	 *
	 * After install from file: 0.0040450096130371
	 * Parsed and in session: 0.0051419734954834
	 *
	 *
	 * Functions tests if already loaded in program cache, session cache, database and at last the file.
	 *
	 * Load the configuration values from the database into a session variable.
	 * This step is done to prevent accessing the database for every configuration variable lookup.
	 *
	 * @author Max Milbers
	 * @param $force boolean Forces the function to load the config from the db
	 */
	static public function loadConfig($force = FALSE,$fresh = FALSE) {

		if($fresh){
			return self::$_jpConfig = new VmConfig();
		}
		vmSetStartTime('loadConfig');
		if(!$force){
			if(!empty(self::$_jpConfig) && !empty(self::$_jpConfig->_params)){

				return self::$_jpConfig;
			}
		}

		self::$_jpConfig = new VmConfig();

		$db = JFactory::getDBO();
		$query = 'SHOW TABLES LIKE "%virtuemart_configs%"';
		$db->setQuery($query);
		$configTable = $db->loadResult();
// 		self::$_debug = true;

		if(empty($configTable)){
			self::$_jpConfig->installVMconfig();
		}

		$app = JFactory::getApplication();
		$install = 'no';
		if(empty(self::$_jpConfig->_raw)){
			$query = ' SELECT `config` FROM `#__virtuemart_configs` WHERE `virtuemart_config_id` = "1";';
			$db->setQuery($query);
			self::$_jpConfig->_raw = $db->loadResult();
			if(empty(self::$_jpConfig->_raw)){
				if(self::installVMconfig()){
					$install = 'yes';
					$db->setQuery($query);
					self::$_jpConfig->_raw = $db->loadResult();
					self::$_jpConfig->_params = NULL;
				} else {
					$app ->enqueueMessage('Error loading configuration file','Error loading configuration file, please contact the storeowner');
				}
			}
		}

		$i = 0;

		$pair = array();
		if (!empty(self::$_jpConfig->_raw)) {
			$config = explode('|', self::$_jpConfig->_raw);
			foreach($config as $item){
				$item = explode('=',$item);
				if(!empty($item[1])){
					// if($item[0]!=='offline_message' && $item[0]!=='dateformat' ){
					if($item[0]!=='offline_message' ){
						try {
							$value = @unserialize($item[1] );

							if($value===FALSE){
								$app ->enqueueMessage('Exception in loadConfig for unserialize '.$item[0]. ' '.$item[1]);
								$uri = JFactory::getURI();
								$configlink = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=config';
								$app ->enqueueMessage('To avoid this message, enter your virtuemart <a href="'.$configlink.'">config</a> and just save it one time');
							} else {
								$pair[$item[0]] = $value;
							}
						}catch (Exception $e) {
							vmdebug('Exception in loadConfig for unserialize '. $e->getMessage(),$item);
						}
					} else {
						$pair[$item[0]] = unserialize(base64_decode($item[1]) );
					}

				} else {
					$pair[$item[0]] ='';
				}

			}

// 			$pair['sctime'] = microtime(true);
			self::$_jpConfig->_params = $pair;

			self::$_jpConfig->set('sctime',microtime(TRUE));
			self::$_jpConfig->set('vmlang',self::setdbLanguageTag());
			self::$_jpConfig->setSession();
			vmTime('loadConfig db '.$install,'loadConfig');

			return self::$_jpConfig;
		}


		$app ->enqueueMessage('Attention config is empty');
		return self::$_jpConfig;
	}


	/*
	 * Set defaut language tag for translatable table
	 *
	 * @author Patrick Kohl
	 * @return string valid langtag
	 */
	static public function setdbLanguageTag($langTag = 0) {

		if (self::$_jpConfig->lang) {
			return self::$_jpConfig->lang;
		}

		$langs = (array)self::$_jpConfig->get('active_languages',array());
		$isBE = !JFactory::getApplication()->isSite();
		if($isBE){
			$siteLang = JRequest::getVar('vmlang',FALSE );// we must have this for edit form save
			//Why not using the userstate?
		} else {
			if (!$siteLang = JRequest::getVar('vmlang',FALSE )) {
				if ( JVM_VERSION===1 ) {
				// try to find in session lang
				// this work with joomfish j1.5 (application.data.lang)

				$session  =JFactory::getSession();
				$registry = $session->get('registry');
				$siteLang = $registry->getValue('application.data.lang') ;
				} else  {
				// TODO test wiht j1.7
				jimport('joomla.language.helper');
				$languages = JLanguageHelper::getLanguages('lang_code');
				$siteLang = JFactory::getLanguage()->getTag();
				}
				if ( ! $siteLang ) {
					// use user default
					$lang =JFactory::getLanguage();
					$siteLang = $lang->getTag();
				}
			}
			/*//What is the difference of this?
			$params = JComponentHelper::getParams('com_languages');
			$siteLang = $params->get('site', 'en_gb');

			//or this
			$siteLang =JFactory::getLanguage()->getTag();
			*/
		}

		if(!in_array($siteLang, $langs)) {
			$params = JComponentHelper::getParams('com_languages');
			$siteLang = $params->get('site', 'en-GB');//use default joomla
		}

		self::$_jpConfig->lang = strtolower(strtr($siteLang,'-','_'));
		vmdebug('self::$_jpConfig->lang '.self::$_jpConfig->lang);
		defined('VMLANG') or define('VMLANG', self::$_jpConfig->lang );

		return self::$_jpConfig->lang;

 	}

	function setSession(){
/*		$session = JFactory::getSession();
		$session->clear('vmconfig');
		// 		$app = JFactory::getApplication();
		// 		$app ->enqueueMessage('setSession session cache <pre>'.print_r(self::$_jpConfig->_params,1).'</pre>');

// 		$session->set('vmconfig', base64_encode(serialize(self::$_jpConfig)),'vm');

		//We must use base64 for text fields
		$params = self::$_jpConfig->_params;
		$params['offline_message'] = base64_encode($params['offline_message']);
		// $params['dateformat'] = base64_encode($params['dateformat']);

		$params['sctime'] = microtime(true);
		$session->set('vmconfig', serialize($params),'vm');*/
		self::$loaded = TRUE;
	}

	/**
	 * Find the configuration value for a given key
	 *
	 * @author Max Milbers
	 * @param string $key Key name to lookup
	 * @return Value for the given key name
	 */
	static function get($key, $default='',$allow_load=FALSE)
	{

		$value = '';
		if ($key) {

			if (empty(self::$_jpConfig->_params) && $allow_load) {
				self::loadConfig();
			}

			if (!empty(self::$_jpConfig->_params)) {
				if(array_key_exists($key,self::$_jpConfig->_params) && isset(self::$_jpConfig->_params[$key])){
					$value = self::$_jpConfig->_params[$key];
				} else {
					$value = $default;
				}

			} else {
				$value = $default;
			}

		} else {
			$app = JFactory::getApplication();
			$app -> enqueueMessage('VmConfig get, empty key given');
		}

		return $value;
	}

	static function set($key, $value){

		if (empty(self::$_jpConfig->_params)) {
			self::loadConfig();
		}

		if (!class_exists ('Permissions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		}
		if(Permissions::getInstance()->check('admin')){
			if (!empty(self::$_jpConfig->_params)) {
				self::$_jpConfig->_params[$key] = $value;
				self::$_jpConfig->setSession();
			}
		}

	}

	/**
	 * For setting params, needs assoc array
	 * @author Max Milbers
	 */
	function setParams($params){

		if (!class_exists ('Permissions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		}
		if(Permissions::getInstance()->check('admin')){
			self::$_jpConfig->_params = array_merge($this->_params,$params);
		}

	}

	/**
	 * Writes the params as string and escape them before
	 * @author Max Milbers
	 */
	function toString(){
		$raw = '';
		$db = JFactory::getDBO();

		jimport( 'joomla.utilities.arrayhelper' );
		foreach(self::$_jpConfig->_params as $paramkey => $value){

			//Texts get broken, when serialized, therefore we do a simple encoding,
			//btw we need serialize for storing arrays   note by Max Milbers
//			if($paramkey!=='offline_message' && $paramkey!=='dateformat'){
			if($paramkey!=='offline_message'){
				$raw .= $paramkey.'='.serialize($value).'|';
			} else {
				$raw .= $paramkey.'='.base64_encode(serialize($value)).'|';
			}
		}
		self::$_jpConfig->_raw = substr($raw,0,-1);
		return self::$_jpConfig->_raw;
	}

	/**
	 * Find the currenlty installed version
	 *
	 * @author RickG
	 * @param boolean $includeDevStatus True to include the development status
	 * @return String of the currently installed version
	 */
	static function getInstalledVersion($includeDevStatus=FALSE)
	{
		// Get the installed version from the wmVersion class.

		return vmVersion::$RELEASE;
	}

	/**
	 * Return if the used joomla function is j15
	 * @deprecated use JVM_VERSION instead
	 */
	function isJ15(){
		return (strpos(JVERSION,'1.5') === 0);
	}


	function getCreateConfigTableQuery(){

		return "CREATE TABLE IF NOT EXISTS `#__virtuemart_configs` (
  `virtuemart_config_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `config` text,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `locked_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`virtuemart_config_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Holds configuration settings' AUTO_INCREMENT=1 ;";
	}

	/**
	 * Read the file vm_config.dat from the install directory, compose the SQL to write
	 * the config record and store it to the dabase.
	 *
	 * @param $_section Section from the virtuemart_defaults.cfg file to be parsed. Currently, only 'config' is implemented
	 * @return Boolean; true on success, false otherwise
	 * @author Oscar van Eijk
	 */
	public function installVMconfig($_section = 'config'){

		$_value = self::readConfigFile(FALSE);

		if (!$_value) {
			return FALSE;
		}

		$qry = self::$_jpConfig->getCreateConfigTableQuery();
		$_db = JFactory::getDBO();
		$_db->setQuery($qry);
		$_db->query();

		$query = 'SELECT `virtuemart_config_id` FROM `#__virtuemart_configs`
						 WHERE `virtuemart_config_id` = 1';
		$_db->setQuery( $query );
		if ($_db->query()){
			$qry = 'DELETE FROM `#__virtuemart_configs` WHERE `virtuemart_config_id`=1';
			$_db->setQuery($qry);
			$_db->query();
		}


		$_value = join('|', $_value);
		$qry = "INSERT INTO `#__virtuemart_configs` (`virtuemart_config_id`, `config`) VALUES ('1', '$_value')";

		self::$_jpConfig->raw = $_value;

		$_db->setQuery($qry);
		if (!$_db->query()) {
			JError::raiseWarning(1, 'VmConfig::installVMConfig: '.JText::_('COM_VIRTUEMART_SQL_ERROR').' '.$_db->stderr(TRUE));
			echo 'VmConfig::installVMConfig: '.JText::_('COM_VIRTUEMART_SQL_ERROR').' '.$_db->stderr(TRUE);
			die;
			return FALSE;
		}else {
			//vmdebug('Config installed file, store values '.$_value);
			return TRUE;
		}

	}

	/**
	 *
	 * @author Oscar van Eijk
	 * @author Max Milbers
	 */
	function readConfigFile($returnDangerousTools){

		$_datafile = JPATH_VM_ADMINISTRATOR.DS.'virtuemart.cfg';
		if (!file_exists($_datafile)) {
			if (file_exists(JPATH_VM_ADMINISTRATOR.DS.'virtuemart_defaults.cfg-dist')) {
				if (!class_exists ('JFile')) {
					require(JPATH_VM_LIBRARIES . DS . 'joomla' . DS . 'filesystem' . DS . 'file.php');
				}
				JFile::copy('virtuemart_defaults.cfg-dist','virtuemart.cfg',JPATH_VM_ADMINISTRATOR);
			} else {
				JError::raiseWarning(500, 'The data file with the default configuration could not be found. You must configure the shop manually.');
				return FALSE;
			}

		} else {
			vmInfo('Taking config from file');
		}

		$_section = '[CONFIG]';
		$_data = fopen($_datafile, 'r');
		$_configData = array();
		$_switch = FALSE;
		while ($_line = fgets ($_data)) {
			$_line = trim($_line);

			if (strpos($_line, '#') === 0) {
				continue; // Commentline
			}
			if ($_line == '') {
				continue; // Empty line
			}
			if (strpos($_line, '[') === 0) {
				// New section, check if it's what we want
				if (strtoupper($_line) == $_section) {
					$_switch = TRUE; // Ok, right section
				} else {
					$_switch = FALSE;
				}
				continue;
			}
			if (!$_switch) {
				continue; // Outside a section or inside the wrong one.
			}

			if (strpos($_line, '=') !== FALSE) {

				$pair = explode('=',$_line);
				if(isset($pair[1])){
					if(strpos($pair[1], 'array:') !== FALSE){
						$pair[1] = substr($pair[1],6);
						$pair[1] = explode('|',$pair[1]);
					}
					// if($pair[0]!=='offline_message' && $pair[0]!=='dateformat'){
					if($pair[0]!=='offline_message'){
						$_line = $pair[0].'='.serialize($pair[1]);
					} else {
						$_line = $pair[0].'='.base64_encode(serialize($pair[1]));
					}

					if($returnDangerousTools && $pair[0] == 'dangeroustools' ){
						vmdebug('dangeroustools'.$pair[1]);
						if ($pair[1] == "0") {
							return FALSE;
						}
						else {
							return TRUE;
						}
					}

				} else {
					$_line = $pair[0].'=';
				}
				$_configData[] = $_line;

			}

		}

		fclose ($_data);

		if (!$_configData) {
			return FALSE; // Nothing to do
		} else {
			return $_configData;
		}
	}

}

class vmRequest{

	static function uword($field, $default, $custom=''){

 		$source = JRequest::getVar($field,$default);

 		if(function_exists('mb_ereg_replace')){
 			//$source is string that will be filtered, $custom is string that contains custom characters
 			return mb_ereg_replace('[^\w'.preg_quote($custom).']', '', $source);
 		} else {
 			return preg_replace('/[^\w'.preg_quote($custom).']/', '', $source);
 		}
 	}


}

/**
 *
 * Class to provide js API of vm
 * @author Patrick Kohl
 * @author Max Milbers
 */
class vmJsApi{


	private function __construct() {

	}
	/**
	 * Write a <script></script> element
	 * @param   string   path to file
	 * @param   string   library name
	 * @param   string   library version
	 * @param   boolean  load minified version
	 * @return  nothing
	 */

	public static function js($namespace,$path=FALSE,$version='', $minified = NULL)
	{

		static $loaded = array();
		// Only load once
		// using of namespace assume same library have same namespace
		// NEVER WRITE FULL NAME AS $namespace IN CASE OF REVISION NUMBER IF YOU WANT PREVENT MULTI LOAD !!!
		// eg. $namespace = 'jquery.1.8.6' and 'jquery.1.6.2' does not prevent load it
		// use $namespace = 'jquery',$revision ='1.8.6' , $namespace = 'jquery',$revision ='1.6.2' ...
		// loading 2 time a JS file with this method simply return and do not load it the second time


		if (!empty($loaded[$namespace])) {
			return;
		}
		$file = vmJsApi::setPath($namespace,$path,$version, $minified , 'js');
		$document = JFactory::getDocument();
		$document->addScript( $file );
		$loaded[$namespace] = TRUE;
	}

	/**
	 * Write a <link ></link > element
	 * @param   string   path to file
	 * @param   string   library name
	 * @param   string   library version
	 * @param   boolean   library version
	 * @return  nothing
	 */

	public static function css($namespace,$path = FALSE ,$version='', $minified = NULL)
	{

		static $loaded = array();

		// Only load once
		// using of namespace assume same css have same namespace
		// loading 2 time css with this method simply return and do not load it the second time

		if (!empty($loaded[$namespace])) {
			return;
		}
		$file = vmJsApi::setPath( $namespace,$path,  $version='', $minified , 'css');

		$document = JFactory::getDocument();
		$document->addStyleSheet($file);
		$loaded[$namespace] = TRUE;

	}

	/*
	 * Set file path(look in template if relative path)
	 */
	public static function setPath( $namespace ,$path = FALSE ,$version='' ,$minified = NULL , $ext = 'js', $absolute_path=false)
	{

		$version = $version ? '.'.$version : '';
		$min	 = $minified ? '.min' : '';
		$file 	 = $namespace.$version.$min.'.'.$ext ;
		$template = JFactory::getApplication()->getTemplate() ;
		if ($path === FALSE) {
			$uri = JPATH_THEMES .'/'. $template.'/'.$ext ;
			$path= 'templates/'. $template .'/'.$ext ;
		}

		if (strpos($path, 'templates/'. $template ) !== FALSE)
		{
			// Search in template or fallback
			if (!file_exists($uri.'/'. $file)) {
				$assets_path = VmConfig::get('assets_general_path','components/com_virtuemart/assets/') ;
				$path = str_replace('templates/'. $template.'/',$assets_path, $path);
				// vmdebug('setPath',$assets_path,$path);
				// vmWarn('file not found in tmpl :'.$file );
			}
			if ($absolute_path) {
				$path = JPATH_BASE .'/'.$path;
			} else {
				$path = JURI::root(TRUE) .'/'.$path;
			}

		}
		elseif (strpos($path, '//') === FALSE)
		{
			if ($absolute_path) {
				$path = JPATH_BASE .'/'.$path;
			} else {
				$path = JURI::root(TRUE) .'/'.$path;
			}
		}
		return $path.'/'.$file ;
	}
	/**
	 * ADD some javascript if needed
	 * Prevent duplicate load of script
	 * @ Author KOHL Patrick
	 */
	static function jQuery() {

		if (JFactory::getApplication ()->get ('jquery')) {
			return FALSE;
		}
		$isSite = JFactory::getApplication()->isSite();
		if (!VmConfig::get ('jquery', TRUE) and $isSite) {
			return FALSE;
		}
		$document = JFactory::getDocument();
		if(VmConfig::get('google_jquery',TRUE)){
			vmJsApi::js('jquery','//ajax.googleapis.com/ajax/libs/jquery/1.6.4','',TRUE);
			//$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js');
			if (!$isSite) {
				vmJsApi::js ('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16', '', TRUE);
			}
			// if (!$isSite) $document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
		} else {
			vmJsApi::js( 'jquery',FALSE,'',TRUE);
			//$document->addScript(JURI::root(true).'/components/com_virtuemart/assets/js/jquery.min.js');
			if (!$isSite) {
				vmJsApi::js ('jquery-ui', FALSE, '', TRUE);
			}
			//if (!$isSite) $document->addScript(JURI::root(true).'/components/com_virtuemart/assets/js/jquery-ui.min.js');
		}
		if (!$isSite) {
			vmJsApi::js ('jquery.ui.autocomplete.html');
		}
		vmJsApi::js( 'jquery.noConflict');
		JFactory::getApplication()->set('jquery',TRUE);
		return TRUE;
	}
	// Virtuemart product and price script
	static function jPrice()
	{

		if (!VmConfig::get ('jprice', TRUE) and JFactory::getApplication ()->isSite ()) {
			return FALSE;
		}
		static $jPrice;
		// If exist exit
		if ($jPrice) {
			return;
		}
		vmJsApi::jQuery();
		//JPlugin::loadLanguage('com_virtuemart');
		$lang = JFactory::getLanguage();
		$lang->load('com_virtuemart');
		vmJsApi::jSite();

		$closeimage = JURI::root(TRUE) .'/components/com_virtuemart/assets/images/facebox/closelabel.png';
		$jsVars  = '
//<![CDATA[
		'."vmSiteurl = '". JURI::root( ) ."' ;\n" ;
		if (VmConfig::get ('vmlang_js', 1))  {
			$jsVars .= "vmLang = '&amp;lang=" . substr (VMLANG, 0, 2) . "' ;\n";
		}
		else {
			$jsVars .= 'vmLang = "";' . "\n";
		}
		$jsVars .= "vmCartText = '". addslashes( JText::_('COM_VIRTUEMART_MINICART_ADDED_JS') )."' ;\n" ;
		$jsVars .= "vmCartError = '". addslashes( JText::_('COM_VIRTUEMART_MINICART_ERROR_JS') )."' ;\n" ;
		$jsVars .= "loadingImage = '".JURI::root(TRUE) ."/components/com_virtuemart/assets/images/facebox/loading.gif' ;\n" ;
		$jsVars .= "closeImage = '".$closeimage."' ; \n";
		$jsVars .= "Virtuemart.addtocart_popup = '".VmConfig::get('addtocart_popup',1)."' ; \n";
		// $jsVars .= 'faceboxHtml = \'<div id="facebox" style="display:none;"><div class="popup"><div class="content"></div> <a href="#" class="close"><img src="'.$closeimage.'" title="close" alt="X" class="close_image" /></a></div></div>\' '."\n";
		$jsVars .= 'faceboxHtml = \'<div id="facebox" style="display:none;"><div class="popup"><div class="content"></div> <a href="#" class="close"></a></div></div>\' '." ;\n";
		$jsVars .= '
//]]>
';
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($jsVars);
		vmJsApi::js( 'facebox');
		vmJsApi::js( 'vmprices');
		vmJsApi::css('facebox');

		$jPrice = TRUE;
		return TRUE;
	}

	// Virtuemart Site Js script
	static function jSite()
	{

		if (!VmConfig::get ('jsite', TRUE) and JFactory::getApplication ()->isSite ()) {
			return FALSE;
		}
		vmJsApi::js('vmsite');
	}

	static function JcountryStateList($stateIds) {
		static $JcountryStateList;
		// If exist exit
		if ($JcountryStateList) {
			return;
		}
		$document = JFactory::getDocument();
		VmJsApi::jSite();
		$document->addScriptDeclaration(' 
//<![CDATA[
		jQuery( function($) {
			$("select.virtuemart_country_id").vm2front("list",{dest : "#virtuemart_state_id",ids : "'.$stateIds.'"});
		});
//]]>
		');
		$JcountryStateList = TRUE;
		return;
	}


	static function JvalideForm($name='#adminForm')
	{
		static $jvalideForm;
		// If exist exit
		if ($jvalideForm === $name) {
			return;
		}
		$document = JFactory::getDocument();
		$document->addScriptDeclaration( "
//<![CDATA[
			jQuery(document).ready(function() {
				jQuery('".$name."').validationEngine();
			});
//]]>
"  );
		if ($jvalideForm) {
			return;
		}
		vmJsApi::js( 'jquery.validationEngine');

		$lg = JFactory::getLanguage();
		$lang = substr($lg->getTag(), 0, 2);
		/*$existingLang = array("cz", "da", "de", "en", "es", "fr", "it", "ja", "nl", "pl", "pt", "ro", "ru", "tr");
		if (!in_array ($lang, $existingLang)) {
			$lang = "en";
		}*/
		$vlePath = vmJsApi::setPath('languages/jquery.validationEngine-'.$lang, FALSE , '' ,$minified = NULL ,   'js', true);
		if(file_exists($vlePath) and !is_dir($vlePath)){
			vmJsApi::js( 'languages/jquery.validationEngine-'.$lang );
		} else {
			vmJsApi::js( 'languages/jquery.validationEngine-en' );
		}

		vmJsApi::css ( 'validationEngine.template' );
		vmJsApi::css ( 'validationEngine.jquery' );
		$jvalideForm = $name;
	}

	// Virtuemart product and price script
	static function jCreditCard()
	{

		static $jCreditCard;
		// If exist exit
		if ($jCreditCard) {
			return;
		}
		JFactory::getLanguage()->load('com_virtuemart');


		$js = "
//<![CDATA[
		var ccErrors = new Array ()
		ccErrors [0] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_UNKNOWN_TYPE') ). "';
		ccErrors [1] =  '" . addslashes( JText::_("COM_VIRTUEMART_CREDIT_CARD_NO_NUMBER") ). "';
		ccErrors [2] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_FORMAT')) . "';
		ccErrors [3] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_NUMBER')) . "';
		ccErrors [4] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_WRONG_DIGIT')) . "';
		ccErrors [5] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_EXPIRE_DATE')) . "';
//]]>
		";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		$jCreditCard = TRUE;
		return TRUE;
	}

	/**
	 * ADD some CSS if needed
	 * Prevent duplicate load of CSS stylesheet
	 * @ Author KOHL Patrick
	 */

	static function cssSite() {

		if (!VmConfig::get ('css', TRUE)) {
			return FALSE;
		}
		static $cssSite;
		if ($cssSite) {
			return;
		}
		// Get the Page direction for right to left support
		$document = JFactory::getDocument ();
		$direction = $document->getDirection ();
		$cssFile = 'vmsite-' . $direction ;

		// If exist exit
		vmJsApi::css ( $cssFile ) ;
		$cssSite = TRUE;
		return TRUE;
	}

	// $yearRange format >> 1980:2010
	// Virtuemart Datepicker script
	static function jDate($date='',$name="date",$id=NULL,$resetBt = TRUE, $yearRange='') {

		if ($yearRange) {
			$yearRange = 'yearRange: "' . $yearRange . '",';
		}
		if ($date == "0000-00-00 00:00:00") {
			$date = 0;
		}
		if (empty($id)) {
			$id = $name;
		}
		static $jDate;

		$dateFormat = JText::_('COM_VIRTUEMART_DATE_FORMAT_INPUT_J16');//="m/d/y"
		$search  = array('m', 'd');
		$replace = array('mm', 'dd');
		$jsDateFormat = str_replace($search, $replace, $dateFormat);

		if ($date) {
			if ( JVM_VERSION===1) {
				$search  = array('m', 'd', 'y');
				$replace = array('%m', '%d', '%y');
				$dateFormat = str_replace($search, $replace, $dateFormat);
			}
			$formatedDate = JHTML::_('date', $date, $dateFormat );
		}
		else {
			$formatedDate = JText::_('COM_VIRTUEMART_NEVER');
		}
		$display  = '<input class="datepicker-db" id="'.$id.'" type="hidden" name="'.$name.'" value="'.$date.'" />';
		$display .= '<input id="'.$id.'_text" class="datepicker" type="text" value="'.$formatedDate.'" />';
		if ($resetBt) {
			$display .= '<span class="vmicon vmicon-16-logout icon-nofloat js-date-reset"></span>';
		}

		// If exist exit
		if ($jDate) {
			return $display;
		}
		$front = 'components/com_virtuemart/assets/';

		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
//<![CDATA[
			jQuery(document).ready( function($) {
			$(".datepicker").live( "focus", function() {
				$( this ).datepicker({
					changeMonth: true,
					changeYear: true,
					'.$yearRange.'
					dateFormat:"'.$jsDateFormat.'",
					altField: $(this).prev(),
					altFormat: "yy-mm-dd"
				});
			});
			$(".js-date-reset").click(function() {
				$(this).prev("input").val("'.JText::_('COM_VIRTUEMART_NEVER').'").prev("input").val("0");
			});
		});
//]]>
		');
		vmJsApi::js ('jquery.ui.core',FALSE,'',TRUE);
		vmJsApi::js ('jquery.ui.datepicker',FALSE,'',TRUE);

		vmJsApi::css ('jquery.ui.all',$front.'css/ui' ) ;
		$lg = JFactory::getLanguage();
		$lang = $lg->getTag();

		$existingLang = array("af","ar","ar-DZ","az","bg","bs","ca","cs","da","de","el","en-AU","en-GB","en-NZ","eo","es","et","eu","fa","fi","fo","fr","fr-CH","gl","he","hr","hu","hy","id","is","it","ja","ko","kz","lt","lv","ml","ms","nl","no","pl","pt","pt-BR","rm","ro","ru","sk","sl","sq","sr","sr-SR","sv","ta","th","tj","tr","uk","vi","zh-CN","zh-HK","zh-TW");
		if (!in_array ($lang, $existingLang)) {
			$lang = substr ($lang, 0, 2);
		}
		elseif (!in_array ($lang, $existingLang)) {
			$lang = "en-GB";
		}
		vmJsApi::js ('jquery.ui.datepicker-'.$lang, $front.'js/i18n' ) ;
		$jDate = TRUE;
		return $display;
	}


	/*
	 * Convert formated date;
	 * @ $date the date to convert
	 * @ $format Joomla DATE_FORMAT Key endding eg. 'LC2' for DATE_FORMAT_LC2
	 * @ revert date format for database- TODO ?
	 */

	static function date($date , $format ='LC2', $joomla=FALSE ,$revert=FALSE ){

		if (!strcmp ($date, '0000-00-00 00:00:00')) {
			return JText::_ ('COM_VIRTUEMART_NEVER');
		}
		If ($joomla) {
			$formatedDate = JHTML::_('date', $date, JText::_('DATE_FORMAT_'.$format));
		} else {
			if (!JVM_VERSION === 1) {
				$J16 = "_J16";
			}
			else {
				$J16 = "";
			}
			$formatedDate = JHTML::_('date', $date, JText::_('COM_VIRTUEMART_DATE_FORMAT_'.$format.$J16));
		}
		return $formatedDate;
	}
}

// pure php no closing tag
