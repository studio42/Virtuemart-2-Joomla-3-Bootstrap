<?php

/**
 *
 * Shipment  table
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
 * @version $Id: shipmentcarriesr.php -1   $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!class_exists('VmTable'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtable.php');

/**
 * Shipment  table class
 * The class is is used to manage the shipment in the shop.
 *
 * @package	VirtueMart
 * @author RickG, Max Milbers
 */
class TableShipmentmethods extends VmTable {

    /** @var int Primary key */
    var $virtuemart_shipmentmethod_id = 0;

    /** @var int Vendor ID */
    var $virtuemart_vendor_id = 0;

    /** @var int Shipment Joomla plugin I */
    var $shipment_jplugin_id = 0;

    /** @var string Shipment  name */
    var $shipment_name = '';

    /** @var string Shipment  name */
    var $shipment_desc = '';
    var $slug;
    /** @var string Element of shipmentmethod */
    var $shipment_element = '';

    /** @var string parameter of the shipmentmethod */
    var $shipment_params = 0;

    var $ordering = 0;
    var $shared = 0;

    /** @var int published boolean */
    var $published = 1;

    /**
     * @author Max Milbers
     * @param JDataBase $db
     */
    function __construct(&$db) {
	parent::__construct('#__virtuemart_shipmentmethods', 'virtuemart_shipmentmethod_id', $db);
	// we can have several time the same shipment name. It is the vendor problem to set up correctly his shipment rate.
	// $this->setUniqueName('shipment_name');
	$this->setObligatoryKeys('shipment_jplugin_id');
	$this->setObligatoryKeys('shipment_name');
	$this->setLoggable();
	$this->setTranslatable(array('shipment_name', 'shipment_desc'));
	$this->setSlug('shipment_name');

    }

}

// pure php no closing tag
