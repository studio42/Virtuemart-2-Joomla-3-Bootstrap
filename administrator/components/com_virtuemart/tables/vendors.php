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
	/** @var string Meta description */
	var $metadesc	= '';
    /** @var string Meta keys */
	var $metakey	= '';
 	/** @var string Meta robot */
	var $metarobot	= '';
	/** @var string Meta author */
	var $metaauthor	= '';
	var $customtitle ='';
    var $vendor_legal_info = '';
    /** @var text Vendor letter CSS */
    var $vendor_letter_css = '';
    /** @var text Vendor letter header */
    var $vendor_letter_header_html = '';
    /** @var text Vendor letter footer */
    var $vendor_letter_footer_html = '';

    /** @author RickG, Max Milbers
     * @param JDataBase $db
     */
    function __construct(&$db) {
		parent::__construct('#__virtuemart_vendors', 'virtuemart_vendor_id', $db);
		$this->setPrimaryKey('virtuemart_vendor_id');
		$this->setUniqueName('vendor_name');
		$this->setSlug('vendor_store_name'); //Attention the slug autoname MUST be also in the translatable, if existing
		$this->setLoggable();
		$this->setTranslatable(array('vendor_store_name','vendor_phone','vendor_store_desc','vendor_terms_of_service','vendor_legal_info','vendor_url','metadesc','metakey','customtitle','vendor_letter_css', 'vendor_letter_header_html', 'vendor_letter_footer_html'));

		$varsToPushParam = array(
		    				'vendor_min_pov'=>array(0.0,'float'),
		    				'vendor_min_poq'=>array(1,'int'),
		    				'vendor_freeshipment'=>array(0.0,'float'),
		    				'vendor_address_format'=>array('','string'),
		    				'vendor_date_format'=>array('','string'),

		    				'vendor_letter_format'=>array('A4','string'),
		    				'vendor_letter_orientation'=>array('P','string'),

		    				'vendor_letter_margin_top'=>array(45,'int'),
		    				'vendor_letter_margin_left'=>array(25,'int'),
		    				'vendor_letter_margin_right'=>array(25,'int'),
		    				'vendor_letter_margin_bottom'=>array(25,'int'),
		    				'vendor_letter_margin_header'=>array(12,'int'),
		    				'vendor_letter_margin_footer'=>array(20,'int'),

		    				'vendor_letter_font'=>array('helvetica','string'),
		    				'vendor_letter_font_size'=>array(8, 'int'),
		    				'vendor_letter_header_font_size'=>array(7, 'int'),
		    				'vendor_letter_footer_font_size'=>array(6, 'int'),
		    				
		    				'vendor_letter_header'=>array(1,'int'),
		    				'vendor_letter_header_line'=>array(1,'int'),
		    				'vendor_letter_header_line_color'=>array("#000000",'string'),
		    				'vendor_letter_header_image'=>array(1,'int'),
		    				'vendor_letter_header_imagesize'=>array(60,'int'),
		    				'vendor_letter_header_cell_height_ratio'=>array(1,'float'),

		    				'vendor_letter_footer'=>array(1,'int'),
		    				'vendor_letter_footer_line'=>array(1,'int'),
		    				'vendor_letter_footer_line_color'=>array("#000000",'string'),
		    				'vendor_letter_footer_cell_height_ratio'=>array(1,'float'),
		    				
		    				'vendor_letter_add_tos' => array(0,'int'),
		    				'vendor_letter_add_tos_newpage' => array(1,'int'),
		    			);

		$this->setParameterable('vendor_params',$varsToPushParam);

		$this->setTableShortCut('v');
// 		vmdebug('myvendor table',$this);
    }

}

//pure php no closing tag
