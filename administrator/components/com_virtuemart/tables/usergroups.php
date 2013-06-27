<?php
/**
*
* Usergroup table
*
* @package	VirtueMart
* @subpackage Userfields
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: usergroups.php 6475 2012-09-21 11:54:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Userfields table class
 * The class is used to manage the userfields in the shop.
 *
 * @package	VirtueMart
 * @author Oscar van Eijk
 * @author Max Milbers
 */
class TableUsergroups extends VmTable {

	/** @var Primary Key*/
	var $virtuemart_permgroup_id = 0;
	/** @var Authentification Groupname*/
	var $group_name='';
	/** @var Authentification level standard is set to demo*/
	var $group_level = 750;

	var $published = 0;

	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_permgroups', 'virtuemart_permgroup_id', $db);

		$this->setUniqueName('group_name');

		$this->setLoggable();
	}

	/**
	 * Validates the userfields record fields.
	 *
	 * @return boolean True if the table buffer is contains valid data, false otherwise.
	 */
	function check($nrOfValues){

		if (preg_match('/[^a-z0-9\._\-]/i', $this->group_name) > 0) {
			vmError(JText::_('COM_VIRTUEMART_PERMISSION_GROUP_NAME_INVALID_CHARACTERS'));
			return false;
		}

		return parent::check();

	}

}

//No CLosing Tag
