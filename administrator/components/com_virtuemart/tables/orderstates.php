<?php
/**
*
* Order status table
*
* @package	VirtueMart
* @subpackage Order status
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: orderstates.php 6475 2012-09-21 11:54:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Order status table class
 * The class is is used to manage the order statuses in the shop.
 *
 * @package	VirtueMart
 * @author Oscar van Eijk
 * @author Max Milbers
 */
class TableOrderstates extends VmTable {

	/** @var int Primary key */
	var $virtuemart_orderstate_id			= 0;
	/** @var int Vendor ID if the status is vendor specific */
	var $virtuemart_vendor_id					= null;
	/** @var boolean */
	/** @var char Order status Code */
	var $order_status_code			= '';
	/** @var string Order status name*/
	var $order_status_name			= null;
	/** @var string Order status description */
	var $order_status_description	= null;
	/** @var string Order status description 
	 *  Default ='A' : available
	 **/
	var $order_stock_handle = 'A';
	/** @var int Order in which the order status is listed */
	var $ordering					= 0;
	 /** @var int published or unpublished */
	var $published = 1;


	/**
	 * @param $db Class constructor; connect to the database
	 */
	function __construct(&$db){

		parent::__construct('#__virtuemart_orderstates', 'virtuemart_orderstate_id', $db);

		$this->setObligatoryKeys('order_status_code');
		$this->setObligatoryKeys('order_status_name');
		$this->setObligatoryKeys('order_stock_handle');
		$this->setLoggable();

	}

	/**
	 * Validates the order status record fields.
	 *
	 * @return boolean True if the table buffer is contains valid data, false otherwise.
	 */
	function check(){

		$db = JFactory::getDBO();
		$q = 'SELECT count(*),virtuemart_orderstate_id FROM `#__virtuemart_orderstates` ';
		$q .= 'WHERE `order_status_code`="' .  $this->order_status_code . '"';
		$db->setQuery($q);

		$row = $db->loadRow();
		if(is_array($row)){
			if($row[0]>0){
				if($row[1] != $this->virtuemart_orderstate_id){
					vmError(JText::_('COM_VIRTUEMART_ORDER_STATUS_CODE_EXISTS'));
					return false;
				}
			}
		}

		return parent::check();
	}
}

//No CLosing Tag
