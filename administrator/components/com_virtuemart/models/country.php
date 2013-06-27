<?php
/**
*
* Data module for shop countries
*
* @package	VirtueMart
* @subpackage Country
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: country.php 6396 2012-09-05 17:35:36Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model class for shop countries
 *
 * @package	VirtueMart
 * @subpackage Country
 * @author RickG
 */
class VirtueMartModelCountry extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('countries');
		array_unshift($this->_validOrderingFieldName,'country_name');
		$this->_selectedOrdering = 'country_name';
		$this->_selectedOrderingDir = 'ASC';

	}

    /**
     * Retreive a country record given a country code.
     *
     * @author RickG
     * @param string $code Country code to lookup
     * @return object Country object from database
     */
    function getCountryByCode($code) {
	$db = JFactory::getDBO();

	$countryCodeLength = strlen($code);
	switch ($countryCodeLength) {
	    case 2:
		$countryCodeFieldname = 'country_2_code';
		break;
	    case 3:
		$countryCodeFieldname = 'country_3_code';
		break;
	    default:
		return false;
	}

	$query = 'SELECT *';
	$query .= ' FROM `#__virtuemart_countries`';
	$query .= ' WHERE `' . $countryCodeFieldname . '` = ' . (int)$code;
	$db->setQuery($query);

	return $db->loadObject();
    }

    /**
     * Retrieve a list of countries from the database.
     *
     * @author RickG
     * @author Max Milbers
     * @param string $onlyPublished True to only retrieve the publish countries, false otherwise
     * @param string $noLimit True if no record count limit is used, false otherwise
     * @return object List of country objects
     */
    function getCountries($onlyPublished=true, $noLimit=false, $filterCountry = false) {

		$where = array();
		$this->_noLimit = $noLimit;
// 		$query = 'SELECT * FROM `#__virtuemart_countries` ';
		/* add filters */
		if ($onlyPublished) $where[] = '`published` = 1';

		if($filterCountry){
			$filterCountry = '"%' . $this->_db->getEscaped( $filterCountry, true ) . '%"' ;
			//$keyword = $this->_db->Quote($filterCountry, false);
			$where[] = '`country_name` LIKE '.$filterCountry.' OR `country_2_code` LIKE '.$filterCountry.' OR `country_3_code` LIKE '.$filterCountry;
		}

		$whereString = '';
		if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

		$ordering = $this->_getOrdering();

		return $this->_data = $this->exeSortSearchListQuery(0,'*',' FROM `#__virtuemart_countries`',$whereString,'',$ordering);


    }


}

//no closing tag pure php