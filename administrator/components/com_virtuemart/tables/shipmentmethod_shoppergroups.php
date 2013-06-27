<?php
/**
*
* shipment_shoppergroups table ( for media)
*
* @package	VirtueMart
* @subpackage Shipmentmethod_shoppergraoups
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: shipment_shoppergroups.php 3002 2011-04-08 12:35:45Z alatak $
*/

defined('_JEXEC') or die();

if(!class_exists('VmTableXarray'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtablexarray.php');

/**
 * shipmentmethod_shoppergroups table class
 * The class is is used to manage the shopper groups with shipment.
 *
 * @author Max Milbers
 * @package		VirtueMart
 */
class TableShipmentmethod_shoppergroups extends VmTableXarray {


	/**
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct(&$db){
		parent::__construct('#__virtuemart_shipmentmethod_shoppergroups', 'id', $db);

		$this->setPrimaryKey('virtuemart_shipmentmethod_id');
		$this->setSecondaryKey('virtuemart_shoppergroup_id');

	}

}
