<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id: permissions.php 6490 2012-10-02 13:15:10Z Milbo $
* @package VirtueMart
* @subpackage classes
* @author Sören
* @author Max Milbers
* @copyright Copyright (C) 2010-2011 Virtuemart Team - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.org
*/

/**
 * The permission handler class for VirtueMart.
 *
 * @todo Further cleanup
 */
class Permissions extends JObject{

	/** @var array Contains all the user groups */
	var $_user_groups;

	/** @var virtuemart_user_id for the permissions*/
	var $_virtuemart_user_id;		//$auth['virtuemart_user_id']

	var $_show_prices; //$auth['show_prices']

	var $_db;

	private $_perms = 'shopper';

	var $_is_registered_customer;

	private $_vendorId = false;

	static $_instance;

	public function __construct() {

		$this->_db = JFactory::getDBO();
 		$this->getUserGroups();
 		$this->_perms = $this->doAuthentication();

	}

	static public function getInstance() {
		if(!is_object(self::$_instance)){
			self::$_instance = new Permissions();
		}else {

		}
 		return self::$_instance;
    }

	public function getUserGroups() {
		if (empty($this->_user_groups)) {
			$this->_db = JFactory::getDBO();
			$q = ('SELECT `virtuemart_permgroup_id`,`group_name`,`group_level`
					FROM `#__virtuemart_permgroups`
					ORDER BY `group_level` ');
			$this->_db->setQuery($q);
			$this->_user_groups = $this->_db->loadObjectList('group_name');
		}

		return $this->_user_groups;
	}

	/**
	 * Get permissions for a user ID
	 *
	 * @param int $virtuemart_user_id the user ID to check. If no user ID is given the currently logged in user will be used.
	 * @return string permissions
	 */
	public function getPermissions ($userId=null) {
		// default to current user
		if ($userId == null) {
			$user = JFactory::getUser();
			$userId = $user->id;
		}

		// only re-run authentication if we have a different user
		//vmdebug('getPermissions',$this->_virtuemart_user_id,$userId);
		if ($userId != $this->_virtuemart_user_id) {
			$perms = $this->doAuthentication($userId);
		} else {
			$perms = $this->_perms;
		}
		return $perms;
	}

	/**
	* description: Validates if someone is registered customer.
	*            by checking if one has a billing address
	* parameters: virtuemart_user_id
	* returns: true if the user has a BT address
	*          false if the user has none
	*
	* Check if a user is registered in the shop (=customer)
	*
	* @param int $virtuemart_user_id the user ID to check. If no user ID is given the currently logged in user will be used.
	* @return boolean
	*/
	public function isRegisteredCustomer($virtuemart_user_id=0) {
		if ($virtuemart_user_id == 0) {
			/* Lets see if we can get the current signed in user */
			$user = JFactory::getUser();
			if ($user->id == 0) return false;
			else $virtuemart_user_id = $user->id;
		}

		$this->_db = JFactory::getDBO();
		/* If the registration type is neither "no registration" nor "optional registration",
			there *must* be a related Joomla! user, we can join */
		if (VmConfig::get('vm_registration_type') != 'NO_REGISTRATION'
			&& VmConfig::get('vm_registration_type') != 'OPTIONAL_REGISTRATION') {
			$q  = "SELECT COUNT(virtuemart_user_id) AS num_rows
				FROM `#__virtuemart_userinfos`, `#__users`
				WHERE `id`=`virtuemart_user_id`
				AND #__virtuemart_userinfos.virtuemart_user_id='" . (int)$virtuemart_user_id . "'
				AND #__virtuemart_userinfos.address_type='BT'";
		}
		else {
			$q  = "SELECT COUNT(virtuemart_user_id) AS num_rows
				FROM `#__virtuemart_userinfos`
				WHERE #__virtuemart_userinfos.virtuemart_user_id='" . (int)$virtuemart_user_id . "'
				AND #__virtuemart_userinfos.address_type='BT'";
		}
		$this->_db->setQuery($q);
		return $this->_db->loadResult();
	}

	/**
	* This function does the basic authentication
	* for a user in the shop.
	* It assigns permissions, the name, country, zip  and
	* the shopper group id with the user and the session.
	* @return array Authentication information
	*/
	function doAuthentication ($user_id=null) {

		$this->_db = JFactory::getDBO();
		$session = JFactory::getSession();
		$user = JFactory::getUser($user_id);

		if (VmConfig::get('vm_price_access_level') != '') {
			// Is the user allowed to see the prices?
			$this->_show_prices  = $user->authorize( 'virtuemart', 'prices' );
		}
		else {
			$this->_show_prices = 1;
		}

		if(!empty($user->id)){
			$this->_virtuemart_user_id   = $user->id;
			$q = 'SELECT `perms` FROM #__virtuemart_vmusers
					WHERE virtuemart_user_id="'.(int)$this->_virtuemart_user_id.'"';
			$this->_db->setQuery($q);
			$perm = $this->_db->loadResult();

			//We must prevent that Administrators or Managers are 'just' shoppers
			//TODO rewrite it working correctly with jooomla ACL
			if(JVM_VERSION === 2 ){
				if($user->authorise('core.admin')){
					$perm  = 'admin';
				}
			} else {
				if(strpos($user->usertype,'Administrator')!== false){
					$perm  = 'admin';
				}
			}

			if(empty($perm)){

				if(JVM_VERSION === 2 ){
					if($user->groups){
						if($user->authorise('core.admin')){
							$perm  = 'admin';
						} else if($user->authorise('core.manage')){
							$perm  = 'storeadmin';
						} else {
							$perm  = 'shopper';
						}
					} else {
						$perm  = 'shopper';
					}

				} else {
					if(strpos($user->usertype,'Administrator')!== false){
						$perm  = 'admin';
					} else if(strpos($user->usertype,'Manager')!== false){
						$perm  = 'storeadmin';
					} else {
						$perm  = 'shopper';
					}
				}

			}

			$this->_is_registered_customer = true;
		} else {

			$this->_virtuemart_user_id = 0;
			$perm  = 'shopper';
			$this->_is_registered_customer = false;
		}

		return $perm;
	}

	/**
	 * Validates the permission to do something.
	 *
	 * @param string $perms
	 * @return boolean Check successful or not
	 * @example $perm->check( 'admin,storeadmin' );
	 * 			returns true when the user is admin or storeadmin
	 */
	public function check($perms,$acl=0) {
		/* Set the authorization for use */

		// Parse all permissions in argument, comma separated
		// It is assumed auth_user only has one group per user.
			$p1 = explode(",", $this->_perms);
			$p2 = explode(",", $perms);
// 			vmdebug('check '.$perms,$p1,$p2);
			while (list($key1, $value1) = each($p1)) {
				while (list($key2, $value2) = each($p2)) {
					if ($value1 == $value2) {
						return true;
					}
				}
			}
		return false;
	}

	/**
	 * Checks if user is admin or has vendorId=1,
	 * if superadmin, but not a vendor it gives back vendorId=1 (single vendor, but multiuser administrated)
	 *
	 * @author Mattheo Vicini
	 * @author Max Milbers
	 */

	public function isSuperVendor(){


		if(!$this->_vendorId){
			$user = JFactory::getUser();

			if(!empty( $user->id)){
				$q = 'SELECT `virtuemart_vendor_id`
							FROM `#__virtuemart_vmusers` `au`
							LEFT JOIN `#__virtuemart_userinfos` `u`
							ON (au.virtuemart_user_id = u.virtuemart_user_id)
							WHERE `u`.`virtuemart_user_id`="' .$user->id.'" AND `au`.`user_is_vendor` = "1" ';
				$db= JFactory::getDbo();
				$db->setQuery($q);
				$virtuemart_vendor_id = $db->loadResult();
				if ($virtuemart_vendor_id) {
					$this->_vendorId = $virtuemart_vendor_id;
				} else {
					$this->_vendorId = 0;
				}
			} else {
				return false;
			}

		}

		if($this->_vendorId!=0){
			return $this->_vendorId;
		} else {
			if($this->check('admin,storeadmin') ){
				$this->_vendorId = 1;
				return $this->_vendorId;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	 * Checks if the user has higher permissions than $perm
	 * does not work properly, do not use or correct it
	 * @param string $perm
	 * @return boolean
	 * @example $perm->hasHigherPerms( 'storeadmin' );
	 * 			returns true when user is admin
	 */
	function atLeastPerms( $perm ) {

		if( $this->_perms && $this->_user_groups[$perm] >= $this->_user_groups[$this->_perms] ) {
			return true;
		}
		else {
			return false;
		}

	}

	/**
	 * lists the permission levels in a select box
	 * @author pablo
	 * @param string $name The name of the select element
	 * @param string $group_name The preselected key
	 */
	function list_perms( $name, $group_name, $size=1, $multi=false ) {

		$auth = $_SESSION['auth'];
		if( $multi ) {
			$multi = 'multiple="multiple"';
		}

		// Get users current permission value
		$dvalue = $this->user_groups[$this->_perms];

		$perms = $this->getUserGroups();
		arsort( $perms );

		if( $size==1 ) {
			$values[0] = JText::_('COM_VIRTUEMART_SELECT');
		}
		foreach($perms as $key => $value) {
			// Display only those permission that this user can set
			if ($value >= $dvalue) {
				$values[$key] = $key;
			}
		}

		if( $size > 1 ) {
			$name .= '[]';
			$values['none'] = JText::_('COM_VIRTUEMART_NO_RESTRICTION');
		}

		echo VmHTML::selectList( $name, $group_name, $values, $size, $multi );
	}




	/**
	* Here we insert groups that are allowed to view prices
	*
	*/
	function prepareACL() {
		// The basic ACL integration in Mambo/Joomla is not awesome
		$child_groups = self::getChildGroups( '#__core_acl_aro_groups', 'g1.virtuemart_shoppergroup_id, g1.name, COUNT(g2.name) AS level', 'g1.name', null, VmConfig::get('vm_price_access_level'));

		echo '<pre>'.print_r($child_groups,1).'</pre>';


		foreach( $child_groups as $child_group ) {
			self::_addToGlobalACL( 'virtuemart', 'prices', 'users', $child_group->name, null, null );
		}
		$admin_groups = self::getChildGroups( '#__core_acl_aro_groups', 'g1.virtuemart_shoppergroup_id, g1.name, COUNT(g2.name) AS level', 'g1.name', null, 'Public Backend' );
		foreach( $admin_groups as $child_group ) {
			self::_addToGlobalACL( 'virtuemart', 'prices', 'users', $child_group->name, null, null );
		}

	}

	/**
	 * Function from an old Mambo phpgacl integration function
	 * @deprecated (but necessary, sigh!)
	 * @static
	 * @param string $table
	 * @param string $fields
	 * @param string $groupby
	 * @param int $root_id
	 * @param string $root_name
	 * @param boolean $inclusive
	 * @return array
	 */
	function getChildGroups($table, $fields, $groupby=null, $root_id=null, $root_name=null, $inclusive=true) {
		$database = JFactory::getDBO();
		$root = new stdClass();
		$root->lft = 0;
		$root->rgt = 0;
		$fields = str_replace( 'virtuemart_shoppergroup_id', 'id', $fields );

		if ($root_id) {
		}
		else if ($root_name) {
			$database->setQuery("SELECT `lft`, `rgt` FROM `".$table."` WHERE `name`='".$root_name."'" );
			$root = $database->loadObject();
		}

		$where = '';
		if ($root->lft+$root->rgt != 0) {
			if ($inclusive) {
				$where = "WHERE g1.lft BETWEEN $root->lft AND $root->rgt";
			} else {
				$where = "WHERE g1.lft BETWEEN $root->lft+1 AND $root->rgt-1";
			}
		}

		$database->setQuery( "SELECT ".$fields
			. "\nFROM ".$table." AS g1"
			. "\nINNER JOIN ".$table." AS g2 ON g1.lft BETWEEN g2.lft AND g2.rgt"
			. "\n". $where
			. ($groupby ? "\nGROUP BY ".$groupby : "")
			. "\nORDER BY g1.lft"
		);

		return $database->loadObjectList();
	}

	/**
	* This is a temporary function to allow 3PD's to add basic ACL checks for their
	* modules and components.  NOTE: this information will be compiled in the db
	* in future versions
	 * @static
	 * @param unknown_type $aco_section_value
	 * @param unknown_type $aco_value
	 * @param unknown_type $aro_section_value
	 * @param unknown_type $aro_value
	 * @param unknown_type $axo_section_value
	 * @param unknown_type $axo_value
	 */
	function _addToGlobalACL( $aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value=NULL, $axo_value=NULL ) {
		global $acl;
		$acl->acl[] = array( $aco_section_value, $aco_value, $aro_section_value, $aro_value, $axo_section_value, $axo_value );
		$acl->acl_count = count( $acl->acl );
	}

	/**
	 * Returns a tree with the children of the root group id
	 * @static
	 * @param int $root_id
	 * @param string $root_name
	 * @param boolean $inclusive
	 * @return unknown
	 */
	function getGroupChildrenTree( $root_id=null, $root_name=null, $inclusive=true ) {
		global $database, $_VERSION;

		$tree = ps_perm::getChildGroups( '#__core_acl_aro_groups',
			'g1.virtuemart_shoppergroup_id, g1.name, COUNT(g2.name) AS level',
			'g1.name',
			$root_id, $root_name, $inclusive );

		// first pass get level limits
		$n = count( $tree );
		$min = $tree[0]->level;
		$max = $tree[0]->level;
		for ($i=0; $i < $n; $i++) {
			$min = min( $min, $tree[$i]->level );
			$max = max( $max, $tree[$i]->level );
		}

		$indents = array();
		foreach (range( $min, $max ) as $i) {
			$indents[$i] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		// correction for first indent
		$indents[$min] = '';

		$list = array();
		for ($i=$n-1; $i >= 0; $i--) {
			$shim = '';
			foreach (range( $min, $tree[$i]->level ) as $j) {
				$shim .= $indents[$j];
			}

			if (@$indents[$tree[$i]->level+1] == '.&nbsp;') {
				$twist = '&nbsp;';
			} else {
				$twist = "-&nbsp;";
			}

			if( $_VERSION->PRODUCT == 'Joomla!' && $_VERSION->RELEASE >= 1.5 ) {
				$tree[$i]->virtuemart_shoppergroup_id = $tree[$i]->id;
			}
			$list[$tree[$i]->virtuemart_shoppergroup_id] = $shim.$twist.$tree[$i]->name;
			if ($tree[$i]->level < @$tree[$i-1]->level) {
				$indents[$tree[$i]->level+1] = '.&nbsp;';
			}
		}

		ksort($list);
		return $list;
	}

	/**
	* Check if the price should be shown including tax
	*
	* @author RolandD
	* @todo Figure out where to get the setting from
	* @access public
	* @param
	* @return bool true if price with tax is shown otherwise false
	*/
	public function showPriceIncludingTax() {
		return true;
	}
}

//pure php no closing tag