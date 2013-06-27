<?php
/**
*
* Data model for shopper group
*
* @package	VirtueMart
* @subpackage ShopperGroup
* @author Markus Öhler
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: shoppergroup.php 6508 2012-10-08 06:57:29Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model class for shopper group
 *
 * @package	VirtueMart
 * @subpackage ShopperGroup
 * @author Markus Öhler
 */
class VirtueMartModelShopperGroup extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct('virtuemart_shoppergroup_id');
		$this->setMainTable('shoppergroups');
	}

    /**
     * Retrieve the detail record for the current $id if the data has not already been loaded.
     *
     * @author Markus Öhler
     */
    function getShopperGroup() {

	    if (empty($this->_data)) {
	      $this->_data = $this->getTable('shoppergroups');
	      $this->_data->load((int) $this->_id);
	      if(!empty($this->_data->price_display)){
	      	$this->_data->price_display = unserialize($this->_data->price_display);
	      } else{
	      	if(!class_exists('JParameter')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter.php' );
	      	$this->_data->price_display = new JParameter('');
	      }
	   }

		return $this->_data;
	}


    /**
     * Retireve a list of shopper groups from the database.
     *
     * @author Markus Öhler
     * @param boolean $onlyPublished
     * @param boolean $noLimit True if no record count limit is used, false otherwise
     * @return object List of shopper group objects
     */
    function getShopperGroups($onlyPublished=false, $noLimit = false) {
    	$db = JFactory::getDBO();

	    $query = 'SELECT * FROM `#__virtuemart_shoppergroups` ORDER BY `virtuemart_vendor_id`,`shopper_group_name` ';

		if ($noLimit) {
			$this->_data = $this->_getList($query);
		}
		else {
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

	    return $this->_data;
    }

   function store(&$data){

   	$myfields = array('basePrice','variantModification','basePriceVariant',
   										'basePriceWithTax','basePriceWithTax','discountedPriceWithoutTax',
   										'salesPrice','priceWithoutTax',
   										'salesPriceWithDiscount','discountAmount','taxAmount');

   	$param ='show_prices='.$data['show_prices']."\n";
   	foreach($myfields as $fields){
   		$param .= $fields.'='.$data[$fields]."\n";		//attention there must be doublequotes
   		$param .= $fields.'Text='.$data[$fields.'Text']."\n";
   		$param .= $fields.'Rounding='.$data[$fields.'Rounding']."\n";
   	}

   	if(!class_exists('JParameter')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter.php' );
		$jparam = new JParameter($param);
   	$data['price_display'] = serialize(new JParameter($param));

   	return parent::store($data);
   }

	function makeDefault($id,$kind = 1) {

		//Prevent making anonymous Shoppergroup as default
		$adId = $this->getDefault(1);
		 $anonymous_sg_id = $adId->virtuemart_shoppergroup_id;
		if($adId == $id){
			$group = $this->getShoppergroupById($id);
			vmError(JText::sprintf('COM_VIRTUEMART_SHOPPERGROUP_CANT_MAKE_DEFAULT',$group->shopper_group_name,$id));
			return false;
		}
		$this->_db->setQuery('UPDATE  `#__virtuemart_shoppergroups`  SET `default` = 0 WHERE `default`<"2"');
		if (!$this->_db->query()) return ;
		$this->_db->setQuery('UPDATE  `#__virtuemart_shoppergroups`  SET `default` = "'.$kind.'" WHERE virtuemart_shoppergroup_id='.(int)$id);
		if (!$this->_db->query()) return ;
		return true;
	}

	/**
	 *
	 * Get default shoppergroup for anonymous and non anonymous
	 * @param unknown_type $kind
	 */
	function getDefault($kind = 1, $onlyPublished = FALSE, $vendorId = 1){

		$kind = $kind + 1;
		$q = 'SELECT * FROM `#__virtuemart_shoppergroups` WHERE `default` = "'.$kind.'" AND (`virtuemart_vendor_id` = "'.$vendorId.'" OR `shared` = "1") ';
		if($onlyPublished){
			$q .= ' AND `published`="1" ';
		}
		$this->_db->setQuery($q);

		if(!$res = $this->_db->loadObject()){
			$app = JFactory::getApplication();
			$app->enqueueMessage('Attention no standard shopper group set '.$this->_db->getErrorMsg());
		} else {
			//vmdebug('getDefault', $res);
			return $res;
		}

	}

	function appendShopperGroups(&$shopperGroups,$user,$onlyPublished = FALSE,$vendorId=1){

		$this->mergeSessionSgrps($shopperGroups);

		if(count($shopperGroups)<1){

			$_defaultShopperGroup = $this->getDefault($user->guest,$onlyPublished,$vendorId);
			$shopperGroups[] = $_defaultShopperGroup->virtuemart_shoppergroup_id;
		/*	$this->_db->setQuery('SELECT `virtuemart_shoppergroup_id` FROM #__virtuemart_shoppergroups
								WHERE `default`="'.($user->guest+1).'" AND `virtuemart_vendor_id`="' . (int) $vendorId . '"');
			$this->_shopperGroupId = $this->_db->loadResultArray();*/
		}
		$this->removeSessionSgrps($shopperGroups);

	}

	function mergeSessionSgrps(&$ids){
		$session = JFactory::getSession();
		$shoppergroup_ids = $session->get('vm_shoppergroups_add',array(),'vm');

		$ids = array_merge($ids,(array)$shoppergroup_ids);
		$ids = array_unique($ids);
		//$session->set('vm_shoppergroups_add',array(),'vm');
		//vmdebug('mergeSessionSgrps',$shoppergroup_ids,$ids);
	}

	function removeSessionSgrps(&$ids){
		$session = JFactory::getSession();
		$shoppergroup_ids_remove = $session->get('vm_shoppergroups_remove',0,'vm');
		if($shoppergroup_ids_remove!==0){

			if(!is_array($shoppergroup_ids_remove)){
				$shoppergroup_ids_remove = (array) $shoppergroup_ids_remove;
			}

			foreach($shoppergroup_ids_remove as $k => $id){
				if(in_array($id,$ids)){
					$key=array_search($id, $ids);
					if($key!==FALSE){
						unset($ids[$key]);
						vmdebug('Anonymous case, remove session shoppergroup by plugin '.$id);
					}
				}
			}
			//$session->set('vm_shoppergroups_remove',0,'vm');
		}

	}

	function remove($ids){

		jimport( 'joomla.utilities.arrayhelper' );
		JArrayHelper::toInteger($ids);

		$table = $this->getTable($this->_maintablename);

		$defaultSgId = $this->getDefault(0);
		$anonymSgId = $this->getDefault(1);

		foreach($ids as $id){

			//Test if shoppergroup is default
			if($id == $defaultSgId->virtuemart_shoppergroup_id){
				$this->_db->setQuery('SELECT shopper_group_name FROM `#__virtuemart_shoppergroups`  WHERE `virtuemart_shoppergroup_id` = "'.(int)$id.'"');
				$name = $this->_db->loadResult();
				vmError(JText::sprintf('COM_VIRTUEMART_SHOPPERGROUP_DELETE_CANT_DEFAULT',$name,$id));
				continue;
			}

			//Test if shoppergroup is default
			if($id == $anonymSgId->virtuemart_shoppergroup_id){
				$this->_db->setQuery('SELECT shopper_group_name FROM `#__virtuemart_shoppergroups`  WHERE `virtuemart_shoppergroup_id` = "'.(int)$id.'"');
				$name = $this->_db->loadResult();
				vmError(JText::sprintf('COM_VIRTUEMART_SHOPPERGROUP_DELETE_CANT_DEFAULT',$name,$id));
				continue;
			}

			//Test if shoppergroup has members
			$this->_db->setQuery('SELECT * FROM `#__virtuemart_vmuser_shoppergroups`  WHERE `virtuemart_shoppergroup_id` = "'.(int)$id.'"');
			if($this->_db->loadResult()){
				$this->_db->setQuery('SELECT shopper_group_name FROM `#__virtuemart_shoppergroups`  WHERE `virtuemart_shoppergroup_id` = "'.(int)$id.'"');
				$name = $this->_db->loadResult();
				vmError(JText::sprintf('COM_VIRTUEMART_SHOPPERGROUP_DELETE_CANT_WITH_MEMBERS',$name,$id));
				continue;
			}

			if (!$table->delete($id)) {
				vmError(get_class( $this ).'::remove '.$table->getError());
				return false;
		    }
		}

		return true;
	}

	/**
	 * Retrieves the Shopper Group Info of the SG specified by $id
	 *
	 * @todo Vendor ID
	 * @param int $id
	 * @param boolean $default_group
	 * @return array
	 */
  	static function getShoppergroupById($id, $default_group = false) {
    	$virtuemart_vendor_id = 1;
    	$db = JFactory::getDBO();

    	$q =  'SELECT `#__virtuemart_shoppergroups`.`virtuemart_shoppergroup_id`, `#__virtuemart_shoppergroups`.`shopper_group_name`, `default` AS default_shopper_group FROM `#__virtuemart_shoppergroups`';

    	if (!empty($id) && !$default_group) {
      		$q .= ', `#__virtuemart_vmuser_shoppergroups`';
      		$q .= ' WHERE `#__virtuemart_vmuser_shoppergroups`.`virtuemart_user_id`="'.(int)$id.'" AND ';
      		$q .= '`#__virtuemart_shoppergroups`.`virtuemart_shoppergroup_id`=`#__virtuemart_vmuser_shoppergroups`.`virtuemart_shoppergroup_id`';
    	}
    	else {
    		$q .= ' WHERE `#__virtuemart_shoppergroups`.`virtuemart_vendor_id`="'.(int)$virtuemart_vendor_id.'" AND `default`="2"';
    	}

    	$db->setQuery($q);
    	return $db->loadAssocList();
  	}

}
// pure php no closing tag