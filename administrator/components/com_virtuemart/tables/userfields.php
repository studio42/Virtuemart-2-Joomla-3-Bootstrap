<?php
/**
*
* Userfield table
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
* @version $Id: userfields.php 6475 2012-09-21 11:54:21Z Milbo $
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
 */
class TableUserfields extends VmTable {

// 	/** @var var Primary Key*/
	var $virtuemart_userfield_id		= 0;

	/**
	 * @param $db Class constructor; connect to the database
	 */
	function __construct(&$db)
	{

		parent::__construct('#__virtuemart_userfields', 'virtuemart_userfield_id', $db);
		parent::loadFields();
		$this->setUniqueName('name');
		$this->setObligatoryKeys('title');

		$this->setLoggable();

		$this->setOrderable('ordering',false);
	}

	/**
	 * Validates the userfields record fields.
	 *
	 * @return boolean True if the table buffer is contains valid data, false otherwise.
	 */
	function check($nrOfValues)
	{

		if (preg_match('/[^a-z0-9\._\-]/i', $this->name) > 0) {
			vmError(JText::_('COM_VIRTUEMART_NAME_OF_USERFIELD_CONTAINS_INVALID_CHARACTERS'));
			return false;
		}
		if($this->name !='virtuemart_country_id' and $this->name !='virtuemart_state_id'){
			$reqValues = array('select', 'multiselect', 'radio', 'multicheckbox');
			if (in_array($this->type, $reqValues) and $nrOfValues == 0 ) {
				vmError(JText::_('COM_VIRTUEMART_VALUES_ARE_REQUIRED_FOR_THIS_TYPE'));
				return false;
			}
		}


		return parent::check();
	}

	/**
	 * Format the field type
	 * @param $_data array array with additional data written to other tables
	 * @return string Field type in SQL syntax
	 */
	function formatFieldType(&$_data = array())
	{
		$_fieldType = $this->type;
		switch($this->type) {
			case 'date':
				$_fieldType = 'DATE';
				break;
			case 'editorta':
			case 'textarea':
			case 'multiselect':
			case 'multicheckbox':
				$_fieldType = 'MEDIUMTEXT';
				break;
			case 'checkbox':
				$_fieldType = 'TINYINT';
				break;

			case 'age_verification':
				$this->params = 'minimum_age='.(int)$_data['minimum_age']."\n";
			default:
				$_fieldType = 'VARCHAR(255)';
				break;
		}

		return $_fieldType;
	}

	/**
	 * Reimplement the store method to return the last inserted ID
	 *
	 * @return mixed When a new record was succesfully inserted, return the ID, otherwise the status
	 */
	public function store($updateNulls = false)
	{
		$isNew = ($this->virtuemart_userfield_id == 0);
		if (!parent::store($updateNulls)) { // Write data to the DB
			vmError($this->getError());
			return false;
		} else {
			return $this->virtuemart_userfield_id;
		}
	}
	
	function checkAndDelete($table,$where = 0){
		$ok = 1;
		$k = $this->_tbl_key;

		if($where!==0){
			$whereKey = $where;
		} else {
			$whereKey = $this->_pkey;
		}
		
		$query = 'SELECT `'.$this->_tbl_key.'` FROM `'.$table.'` WHERE '.$whereKey.' = "' .$this->$k . '"';
		
		// stAn - it should be better to add this directly to the controller of the shopper fields
		// only additionally, controllers are not considered as safe.
		if (isset($this->name))
		 {
		    
			$umodel = VmModel::getModel('userfields'); 
			$arr = $umodel->getCoreFields(); 
			
			if (in_array($this->name, $arr))
			 {
			  vmError('Cannot delete core field!'); 
			  return false; 
			 }
		 }

		$this->_db->setQuery( $query );
		$list = $this->_db->loadResultArray();

		if($list){

			foreach($list as $row){
				$ok = $row;
				$query = 'DELETE FROM `'.$table.'` WHERE '.$this->_tbl_key.' = "'.$row.'"';
				$this->_db->setQuery( $query );

				if (!$this->_db->query()){
					$this->setError($this->_db->getErrorMsg());
					vmError('checkAndDelete '.$this->_db->getErrorMsg());
					$ok = 0;
				}
			}

		}
		return $ok;
	}
}

//No CLosing Tag
