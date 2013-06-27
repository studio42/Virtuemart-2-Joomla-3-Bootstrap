<?php
/**
*
* Coupon table
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
* @version $Id: coupons.php 4455 2011-10-18 20:42:02Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Coupon table class
 * The class is is used to manage the coupons in the shop.
 *
 * @package		VirtueMart
 * @author RickG
 */
class TableCoupons extends VmTable {

	/** @var int Primary key */
	var $virtuemart_coupon_id			 	= 0;
	/** @var varchar Coupon name */
	var $coupon_code         	= '';
	/** @var string Coupon percentage or total */
	var $percent_or_total    	= 'percent';
	/** @var string Coupon type */
	var $coupon_type		    = 'permanent';
	/** @var Decimal Coupon value */
	var $coupon_value 			= '';
	/** @var datetime Coupon start date */
	var $coupon_start_date 		= '';
	/** @var datetime Coupon expiry date */
	var $coupon_expiry_date 	= '';
	/** @var decimal Coupon valid value */
	var $coupon_value_valid 	= 0;

	/**
	 * @author RickG, Max Milbers
	 * @param $db A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_coupons', 'virtuemart_coupon_id', $db);
		$this->setObligatoryKeys('coupon_code');
		$this->setLoggable();
	}


}
// pure php no closing tag
