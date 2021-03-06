<?php
/**
*
* Data module for shop coupons
*
* @package	VirtueMart
* @subpackage Coupon
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id$
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

JLoader::register('VmModel', JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model class for shop coupons
 *
 * @package	VirtueMart
 * @subpackage Coupon
 * @author RickG
 */
class VirtueMartModelCoupon extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('coupons');
		$this->_selectedOrdering = 'coupon_code';
		$this->_selectedOrderingDir = 'DESC';
		$this->addvalidOrderingFieldName(
			array('coupon_code', 'percent_or_total', 'coupon_type', 'coupon_value', 'coupon_start_date', 'coupon_expiry_date', 'coupon_value_valid', 'published'));

	}

	/**
	 * Bind the post data to the coupon table and save it
     *
     * @author RickG, Oscar van Eijk
     * @return mixed False if the save was unsuccessful, the coupon ID otherwise.
	 */
    function store(&$data)
	{
		// Convert selected dates to MySQL format for storing.
		if ($data['coupon_start_date']) {
		    $startDate = JFactory::getDate($data['coupon_start_date']);
		    $data['coupon_start_date'] = $startDate->toSql();
		}
		if ($data['coupon_expiry_date']) {
		    $expireDate = JFactory::getDate($data['coupon_expiry_date']);
		    $data['coupon_expiry_date'] = $expireDate->toSql();
		}
		return parent::store($data);
	}


	/**
	 * Retireve a list of coupons from the database.
	 *
     * @author RickG
	 * @return object List of coupon objects
	 */
	function getCoupons($search='') {

		$where = array();
		$whereString = '';
// 		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;
		
		if($search){
			$search = '"%' . $this->_db->escape( $search, true ) . '%"' ;
			//$keyword = $this->_db->Quote($filterCountry, false);
			$where[] = ' WHERE `coupon_code` LIKE '.$search;
		}
		$published = JRequest::getVar('filter_published', false);
		if ($published !== false) {
			if ($published === '1') {
				$where[] = " `published` = 1 ";
			} else if ($published === '0') {
				$where[] = " `published` = 0 ";
			}
		}
		$whereString= '';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;
		return $this->_data = $this->exeSortSearchListQuery(0,'*',' FROM `#__virtuemart_coupons`',$whereString,'',$this->_getOrdering());

	}
}

// pure php no closing tag