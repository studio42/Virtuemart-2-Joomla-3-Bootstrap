<?php
/**
*
* User Info Table
*
* @package	VirtueMart
* @subpackage User
* @author 	RickG, RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: userinfos.php 6475 2012-09-21 11:54:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTableData'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtabledata.php');

/**
 * User Info table class
 * The class is is used to manage the user_info table.
 *
 * @package	VirtueMart
 * @author 	RickG, RolandD, Max Milbers
 */
class TableUserinfos extends VmTableData {


	/** @var int Primary key */
	var $virtuemart_user_id = 0;

	/** @var int hidden userkey */
	var $virtuemart_userinfo_id = 0;
// 	var $virtuemart_state_id = '';
// 	var $virtuemart_country_id = '';

// 	var $user_is_vendor = 0;
// 	var $address_type = null;
// 	var $address_type_name = null;
//  	var $name = '';
// 	var $company = '';
// 	var $title ='';
//  	var $last_name = '';
// 	var $first_name = '';
// 	var $middle_name = '';
// 	var $phone_1 = '';
// 	var $phone_2 = '';
// 	var $fax = '';
// 	var $address_1 = '';
// 	var $address_2 = '';
// 	var $city = '';

// 	var $zip = '';


	/**
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct($db) {

		/* Make sure the custom fields are added */
		parent::__construct('#__virtuemart_userinfos', 'virtuemart_userinfo_id', $db);
		parent::loadFields();
		$this->setPrimaryKey('virtuemart_userinfo_id');
		$this->setObligatoryKeys('address_type');
		$this->setObligatoryKeys('virtuemart_user_id');

		$this->setLoggable();

		$this->setTableShortCut('ui');

	}

	/**
	* Validates the user info record fields.
	*
	* @author RickG, RolandD, Max Milbers
	* @return boolean True if the table buffer is contains valid data, false otherwise.
	*/
	public function check(){

		if($this->address_type=='BT' or $this->address_type=='ST' ){
			if($this->address_type=='ST' and empty($this->address_type_name)){
				vmError('Table userinfos check failed: address_type '.$this->address_type.' without name','check failed: ST has no name');
				return false;
			}
		} else {
			vmError('Table userinfos check failed: Unknown address_type '.$this->address_type,'check failed: Unknown address_type ');
			vmdebug('Table userinfos check failed: Unknown address_type '.$this->address_type.' virtuemart_user_id '.$this->virtuemart_user_id.' name '.$this->name);
			return false;
		}

		if (!empty($this->virtuemart_userinfo_id)) {
			$this->virtuemart_userinfo_id = (int)$this->virtuemart_userinfo_id;

			if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
			if(!Permissions::getInstance()->check("admin")) {
				$q = "SELECT virtuemart_user_id
										FROM #__virtuemart_userinfos
										WHERE virtuemart_userinfo_id = ".$this->virtuemart_userinfo_id;
				$this->_db->setQuery($q);
				$total = $this->_db->loadResultArray();

				if (count($total) > 0) {

					$userId = JFactory::getUser()->id;
					if($total[0]!=$userId){
						vmError('Hacking attempt uid check, you got logged');
						echo 'Hacking attempt uid check, you got logged';
						return false;
					}
				}
			}

			//return parent::check();
		} else {
			if(empty($this->address_type)) $this->address_type = 'BT';
			/* Check if a record exists */
			$q = "SELECT virtuemart_userinfo_id
			FROM #__virtuemart_userinfos
			WHERE virtuemart_user_id = ".$this->virtuemart_user_id."
			AND address_type = ".$this->_db->Quote($this->address_type);
			if($this->address_type!='BT'){
				$q .= " AND address_type_name = ".$this->_db->Quote($this->address_type_name);
			}

			$this->_db->setQuery($q);
			$total = $this->_db->loadResultArray();

			if (count($total) > 0) {
				$this->virtuemart_userinfo_id = (int)$total[0];
			} else {
				$this->virtuemart_userinfo_id = 0;//md5(uniqid($this->virtuemart_user_id));
			}
		}
		return parent::check();

	}

	/**
	 * Overloaded delete() to delete a list of virtuemart_userinfo_id's based on the user id
	 * @var mixed id
	 * @return boolean True on success
	 * @author Oscar van Eijk
	 */
	function delete( $id=null , $where = 0 ){
		// TODO If $id is not numeric, assume it's a virtuemart_userinfo_id. Validate if this is safe enough
		if (!is_numeric($id)) {
			return (parent::delete($id));
		}
		// Implicit else
		$this->_db->setQuery('DELETE from `#__virtuemart_userinfos` WHERE `virtuemart_user_id` = ' . $id);
		if ($this->_db->query() === false) {
			vmError($this->_db->getError());
			return false;
		}
		return true;
	}

}

// No Closing tag
