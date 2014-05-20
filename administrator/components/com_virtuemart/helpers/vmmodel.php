<?php
/**
 * abstract model class containing some standards
 *  get,store,delete,publish
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */

defined('_JEXEC') or die();

define('USE_SQL_CALC_FOUND_ROWS' , true);

// j3 FIX if(!class_exists('JModelLegacy ')) require JPATH_VM_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';

class VmModel extends JModelLegacy  {

	var $_id 			= 0;
	var $_data			= null;
	var $_query 		= null;
	var $_total			= null;
	var $_pagination 	= 0;
	var $_limit			= 0;
	var $_limitStart	= 0;
	var $_maintable 	= '';	// something like #__virtuemart_calcs
	var $_maintablename = '';
	var $_idName		= '';
	var $_cidName		= 'cid';
	var $_togglesName	= null;
	var $_selectedOrderingDir = 'DESC';
	private $_withCount = true;
	var $_noLimit = false;

	public function __construct($cidName=null, $config=array()){

		$this->_cname = strtolower(substr(get_class( $this ), 15));
		if ($cidName === null) $cidName = 'virtuemart_'.$this->_cname.'_id';
		$cidName = 'virtuemart_'.$this->_cname.'_id';
		$jinput = JFactory::getApplication()->input;
		$cid = $jinput->get('cid', null, 'array');
		if (isset($cid)) {
			$cidName = 'cid';
			$id = (int)$cid[0];
			$jinput->set($cidName, $id);
		} else {
			
			$id = $jinput->get($cidName, null, 'INT');
		}
		$this->_cidName = $cidName;

		parent::__construct($config);

		// Get the task
		$task = JRequest::getWord('task','');
		if($task!=='add'){
			$this->setId($id);
		}

		$this->setToggleName('published');
	}

	static private $_vmmodels = array();

	/**
	 *
	 * @author Patrick Kohl
	 * @author Max Milbers
	 */
	static function getModel($name=false){

		if (!$name){
			$name = JRequest::getCmd('view','');
// 			vmdebug('Get standard model of the view');
		}
		$name = strtolower($name);
		$className = 'VirtueMartModel'.ucfirst($name);


		if(empty(self::$_vmmodels[strtolower($className)])){
			if( !class_exists($className) ){

				$modelPath = JPATH_VM_ADMINISTRATOR.DS."models".DS.$name.".php";

				if( file_exists($modelPath) ){
					require( $modelPath );
				}
				else{
					JError::raiseWarning( 0, 'Model '. $name .' not found.' );
					echo 'File for Model '. $name .' not found.';
					return false;
				}
			}

			self::$_vmmodels[strtolower($className)] = new $className();
			return self::$_vmmodels[strtolower($className)];
		} else {
			return self::$_vmmodels[strtolower($className)];
		}

	}

	public function setIdName($idName){
		$this->_idName = $idName;
	}

	public function getIdName(){
		return $this->_idName;
	}

	public function getId(){
		return $this->_id;
	}

	/**
	 * Resets the id and data
	 *
	 * @author Max Milbers
	 */
	function setId($id){

		if(is_array($id) && count($id)!==0) $id = $id[0];
		if($this->_id!=$id){
			$this->_id = (int)$id;
			$this->_data = null;
		}
		return $this->_id;
	}


	public function setMainTable($maintablename,$maintable=0){

		$this->_maintablename = $maintablename;
		if(empty($maintable)){
			$this->_maintable = '#__virtuemart_'.$maintablename;
		} else {
			$this->_maintable = $maintable;
		}
		$defaultTable = $this->getTable($this->_maintablename);
		$this->_idName = $defaultTable->getKeyName();

		$this->setDefaultValidOrderingFields($defaultTable);
		$this->_selectedOrdering = $this->_validOrderingFieldName[0];

	}

	function getDefaultOrdering(){
		return $this->_selectedOrdering;
	}


	function addvalidOrderingFieldName($add){
		$this->_validOrderingFieldName = array_merge($this->_validOrderingFieldName,$add);
	}

	function removevalidOrderingFieldName($name){
		$key=array_search($name, $this->_validOrderingFieldName);
		if($key!==false){
		unset($this->_validOrderingFieldName[$key]) ;
	}
	}

	var $_tablePreFix = '';
	/**
	 *
	 * This function sets the valid ordering fields for this model with the default table attributes
	 * @author Max Milbers
	 * @param unknown_type $defaultTable
	 */
	function setDefaultValidOrderingFields($defaultTable=null){

		if($defaultTable===null){
			$defaultTable = $this->getTable($this->_maintablename);
		}

		$this->_tablePreFix = $defaultTable->_tablePreFix;
		$dTableArray = get_object_vars($defaultTable);

		// Iterate over the object variables to build the query fields and values.
		foreach ($dTableArray as $k => $v){

			// Ignore any internal fields.
			$posUnderLine = strpos ($k,'_');

			if (( $posUnderLine!==false && $posUnderLine === 0) ) {
				continue;
			}

// 			$this->_validOrderingFieldName[] = $this->_tablePreFix.$k;
			$this->_validOrderingFieldName[] = $k;

		}

	}


	function _getOrdering($preTable='') {
		if(empty($this->_selectedOrdering)) vmTrace('empty _getOrdering');
		if(empty($this->_selectedOrderingDir)) vmTrace('empty _selectedOrderingDir');
		return ' ORDER BY '.$preTable.$this->_selectedOrdering.' '.$this->_selectedOrderingDir ;
	}


	var $_validOrderingFieldName = array();

	function checkFilterOrder($toCheck){

		if(empty($toCheck)) return $this->_selectedOrdering;
		if(!in_array($toCheck, $this->_validOrderingFieldName)){

			$break = false;
			$toCheck = $this->_selectedOrdering;
			foreach($this->_validOrderingFieldName as $name){
				if(!empty($name) and strpos($name,$toCheck)!==FALSE){
					$this->_selectedOrdering = $name;
					$break = true;
					break;
				}
			}
			if(!$break){
			$app = JFactory::getApplication();
			$view = JRequest::getWord('view','virtuemart');
			$app->setUserState( 'com_virtuemart.'.$view.'.filter_order',$this->_selectedOrdering);
			}
		} else {
			$this->_selectedOrdering = $toCheck;
		}

		return $this->_selectedOrdering;
	}

	var $_validFilterDir = array('ASC','DESC');
	function checkFilterDir($toCheck){

		$filter_order_Dir = strtoupper($toCheck);

		if(empty($filter_order_Dir) or !in_array($filter_order_Dir, $this->_validFilterDir)){
// 			vmdebug('checkFilterDir: programmer choosed invalid ordering direction '.$filter_order_Dir,$this->_validFilterDir);
// 			vmTrace('checkFilterDir');
			$filter_order_Dir = $this->_selectedOrderingDir;
			$view = JRequest::getWord('view','virtuemart');
			$app = JFactory::getApplication();
			$app->setUserState( 'com_virtuemart.'.$view.'.filter_order_Dir',$filter_order_Dir);
		}
// 		vmdebug('checkFilterDir '.$filter_order_Dir);

		$this->_selectedOrderingDir = $filter_order_Dir;
		return $this->_selectedOrderingDir;
	}


	/**
	 * Loads the pagination
	 *
	 * @author Max Milbers, Patrick Kohl
	 */
	public function getPagination($perRow = 5) {

			$app = JFactory::getApplication();
			if(empty($this->_limit) ){
				$this->setPaginationLimits();
			}
			if ($app->isAdmin() || jRequest::getWord('tmpl') =="component") {
			JLoader::register('VmPagination', JPATH_VM_ADMINISTRATOR.'/helpers/vmpagination.php');
			} else JLoader::register('VmPagination', JPATH_VM_SITE.'/helpers/vmpagination.php');
			$this->_pagination = new VmPagination($this->_total , $this->_limitStart, $this->_limit , $perRow );
// 		}
// 		vmdebug('$this->pagination $total '.$this->_total,$this->_pagination);vmTrace('getPagination');
		return $this->_pagination;
	}

	public function setPaginationLimits(){

		$app = JFactory::getApplication();
		$view = JRequest::getWord('view',$this->_maintablename);

		$limit = (int)$app->getUserStateFromRequest('com_virtuemart.'.$view.'.limit', 'limit');
		if(empty($limit)){
			if($app->isSite()){
				$limit = VmConfig::get ('llimit_init_FE');
			} else {
				$limit = VmConfig::get ('llimit_init_BE');
			}
			if(empty($limit)){
				$limit = 30;
			}
		}

		$this->setState('limit', $limit);
		$this->setState('com_virtuemart.'.$view.'.limit',$limit);
		$this->_limit = $limit;

		$limitStart = $app->getUserStateFromRequest('com_virtuemart.'.$view.'.limitstart', 'limitstart',  JRequest::getInt('limitstart',0), 'int');

		//There is a strange error in the frontend giving back 9 instead of 10, or 24 instead of 25
		//This functions assures that the steps of limitstart fit with the limit
		$limitStart = ceil((float)$limitStart/(float)$limit) * $limit;
		$this->setState('limitstart', $limitStart);
		$this->setState('com_virtuemart.'.$view.'.limitstart',$limitStart);
		$this->_limitStart = $limitStart;

		return array($this->_limitStart,$this->_limit);
	}

	/**
	 * Gets the total number of entries
	 *TODO filters and search ar not set
	 * @author Max Milbers
	 * @return int Total number of entries in the database
	 */
	public function getTotal() {

		if (empty($this->_total)) {
			$query = 'SELECT '.$this->_db->quote($this->_idName).' FROM '.$this->_db->quoteName($this->_maintable);
			$this->_db->setQuery( $query );
			if(!$this->_db->execute()){
				if(empty($this->_maintable)) vmError('Model '.get_class( $this ).' has no maintable set');
				$this->_total = 0;
			} else {
				$this->_total = $this->_db->getNumRows();
			}
			//			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}


	public function setGetCount($withCount){

		$this->_withCount = $withCount;
	}

	/**
	 *
	 * exeSortSearchListQuery
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 * @param boolean $object use single result array = 2, assoc. array = 1 or object list = 0 as return value
	 * @param string $select the fields to select
	 * @param string $joinedTables the string of the joined tables or the table
	 * @param string $whereString for the where condition
	 * @param string $groupBy
	 * @param string $orderBy
	 * @param string $filter_order_Dir
	 */

	public function exeSortSearchListQuery($object, $select, $joinedTables, $whereString = '', $groupBy = '', $orderBy = '', $filter_order_Dir = '', $nbrReturnProducts = false){

		// 		vmSetStartTime('exe');
		// 		if(USE_SQL_CALC_FOUND_ROWS){

		//and the where conditions
		$joinedTables .= $whereString .$groupBy .$orderBy .$filter_order_Dir ;
		// 			$joinedTables .= $whereString .$groupBy .$orderBy;

		if($nbrReturnProducts){
			$limitStart = 0;
			$limit = $nbrReturnProducts;
			$this->_withCount = false;
		} else if($this->_noLimit){
			$this->_withCount = false;
			$limitStart = 0;
			$limit = 0;
		} else {
			$limits = $this->setPaginationLimits();
			$limitStart = $limits[0];
			$limit = $limits[1];
		}

		if($this->_withCount){
			$q = 'SELECT SQL_CALC_FOUND_ROWS '.$select.$joinedTables;
		} else {
			$q = 'SELECT '.$select.$joinedTables;
		}

		if($this->_noLimit or empty($limit)){
// 			vmdebug('exeSortSearchListQuery '.get_class($this).' no limit');
			$this->_db->setQuery($q);
		} else {
			$this->_db->setQuery($q,$limitStart,$limit);
// 			vmdebug('exeSortSearchListQuery '.get_class($this).' with limit');
		}
 	//	vmdebug('exeSortSearchListQuery '.$orderBy .$filter_order_Dir,$q);

		if($object == 2){
			 $this->ids = $this->_db->loadColumn();
		} else if($object == 1 ){
			 $this->ids = $this->_db->loadAssocList();
		} else {
			 $this->ids = $this->_db->loadObjectList();
		}
		if($err=$this->_db->getErrorMsg()){
			vmError('exeSortSearchListQuery '.$err);
		}
 		//vmdebug('my $limitStart '.$limitStart.'  $limit '.$limit.' q ',$this->_db->getQuery() );

		if($this->_withCount){

			$this->_db->setQuery('SELECT FOUND_ROWS()');
			$count = $this->_db->loadResult();

			if($count == false){
				$count = 0;
			}
			$this->_total = $count;
			if($limitStart>=$count){
				if(empty($limit)){
					$limit = 1.0;
				}
				$limitStart = floor($count/$limit);
				$this->_db->setQuery($q,$limitStart,$limit);
				if($object == 2){
					$this->ids = $this->_db->loadColumn();
				} else if($object == 1 ){
					$this->ids = $this->_db->loadAssocList();
				} else {
					$this->ids = $this->_db->loadObjectList();
				}
			}
// 			$this->getPagination(true);

		} else {
			$this->_withCount = true;
		}

		//print_r( $this->_db->_sql );
		// 			vmdebug('my $list',$list);
		if(empty($this->ids)){
			$errors = $this->_db->getErrorMsg();
			if( !empty( $errors)){
				vmdebug('exeSortSearchListQuery error in class '.get_class($this).' sql:',$this->_db->getErrorMsg());
			}
			if($object == 2 or $object == 1){
				$this->ids = array();
			}
		}
		// 			vmTime('exeSortSearchListQuery SQL_CALC_FOUND_ROWS','exe');
		return $this->ids;

	}

	/**
	 *
	 * @author Max Milbers
	 *
	 */

	public function getData(){

		if (empty($this->_data)) {
			$this->_data = $this->getTable($this->_maintablename);
			$this->_data->load($this->_id);

			//just an idea
			if(isset($this->_data->virtuemart_vendor_id) && empty($this->_data->virtuemart_vendor_id)){
				if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
				$this->_data->virtuemart_vendor_id = VirtueMartModelVendor::getLoggedVendor();
			}
		}

		return $this->_data;
	}
	
	/*
	 * check if it's own item
	 */
	public function checkOwn($id){
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$table 		= $this->getTable($this->_maintablename);
		$created_by = $table->checkOwn($id);
		if (isset($created_by) )
			return ($created_by == $userId);
		else return false;
	}
	/*
	 * check if it's own item from a table
	 */
	public function checkCanChangeOwn($table){
		static $user_id = null;
		static $vendor = null;
		if ($vendor === null) $vendor = Permissions::getInstance()->isSuperVendor();
		if ($vendor == 1) return true;
		if ($user_id === null) $user_id = JFactory::getUser()->get('id');
		if (isset($table->created_by) )
			return ($table->created_by && $user_id);
		else return null; // or false ???
	}

	public function store(&$data){

		$table = $this->getTable($this->_maintablename);

		$table->bindChecknStore($data);

		$errors = $table->getErrors();
		foreach($errors as $error){
			vmError( get_class( $this ).'::store '.$error);
		}

		if(is_object($data)){
			$_idName = $this->_idName;
			return $data->$_idName;
		} else {
			return $data[$this->_idName];
		}

	}

	/**
	 * Delete all record cids selected
	 *
	 * @author Patrick Kohl
	 * @return successfully removed cids or false otherwise.
	 */
	public function remove($cids,$vendor_id = 1) {
		JSession::checkToken() or jexit( 'Invalid Token, in remove '.$this->_cname);
		$ret = true ;
		$table = $this->getTable($this->_maintablename);
		foreach($cids as $key => $cid) {
				if (!$this->checkOwn($cid)) {
						vmError('no right to remove '.$cid,'no right to remove '.$this->_cname.' '.$cid);
						$ret = false;
						unset($cids[$key]);
				} else if (!$table->delete($cid)) {
				    vmError($table->getError());
				    unset($cids[$key]);
				}
		}
		if (empty($cids)) return false;

		return $cids;
	}

	public function setToggleName($togglesName){
		$this->_togglesName[] = $togglesName ;
	}
	/**
	 * toggle (0/1) a field
	 * or invert by $val for multi IDS;
	 * @author Patrick Kohl
	 * @param string $field the field to toggle
	 * @param string $postName the name of id Post  (Primary Key in table Class constructor)
	 */

	function toggle($field,$val = NULL, $cidname = 0,$tablename = 0  ) {
		$ok = true;

		if (!in_array($field, $this->_togglesName)) {
			return false ;
		}
		if($tablename === 0) $tablename = $this->_maintablename;
		if($cidname === 0) $cidname = $this->_cidName;

		$table = $this->getTable($tablename);
		//if(empty($cidName)) $cidName = $this->_cidName;
		$ids = JRequest::getVar( $cidname, JRequest::getVar('cid',array(0)), 'post', 'array' );

		foreach($ids as $id){
			$table->load( (int)$id );
			// vendor check
			if (!$this->checkCanChangeOwn($table)) continue;

			if (!$table->toggle($field, $val)) {
				//			if (!$table->store()) {
				vmError(get_class( $this ).'::toggle '.$table->getError() .' '.$id);
				$ok = false;
			}
		}

		return $ok;

	}
	/**
	 * Original From Joomla Method to move a weblink
	 * @ Author Kohl Patrick
	 * @$filter the field to group by
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function move($direction, $filter=null)
	{
		$table = $this->getTable($this->_maintablename);
		if (!$table->load($this->_id)) {
			vmError('VmModel move '.$this->_db->getErrorMsg());
			return false;
		}
		if (!$this->checkCanChangeOwn($table)) return false;
		if (!$table->move( $direction, $filter )) {
			vmError('VmModel move '.$this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	/**
	 * Original From Joomla Method to move a weblink
	 * @ Author Kohl Patrick
	 * @$filter the field to group by
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order, $filter = null)
	{
		$table = $this->getTable($this->_maintablename);
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$table->load( (int) $cid[$i] );
			if (!$this->checkCanChangeOwn($table) ) continue;
			// track categories
			if ($filter) $groupings[] = $table->$filter;

			if ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];
				if (!$table->store()) {
					vmError('VmModel saveorder '.$this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		if ($filter) {
			$groupings = array_unique( $groupings );
			foreach ($groupings as $group){
				$table->reorder(	$filter.' = '.(int) $group);
			}
		}

		return true;
	}


	/**
	 * Since an object like product, category dont need always an image, we can attach them to the object with this function
	 * The parameter takes a single product or arrays of products, look for BE/views/product/view.html.php
	 * for an exampel using it
	 *
	 * @author Max Milbers
	 * @param object $obj some object with a _medias xref table
	 */

	public function addImages($obj,$limit=0){

		$mediaModel = VmModel::getModel('Media');

		$mediaModel->attachImages($obj,$this->_maintablename,'image',$limit);

	}

	public function resetErrors(){

		$this->_errors = array();
	}

}
