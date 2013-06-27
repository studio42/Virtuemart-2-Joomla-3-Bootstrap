<?php
/**
*
* Data module for the shipment zones
*
* @package	VirtueMart
* @subpackage Shipment
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: worldzones.php 6350 2012-08-14 17:18:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model class for shipment zone
 *
 * @package	VirtueMart
 * @subpackage Shipment
 * @author RickG, Max Milbers
 */
class VirtueMartModelWorldzones extends VmModel {


	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('worldzones');
	}

    /**
     * Retrieve the detail record for the current $id if the data has not already been loaded.
     *
     * @author RickG
     */
	function getShipmentZone()
	{
		$db = JFactory::getDBO();

		if (empty($this->_data)) {
			$query = 'SELECT * ';
			$query .= 'FROM `#__virtuemart_worldzones` ';
			$query .= 'WHERE `virtuemart_worldzone_id` = ' . (int)$this->_id;
			$db->setQuery($query);
			$this->_data = $db->loadObject();
		}

		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_id = 0;
			$this->_data = null;
		}

		return $this->_data;
	}


    /**
     * Retrieve a list of zone ids and zone names for use in a HTML select list.
     *
     * @author RickG
     */
    function getWorldZonesSelectList()
    {
    	$db = JFactory::getDBO();

    	$query = 'SELECT `virtuemart_worldzone_id`, `zone_name` ';
		$query .= 'FROM `#__virtuemart_worldzones`';
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
// pure php no closing tag