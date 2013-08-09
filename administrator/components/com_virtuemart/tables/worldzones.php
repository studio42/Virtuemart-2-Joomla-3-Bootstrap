<?php
/**
*
* Currency table
*
* @package	VirtueMart
* @subpackage Currency
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: currencies.php 3256 2011-05-15 20:04:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Worldzones table class
 * The class is is used to manage the currencies in the shop.
 *
 * @package		VirtueMart
 * @author RickG, Max Milbers
 */
class TableWorldzones extends VmTable {


	/** @var int Primary key */
	var $virtuemart_worldzone_id	= 0;
	/** @var int vendor id */
	var $virtuemart_vendor_id		= 1;
	/** @var string Currency name*/
	var $zone_name           		= '';
	/** @var char Currency code */
	var $zone_cost					= '';
	var $zone_limit         		= ''; //should be renamed to $currency_code_2
	/** @var char Currency symbol */
	var $zone_description 			= 0;
    var $zone_tax_rate         		= '';

    var $ordering					= 0;

	  /** @var boolean */
	var $published					= 0;
	var $shared						= 1;


	/**
	 * @author Max Milbers
	 * @param JDataBase $db
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_worldzones', 'virtuemart_worldzone_id', $db);

		$this->setUniqueName('zone_name');

		$this->setLoggable();

	}


}
// pure php no closing tag
