<?php
/**
*
* Vendor Table
*
* @package	VirtueMart
* @subpackage Vendor
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: vendors.php 5314 2012-01-24 15:23:17Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTableData'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtabledata.php');

/**
 * Vendor table class
 * The class is is used to manage the vendors in the shop.
 *
 * @package		VirtueMart
 * @author RickG
 * @author Max Milbers
 */
class TableVendors extends VmTableData {

    /** @var int Primary key */
    var $virtuemart_vendor_id			= 0;
    /** @var varchar Vendor name*/
    var $vendor_name  	         	= '';
    /** @var varchar Vendor phone number */
    var $vendor_phone         		= '';
    /** @var varchar Vendor store name */
    var $vendor_store_name		= '';
    /** @var text Vendor store description */
    var $vendor_store_desc   		= '';

    /** @var varchar Currency */
    var $vendor_currency	  		= 0;
    /** @var varchar Path to vendor images */
//     var $vendor_image_path   		= '';
    /** @var text Vendor terms of service */
    var $vendor_terms_of_service	= '';
    /** @var varchar Vendor url */
    var $vendor_url					= '';
    /** @var text Currencies accepted by this vendor */
    var $vendor_accepted_currencies = array();

    var $vendor_params = '';

    var $vendor_legal_info = '';

    /* @author RickG, Max Milbers
     * @param $db A database connector object
     */
    function __construct(&$db) {
		parent::__construct('#__virtuemart_vendors', 'virtuemart_vendor_id', $db);
		$this->setPrimaryKey('virtuemart_vendor_id');
		$this->setUniqueName('vendor_name');
		$this->setSlug('vendor_store_name'); //Attention the slug autoname MUST be also in the translatable, if existing
		$this->setLoggable();
		$this->setTranslatable(array('vendor_store_name','vendor_phone','vendor_store_desc','vendor_terms_of_service','vendor_legal_info','vendor_url'));

		$varsToPushParam = array(
		    				'vendor_min_pov'=>array(0.0,'float'),
		    				'vendor_min_poq'=>array(1,'int'),
		    				'vendor_freeshipment'=>array(0.0,'float'),
		    				'vendor_address_format'=>array('','string'),
		    				'vendor_date_format'=>array('','string'));

		$this->setParameterable('vendor_params',$varsToPushParam);

		$this->setTableShortCut('v');
// 		vmdebug('myvendor table',$this);
    }

}

//pure php no closing tag
