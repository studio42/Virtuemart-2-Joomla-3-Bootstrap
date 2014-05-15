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
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 *
 * We need this extra paths to have always the correct path undependent by loaded application, module or plugin
 * Plugin, module developers must always include this config at start of their application
 *   $vmConfig = VmConfig::loadConfig(); // load the config and create an instance
 *  $vmConfig -> jQuery(); // for use of jQuery
 *  Then always use the defined paths below to ensure future stability
 */
 if (!defined('JPATH_VM_ADMINISTRATOR')) {
	define( 'JPATH_VM_SITE', JPATH_ROOT.DS.'components'.DS.'com_virtuemart' );
	define('JPATH_VM_ADMINISTRATOR', JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart');
	define( 'JPATH_VM_PLUGINS', JPATH_VM_ADMINISTRATOR.DS.'plugins' );
	define ('JPATH_VM_LIBRARIES', JPATH_LIBRARIES);

	// Set path to bootstrap,jquery ...
	defined ('J3UI') or define ('J3UI', 'administrator/components/com_virtuemart/assets/jui/');

	// overide jquery ui core and jquery
	JLoader::register('JHtmlJquery', JPATH_VM_ADMINISTRATOR.'/html/jquery.php');
	if (version_compare (JVERSION, '3.0.0', 'ge')) {
		define ('JVM_VERSION', 3);
	}
	else {
		define ('JVM_VERSION', 2);
		// load missing bootstrap +css library ... in joomla 2.5

		JLoader::register('JHtmlJquery', JPATH_VM_ADMINISTRATOR.'/html/jquery.php');
		JLoader::register('JHtmlBootstrap', JPATH_VM_ADMINISTRATOR.'/html/bootstrap.php');
		JLoader::register('JHtmlFormbehavior', JPATH_VM_ADMINISTRATOR.'/html/formbehavior.php');
		JLoader::register('JHtmlIcons', JPATH_VM_ADMINISTRATOR.'/html/icons.php');
		JLoader::register('JHtmlSidebar', JPATH_VM_ADMINISTRATOR.'/html/sidebar.php');
		JLoader::register('JHtmlSortablelist', JPATH_VM_ADMINISTRATOR.'/html/sortablelist.php');
	}

	//This number is for obstruction, similar to the prefix jos_ of joomla it should be avoided
	//to use the standard 7, choose something else between 1 and 99, it is added to the ordernumber as counter
	// and must not be lowered.
	defined('VM_ORDER_OFFSET') or define('VM_ORDER_OFFSET',3);


	require(JPATH_VM_ADMINISTRATOR.DS.'version.php');

	JTable::addIncludePath(JPATH_VM_ADMINISTRATOR.DS.'tables');
	JLoader::register('VmModel', JPATH_VM_ADMINISTRATOR.'/helpers/vmmodel.php');
	JLoader::register('vmJsApi', JPATH_VM_ADMINISTRATOR.'/helpers/vmjsapi.php');
}
/**
 * This function shows an info message, the messages gets translated with JText::,
 * you can overload the function, so that automatically sprintf is taken, when needed.
 * So this works vmInfo('COM_VIRTUEMART_MEDIA_NO_PATH_TYPE',$type,$link )
 * and also vmInfo('COM_VIRTUEMART_MEDIA_NO_PATH_TYPE');
 *
 * @author Max Milbers
 * @param string $publicdescr
 * @param string $value
 */

function vmInfo($publicdescr,$value=NULL){

	$app = JFactory::getApplication();

	$msg = '';
	$type = 'message';
	if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
		$lang = JFactory::getLanguage();
		if($value!==NULL){

			$args = func_get_args();
			if (count($args) > 0) {
				$args[0] = $lang->_($args[0]);
				$msg = call_user_func_array('sprintf', $args);
			}
		}	else {
			// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
			//$publicdescr = $lang->_($publicdescr);
			$msg = JText::_($publicdescr);
			// 		debug_print_backtrace();
		}
	}
	else {
		if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
			$msg = 'Max messages reached';
			$type = 'warning';
		} else {
			return false;
		}
	}

	if(!empty($msg)){
		VmConfig::$maxMessageCount++;
		$app ->enqueueMessage($msg,$type);
	} else {
		vmTrace('vmInfo Message empty '.$msg);
	}

	return $msg;
}

/**
 * Informations for the vendors or the administrators of the store, but not for developers like vmdebug
 * @param      $publicdescr
 * @param null $value
 */
function vmAdminInfo($publicdescr,$value=NULL){

	JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/Permissions.php');
	if(Permissions::getInstance()->isSuperVendor()){

		$app = JFactory::getApplication();

		if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
			$lang = JFactory::getLanguage();
			if($value!==NULL){

				$args = func_get_args();
				if (count($args) > 0) {
					$args[0] = $lang->_($args[0]);
					VmConfig::$maxMessageCount++;
					$app ->enqueueMessage(call_user_func_array('sprintf', $args),'message');
				}
			}	else {
				VmConfig::$maxMessageCount++;
				// 		$app ->enqueueMessage('message: '.JText::_($publicdescr));
				$publicdescr = $lang->_($publicdescr);
				$app ->enqueueMessage('Info: '.JText::_($publicdescr),'message');
				// 		debug_print_backtrace();
			}
		}
		else {
			if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
				$app->enqueueMessage ('Max messages reached', 'message');
			}else {
				return false;
			}
		}
	}

}

function vmWarn($publicdescr,$value=NULL){


	$app = JFactory::getApplication();
	$msg = '';
	if(VmConfig::$maxMessageCount<VmConfig::$maxMessage){
		$lang = JFactory::getLanguage();
		if($value!==NULL){

			$args = func_get_args();
			if ($args) {
				$args[0] = $lang->_($args[0]);
				$msg = call_user_func_array('sprintf', $args);

			}
		}	else {
			// 		$app ->enqueueMessage('Info: '.JText::_($publicdescr));
			$msg = $lang->_($publicdescr);
			//$app ->enqueueMessage('Info: '.$publicdescr,'warning');
			// 		debug_print_backtrace();
		}
	}
	else {
		if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
			$msg = 'Max messages reached';
		} else {
			return false;
		}
	}

	if(!empty($msg)){
		VmConfig::$maxMessageCount++;
		$app ->enqueueMessage($msg,'warning');
		return $msg;
	} else {
		vmTrace('vmWarn Message empty');
		return false;
	}

}

/**
 * Shows an error message, sensible information should be only in the first one, the second one is for non BE users
 * @author Max Milbers
 */
function vmError($descr,$publicdescr=''){

	$msg = '';
	if(VmConfig::$maxMessageCount< (VmConfig::$maxMessage+5)){
		if (empty($descr)) {
			vmTrace ('vmError message empty');
		}
		$lang = JFactory::getLanguage();
		JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/Permissions.php');
		if(Permissions::getInstance()->check('admin')){
			//$app = JFactory::getApplication();
			$descr = $lang->_($descr);
			$msg = 'vmError: '.$descr;
		} else {
			if(!empty($publicdescr)){
				$msg = $lang->_($publicdescr);
			}
		}
	}
	else {
		if (VmConfig::$maxMessageCount == (VmConfig::$maxMessage+5)) {
			$msg = 'Max messages reached';
		} else {
			return false;
		}
	}

	if(!empty($msg)){
		VmConfig::$maxMessageCount++;
		$app = JFactory::getApplication();
		$app ->enqueueMessage($msg,'error');
		return $msg;
	}

	return $msg;

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
				VmConfig::$maxMessageCount++;
				$app = JFactory::getApplication();
				$app ->enqueueMessage('<span class="vmdebug" >vmdebug '.$debugdescr.'</span>');
			} else {
				VmConfig::$maxMessageCount++;
				echo $debugdescr;
			}

		}
		else {
			if (VmConfig::$maxMessageCount == VmConfig::$maxMessage) {
				$app->enqueueMessage ('Max messages reached', 'notice');
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

			// 1 show debug only to admins
			if($debug === 'admin' ){
				JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/Permissions.php');
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
	 * Note Patrick Kohl STUDIO42
	 * added prefix from joomla in like to prevent getting false config for multiple use of joomla in same database
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
		$prefix = $db->getPrefix();
		$query = 'SHOW TABLES LIKE "'.$prefix.'virtuemart_configs"';
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
			self::$_jpConfig->_raw = json_decode( $db->loadResult(), TRUE );
			if(empty(self::$_jpConfig->_raw)){
				if(self::installVMconfig()){
					$install = 'yes';
					$db->setQuery($query);
					self::$_jpConfig->_raw = json_decode( $db->loadResult(), TRUE );
					self::$_jpConfig->_params = &self::$_jpConfig->_raw;
				} else {
					$app ->enqueueMessage('Error loading configuration file','Error loading configuration file, please contact the storeowner');
				}
			}
		}

		$i = 0;

		$pair = array();
		if (!empty(self::$_jpConfig->_raw)) {

// 			$pair['sctime'] = microtime(true);
			self::$_jpConfig->_params = &self::$_jpConfig->_raw;
			
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

				// TODO test wiht j1.7
				jimport('joomla.language.helper');
				$languages = JLanguageHelper::getLanguages('lang_code');
				$siteLang = JFactory::getLanguage()->getTag();

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

		JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/Permissions.php');
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

		JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/Permissions.php');

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
	 * Writes the params as string and escape them before
	 * @author Max Milbers
	 */
	function toJson(){
		return json_encode(self::$_jpConfig->_raw);
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

		if (!$_value = self::readConfigFile(FALSE) ) return FALSE;

		$qry = self::$_jpConfig->getCreateConfigTableQuery();
		$_db = JFactory::getDBO();
		$_db->setQuery($qry);
		$_db->execute();

		$query = 'SELECT `virtuemart_config_id` FROM `#__virtuemart_configs`
						 WHERE `virtuemart_config_id` = 1';
		$_db->setQuery( $query );
		if ($_db->execute()){
			$qry = 'DELETE FROM `#__virtuemart_configs` WHERE `virtuemart_config_id`=1';
			$_db->setQuery($qry);
			$_db->execute();
		}


		$_value = json_encode( $_value );
		$qry = 'INSERT INTO `#__virtuemart_configs` (`virtuemart_config_id`, `config`) VALUES ( 1, "'.$_db->escape($_value).'")';

		self::$_jpConfig->raw = $_value;

		$_db->setQuery($qry);
		if (!$_db->execute()) {
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
		if(!$config = parse_ini_file($_datafile) ) return false;
		return $config ;

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


// pure php no closing tag
