<?php
/**
*
* Userfield Values table
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
* @version $Id: userfield_values.php 6361 2012-08-21 16:05:40Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Userfields table class
 * The class is used to manage the values for select-type userfields in the shop.
 *
 * @package	VirtueMart
 * @author Oscar van Eijk
 */
class TableUserfield_values extends VmTable {

	/** @var int Primary key */
	var $virtuemart_userfield_value_id	= 0;
	/** @var int Reference to the userfield */
	var $virtuemart_userfield_id		= 0;
	/** @var string Label of the value */
	var $fieldtitle		= null;
	/** @var string Selectable value */
	var $fieldvalue		= null;
	/** @var int Value ordering */
	var $ordering		= 0;
	/** @var boolean True if part of the VirtueMart installation; False for User specified*/
	var $sys			= 0;
         /** @var boolean */
	var $locked_on	= 0;
	/** @var time */
	var $locked_by	= 0;
	/**
	 * @param $db Class constructor; connect to the database
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_userfield_values', 'virtuemart_userfield_value_id', $db);
		$this->setPrimaryKey('virtuemart_userfield_id');

	}

	/**
	 * Validates the userfields record fields, and checks if the given value already exists.
	 * If so, the primary key is set.
	 *
	 * @return boolean True if the table buffer is contains valid data, false otherwise.
	 */
	function check()
	{
		if (preg_match('/[^a-z0-9\._\-]/i', $this->fieldvalue) > 0) {
			vmError(JText::_('COM_VIRTUEMART_TITLE_IN_FIELDVALUES_CONTAINS_INVALID_CHARACTERS'));
			return false;
		}

		$db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_userfield_value_id` FROM `#__virtuemart_userfield_values` '
			. 'WHERE `fieldvalue`="' . $this->fieldvalue . '" '
			. 'AND   `virtuemart_userfield_id`=' . $this->virtuemart_userfield_id;
		$db->setQuery($q);
		$_id = $db->loadResult();
		if ($_id === null) {
			$this->virtuemart_userfield_value_id = null;
		} else {
			$this->virtuemart_userfield_value_id = $_id;
		}
		return true;
	}

	/**
	 * Reimplement delete() to get a list if value IDs based on the field id
	 * @var Field id
	 * @return boolean True on success
	 */
	function delete( $virtuemart_userfield_id=null , $where = 0 ){

		$db = JFactory::getDBO();
		$db->setQuery('DELETE from `#__virtuemart_userfield_values` WHERE `virtuemart_userfield_id` = ' . $virtuemart_userfield_id);
		if ($db->query() === false) {
			vmError($db->getError());
			return false;
		}
		return true;
	}
}

//No CLosing Tag
