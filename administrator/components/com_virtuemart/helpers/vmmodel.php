<?php
/**
 * abstract model class containing some standards
 *  get,store,delete,publish and pagination
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

if(!class_exists('JModel')) require JPATH_VM_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';

class VmModel extends JModel {

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

	public function __construct($cidName='cid', $config=array()){
		parent::__construct($config);

		$this->_cidName = $cidName;

		// Get the task
		$task = JRequest::getWord('task','');
		if($task!=='add'){
			// Get the id or array of ids.
			$idArray = JRequest::getVar($this->_cidName,  0, '', 'array');
			if(empty($idArray[0])) $idArray[0] = 0;
			$this->setId((int)$idArray[0]);
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
			//			$idName = $this->_idName;
			//			$this->$idName = $this->_id;
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
		unset($this->_validOrderingFieldName[$key]) ;
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

		if(!in_array($toCheck, $this->_validOrderingFieldName)){

			vmdebug('checkValidOrderingField:'.get_class($this).' programmer choosed invalid ordering '.$toCheck.', use '.$this->_selectedOrdering);
			$toCheck = $this->_selectedOrdering;
			$app = JFactory::getApplication();
			$view = JRequest::getWord('view','virtuemart');
			$app->setUserState( 'com_virtuemart.'.$view.'.filter_order',$this->_selectedOrdering);
		}
		$this->_selectedOrdering = $toCheck;
		return $toCheck;
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
	 * @author Max Milbers
	 */
	public function getPagination($perRow = 5) {

			if(empty($this->_limit) ){
				$this->setPaginationLimits();
			}

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
			$limit = VmConfig::get ('list_limit', 20);
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
			$query = 'SELECT `'.$this->_db->getEscaped($this->_idName).'` FROM `'.$this->_db->getEscaped($this->_maintable).'`';;
			$this->_db->setQuery( $query );
			if(!$this->_db->query()){
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
			 $this->ids = $this->_db->loadResultArray();
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
					$this->ids = $this->_db->loadResultArray();
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
	 * Delete all record ids selected
	 *
	 * @author Max Milbers
	 * @return boolean True is the delete was successful, false otherwise.
	 */
	public function remove($ids) {

		$table = $this->getTable($this->_maintablename);
		foreach($ids as $id) {
			if (!$table->delete((int)$id)) {
				vmError(get_class( $this ).'::remove '.$id.' '.$table->getError());
				return false;
			}
		}

		return true;
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
		vmdebug('toggle $cidname: '.$cidname,$ids);
		foreach($ids as $id){
			$table->load( (int)$id );

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

jimport('joomla.html.pagination');

class VmPagination extends JPagination {

	private $_perRow = 5;

	function __construct($total, $limitstart, $limit, $perRow=5){
		if($perRow!==0){
			$this->_perRow = $perRow;
		}
		parent::__construct($total, $limitstart, $limit);
	}

	/** Creates a dropdown box for selecting how many records to show per page.
	 * Modification of Joomla Core libraries/html/pagination.php getLimitBox function
	 * The function uses as sequence a generic function or a sequence configured in the vmconfig
	 *
	 * use in a view.html.php $vmModel->setPerRow($perRow); to activate it
	 *
	 * @author Joe Motacek (Cleanshooter)
	 * @author Max Milbers
	 * @return  string   The HTML for the limit # input box.
	 * @since   11.1
	 */

	function getLimitBox()
	{
		$app = JFactory::getApplication();

		// Initialize variables
		$limits = array ();

		// Make the option list
		//for 3 = 3,6,12,24,60,90 rows, 4 rows, 6 rows
		$sequence = VmConfig::get('pagination_sequence',0);

		$selected = $this->_viewall ? 0 : $this->limit;
		// Build the select list
		if ($app->isAdmin()) {
// 			$limits[] = JHTML::_('select.option', '0', JText::_('COM_VIRTUEMART_ALL'));
			if(!empty($sequence)){
				$sequenceArray = explode(',', $sequence);
				foreach($sequenceArray as $items){
					$limits[]=JHtml::_('select.option', $items);
				}

			} else {
				if($this->_perRow===1) $this->_perRow = 5;
				$iterationAmount = 4;
				for ($i = 1; $i <= $iterationAmount; $i ++) {
					$limits[] = JHtml::_('select.option', $i*$this->_perRow);
				}

				$limits[] = JHTML::_('select.option', $this->_perRow * 10);
				$limits[] = JHTML::_('select.option', $this->_perRow * 20);
				$limits[] = JHTML::_('select.option', $this->_perRow * 40);
				$limits[] = JHTML::_('select.option', $this->_perRow * 80);
	// 			vmdebug('getLimitBox',$this->_perRow);
			}

			$namespace = '';
			if (JVM_VERSION!==1) {
				$namespace = 'Joomla.';
			}

			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="'.$namespace.'submitform();"', 'value', 'text', $selected);
		} else {

			$getArray = (JRequest::get( 'get' ));
			$link ='';
			unset ($getArray['limit']);

			// foreach ($getArray as $key => $value ) $link .= '&'.$key.'='.$value;
			foreach ($getArray as $key => $value ){
				if (is_array($value)){
					foreach ($value as $k => $v ){
						$link .= '&'.$key.'['.$k.']'.'='.$v;
					}
				} else {
					$link .= '&'.$key.'='.$value;
				}
			}
			$link[0] = "?";
			$link = 'index.php'.$link ;
			// $limits[] = JHTML::_('select.option',JRoute::_( $link.'&limit=0'), JText::_('all'));

			if(!empty($sequence)){
				$sequenceArray = explode(',', $sequence);
				foreach($sequenceArray as $items){
					$limits[]=JHtml::_('select.option', JRoute::_( $link.'&limit='. $items, false), $items);
				}

			} else {
				if($this->_perRow===1) $this->_perRow = 5;
				$iterationAmount = 4;
				for ($i = 1; $i <= $iterationAmount; $i ++) {
					$limits[] = JHtml::_('select.option',JRoute::_( $link.'&limit='. $i*$this->_perRow, false) ,$i*$this->_perRow );
				}

				$limits[] = JHTML::_('select.option',JRoute::_( $link.'&limit='. $this->_perRow * 10, false) , $this->_perRow * 10 );
				$limits[] = JHTML::_('select.option',JRoute::_( $link.'&limit='. $this->_perRow * 20, false) , $this->_perRow * 20 );
	// 			vmdebug('getLimitBox',$this->_perRow);
			}
			$selected= JRoute::_( $link.'&limit='. $selected) ;
			$js = 'onchange="window.top.location.href=this.options[this.selectedIndex].value"';

			$html = JHTML::_('select.genericlist',  $limits, '', 'class="inputbox" size="1" '.$js , 'value', 'text', $selected);
		}
		return $html;
	}


}
