<?php

/**
*
* Data module for shop extensions
*
* @package	VirtueMart
* @subpackage Extensions
* @author StephanieS
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: usergroups.php 6350 2012-08-14 17:18:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');
if(!class_exists('TableUsergroups'))require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'usergroups.php');

/**
 * Model class for shop Currencies
 *
 * @package	VirtueMart
 * @subpackage Extensions
 * @author StephanieS, Max Milbers
 */

class VirtueMartModelUsergroups extends VmModel {


	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('usergroups');
	}

    function getUsergroup() {

		$db = JFactory::getDBO();

		if (empty($this->_data)) {
		    $this->_data = $this->getTable('usergroups');
		    $this->_data->load((int)$this->_id);
		}

		return $this->_data;
    }


    function getUsergroups($onlyPublished=false, $noLimit=false) {

    	$where = array();
    	if ($onlyPublished) {
    		$where[] = ' `#__virtuemart_shoppergroups`.`published` = 1';
    	}

    	$whereString = '';
    	if (count($where) > 0) $whereString = ' WHERE '.implode(' AND ', $where) ;

    	return $this->_data = $this->exeSortSearchListQuery(0,'*',' FROM `#__virtuemart_permgroups`',$whereString,'',$this->_getOrdering());

    }

}
