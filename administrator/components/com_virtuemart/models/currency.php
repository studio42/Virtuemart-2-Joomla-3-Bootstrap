<?php
/**
 *
 * Data module for shop currencies
 *
 * @package	VirtueMart
 * @subpackage Currency
 * @author RickG
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: currency.php 6350 2012-08-14 17:18:08Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model class for shop Currencies
 *
 * @package	VirtueMart
 * @subpackage Currency
 * @author RickG
 */
class VirtueMartModelCurrency extends VmModel {


	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('currencies');
	}

	/**
	 * Retrieve the detail record for the current $id if the data has not already been loaded.
	 *
	 * @author Max Milbers
	 */
	function getCurrency($currency_id=0) {
		if(!empty($currency_id)) $this->setId((int)$currency_id);
		if (empty($this->_data)   ) {
			$this->_data = $this->getTable('currencies');
			$this->_data->load((int)$this->_id);
		}

		return $this->_data;
	}


	/**
	 * Retireve a list of currencies from the database.
	 * This function is used in the backend for the currency listing, therefore no asking if enabled or not
	 * @author Max Milbers
	 * @return object List of currency objects
	 */
	function getCurrenciesList($search,$vendorId=1) {

		$where = array();
		// 		$this->_query = 'SELECT * FROM `#__virtuemart_currencies` ';

		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		if( !Permissions::getInstance()->check('admin') ){
			$where[]  = '(`virtuemart_vendor_id` = "'.(int)$vendorId.'" OR `shared`="1")';
		}

		if(empty($search)){
			$search = JRequest::getString('search', false);
		}
		/* add filters */
		if($search){
			$search = '"%' . $this->_db->getEscaped( $search, true ) . '%"' ;
			//$search = $this->_db->Quote($search, false);
			$where[] = '`currency_name` LIKE '.$search.' OR `currency_code_2` LIKE '.$search.' OR `currency_code_3` LIKE '.$search;
		}

		// 		if (JRequest::getString('search', false)) $where[] = '`currency_name` LIKE "%'.$this->_db->getEscaped(JRequest::getString('search')).'%"';

		$whereString='';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

		// 		if (count($where) > 0) $this->_query .= ' WHERE '.implode(' AND ', $where) ;
		// 		$this->_query .= $this->_getOrdering('currency_name');
		// 		$this->_data = $this->_getList($this->_query, $this->getState('limitstart'), $this->getState('limit'));
		// 		$this->_total = $this->_getListCount($this->_query) ;

		// 		$object, $select, $joinedTables, $whereString = '', $groupBy = '', $orderBy = '', $filter_order_Dir = '', $nbrReturnProducts = false
		$this->_data = $this->exeSortSearchListQuery(0,'*',' FROM `#__virtuemart_currencies`',$whereString,'',$this->_getOrdering());

		return $this->_data;
		// 		return $this->_data;
	}

	/**
	 * Retireve a list of currencies from the database.
	 *
	 * This is written to get a list for selecting currencies. Therefore it asks for enabled
	 * @author RolandD, Max Milbers
	 * @return object List of currency objects
	 */
	function getCurrencies($vendorId=1) {
		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `#__virtuemart_currencies` WHERE (`virtuemart_vendor_id` = "'.(int)$vendorId.'" OR `shared`="1") AND published = "1" ORDER BY `#__virtuemart_currencies`.`currency_name`';
		$db->setQuery($q);
		return $db->loadObjectList();
	}

}
// pure php no closing tag