<?php
/**
*
* Product table
*
* @package	VirtueMart
* @subpackage Product
* @author RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: products.php 6306 2012-08-06 14:19:51Z Milbo $
*/

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Product table class
 * The class is is used to manage the products in the shop.
 *
 * @package	VirtueMart
 * @author RolandD
 * @author Max Milbers
 */
class TableProducts extends VmTable {

	/** @var int Primary key */
	var $virtuemart_product_id	 = 0;
	/** @var integer Product id */
	var $virtuemart_vendor_id = 0;
	/** @var string File name */
	var $product_parent_id		= 0;
	/** @var string File title */
	var $product_sku= '';
    /** @var string Name of the product */
	var $product_name	= '';
	var $slug			= '';
    /** @var string File description */
	var $product_s_desc		= '';
    /** @var string File extension */
	var $product_desc			= '';
	/** @var int File is an image or other */
	var $product_weight			= 0;
	/** @var int File image height */
	var $product_weight_uom		= '';
	/** @var int File image width */
	var $product_length		= 0;
	/** @var int File thumbnail image height */
	var $product_width = 0;
	/** @var int File thumbnail image width */
	var $product_height	= 0;
	/** @var int File thumbnail image width */
	var $product_lwh_uom	= '';
	/** @var int File thumbnail image width */
	var $product_url	= '';
	/** @var int File thumbnail image width */
	var $product_in_stock	= 0;
	var $product_ordered		= 0;
	/** @var int File thumbnail image width */
	var $low_stock_notification	= 0;
	/** @var int File thumbnail image width */
	var $product_available_date	= null;
	/** @var int File thumbnail image width */
	var $product_availability	= null;
	/** @var int File thumbnail image width */
	var $product_special	= null;

	/** @var int product internal ordering, it is for the ordering for child products under a parent null */
	var $pordering = null;
	/** @var int File thumbnail image width */
	var $product_sales	= 0;

	/** @var int File thumbnail image width */
	var $product_unit	= null;
	/** @var int File thumbnail image width */
	var $product_packaging	= null;
	/** @var int File thumbnail image width */
	var $product_params	= null;
	/** @var string Internal note for product */
	var $intnotes = '';
	/** @var string custom title */
	var $customtitle	= '';
	/** @var string Meta description */
	var $metadesc	= '';
	/** @var string Meta keys */
	var $metakey	= '';
	/** @var string Meta robot */
	var $metarobot	= '';
	/** @var string Meta author */
	var $metaauthor	= '';
	/** @var string Name of the details page to use for showing product details in the front end */
	var $layout = '';
       /** @var int published or unpublished */
	var $published 		        = 1;



	/**
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct($db) {
		parent::__construct('#__virtuemart_products', 'virtuemart_product_id', $db);

		//In a VmTable the primary key is the same as the _tbl_key and therefore not needed
// 		$this->setPrimaryKey('virtuemart_product_id');
		$this->setObligatoryKeys('product_name');
		$this->setLoggable();
		$this->setTranslatable(array('product_name','product_s_desc','product_desc','metadesc','metakey','customtitle'));
		$this->setSlug('product_name');
		$this->setTableShortCut('p');

		//We could put into the params also the product_availability and the low_stock_notification
		$varsToPushParam = array(
				    				'min_order_level'=>array(null,'float'),
				    				'max_order_level'=>array(null,'float'),
				    				'step_order_level'=>array(null,'float'),
									//'product_packaging'=>array(null,'float'),
									'product_box'=>array(null,'float')
									);

		$this->setParameterable('product_params',$varsToPushParam);

	}

}
// pure php no closing tag
