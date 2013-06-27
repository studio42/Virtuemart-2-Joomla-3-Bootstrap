<?php
/**
*
* State table
*
* @package	VirtueMart
* @subpackage Country
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: states.php 3488 2011-06-14 14:43:27Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * State table class
 * The class is is used to manage the states in a country
 *
 * @package		VirtueMart
 * @author RickG
 */
class TableStates extends VmTable {

	/** @var int Primary key */
	var $virtuemart_state_id				= 0;
	/** @var integer Country id */
	var $virtuemart_country_id           	= 0;
	/** @var integer Zone id */
	var $virtuemart_worldzone_id           	= 0;
	/** @var string State name */
	var $state_name           	= '';
	/** @var char 3 character state code */
	var $state_3_code         	= '';
    /** @var char 2 character state code */
	var $state_2_code         	= '';
	/** @var int published or unpublished */
	var $published         		= 1;


	/**
	 * @author RickG
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_states', 'virtuemart_state_id', $db);

		$this->setUniqueName('state_name');
		$this->setObligatoryKeys('state_2_code');
		$this->setObligatoryKeys('state_3_code');

		$this->setLoggable();
	}

}
// pure php no closing tag
