<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: inventory.php 6350 2012-08-14 17:18:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model for VirtueMart Products
 *
 * @package VirtueMart
 * @author RolandD
 */
class VirtueMartModelInventory extends VmModel {

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct('virtuemart_product_id');
		$this->setMainTable('products');
		$this->addvalidOrderingFieldName(array('product_name','product_sku','product_in_stock','product_price','product_weight','published'));
	}

    /**
     * Select the products to list on the product list page
     * @author Max Milbers
     */
    public function getInventory() {

		$select = ' `#__virtuemart_products`.`virtuemart_product_id`,
     				`#__virtuemart_products`.`product_parent_id`,
     				`product_name`,
     				`product_sku`,
     				`product_in_stock`,
     				`product_weight`,
     				`published`,
     				`product_price`';

     	$joinedTables = 'FROM `#__virtuemart_products`
			LEFT JOIN `#__virtuemart_product_prices`
			ON `#__virtuemart_products`.`virtuemart_product_id` = `#__virtuemart_product_prices`.`virtuemart_product_id`
			LEFT JOIN `#__virtuemart_shoppergroups`
			ON `#__virtuemart_product_prices`.`virtuemart_shoppergroup_id` = `#__virtuemart_shoppergroups`.`virtuemart_shoppergroup_id`';


		return $this->_data = $this->exeSortSearchListQuery(0,$select,$joinedTables,$this->getInventoryFilter(),'',$this->_getOrdering());

    }


    /**
    * Collect the filters for the query
    * @author RolandD
	* @author Max Milbers
    */
    private function getInventoryFilter() {
    	/* Check some filters */
     	$filters = array();
     	if ($search = JRequest::getVar('filter_inventory', false)){
     		$search = '"%' . $this->_db->getEscaped( $search, true ) . '%"' ;
			//$search = $this->_db->Quote($search, false);
     		$filters[] = '`#__virtuemart_products`.`product_name` LIKE '.$search;
     	}
     	if (JRequest::getInt('stockfilter', 0) == 1){
     		$filters[] = '`#__virtuemart_products`.`product_in_stock` > 0';
     	}
     	if ($catId = JRequest::getInt('virtuemart_category_id', 0) > 0){
     		$filters[] = '`#__virtuemart_categories`.`virtuemart_category_id` = '.$catId;
     	}
     	$filters[] = '(`#__virtuemart_shoppergroups`.`default` = 1 OR `#__virtuemart_shoppergroups`.`default` is NULL)';

     	return ' WHERE '.implode(' AND ', $filters).$this->_getOrdering();
    }
}
// pure php no closing tag