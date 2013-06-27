<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD, Max Milbers, Patrick Kohl, Valerie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product.php 6585 2012-10-25 15:35:07Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');


if (!class_exists ('VmModel')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

// JTable::addIncludePath(JPATH_VM_ADMINISTRATOR.DS.'tables');
/**
 * Model for VirtueMart Products
 *
 * @package VirtueMart
 * @author Max Milbers
 * @todo Replace getOrderUp and getOrderDown with JTable move function. This requires the vm_product_category_xref table to replace the ordering with the ordering column
 */
class VirtueMartModelProduct extends VmModel {

	/**
	 * products object
	 *
	 * @var integer
	 */
	var $products = NULL;

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 *
	 * @author Max Milbers
	 */
	function __construct () {

		parent::__construct ('virtuemart_product_id');
		$this->setMainTable ('products');
		$this->starttime = microtime (TRUE);
		$this->maxScriptTime = ini_get ('max_execution_time') * 0.95 - 1;
		// 	$this->addvalidOrderingFieldName(array('m.mf_name','pp.product_price'));

		$app = JFactory::getApplication ();
		if ($app->isSite ()) {
			$browseOrderByFields = VmConfig::get ('browse_orderby_fields',array('product_sku','category_name','mf_name','product_name'));

		}
		else {
			if (!class_exists ('shopFunctions')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
			}
			$browseOrderByFields = ShopFunctions::getValidProductFilterArray ();
			$this->addvalidOrderingFieldName (array('product_price','p.`product_sales`'));
			//$this->addvalidOrderingFieldName (array('product_price'));
			// 	vmdebug('$browseOrderByFields',$browseOrderByFields);
		}
		$this->addvalidOrderingFieldName ((array)$browseOrderByFields);
		$this->removevalidOrderingFieldName ('virtuemart_product_id');
		$this->removevalidOrderingFieldName ('product_sales');
		//unset($this->_validOrderingFieldName[0]);//virtuemart_product_id
		array_unshift ($this->_validOrderingFieldName, 'p.virtuemart_product_id');
		$this->_selectedOrdering = VmConfig::get ('browse_orderby_field', 'category_name');

		$this->setToggleName('product_special');

		$this->initialiseRequests ();

		//This is just done now for the moment for developing, the idea is of course todo this only when needed.
		$this->updateRequests ();

	}

	var $keyword = "0";
	var $product_parent_id = FALSE;
	var $virtuemart_manufacturer_id = FALSE;
	var $virtuemart_category_id = 0;
	var $search_type = '';
	var $searchcustoms = FALSE;
	var $searchplugin = 0;
	var $filter_order = 'p.virtuemart_product_id';
	var $filter_order_Dir = 'DESC';
	var $valid_BE_search_fields = array('product_name', 'product_sku', 'product_s_desc', '`l`.`metadesc`');
	private $_autoOrder = 0;
	private $orderByString = 0;
	private $listing = FALSE;

	/**
	 * This function resets the variables holding request depended data to the initial values
	 *
	 * @author Max Milbers
	 */
	function initialiseRequests () {

		$this->keyword = "0";
		$this->valid_search_fields = $this->valid_BE_search_fields;
		$this->product_parent_id = FALSE;
		$this->virtuemart_manufacturer_id = FALSE;
		$this->search_type = '';
		$this->searchcustoms = FALSE;
		$this->searchplugin = 0;
		$this->filter_order = VmConfig::get ('browse_orderby_field');
		;
		$this->filter_order_Dir = 'DESC';

		$this->_uncategorizedChildren = null;
	}

	/**
	 * This functions updates the variables of the model which are used in the sortSearchListQuery
	 *  with the variables from the Request
	 *
	 * @author Max Milbers
	 */
	function updateRequests () {

		//hmm how to trigger that in the module or so?
		$this->keyword = vmRequest::uword ('keyword', "0", ' ,-,+');
		if ($this->keyword == "0") {
			$this->keyword = vmRequest::uword ('filter_product', "0", ' ,-,+');
		}

		$app = JFactory::getApplication ();
		$option = 'com_virtuemart';
		$view = 'product';

		//Filter order and dir  This is unecessary complex and maybe even wrong, but atm it seems to work
		if ($app->isSite ()) {
			$filter_order = JRequest::getString ('orderby', VmConfig::get ('browse_orderby_field', '`p`.virtuemart_product_id'));
			$filter_order = $this->checkFilterOrder ($filter_order);

			$filter_order_Dir = strtoupper (JRequest::getWord ('order', 'ASC'));
			$valid_search_fields = VmConfig::get ('browse_search_fields');
		}
		else {
			$filter_order = strtolower ($app->getUserStateFromRequest ('com_virtuemart.' . $view . '.filter_order', 'filter_order', $this->_selectedOrdering, 'cmd'));

			$filter_order = $this->checkFilterOrder ($filter_order);
			$filter_order_Dir = strtoupper ($app->getUserStateFromRequest ($option . '.' . $view . '.filter_order_Dir', 'filter_order_Dir', '', 'word'));
			$valid_search_fields = $this->valid_BE_search_fields;
		}
		$filter_order_Dir = $this->checkFilterDir ($filter_order_Dir);

		$this->filter_order = $filter_order;
		$this->filter_order_Dir = $filter_order_Dir;
		$this->valid_search_fields = $valid_search_fields;

		$this->product_parent_id = JRequest::getInt ('product_parent_id', FALSE);

		$this->virtuemart_manufacturer_id = JRequest::getInt ('virtuemart_manufacturer_id', FALSE);

		$this->search_type = JRequest::getVar ('search_type', '');

		$this->searchcustoms = JRequest::getVar ('customfields', array(), 'default', 'array');

		$this->searchplugin = JRequest::getInt ('custom_parent_id', 0);

	}

	/**
	 * Sets the keyword variable for the search
	 *
	 * @param string $keyword
	 */
	function setKeyWord ($keyword) {

		$this->keyword = $keyword;
	}

	/**
	 * New function for sorting, searching, filtering and pagination for product ids.
	 *
	 * @author Max Milbers
	 */
	function sortSearchListQuery ($onlyPublished = TRUE, $virtuemart_category_id = FALSE, $group = FALSE, $nbrReturnProducts = FALSE) {

		$app = JFactory::getApplication ();

		//User Q.Stanley said that removing group by is increasing the speed of product listing in a bigger shop (10k products) by factor 60
		//So what was the reason for that we have it? TODO experiemental, find conditions for the need of group by
		$groupBy = ' group by p.`virtuemart_product_id` ';

		//administrative variables to organize the joining of tables
		$joinCategory = FALSE;
		$joinMf = FALSE;
		$joinPrice = FALSE;
		$joinCustom = FALSE;
		$joinShopper = FALSE;
		$joinChildren = FALSE;
		$joinLang = TRUE;
		$orderBy = ' ';

		$where = array();
		$useCore = TRUE;
		if ($this->searchplugin !== 0) {
			//reset generic filters ! Why? the plugin can do it, if it wishes it.
			// 			if ($this->keyword ==='') $where=array();
			JPluginHelper::importPlugin ('vmcustom');
			$dispatcher = JDispatcher::getInstance ();
			$PluginJoinTables = array();
			$ret = $dispatcher->trigger ('plgVmAddToSearch', array(&$where, &$PluginJoinTables, $this->searchplugin));
			foreach ($ret as $r) {
				if (!$r) {
					$useCore = FALSE;
				}
			}
		}

		if ($useCore) {
			$isSite = $app->isSite ();
// 		if ( $this->keyword !== "0" and $group ===false) {
			if (!empty($this->keyword) and $this->keyword !== '' and $group === FALSE) {

				$keyword = '"%' . $this->_db->getEscaped ($this->keyword, TRUE) . '%"';

				foreach ($this->valid_search_fields as $searchField) {
					if ($searchField == 'category_name' || $searchField == 'category_description') {
						$joinCategory = TRUE;
					}
					else {
						if ($searchField == 'mf_name') {
							$joinMf = TRUE;
						}
						else {
							if ($searchField == 'product_price') {
								$joinPrice = TRUE;
							}
							else {
								//vmdebug('sortSearchListQuery $searchField',$searchField);
							/*	if (strpos ($searchField, 'p.') == 1) {
									$searchField = 'p`.`' . substr ($searchField, 2, (strlen ($searchField)));
									//vmdebug('sortSearchListQuery $searchField recreated',$searchField);
								}*/
							}
						}
					}
					if (strpos ($searchField, '`') !== FALSE){
						$keywords_plural = preg_replace('/\s+/', '%" AND '.$searchField.' LIKE "%', $keyword);
						$filter_search[] =  $searchField . ' LIKE ' . $keywords_plural;
					} else {
						$keywords_plural = preg_replace('/\s+/', '%" AND `'.$searchField.'` LIKE "%', $keyword);
						$filter_search[] = '`'.$searchField.'` LIKE '.$keywords_plural;
						//$filter_search[] = '`' . $searchField . '` LIKE ' . $keyword;
					}


				}
				if (!empty($filter_search)) {
					$where[] = '(' . implode (' OR ', $filter_search) . ')';
				}
				else {
					$where[] = '`product_name` LIKE ' . $keyword;
					//If they have no check boxes selected it will default to product name at least.
				}
				$joinLang = TRUE;
			}

// 		vmdebug('my $this->searchcustoms ',$this->searchcustoms);
			if (!empty($this->searchcustoms)) {
				$joinCustom = TRUE;
				foreach ($this->searchcustoms as $key => $searchcustom) {
					$custom_search[] = '(pf.`virtuemart_custom_id`="' . (int)$key . '" and pf.`custom_value` like "%' . $this->_db->getEscaped ($searchcustom, TRUE) . '%")';
				}
				$where[] = " ( " . implode (' OR ', $custom_search) . " ) ";
			}

			if ($onlyPublished) {
				$where[] = ' p.`published`="1" ';
			}

			if($isSite and !VmConfig::get('use_as_catalog',0)) {
				if (VmConfig::get('stockhandle','none')=='disableit_children') {
					$where[] = ' (p.`product_in_stock` - p.`product_ordered` >"0" OR children.`product_in_stock` - children.`product_ordered` > "0") ';
					$joinChildren = TRUE;
				} else if (VmConfig::get('stockhandle','none')=='disableit') {
					$where[] = ' p.`product_in_stock` - p.`product_ordered` >"0" ';
				}
 			}

			if ($virtuemart_category_id > 0) {
				$joinCategory = TRUE;
				$where[] = ' `pc`.`virtuemart_category_id` = ' . $virtuemart_category_id;
			}

			if ($isSite and !VmConfig::get('show_uncat_child_products',TRUE)) {
				$joinCategory = TRUE;
				$where[] = ' `pc`.`virtuemart_category_id` > 0 ';
			}

			if ($this->product_parent_id) {
				$where[] = ' p.`product_parent_id` = ' . $this->product_parent_id;
			}

			if ($isSite) {
				$usermodel = VmModel::getModel ('user');
				$currentVMuser = $usermodel->getUser ();
				$virtuemart_shoppergroup_ids = (array)$currentVMuser->shopper_groups;

				if (is_array ($virtuemart_shoppergroup_ids)) {
					$sgrgroups = array();
					foreach ($virtuemart_shoppergroup_ids as $key => $virtuemart_shoppergroup_id) {
						$sgrgroups[] = 's.`virtuemart_shoppergroup_id`= "' . (int)$virtuemart_shoppergroup_id . '" ';
					}
					$sgrgroups[] = 's.`virtuemart_shoppergroup_id` IS NULL ';
					$where[] = " ( " . implode (' OR ', $sgrgroups) . " ) ";

					$joinShopper = TRUE;
				}
			}

			if ($this->virtuemart_manufacturer_id) {
				$joinMf = TRUE;
				$where[] = ' `#__virtuemart_product_manufacturers`.`virtuemart_manufacturer_id` = ' . $this->virtuemart_manufacturer_id;
			}

			// Time filter
			if ($this->search_type != '') {
				$search_order = $this->_db->getEscaped (JRequest::getWord ('search_order') == 'bf' ? '<' : '>');
				switch ($this->search_type) {
					case 'parent':
						$where[] = 'p.`product_parent_id` = "0"';
						break;
					case 'product':
						$where[] = 'p.`modified_on` ' . $search_order . ' "' . $this->_db->getEscaped (JRequest::getVar ('search_date')) . '"';
						break;
					case 'price':
						$joinPrice = TRUE;
						$where[] = 'pp.`modified_on` ' . $search_order . ' "' . $this->_db->getEscaped (JRequest::getVar ('search_date')) . '"';
						break;
					case 'withoutprice':
						$joinPrice = TRUE;
						$where[] = 'pp.`product_price` IS NULL';
						break;
					case 'stockout':
						$where[] = 'p.`product_in_stock`- p.`product_ordered` < 1';
						break;
					case 'stocklow':
						$where[] = 'p.`product_in_stock`- p.`product_ordered` < p.`low_stock_notification`';
						break;
				}
			}

			// special  orders case
			//vmdebug('my filter ordering ',$this->filter_order);
			switch ($this->filter_order) {
				case 'product_special':
					if($isSite){
						$where[] = ' p.`product_special`="1" '; // TODO Change  to  a  individual button
						$orderBy = 'ORDER BY RAND()';
					} else {
						$orderBy = 'ORDER BY `product_special`';
					}

					break;
				case 'category_name':
					$orderBy = ' ORDER BY `category_name` ';
					$joinCategory = TRUE;
					break;
				case 'category_description':
					$orderBy = ' ORDER BY `category_description` ';
					$joinCategory = TRUE;
					break;
				case 'mf_name':
					$orderBy = ' ORDER BY `mf_name` ';
					$joinMf = TRUE;
					break;
				case 'pc.ordering':
					$orderBy = ' ORDER BY `pc`.`ordering` ';
					$joinCategory = TRUE;
					break;
				case 'product_price':
					//$filters[] = 'p.`virtuemart_product_id` = p.`virtuemart_product_id`';
					$orderBy = ' ORDER BY `product_price` ';
					$joinPrice = TRUE;
					break;
				case 'created_on':
					$orderBy = ' ORDER BY p.`created_on` ';
					break;
				default;
					if (!empty($this->filter_order)) {
						$orderBy = ' ORDER BY ' . $this->filter_order . ' ';
					}
					else {
						$this->filter_order_Dir = '';
					}
					break;
			}

			//Group case from the modules
			if ($group) {

				$latest_products_days = VmConfig::get ('latest_products_days', 7);
				$latest_products_orderBy = VmConfig::get ('latest_products_orderBy','created_on');
				$groupBy = 'group by p.`virtuemart_product_id` ';
				switch ($group) {
					case 'featured':
						$where[] = 'p.`product_special`="1" ';
						$orderBy = 'ORDER BY RAND()';
						break;
					case 'latest':
						$date = JFactory::getDate (time () - (60 * 60 * 24 * $latest_products_days));
						$dateSql = $date->toMySQL ();
						$where[] = 'p.`' . $latest_products_orderBy . '` > "' . $dateSql . '" ';
						$orderBy = 'ORDER BY p.`' . $latest_products_orderBy . '`';
						$this->filter_order_Dir = 'DESC';
						break;
					case 'random':
						$orderBy = ' ORDER BY RAND() '; //LIMIT 0, '.(int)$nbrReturnProducts ; //TODO set limit LIMIT 0, '.(int)$nbrReturnProducts;
						break;
					case 'topten';
						$orderBy = ' ORDER BY p.`product_sales` '; //LIMIT 0, '.(int)$nbrReturnProducts;  //TODO set limitLIMIT 0, '.(int)$nbrReturnProducts;
						$this->filter_order_Dir = 'DESC';
					break;
					case 'recent';
						$rSession = JFactory::getSession();
						$rIds = $rSession->get('vmlastvisitedproductids', array(), 'vm'); // get recent viewed from browser session
						return $rIds;
				}
				// 			$joinCategory 	= false ; //creates error
				// 			$joinMf 		= false ;	//creates error
				$joinPrice = TRUE;
				$this->searchplugin = FALSE;
// 			$joinLang = false;
			}
		}

		//write the query, incldue the tables
		//$selectFindRows = 'SELECT SQL_CALC_FOUND_ROWS * FROM `#__virtuemart_products` ';
		//$selectFindRows = 'SELECT COUNT(*) FROM `#__virtuemart_products` ';
		if ($joinLang) {
			$select = ' l.`virtuemart_product_id` FROM `#__virtuemart_products_' . VMLANG . '` as l';
			$joinedTables = ' JOIN `#__virtuemart_products` AS p using (`virtuemart_product_id`)';
		}
		else {
			$select = ' p.`virtuemart_product_id` FROM `#__virtuemart_products` as p';
			$joinedTables = '';
		}

		if ($joinCategory == TRUE) {
			$joinedTables .= ' LEFT JOIN `#__virtuemart_product_categories` as pc ON p.`virtuemart_product_id` = `pc`.`virtuemart_product_id`
			 LEFT JOIN `#__virtuemart_categories_' . VMLANG . '` as c ON c.`virtuemart_category_id` = `pc`.`virtuemart_category_id`';
		}
		if ($joinMf == TRUE) {
			$joinedTables .= ' LEFT JOIN `#__virtuemart_product_manufacturers` ON p.`virtuemart_product_id` = `#__virtuemart_product_manufacturers`.`virtuemart_product_id`
			 LEFT JOIN `#__virtuemart_manufacturers_' . VMLANG . '` as m ON m.`virtuemart_manufacturer_id` = `#__virtuemart_product_manufacturers`.`virtuemart_manufacturer_id` ';
		}

		if ($joinPrice == TRUE) {
			$joinedTables .= ' LEFT JOIN `#__virtuemart_product_prices` as pp ON p.`virtuemart_product_id` = pp.`virtuemart_product_id` ';
		}
		if ($this->searchcustoms) {
			$joinedTables .= ' LEFT JOIN `#__virtuemart_product_customfields` as pf ON p.`virtuemart_product_id` = pf.`virtuemart_product_id` ';
		}
		if ($this->searchplugin !== 0) {
			if (!empty($PluginJoinTables)) {
				$plgName = $PluginJoinTables[0];
				$joinedTables .= ' LEFT JOIN `#__virtuemart_product_custom_plg_' . $plgName . '` as ' . $plgName . ' ON ' . $plgName . '.`virtuemart_product_id` = p.`virtuemart_product_id` ';
			}
		}
		if ($joinShopper == TRUE) {
			$joinedTables .= ' LEFT JOIN `#__virtuemart_product_shoppergroups` ON p.`virtuemart_product_id` = `#__virtuemart_product_shoppergroups`.`virtuemart_product_id`
			 LEFT  OUTER JOIN `#__virtuemart_shoppergroups` as s ON s.`virtuemart_shoppergroup_id` = `#__virtuemart_product_shoppergroups`.`virtuemart_shoppergroup_id`';
		}

		if ($joinChildren) {
			$joinedTables .= ' LEFT OUTER JOIN `#__virtuemart_products` children ON p.`virtuemart_product_id` = children.`product_parent_id` ';
		}

		if (count ($where) > 0) {
			$whereString = ' WHERE (' . implode (' AND ', $where) . ') ';
		}
		else {
			$whereString = '';
		}
		//vmdebug ( $joinedTables.' joined ? ',$select, $joinedTables, $whereString, $groupBy, $orderBy, $this->filter_order_Dir );		/* jexit();  */
		$this->orderByString = $orderBy;
		$product_ids = $this->exeSortSearchListQuery (2, $select, $joinedTables, $whereString, $groupBy, $orderBy, $this->filter_order_Dir, $nbrReturnProducts);

		// This makes products searchable, we decided that this is not good, because variant childs appear then in lists
		//So the new convention is that products which should be shown on a category or a manufacturer page should have entered this data
		/*		if ($joinCategory == true || $joinMf) {

		$tmp = array();;
		foreach($product_ids as $k=>$id){
		$tmp[] = $id;
		$children = $this->getProductChildIds($id);
		if($children){
		$tmp = array_merge($tmp,$children);
		}
		}
		$product_ids = $tmp;
		}*/

		 //vmdebug('my product ids',$product_ids);

		return $product_ids;

	}

	/**
	 * Override
	 *
	 * @see VmModel::setPaginationLimits()
	 */
	public function setPaginationLimits () {

		$app = JFactory::getApplication ();
		$view = JRequest::getWord ('view','virtuemart');

		$cateid = JRequest::getInt ('virtuemart_category_id', 0);
		$manid = JRequest::getInt ('virtuemart_manufacturer_id', 0);

		$limitString = 'com_virtuemart.' . $view . 'c' . $cateid . '.limit';
		$limit = (int)$app->getUserStateFromRequest ($limitString, 'limit');

		$limitStartString  = 'com_virtuemart.' . $view . '.limitstart';
		if ($app->isSite () and ($cateid != 0 or $manid != 0) ) {

			$lastCatId = ShopFunctionsf::getLastVisitedCategoryId ();
			$lastManId = ShopFunctionsf::getLastVisitedManuId ();
			if ($lastCatId != $cateid or $lastManId != $manid) {
				$limitStart = 0;
			}
			else {
				$limitStartString  = 'com_virtuemart.' . $view . 'c' . $cateid .'m'.$manid. '.limitstart';
				$limitStart = $app->getUserStateFromRequest ($limitStartString, 'limitstart', JRequest::getInt ('limitstart', 0), 'int');
			}

			$catModel= VmModel::getModel('category');
			$category = $catModel->getCategory();
			if(empty($limit)){
				if(!empty($category->limit_list_initial)){
					$suglimit = $category->limit_list_initial;
				} else {
					if(empty($category->limit_list_step)){
						$suglimit = VmConfig::get ('list_limit', 20);
					} else {
						$suglimit = $category->limit_list_step;
					}
				}
				if(empty($category->products_per_row)){
					$category->products_per_row = VmConfig::get ('products_per_row', 3);
				}
				$rest = $suglimit%$category->products_per_row;
				$limit = $suglimit - $rest;

				//fix by hjet
				$prod_per_page = explode(",",VmConfig::get('pagination_sequence'));
				if($limit <= $prod_per_page['0'] && array_key_exists('0',$prod_per_page)){
					$limit = $prod_per_page['0'];
				}
			}

			//vmdebug('my cat',$category);
			//vmdebug('Looks like the category lastCatId '.$lastCatId.' actual id '.$cateid );
		}
		else {
			$limitStart = $app->getUserStateFromRequest ('com_virtuemart.' . $view . '.limitstart', 'limitstart', JRequest::getInt ('limitstart', 0), 'int');
		}

		if(empty($limit)){
			$limit = VmConfig::get ('list_limit', 20);
		}
		$this->setState ('limit', $limit);
		$this->setState ($limitString, $limit);
		$this->_limit = $limit;

		//There is a strange error in the frontend giving back 9 instead of 10, or 24 instead of 25
		//This functions assures that the steps of limitstart fit with the limit
		$limitStart = ceil ((float)$limitStart / (float)$limit) * $limit;

		$this->setState ('limitstart', $limitStart);
		$this->setState ($limitStartString, $limitStart);

		$this->_limitStart = $limitStart;

		return array($this->_limitStart, $this->_limit);
	}

	/**
	 * This function creates a product with the attributes of the parent.
	 *
	 * @param int     $virtuemart_product_id
	 * @param boolean $front for frontend use
	 * @param boolean $withCalc calculate prices?
	 */
	public function getProduct ($virtuemart_product_id = NULL, $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $quantity = 1) {

		if (isset($virtuemart_product_id)) {
			$virtuemart_product_id = $this->setId ($virtuemart_product_id);
		}
		else {
			if (empty($this->_id)) {
				vmError('Can not return product with empty id');
				return FALSE;
			}
			else {
				$virtuemart_product_id = $this->_id;
			}
		}
		$productKey = (int)$virtuemart_product_id;
		static $_products = array();
		if (!array_key_exists ($productKey, $_products)) {

			$child = $this->getProductSingle ($virtuemart_product_id, $front,$quantity);
			if (!$child->published && $onlyPublished) {
				vmdebug('getProduct child is not published, returning zero');
				return FALSE;
			}
			if(!isset($child->orderable)){
				$child->orderable = TRUE;
			}
			//store the original parent id
			$pId = $child->virtuemart_product_id;
			$ppId = $child->product_parent_id;
			$published = $child->published;

			//$this->product_parent_id = $child->product_parent_id;

			$i = 0;
			$runtime = microtime (TRUE) - $this->starttime;
			//Check for all attributes to inherited by parent products
			while (!empty($child->product_parent_id)) {
				$runtime = microtime (TRUE) - $this->starttime;
				if ($runtime >= $this->maxScriptTime) {
					vmdebug ('Max execution time reached in model product getProduct() ', $child);
					vmError ('Max execution time reached in model product getProduct() ' . $child->product_parent_id);
					break;
				}
				else {
					if ($i > 10) {
						vmdebug ('Time: ' . $runtime . ' Too many child products in getProduct() ', $child);
						vmError ('Time: ' . $runtime . ' Too many child products in getProduct() ' . $child->product_parent_id);
						break;
					}
				}
				$parentProduct = $this->getProductSingle ($child->product_parent_id, $front,$quantity);
				if ($child->product_parent_id === $parentProduct->product_parent_id) {
					vmError('Error, parent product with virtuemart_product_id = '.$parentProduct->virtuemart_product_id.' has same parent id like the child with virtuemart_product_id '.$child->virtuemart_product_id);
					break;
				}
				$attribs = get_object_vars ($parentProduct);

				foreach ($attribs as $k=> $v) {
					if ('product_in_stock' != $k and 'product_ordered' != $k) {// Do not copy parent stock into child
						if (strpos ($k, '_') !== 0 and empty($child->$k)) {
							$child->$k = $v;
// 							vmdebug($child->product_parent_id.' $child->$k',$child->$k);
						}
					}
				}
				$i++;
				if ($child->product_parent_id != $parentProduct->product_parent_id) {
					$child->product_parent_id = $parentProduct->product_parent_id;
				}
				else {
					$child->product_parent_id = 0;
				}

			}

			//vmdebug('getProduct Time: '.$runtime);
			$child->published = $published;
			$child->virtuemart_product_id = $pId;
			$child->product_parent_id = $ppId;

			if ($withCalc) {
				$child->prices = $this->getPrice ($child, array(), 1);
				//vmdebug(' use of $child->prices = $this->getPrice($child,array(),1)');
			}

			if (empty($child->product_template)) {
				$child->product_template = VmConfig::get ('producttemplate');
			}

			// Add the product link  for canonical
			$child->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->virtuemart_category_id;
			$child->link = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id . '&virtuemart_category_id=' . $child->virtuemart_category_id);

			/*if (empty($child->layout)) {
				// product_layout ?
				$child->layout = VmConfig::get ('productlayout');
			}*/

			$app = JFactory::getApplication ();
			if ($app->isSite () and VmConfig::get ('stockhandle', 'none') == 'disableit' and ($child->product_in_stock - $child->product_ordered) <= 0) {
				vmdebug ('STOCK 0', VmConfig::get ('use_as_catalog', 0), VmConfig::get ('stockhandle', 'none'), $child->product_in_stock);
				return FALSE;
			} else {
				$_products[$productKey] = $child;
			}

		}

		return $_products[$productKey];
	}

	private function loadProductPrices($productId,$quantity,$virtuemart_shoppergroup_ids,$front){

		$db = JFactory::getDbo();
		$this->_nullDate = $db->getNullDate();
		$jnow = JFactory::getDate();
		$this->_now = $jnow->toMySQL();

		//$productId = $this->_id===0? $product->virtuemart_product_id:$this->_id;
		//$productId = $product->virtuemart_product_id===0? $this->_id:$product->virtuemart_product_id;
		$q = 'SELECT * FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_id` = "'.$productId.'" ';

		if($front){
			if(count($virtuemart_shoppergroup_ids)>0){
				$q .= ' AND (';
				$sqrpss = '';
				foreach($virtuemart_shoppergroup_ids as $sgrpId){
					$sqrpss .= ' `virtuemart_shoppergroup_id` ="'.$sgrpId.'" OR ';
				}
				$q .= substr($sqrpss,0,-4);
				$q .= ' OR `virtuemart_shoppergroup_id` IS NULL OR `virtuemart_shoppergroup_id`="0") ';
			}
			$quantity = (int)$quantity;
			$q .= ' AND ( (`product_price_publish_up` IS NULL OR `product_price_publish_up` = "' . $db->getEscaped($this->_nullDate) . '" OR `product_price_publish_up` <= "' .$db->getEscaped($this->_now) . '" )
		        AND (`product_price_publish_down` IS NULL OR `product_price_publish_down` = "' .$db->getEscaped($this->_nullDate) . '" OR product_price_publish_down >= "' . $db->getEscaped($this->_now) . '" ) )';
			$q .= ' AND( (`price_quantity_start` IS NULL OR `price_quantity_start`="0" OR `price_quantity_start` <= '.$quantity.') AND (`price_quantity_end` IS NULL OR `price_quantity_end`="0" OR `price_quantity_end` >= '.$quantity.') )';
		} else {
			$q .= ' ORDER BY `product_price` DESC';
		}

		$db->setQuery($q);
		$prices = $db->loadAssocList();
		$err = $db->getErrorMsg();
		if(!empty($err)){
			vmError('getProductSingle '.$err);
		} else {
			//vmdebug('getProductSingle getPrice query',$q);
		}
		return $prices;
	}

	public function getProductPrices(&$product,$quantity,$virtuemart_shoppergroup_ids,$front,$loop=false){

		$product->product_price = null;
		$product->product_override_price = null;
		$product->override = null;
		$product->virtuemart_product_price_id = null;
		$product->virtuemart_shoppergroup_id = null;
		$product->product_price_publish_up = null;
		$product->product_price_publish_down = null;
		$product->price_quantity_start = null;
		$product->price_quantity_end = null;

		$productId = $product->virtuemart_product_id===0? $this->_id:$product->virtuemart_product_id;
		$product->prices = $this->loadProductPrices($productId,$quantity,$virtuemart_shoppergroup_ids,$front);
		$i = 0;
		$runtime = microtime (TRUE) - $this->starttime;
		$product_parent_id = $product->product_parent_id;

		//Check for all attributes to inherited by parent products
		if($loop) {
			while ( $product_parent_id and count($product->prices)==0) {
				$runtime = microtime (TRUE) - $this->starttime;
				if ($runtime >= $this->maxScriptTime) {
					vmdebug ('Max execution time reached in model product getProductPrices() ', $product);
					vmError ('Max execution time reached in model product getProductPrices() ' . $product->product_parent_id);
					break;
				}
				else {
					if ($i > 10) {
						vmdebug ('Time: ' . $runtime . ' Too many child products in getProductPrices() ', $product);
						vmError ('Time: ' . $runtime . ' Too many child products in getProductPrices() ' . $product->product_parent_id);
						break;
					}
				}
				$product->prices = $this->loadProductPrices($product_parent_id,$quantity,$virtuemart_shoppergroup_ids,$front);

				$i++;

				if(!isset($product->prices['salesPrice']) and $product->product_parent_id!=0){
					$db = JFactory::getDbo();
					$db->setQuery (' SELECT `product_parent_id` FROM `#__virtuemart_products` WHERE `virtuemart_product_id` =' . $product_parent_id);
					$product_parent_id = $db->loadResult ();
				}
			}
		}

		if(count($product->prices)===1){
			unset($product->prices[0]['virtuemart_product_id']);
			$product = (object)array_merge ((array)$product, (array)$product->prices[0]);
		} else if ( $front and count($product->prices)>1 ) {
			foreach($product->prices as $price){

				if(empty($price['virtuemart_shoppergroup_id'])){
					if(empty($emptySpgrpPrice))$emptySpgrpPrice = $price;
				} else if(in_array($price['virtuemart_shoppergroup_id'],$virtuemart_shoppergroup_ids)){
					$spgrpPrice = $price;
					break;
				}
			}

			if(!empty($spgrpPrice)){
				$product = (object)array_merge ((array)$product, (array)$spgrpPrice);
				//$prices = (array)$spgrpPrice;
			}
			else if(!empty($emptySpgrpPrice)){
				$product = (object)array_merge ((array)$product, (array)$emptySpgrpPrice);
				//$prices = (array)$emptySpgrpPrice;
			} else {
				vmWarn('COM_VIRTUEMART_PRICE_AMBIGUOUS');
				$product = (object)array_merge ((array)$product, (array)$product->prices[0]);
				//$prices = (array)$product->prices[0];
			}
		}
	}

	public function getProductSingle ($virtuemart_product_id = NULL, $front = TRUE, $quantity = 1) {

		//$this->fillVoidProduct($front);
		if (!empty($virtuemart_product_id)) {
			$virtuemart_product_id = $this->setId ($virtuemart_product_id);
		}

		//		if(empty($this->_data)){
		if (!empty($this->_id)) {

// 			$joinIds = array('virtuemart_product_price_id' =>'#__virtuemart_product_prices','virtuemart_manufacturer_id' =>'#__virtuemart_product_manufacturers','virtuemart_customfield_id' =>'#__virtuemart_product_customfields');
			$joinIds = array('virtuemart_manufacturer_id' => '#__virtuemart_product_manufacturers', 'virtuemart_customfield_id' => '#__virtuemart_product_customfields');

			$product = $this->getTable ('products');
			$product->load ($this->_id, 0, 0, $joinIds);

			$xrefTable = $this->getTable ('product_medias');
			$product->virtuemart_media_id = $xrefTable->load ((int)$this->_id);

			// Load the shoppers the product is available to for Custom Shopper Visibility
			$product->shoppergroups = $this->getProductShoppergroups ($this->_id);

			$usermodel = VmModel::getModel ('user');
			$currentVMuser = $usermodel->getCurrentUser ();
			if(!is_array($currentVMuser->shopper_groups)){
				$virtuemart_shoppergroup_ids = (array)$currentVMuser->shopper_groups;
			} else {
				$virtuemart_shoppergroup_ids = $currentVMuser->shopper_groups;
			}

			if (!empty($product->shoppergroups) and $front) {
				if (!class_exists ('VirtueMartModelUser')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
				}
				$commonShpgrps = array_intersect ($virtuemart_shoppergroup_ids, $product->shoppergroups);
				if (empty($commonShpgrps)) {
					vmdebug('getProductSingle creating void product, usergroup does not fit ',$product->shoppergroups);
					return $this->fillVoidProduct ($front);
				}
			}

			$this->getProductPrices($product,$quantity,$virtuemart_shoppergroup_ids,$front);

			//$product = array_merge ($prices, (array)$product);
			//$product = (object)array_merge ((array)$prices, (array)$product);
			//vmdebug('my prices count 1',$product,$prices);

			if (!empty($product->virtuemart_manufacturer_id)) {
				$mfTable = $this->getTable ('manufacturers');
				$mfTable->load ((int)$product->virtuemart_manufacturer_id);
				$product = (object)array_merge ((array)$mfTable, (array)$product);
			}
			else {
				$product->virtuemart_manufacturer_id = array();
				$product->mf_name = '';
				$product->mf_desc = '';
				$product->mf_url = '';
			}

			// Load the categories the product is in
			//$product->categories = $this->getProductCategories ($this->_id, $front);
			$product->categories = $this->getProductCategories ($this->_id, FALSE); //We need also the unpublished categories, else the calculation rules do not work

			$product->virtuemart_category_id = 0;
			if ($front) {

				$canonCatLink = 0;
				if (!empty($product->categories) and is_array ($product->categories) and count($product->categories)>1){

					if (!class_exists ('shopFunctionsF')) {
						require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
					}
					if (!empty($product->categories) and is_array ($product->categories)) {
						$categories = $this->getProductCategories ($this->_id, TRUE);   //only published
						if(!is_array($categories)) $categories = (array)$categories;
						$canonCatLink = $categories[0];
					}
					//We must first check if we come from another category, due the canoncial link we would have always the same catgory id for a product
					//But then we would have wrong neighbored products / category and product layouts
					$last_category_id = shopFunctionsF::getLastVisitedCategoryId ();
					if ($last_category_id!==0 and in_array ($last_category_id, $product->categories)) {
						$product->virtuemart_category_id = $last_category_id;
						vmdebug('I take for product the last category ',$last_category_id,$product->categories);
					} else {
						$virtuemart_category_id = JRequest::getInt ('virtuemart_category_id', 0);
						if ($virtuemart_category_id!==0 and in_array ($virtuemart_category_id, $product->categories)) {
							$product->virtuemart_category_id = $virtuemart_category_id;
							vmdebug('I take for product the requested category ',$virtuemart_category_id,$product->categories);
						} else {
							if (!empty($product->categories) and is_array ($product->categories) and array_key_exists (0, $product->categories)) {
								$product->virtuemart_category_id = $canonCatLink;
								vmdebug('I take for product the main category ',$product->virtuemart_category_id,$product->categories);
							}
						}
					}

				} else if (!empty($product->categories) and is_array ($product->categories) and count($product->categories)===1){
					$product->virtuemart_category_id = $canonCatLink = $product->categories[0];
				} else {
					/*$last_category_id = shopFunctionsF::getLastVisitedCategoryId ();
					if($last_category_id){
						$product->virtuemart_category_id = $last_category_id;
					}
					//$product->virtuemart_category_id = $canonCatLink = 0;*/
				}

				// Add the product link  for canonical
			//	$product->canonical = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->_id . '&virtuemart_category_id=' . $product->virtuemart_category_id;
			//	$product->link = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->_id . '&virtuemart_category_id=' . $product->virtuemart_category_id);

			} else {
				$virtuemart_category_id = JRequest::getInt ('virtuemart_category_id', 0);

				if($virtuemart_category_id!==0 and !empty($product->categories) ) {
					if(is_array($product->categories) and in_array ($virtuemart_category_id, $product->categories)){
						$product->virtuemart_category_id = $virtuemart_category_id;
					} else if($product->categories==$virtuemart_category_id) {
						$product->virtuemart_category_id = $virtuemart_category_id;
					}

				}

				if (empty($product->virtuemart_category_id)) {
					if (!empty($product->categories) and is_array ($product->categories) and !empty($product->categories[0])) {
						$product->virtuemart_category_id = $product->categories[0];
					} else {
						$product->virtuemart_category_id = null;
					}
				}

				vmdebug('getProductSingle BE request $virtuemart_category_id',$virtuemart_category_id,$product->virtuemart_category_id);
			}

			if(!empty($product->virtuemart_category_id)){

				$q = 'SELECT `ordering`,`id` FROM `#__virtuemart_product_categories`
					WHERE `virtuemart_product_id` = "' . $this->_id . '" and `virtuemart_category_id`= "' . $product->virtuemart_category_id . '" ';
				$this->_db->setQuery ($q);
				// change for faster ordering
				$ordering = $this->_db->loadObject ();
				if (!empty($ordering)) {
					$product->ordering = $ordering->ordering;
					//This is the ordering id in the list to store the ordering notice by Max Milbers
					$product->id = $ordering->id;
				} else {
					$product->ordering = $this->_autoOrder++;
					$product->id = $ordering->id;
					vmdebug('$product->virtuemart_category_id no ordering stored for '.$ordering->id);
				}

				$catTable = $this->getTable ('categories');
				$catTable->load ($product->virtuemart_category_id);
				$product->category_name = $catTable->category_name;
			} else {
				$product->category_name = null;
				$product->virtuemart_category_id = null;
				$product->ordering = null;
				$product->id = $this->_autoOrder++;
				vmdebug('$product->virtuemart_category_id is empty');
			}

			if (!$front) {
				if(!$this->listing){
					$customfields = VmModel::getModel ('Customfields');
					$product->customfields = $customfields->getproductCustomslist ($this->_id);

					if (empty($product->customfields) and !empty($product->product_parent_id)) {
						//$product->customfields = $this->productCustomsfieldsClone($product->product_parent_id,true) ;
						$product->customfields = $customfields->getproductCustomslist ($product->product_parent_id, $this->_id);
						$product->customfields_fromParent = TRUE;
					}
				}
			}
			else {


				//only needed in FE productdetails, is now loaded in the view.html.php
				//				/* Load the neighbours */
				//				$product->neighbours = $this->getNeighborProducts($product);

				// Fix the product packaging
				if ($product->product_packaging) {
					$product->packaging = $product->product_packaging & 0xFFFF;
					$product->box = ($product->product_packaging >> 16) & 0xFFFF;
				}
				else {
					$product->packaging = '';
					$product->box = '';
				}

				// set the custom variants
				//vmdebug('getProductSingle id '.$product->virtuemart_product_id.' $product->virtuemart_customfield_id '.$product->virtuemart_customfield_id);
				if (!empty($product->virtuemart_customfield_id)) {

					$customfields = VmModel::getModel ('Customfields');
					// Load the custom product fields
					$product->customfields = $customfields->getProductCustomsField ($product);
					$product->customfieldsRelatedCategories = $customfields->getProductCustomsFieldRelatedCategories ($product);
					$product->customfieldsRelatedProducts = $customfields->getProductCustomsFieldRelatedProducts ($product);
					//  custom product fields for add to cart
					$product->customfieldsCart = $customfields->getProductCustomsFieldCart ($product);
					$child = $this->getProductChilds ($this->_id);
					$product->customsChilds = $customfields->getProductCustomsChilds ($child, $this->_id);
				}

				// Check the stock level
				if (empty($product->product_in_stock)) {
					$product->product_in_stock = 0;
				}

			}

		}
		else {
			return $this->fillVoidProduct ($front);
		}
		//		}

		$this->product = $product;
		return $product;
	}

	/**
	 * This fills the empty properties of a product
	 * todo add if(!empty statements
	 *
	 * @author Max Milbers
	 * @param unknown_type $product
	 * @param unknown_type $front
	 */
	private function fillVoidProduct ($front = TRUE) {

		/* Load an empty product */
		$product = $this->getTable ('products');
		$product->load ();

		/* Add optional fields */
		$product->virtuemart_manufacturer_id = NULL;
		$product->virtuemart_product_price_id = NULL;

		if (!class_exists ('VirtueMartModelVendor')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
		}
		//$product->virtuemart_vendor_id = VirtueMartModelVendor::getLoggedVendor();

		$product->product_price = NULL;
		$product->product_currency = NULL;
		$product->product_price_quantity_start = NULL;
		$product->product_price_quantity_end = NULL;
		$product->product_price_publish_up = NULL;
		$product->product_price_publish_down = NULL;
		$product->product_tax_id = NULL;
		$product->product_discount_id = NULL;
		$product->product_override_price = NULL;
		$product->override = NULL;
		$product->categories = array();
		$product->shoppergroups = array();

		if ($front) {
			$product->link = '';

			$product->prices = array();
			$product->virtuemart_category_id = 0;
			$product->virtuemart_shoppergroup_id = 0;
			$product->mf_name = '';
			$product->packaging = '';
			$product->related = '';
			$product->box = '';
		}

		return $product;
	}

	/**
	 * Load  the product category
	 *
	 * @author Kohl Patrick,RolandD,Max Milbers
	 * @return array list of categories product is in
	 */
	public function getProductCategories ($virtuemart_product_id = 0, $front = FALSE) {

		$categories = array();
		if ($virtuemart_product_id > 0) {
			$q = 'SELECT pc.`virtuemart_category_id` FROM `#__virtuemart_product_categories` as pc';
			if ($front) {
				$q .= ' LEFT JOIN `#__virtuemart_categories` as c ON c.`virtuemart_category_id` = pc.`virtuemart_category_id`';
			}
			$q .= ' WHERE pc.`virtuemart_product_id` = ' . (int)$virtuemart_product_id;
			if ($front) {
				$q .= ' AND `published`=1';
			}
			$this->_db->setQuery ($q);
			$categories = $this->_db->loadResultArray ();
		}

		return $categories;
	}

	/**
	 * Load  the product shoppergroups
	 *
	 * @author Kohl Patrick,RolandD,Max Milbers, Cleanshooter
	 * @return array list of updateProductShoppergroupsTable that can view the product
	 */
	private function getProductShoppergroups ($virtuemart_product_id = 0) {

		$shoppergroups = array();
		if ($virtuemart_product_id > 0) {
			$q = 'SELECT `virtuemart_shoppergroup_id` FROM `#__virtuemart_product_shoppergroups` WHERE `virtuemart_product_id` = "' . (int)$virtuemart_product_id . '"';
			$this->_db->setQuery ($q);
			$shoppergroups = $this->_db->loadResultArray ();
		}

		return $shoppergroups;
	}

	/**
	 * Get the products in a given category
	 *
	 * @author RolandD
	 * @access public
	 * @param int $virtuemart_category_id the category ID where to get the products for
	 * @return array containing product objects
	 */
	public function getProductsInCategory ($categoryId) {

		$ids = $this->sortSearchListQuery (TRUE, $categoryId);
		$this->products = $this->getProducts ($ids);
		return $this->products;
	}


	/**
	 * Loads different kind of product lists.
	 * you can load them with calculation or only published onces, very intersting is the loading of groups
	 * valid values are latest, topten, featured.
	 *
	 * The function checks itself by the config if the user is allowed to see the price or published products
	 *
	 * @author Max Milbers
	 */
	public function getProductListing ($group = FALSE, $nbrReturnProducts = FALSE, $withCalc = TRUE, $onlyPublished = TRUE, $single = FALSE, $filterCategory = TRUE, $category_id = 0) {

		$app = JFactory::getApplication ();
		if ($app->isSite ()) {
			$front = TRUE;
			if (!class_exists ('Permissions')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			}
			if (!Permissions::getInstance ()->check ('admin', 'storeadmin')) {
				$onlyPublished = TRUE;
				if ($show_prices = VmConfig::get ('show_prices', 1) == '0') {
					$withCalc = FALSE;
				}
			}
		}
		else {
			$front = FALSE;
		}

		$this->setFilter ();
		if ($filterCategory === TRUE) {
			if ($category_id) {
				$this->virtuemart_category_id = $category_id;
			}
		}
		else {
			$this->virtuemart_category_id = FALSE;
		}
		$ids = $this->sortSearchListQuery ($onlyPublished, $this->virtuemart_category_id, $group, $nbrReturnProducts);

		//quickndirty hack for the BE list, we can do that, because in vm2.1 this is anyway fixed correctly
		$this->listing = TRUE;
		$products = $this->getProducts ($ids, $front, $withCalc, $onlyPublished, $single);
		$this->listing = FALSE;
		return $products;
	}

	/**
	 * overriden getFilter to persist filters
	 *
	 * @author OSP
	 */
	public function setFilter () {

		$app = JFactory::getApplication ();
		if (!$app->isSite ()) { //persisted filter only in admin
			$view = JRequest::getWord ('view');
			$mainframe = JFactory::getApplication ();
			$this->virtuemart_category_id = $mainframe->getUserStateFromRequest ('com_virtuemart.' . $view . '.filter.virtuemart_category_id', 'virtuemart_category_id', 0, 'int');
			$this->setState ('virtuemart_category_id', $this->virtuemart_category_id);
			$this->virtuemart_manufacturer_id = $mainframe->getUserStateFromRequest ('com_virtuemart.' . $view . '.filter.virtuemart_manufacturer_id', 'virtuemart_manufacturer_id', 0, 'int');
			$this->setState ('virtuemart_manufacturer_id', $this->virtuemart_manufacturer_id);
		}
		else {
			$this->virtuemart_category_id = JRequest::getInt ('virtuemart_category_id', FALSE);
		}
	}

	/**
	 * Returns products for given array of ids
	 *
	 * @author Max Milbers
	 * @param int $productIds
	 * @param boolean $front
	 * @param boolean $withCalc
	 * @param boolean $onlyPublished
	 */
	public function getProducts ($productIds, $front = TRUE, $withCalc = TRUE, $onlyPublished = TRUE, $single = FALSE) {

		if (empty($productIds)) {
			// 			vmdebug('getProducts has no $productIds','No ids given to get products');
			// 			vmTrace('getProducts has no $productIds');
			return array();
		}

		$maxNumber = VmConfig::get ('absMaxProducts', 700);
		$products = array();
		if ($single) {

			foreach ($productIds as $id) {
				$i = 0;
				if ($product = $this->getProductSingle ((int)$id, $front)) {
					$products[] = $product;
					$i++;
				}
				if ($i > $maxNumber) {
					vmdebug ('Better not to display more than ' . $maxNumber . ' products');
					return $products;
				}
			}
		}
		else {
			$i = 0;
			foreach ($productIds as $id) {
				if ($product = $this->getProduct ((int)$id, $front, $withCalc, $onlyPublished)) {
					$products[] = $product;
					$i++;
				}
				if ($i > $maxNumber) {
					vmdebug ('Better not to display more than ' . $maxNumber . ' products');
					return $products;
				}
			}
		}

		return $products;
	}


	/**
	 * This function retrieves the "neighbor" products of a product specified by $virtuemart_product_id
	 * Neighbors are the previous and next product in the current list
	 *
	 * @author RolandD, Max Milbers
	 * @param object $product The product to find the neighours of
	 * @return array
	 */
	public function getNeighborProducts ($product, $onlyPublished = TRUE, $max = 1) {

		$db = JFactory::getDBO ();
		$neighbors = array('previous' => '', 'next' => '');
		$direction = 'DESC';
		$op = '<';
		$app = JFactory::getApplication();
		if ($app->isSite ()) {
			$usermodel = VmModel::getModel ('user');
			$currentVMuser = $usermodel->getUser ();
			$virtuemart_shoppergroup_ids = (array)$currentVMuser->shopper_groups;
		}
		foreach ($neighbors as &$neighbor) {

			$q = 'SELECT `l`.`virtuemart_product_id`, `l`.`product_name`
				FROM `#__virtuemart_products` as `p`
				JOIN `#__virtuemart_products_' . VMLANG . '` as `l` using (`virtuemart_product_id`)
				JOIN `#__virtuemart_product_categories` as `pc` using (`virtuemart_product_id`)';
			if ($app->isSite ()) {
				$q .= '	LEFT JOIN `#__virtuemart_product_shoppergroups` as `psgr` on (`psgr`.`virtuemart_product_id`=`l`.`virtuemart_product_id`)';
			}

			$q .= '	WHERE `virtuemart_category_id` = ' . (int)$product->virtuemart_category_id;

			$q .= ' and `slug` ' . $op . ' "' . $product->slug . '" ';
			if ($app->isSite ()) {

				if (is_array ($virtuemart_shoppergroup_ids)) {
					$sgrgroups = array();
					foreach ($virtuemart_shoppergroup_ids as $key => $virtuemart_shoppergroup_id) {
						$sgrgroups[] = 'psgr.`virtuemart_shoppergroup_id`= "' . (int)$virtuemart_shoppergroup_id . '" ';
					}
					$sgrgroups[] = 'psgr.`virtuemart_shoppergroup_id` IS NULL ';
					$q .= " AND ( " . implode (' OR ', $sgrgroups) . " ) ";
				}
			}

			if ($onlyPublished) {
				$q .= ' AND p.`published`= 1';
			}

			if(!empty($this->orderByString)){
				$orderBy = $this->orderByString;
			} else {
				$orderBy = ' ORDER BY '.$this->filter_order.' ';
			}
			$q .=  $orderBy . $direction . ' LIMIT 0,' . (int)$max;

			$db->setQuery ($q);
			if ($result = $db->loadAssocList ()) {
				$neighbor = $result;
			}
			$err = $db->getErrorMsg();
			if($err){
				vmError('getNeighborProducts '.$err);
			}
			$direction = 'ASC';
			$op = '>';
 			//vmdebug('getNeighborProducts '.$db->getQuery());
			//vmdebug('getNeighborProducts '.$db->getErrorMsg());
		}

		return $neighbors;
	}


	/* reorder product in one category
	 * TODO this not work perfect ! (Note by Patrick Kohl)
	*/
	function saveorder ($cid = array(), $order, $filter = NULL) {

		JRequest::checkToken () or jexit ('Invalid Token');

		$virtuemart_category_id = JRequest::getInt ('virtuemart_category_id', 0);

		$q = 'SELECT `id`,`ordering` FROM `#__virtuemart_product_categories`
			WHERE virtuemart_category_id=' . (int)$virtuemart_category_id . '
			ORDER BY `ordering` ASC';
		$this->_db->setQuery ($q);
		$pkey_orders = $this->_db->loadObjectList ();

		$tableOrdering = array();
		foreach ($pkey_orders as $orderTmp) {
			$tableOrdering[$orderTmp->id] = $orderTmp->ordering;
		}
		// set and save new ordering
		foreach ($order as $key => $ord) {
			$tableOrdering[$key] = $ord;
		}
		asort ($tableOrdering);
		$i = 1;
		$ordered = 0;
		foreach ($tableOrdering as $key => $ord) {
// 			if ($order != $i) {
			$this->_db->setQuery ('UPDATE `#__virtuemart_product_categories`
					SET `ordering` = ' . $i . '
					WHERE `id` = ' . (int)$key . ' ');
			if (!$this->_db->query ()) {
				vmError ($this->_db->getErrorMsg ());
				return FALSE;
			}
			$ordered++;
// 			}
			$i++;
		}
		if ($ordered) {
			$msg = JText::sprintf ('COM_VIRTUEMART_ITEMS_MOVED', $ordered);
		}
		else {
			$msg = JText::_ ('COM_VIRTUEMART_ITEMS_NOT_MOVED');
		}
		JFactory::getApplication ()->redirect ('index.php?option=com_virtuemart&view=product&virtuemart_category_id=' . $virtuemart_category_id, $msg);

	}

	/**
	 * Moves the order of a record
	 *
	 * @param integer The increment to reorder by
	 */
	function move ($direction, $filter = NULL) {

		JRequest::checkToken () or jexit ('Invalid Token');

		// Check for request forgeries
		$table = $this->getTable ('product_categories');
		$table->move ($direction);

		JFactory::getApplication ()->redirect ('index.php?option=com_virtuemart&view=product&virtuemart_category_id=' . JRequest::getInt ('virtuemart_category_id', 0));
	}

	/**
	 * Store a product
	 *
	 * @author RolandD
	 * @author Max Milbers
	 * @access public
	 */
	public function store (&$product, $isChild = FALSE) {

		JRequest::checkToken () or jexit ('Invalid Token');


		if ($product) {
			$data = (array)$product;
		}

		if (!class_exists ('Permissions')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');

		$perm = Permissions::getInstance();
		$superVendor = $perm->isSuperVendor();
		if(empty($superVendor)){
			vmError('You are not a vendor or administrator, storing of product cancelled');
			return FALSE;
		}

		if (isset($data['intnotes'])) {
			$data['intnotes'] = trim ($data['intnotes']);
		}
		// Setup some place holders
		$product_data = $this->getTable ('products');

		//Set the product packaging
		if (array_key_exists ('product_packaging', $data)) {
			$data['product_packaging'] = str_replace(',','.',$data['product_packaging']);
		}

		//with the true, we do preloading and preserve so old values note by Max Milbers
	//	$product_data->bindChecknStore ($data, $isChild);

		$stored = $product_data->bindChecknStore ($data, TRUE);

		$errors = $product_data->getErrors ();
		if(!$stored or count($errors)>0){
			foreach ($errors as $error) {
				vmError ('Product store '.$error);
			}
			if(!$stored){
				vmError('You are not an administrator or the correct vendor, storing of product cancelled');
			}
			return FALSE;
		}


		$this->_id = $data['virtuemart_product_id'] = (int)$product_data->virtuemart_product_id;

		if (empty($this->_id)) {
			vmError('Product not stored, no id');
			return FALSE;
		}

		//We may need to change this, the reason it is not in the other list of commands for parents
		if (!$isChild) {
			if (!empty($data['save_customfields'])) {
				if (!class_exists ('VirtueMartModelCustomfields')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'customfields.php');
				}
				VirtueMartModelCustomfields::storeProductCustomfields ('product', $data, $product_data->virtuemart_product_id);
			}
		}

		// Get old IDS
		$this->_db->setQuery( 'SELECT `virtuemart_product_price_id` FROM `#__virtuemart_product_prices` WHERE virtuemart_product_id ='.$this->_id );
		$old_price_ids = $this->_db->loadResultArray();

		foreach($data['mprices']['product_price'] as $k => $product_price){

			$pricesToStore = array();
			$pricesToStore['virtuemart_product_id'] = $this->_id;
			$pricesToStore['virtuemart_product_price_id'] = (int)$data['mprices']['virtuemart_product_price_id'][$k];


			if (!$isChild){
				//$pricesToStore['basePrice'] = $data['mprices']['basePrice'][$k];
				$pricesToStore['product_override_price'] = $data['mprices']['product_override_price'][$k];
				$pricesToStore['override'] = (int)$data['mprices']['override'][$k];
				$pricesToStore['virtuemart_shoppergroup_id'] = (int)$data['mprices']['virtuemart_shoppergroup_id'][$k];
				$pricesToStore['product_tax_id'] = (int)$data['mprices']['product_tax_id'][$k];
				$pricesToStore['product_discount_id'] = (int)$data['mprices']['product_discount_id'][$k];
				$pricesToStore['product_currency'] = (int)$data['mprices']['product_currency'][$k];
				$pricesToStore['product_price_publish_up'] = $data['mprices']['product_price_publish_up'][$k];
				$pricesToStore['product_price_publish_down'] = $data['mprices']['product_price_publish_down'][$k];
				$pricesToStore['price_quantity_start'] = (int)$data['mprices']['price_quantity_start'][$k];
				$pricesToStore['price_quantity_end'] = (int)$data['mprices']['price_quantity_end'][$k];
			}

			if (!$isChild and isset($data['mprices']['use_desired_price'][$k]) and $data['mprices']['use_desired_price'][$k] == "1") {
				if (!class_exists ('calculationHelper')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
				}
				$calculator = calculationHelper::getInstance ();
				$pricesToStore['salesPrice'] = $data['mprices']['salesPrice'][$k];
				$pricesToStore['product_price'] = $data['mprices']['product_price'][$k] = $calculator->calculateCostprice ($this->_id, $pricesToStore);
				unset($data['mprices']['use_desired_price'][$k]);
			} else {
				if(isset($data['mprices']['product_price'][$k]) ){
					$pricesToStore['product_price'] = $data['mprices']['product_price'][$k];
				}

			}

			if (isset($data['mprices']['product_price'][$k]) and $data['mprices']['product_price'][$k]!='') {

				if ($isChild) {
					unset($data['mprices']['product_override_price'][$k]);
					unset($pricesToStore['product_override_price']);
					unset($data['mprices']['override'][$k]);
					unset($pricesToStore['override']);
				}

				//$data['mprices'][$k] = $data['virtuemart_product_id'];

				$this->updateXrefAndChildTables ($pricesToStore, 'product_prices',$isChild);

				$key = array_search($pricesToStore['virtuemart_product_price_id'], $old_price_ids );
				if ($key !== false ) unset( $old_price_ids[ $key ] );

			}
		}


		if ( count($old_price_ids) ) {
			// delete old unused Customfields
			$this->_db->setQuery( 'DELETE FROM `#__virtuemart_product_prices` WHERE `virtuemart_product_price_id` in ("'.implode('","', $old_price_ids ).'") ');
			$this->_db->query();
		}

		if (!empty($data['childs'])) {
			foreach ($data['childs'] as $productId => $child) {
				$child['product_parent_id'] = $data['virtuemart_product_id'];
				$child['virtuemart_product_id'] = $productId;
				$this->store ($child, TRUE);
			}
		}

		if (!$isChild) {

			$data = $this->updateXrefAndChildTables ($data, 'product_shoppergroups');

			$data = $this->updateXrefAndChildTables ($data, 'product_manufacturers');

			if (!empty($data['categories']) && count ($data['categories']) > 0) {
				$data['virtuemart_category_id'] = $data['categories'];
			}
			else {
				$data['virtuemart_category_id'] = array();
			}
			$data = $this->updateXrefAndChildTables ($data, 'product_categories');

			// Update waiting list
			//TODO what is this doing?
			if (!empty($data['notify_users'])) {
				if ($data['product_in_stock'] > 0 && $data['notify_users'] == '1') {
					$waitinglist = VmModel::getModel ('Waitinglist');
					$waitinglist->notifyList ($data['virtuemart_product_id']);
				}
			}

			// Process the images
			$mediaModel = VmModel::getModel ('Media');

			$mediaModel->storeMedia ($data, 'product');
			$errors = $mediaModel->getErrors ();
			foreach ($errors as $error) {
				vmError ($error);
			}

		}

		return $product_data->virtuemart_product_id;
	}

	public function updateXrefAndChildTables ($data, $tableName, $preload = FALSE) {

		JRequest::checkToken () or jexit ('Invalid Token');
		//First we load the xref table, to get the old data
		$product_table_Parent = $this->getTable ($tableName);
		//We must go that way, because the load function of the vmtablexarry
		// is working different.
		if($preload){
			//$product_table_Parent->setOrderable('ordering',false);
			$orderingA = $product_table_Parent->load($data['virtuemart_product_id']);

		/*	if(isset($orderingA) and isset($orderingA[0])){
				$product_table_Parent->ordering = $orderingA[0];
			}*/
			//$product_table_Parent->ordering = $product_table_Parent->load($data['virtuemart_product_id']);
			//vmdebug('my ordering ',$product_table_Parent->ordering);
		}
		$product_table_Parent->bindChecknStore ($data);
		$errors = $product_table_Parent->getErrors ();
		foreach ($errors as $error) {
			vmError ($error);
		}
		return $data;

	}

	/**
	 * This function creates a child for a given product id
	 *
	 * @author Max Milbers
	 * @author Patrick Kohl
	 * @param int id of parent id
	 */
	public function createChild ($id) {

		// created_on , modified_on
		$db = JFactory::getDBO ();
		$vendorId = 1;
		$childs = count ($this->getProductChildIds ($id));
		$db->setQuery ('SELECT `product_name`,`slug` FROM `#__virtuemart_products` JOIN `#__virtuemart_products_' . VMLANG . '` as l using (`virtuemart_product_id`) WHERE `virtuemart_product_id`=' . (int)$id);
		$parent = $db->loadObject ();
		$newslug = $parent->slug . $id . rand (1, 9);
		$data = array('product_name' => $parent->product_name, 'slug' => $newslug, 'virtuemart_vendor_id' => (int)$vendorId, 'product_parent_id' => (int)$id);

		$prodTable = $this->getTable ('products');
		$prodTable->bindChecknStore ($data);

		$langs = (array)VmConfig::get ('active_languages');
		if (count ($langs) > 1) {
			foreach ($langs as $lang) {
				$lang = str_replace ('-', '_', strtolower ($lang));
				$db->setQuery ('SELECT `product_name` FROM `#__virtuemart_products_' . $lang . '` WHERE `virtuemart_product_id` = "' . $prodTable->virtuemart_product_id . '" ');
				$res = $db->loadResult ();
				if (!$res) {
					$db->setQuery ('INSERT INTO `#__virtuemart_products_' . $lang . '` (`virtuemart_product_id`,`slug`) VALUES ("' . $prodTable->virtuemart_product_id . '","' . $newslug . '");');
					$db->query ();
					$err = $db->getErrorMsg ();
					if (!empty($err)) {
						vmError ('Database error: createChild ' . $err);
					}
				}
			}

		}
		return $data['virtuemart_product_id'];
	}

	/**
	 * Creates a clone of a given product id
	 *
	 * @author Max Milbers
	 * @param int $virtuemart_product_id
	 */

	public function createClone ($id) {

		//	if (is_array($cids)) $cids = array($cids);
		$product = $this->getProduct ($id, TRUE, FALSE, FALSE);
		$product->field = $this->productCustomsfieldsClone ($id);
// 		vmdebug('$product->field',$product->field);
		$product->virtuemart_product_id = $product->virtuemart_product_price_id = 0;
		//Lets check if the user is admin or the mainvendor
		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		$admin = Permissions::getInstance()->check('admin');
		if($admin){
			$product->created_on = false;
			$product->created_by = 0;
		}
		$product->slug = $product->slug . '-' . $id;
		$product->save_customfields = 1;
		$this->store ($product);
		return $this->_id;
	}

	/* look if whe have a product type */
	private function productCustomsfieldsClone ($virtuemart_product_id) {

		$this->_db = JFactory::getDBO ();
		$q = "SELECT * FROM `#__virtuemart_product_customfields`";
		$q .= " WHERE `virtuemart_product_id` = " . $virtuemart_product_id;
		$this->_db->setQuery ($q);
		$customfields = $this->_db->loadAssocList ();
		if ($customfields) {
			foreach ($customfields as &$customfield) {
				unset($customfield['virtuemart_product_id'], $customfield['virtuemart_customfield_id']);
			}
			return $customfields;
		}
		else {
			return NULL;
		}
	}

	/**
	 * removes a product and related table entries
	 *
	 * @author Max Milberes
	 */
	public function remove ($ids) {

		$table = $this->getTable ($this->_maintablename);

		$cats = $this->getTable ('product_categories');
		$customs = $this->getTable ('product_customfields');
		$manufacturers = $this->getTable ('product_manufacturers');
		$medias = $this->getTable ('product_medias');
		$prices = $this->getTable ('product_prices');
		$shop = $this->getTable ('product_shoppergroups');
		$rating = $this->getTable ('ratings');
		$review = $this->getTable ('rating_reviews');
		$votes = $this->getTable ('rating_votes');

		$ok = TRUE;
		foreach ($ids as $id) {

			$childIds = $this->getProductChildIds ($id);
			if (!empty($childIds)) {
				vmError (JText::_ ('COM_VIRTUEMART_PRODUCT_CANT_DELETE_CHILD'));
				$ok = FALSE;
				continue;
			}

			if (!$table->delete ($id)) {
				vmError ('Product delete ' . $table->getError ());
				$ok = FALSE;
			}

			if (!$cats->delete ($id)) {
				vmError ('Product delete categories ' . $cats->getError ());
				$ok = FALSE;
			}

			if (!$customs->delete ($id)) {
				vmError ('Product delete customs ' . $customs->getError ());
				$ok = FALSE;
			}

			if (!$manufacturers->delete ($id)) {
				vmError ('Product delete manufacturer ' . $manufacturers->getError ());
				$ok = FALSE;
			}

			if (!$medias->delete ($id)) {
				vmError ('Product delete medias ' . $medias->getError ());
				$ok = FALSE;
			}

			if (!$prices->delete ($id)) {
				vmError ('Product delete prices ' . $prices->getError ());
				$ok = FALSE;
			}

			if (!$shop->delete ($id)) {
				vmError ('Product delete shoppergroups ' . $shop->getError ());
				$ok = FALSE;
			}

			if (!$rating->delete ($id, 'virtuemart_product_id')) {
				vmError ('Product delete rating ' . $rating->getError ());
				$ok = FALSE;
			}

			if (!$review->delete ($id, 'virtuemart_product_id')) {
				vmError ('Product delete reviews ' . $review->getError ());
				$ok = FALSE;
			}
			if (!$votes->delete ($id, 'virtuemart_product_id')) {
				vmError ('Product delete votes ' . $votes->getError ());
				$ok = FALSE;
			}

			// delete plugin on product delete
			// $ok must be set to false if an error occurs
			JPluginHelper::importPlugin ('vmcustom');
			$dispatcher = JDispatcher::getInstance ();
			$dispatcher->trigger ('plgVmOnDeleteProduct', array($id, &$ok));
		}

		return $ok;
	}


	/**
	 * Gets the price for a variant
	 *
	 * @author Max Milbers
	 */
	public function getPrice ($product, $customVariant, $quantity) {

		$this->_db = JFactory::getDBO ();
		// 		vmdebug('strange',$product);
		if (!is_object ($product)) {
// 		vmError('deprecated use of getPrice');
			$product = $this->getProduct ($product, TRUE, FALSE, TRUE,$quantity);
// 		return false;
		}

		// Loads the product price details
		if (!class_exists ('calculationHelper')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		}
		$calculator = calculationHelper::getInstance ();

		// Add in the quantity in case the customfield plugins need it
		$product->quantity = $quantity;

		// Calculate the modificator
		$variantPriceModification = $calculator->calculateModificators ($product, $customVariant);

		$prices = $calculator->getProductPrices ($product, $variantPriceModification, $quantity);

		return $prices;

	}


	/**
	 * Get the Order By Select List
	 *
	 * notice by Max Milbers html tags should never be in a model. This function should be moved to a helper or simular,...
	 *
	 * @author Kohl Patrick
	 * @access public
	 * @param $fieds from config Back-end
	 * @return $orderByList
	 * Order,order By, manufacturer and category link List to echo Out
	 **/
	function getOrderByList ($virtuemart_category_id = FALSE) {

		$getArray = (JRequest::get ('get'));
		$link = '';
		$fieldLink = '';
		// remove setted variable
		unset ($getArray['globalCurrencyConverter'], $getArray['virtuemart_manufacturer_id'], $getArray['order'], $getArray['orderby']);

		// foreach ($getArray as $key => $value )
		// $fieldLink .= '&'.$key.'='.$value;
		foreach ($getArray as $key => $value) {
			if (is_array ($value)) {
				foreach ($value as $k => $v) {
					$fieldLink .= '&' . $key . '[' . $k . ']' . '=' . $v;
				}
			}
			else {
				$fieldLink .= '&' . $key . '=' . $value;
			}
		}
		$fieldLink[0] = "?";
		$fieldLink = 'index.php' . $fieldLink;
		$orderTxt = '';

		$order = JRequest::getWord ('order', 'ASC');
		if ($order == 'DESC') {
			$orderTxt .= '&order=' . $order;
		}

		$orderbyTxt = '';
		$orderby = JRequest::getVar ('orderby', VmConfig::get ('browse_orderby_field'));
		$orderbyCfg = VmConfig::get ('browse_orderby_field');
		if ($orderby != '' && $orderby != $orderbyCfg) {
			$orderbyTxt = '&orderby=' . $orderby;
		}

		$manufacturerTxt = '';
		$manufacturerLink = '';
		if (VmConfig::get ('show_manufacturers')) {
			$tmp = $this->_noLimit;
			$this->_noLimit = TRUE;

			$this->_noLimit = $tmp;

			// manufacturer link list

			$virtuemart_manufacturer_id = JRequest::getInt ('virtuemart_manufacturer_id', 0);
			if ($virtuemart_manufacturer_id != '') {
				$manufacturerTxt = '&virtuemart_manufacturer_id=' . $virtuemart_manufacturer_id;
			}

			// if ($mf_virtuemart_product_ids) {
			$query = 'SELECT DISTINCT l.`mf_name`,l.`virtuemart_manufacturer_id` FROM `#__virtuemart_manufacturers_' . VMLANG . '` as l';
			$query .= ' JOIN `#__virtuemart_product_manufacturers` AS pm using (`virtuemart_manufacturer_id`)';
			$query .= ' LEFT JOIN `#__virtuemart_products` as p ON p.`virtuemart_product_id` = pm.`virtuemart_product_id` ';
			$query .= ' LEFT JOIN `#__virtuemart_product_categories` as c ON c.`virtuemart_product_id` = pm.`virtuemart_product_id` ';
			$query .= ' WHERE p.`published` =1';
			if ($virtuemart_category_id) {
				$query .= ' AND c.`virtuemart_category_id` =' . (int)$virtuemart_category_id;
			}
			$query .= ' ORDER BY l.`mf_name`';
			$this->_db->setQuery ($query);
			$manufacturers = $this->_db->loadObjectList ();
			// 		vmdebug('my manufacturers',$this->_db->getQuery());
			$manufacturerLink = '';
			if (count ($manufacturers) > 0) {
				$manufacturerLink = '<div class="orderlist">';
				if ($virtuemart_manufacturer_id > 0) {
					$manufacturerLink .= '<div><a title="" href="' . JRoute::_ ($fieldLink . $orderTxt . $orderbyTxt) . '">' . JText::_ ('COM_VIRTUEMART_SEARCH_SELECT_ALL_MANUFACTURER') . '</a></div>';
				}
				if (count ($manufacturers) > 1) {
					foreach ($manufacturers as $mf) {
						$link = JRoute::_ ($fieldLink . '&virtuemart_manufacturer_id=' . $mf->virtuemart_manufacturer_id . $orderTxt . $orderbyTxt);
						if ($mf->virtuemart_manufacturer_id != $virtuemart_manufacturer_id) {
							$manufacturerLink .= '<div><a title="' . $mf->mf_name . '" href="' . $link . '">' . $mf->mf_name . '</a></div>';
						}
						else {
							$currentManufacturerLink = '<div class="title">' . JText::_ ('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') . '</div><div class="activeOrder">' . $mf->mf_name . '</div>';
						}
					}
				}
				elseif ($virtuemart_manufacturer_id > 0) {
					$currentManufacturerLink = '<div class="title">' . JText::_ ('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') . '</div><div class="activeOrder">' . $manufacturers[0]->mf_name . '</div>';
				}
				else {
					$currentManufacturerLink = '<div class="title">' . JText::_ ('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') . '</div><div class="Order"> ' . $manufacturers[0]->mf_name . '</div>';
				}
				$manufacturerLink .= '</div>';
			}
			// }
		}

		/* order by link list*/
		$orderByLink = '';
		$fields = VmConfig::get ('browse_orderby_fields');
		if (count ($fields) > 1) {
			$orderByLink = '<div class="orderlist">';
			foreach ($fields as $field) {
				if ($field != $orderby) {

					$dotps = strrpos ($field, '.');
					if ($dotps !== FALSE) {
						$prefix = substr ($field, 0, $dotps + 1);
						$fieldWithoutPrefix = substr ($field, $dotps + 1);
						// 				vmdebug('Found dot '.$dotps.' $prefix '.$prefix.'  $fieldWithoutPrefix '.$fieldWithoutPrefix);
					}
					else {
						$prefix = '';
						$fieldWithoutPrefix = $field;
					}

					$text = JText::_ ('COM_VIRTUEMART_' . strtoupper ($fieldWithoutPrefix));

					if ($field == $orderbyCfg) {
						$link = JRoute::_ ($fieldLink . $manufacturerTxt);
					}
					else {
						$link = JRoute::_ ($fieldLink . $manufacturerTxt . '&orderby=' . $field);
					}
					$orderByLink .= '<div><a title="' . $text . '" href="' . $link . '">' . $text . '</a></div>';
				}
			}
			$orderByLink .= '</div>';
		}

		/* invert order value set*/
		if ($order == 'ASC') {
			$orderlink = '&order=DESC';
			$orderTxt = JText::_ ('COM_VIRTUEMART_SEARCH_ORDER_DESC');
		}
		else {
			$orderTxt = JText::_ ('COM_VIRTUEMART_SEARCH_ORDER_ASC');
			$orderlink = '';
		}

		/* full string list */
		if ($orderby == '') {
			$orderby = $orderbyCfg;
		}
		$orderby = strtoupper ($orderby);
		$link = JRoute::_ ($fieldLink . $orderlink . $orderbyTxt . $manufacturerTxt);

		$dotps = strrpos ($orderby, '.');
		if ($dotps !== FALSE) {
			$prefix = substr ($orderby, 0, $dotps + 1);
			$orderby = substr ($orderby, $dotps + 1);
			// 				vmdebug('Found dot '.$dotps.' $prefix '.$prefix.'  $fieldWithoutPrefix '.$fieldWithoutPrefix);
		}
		else {
			$prefix = '';
			// 		$orderby = $orderby;
		}

		$orderByList = '<div class="orderlistcontainer"><div class="title">' . JText::_ ('COM_VIRTUEMART_ORDERBY') . '</div><div class="activeOrder"><a title="' . $orderTxt . '" href="' . $link . '">' . JText::_ ('COM_VIRTUEMART_SEARCH_ORDER_' . $orderby) . ' ' . $orderTxt . '</a></div>';
		$orderByList .= $orderByLink . '</div>';

		$manuList = '';
		if (VmConfig::get ('show_manufacturers')) {
			if (empty ($currentManufacturerLink)) {
				$currentManufacturerLink = '<div class="title">' . JText::_ ('COM_VIRTUEMART_PRODUCT_DETAILS_MANUFACTURER_LBL') . '</div><div class="activeOrder">' . JText::_ ('COM_VIRTUEMART_SEARCH_SELECT_MANUFACTURER') . '</div>';
			}
			$manuList = ' <div class="orderlistcontainer">' . $currentManufacturerLink;
			$manuList .= $manufacturerLink . '</div><div class="clear"></div>';

		}

		return array('orderby'=> $orderByList, 'manufacturer'=> $manuList);
	}


// **************************************************
//Stocks
//
	/**
	 * Get the stock level for a given product
	 *
	 * @author RolandD
	 * @access public
	 * @param object $product the product to get stocklevel for
	 * @return array containing product objects
	 */
	public function getStockIndicator ($product) {

		$this->_db = JFactory::getDBO ();

		/* Assign class to indicator */
		$stock_level = $product->product_in_stock - $product->product_ordered;
		$reorder_level = $product->low_stock_notification;
		$level = 'normalstock';
		$stock_tip = JText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_NORMAL_TIP');
		if ($stock_level <= $reorder_level) {
			$level = 'lowstock';
			$stock_tip = JText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_LOW_TIP');
		}
		if ($stock_level <= 0) {
			$level = 'nostock';
			$stock_tip = JText::_ ('COM_VIRTUEMART_STOCK_LEVEL_DISPLAY_OUT_TIP');
		}
		$stock = new Stdclass();
		$stock->stock_tip = $stock_tip;
		$stock->stock_level = $level;
		return $stock;
	}


	public function updateStockInDB ($product, $amount, $signInStock, $signOrderedStock) {

// 	vmdebug( 'stockupdate in DB', $product->virtuemart_product_id,$amount, $signInStock, $signOrderedStock );
		$validFields = array('=', '+', '-');
		if (!in_array ($signInStock, $validFields)) {
			return FALSE;
		}
		if (!in_array ($signOrderedStock, $validFields)) {
			return FALSE;
		}
		//sanitize fields
		$id = (int)$product->virtuemart_product_id;

		$amount = (float)$amount;
		$update = array();

		if ($signInStock != '=' or $signOrderedStock != '=') {

			if ($signInStock != '=') {
				$update[] = '`product_in_stock` = `product_in_stock` ' . $signInStock . $amount;

				if (strpos ($signInStock, '+') !== FALSE) {
					$signInStock = '-';
				}
				else {
					$signInStock = '+';
				}
				$update[] = '`product_sales` = `product_sales` ' . $signInStock . $amount;

			}
			if ($signOrderedStock != '=') {
				$update[] = '`product_ordered` = `product_ordered` ' . $signOrderedStock . $amount;
			}
			$q = 'UPDATE `#__virtuemart_products` SET ' . implode (", ", $update) . ' WHERE `virtuemart_product_id` = ' . $id;

			$this->_db->setQuery ($q);
			$this->_db->query ();

			//The low on stock notification comes now, when the people ordered.
			//You need to know that the stock is going low before you actually sent the wares, because then you ususally know it already yoursefl
			//note by Max Milbers
			if ($signInStock == '+') {

				$this->_db->setQuery ('SELECT (`product_in_stock`+`product_ordered`) < `low_stock_notification` '
						. 'FROM `#__virtuemart_products` '
						. 'WHERE `virtuemart_product_id` = ' . $id
				);
				if ($this->_db->loadResult () == 1) {
					$this->lowStockWarningEmail( $id) ;
				}
			}
		}

	}
function lowStockWarningEmail($virtuemart_product_id) {

	if(VmConfig::get('lstockmail',TRUE)){
		if (!class_exists ('shopFunctionsF')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
		}

		/* Load the product details */
		$q = "SELECT l.product_name,product_in_stock FROM `#__virtuemart_products_" . VMLANG . "` l
				JOIN `#__virtuemart_products` p ON p.virtuemart_product_id=l.virtuemart_product_id
			   WHERE p.virtuemart_product_id = " . $virtuemart_product_id;
		$this->_db->setQuery ($q);
		$vars = $this->_db->loadAssoc ();

		$url = JURI::root () . 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $virtuemart_product_id;
		$link = '<a href="'. $url.'">'. $vars['product_name'].'</a>';
		$vars['subject'] = JText::sprintf('COM_VIRTUEMART_PRODUCT_LOW_STOCK_EMAIL_SUBJECT',$vars['product_name']);
		$vars['mailbody'] =JText::sprintf('COM_VIRTUEMART_PRODUCT_LOW_STOCK_EMAIL_BODY',$link, $vars['product_in_stock']);

		$virtuemart_vendor_id = 1;
		$vendorModel = VmModel::getModel ('vendor');
		$vendor = $vendorModel->getVendor ($virtuemart_vendor_id);
		$vendorModel->addImages ($vendor);
		$vars['vendor'] = $vendor;

		$vars['vendorAddress']= shopFunctions::renderVendorAddress($virtuemart_vendor_id);
		$vars['vendorEmail'] = $vendorModel->getVendorEmail ($virtuemart_vendor_id);

		$vars['user'] =  $vendor->vendor_store_name ;
		shopFunctionsF::renderMail ('productdetails', $vars['vendorEmail'], $vars, 'productdetails', TRUE) ;

		return TRUE;
	} else {
		return FALSE;
	}

}

	public function getUncategorizedChildren ($withParent) {
		if (empty($this->_uncategorizedChildren)) {

			//Todo add check for shoppergroup depended product display
			$q = 'SELECT * FROM `#__virtuemart_products` as p
				LEFT JOIN `#__virtuemart_products_' . VMLANG . '` as pl
				USING (`virtuemart_product_id`)
				LEFT JOIN `#__virtuemart_product_categories` as pc
				USING (`virtuemart_product_id`) ';

//	 		$q .= ' WHERE (`product_parent_id` = "'.$this->_id.'" AND (pc.`virtuemart_category_id`) IS NULL  ) OR (`virtuemart_product_id` = "'.$this->_id.'" ) ';
			if ($withParent) {
				$q .= ' WHERE (`product_parent_id` = "' . $this->_id . '"  OR `virtuemart_product_id` = "' . $this->_id . '") ';
			}
			else {
				$q .= ' WHERE `product_parent_id` = "' . $this->_id . '" ';
			}

			$app = JFactory::getApplication ();
			if ($app->isSite () && !VmConfig::get ('use_as_catalog', 0) && VmConfig::get ('stockhandle', 'none') == 'disableit') {
				$q .= ' AND p.`product_in_stock`>"0" ';
			}

			if ($app->isSite ()) {

				$q .= ' AND p.`published`="1"';
			}

			$q .= ' GROUP BY `virtuemart_product_id` ORDER BY p.pordering ASC';
			$this->_db->setQuery ($q);
			$this->_uncategorizedChildren = $this->_db->loadAssocList ();

			$err = $this->_db->getErrorMsg ();
			if (!empty($err)) {
				vmError ('getUncategorizedChildren sql error ' . $err, 'getUncategorizedChildren sql error');
				vmdebug ('getUncategorizedChildren ' . $err);
				return FALSE;
			}
 //			vmdebug('getUncategorizedChildren '.$this->_db->getQuery(),$this->_uncategorizedChildren);
		}
		return $this->_uncategorizedChildren;
	}

	/**
	 * Check if the product has any children
	 *
	 * @author RolandD
	 * @author Max Milbers
	 * @param int $virtuemart_product_id Product ID
	 * @return bool True if there are child products, false if there are no child products
	 */
	public function checkChildProducts ($virtuemart_product_id) {

		$q = 'SELECT IF(COUNT(virtuemart_product_id) > 0, "0", "1") FROM `#__virtuemart_products` WHERE `product_parent_id` = "' . (int)$virtuemart_product_id . '"';
		$this->_db->setQuery ($q);

		return $this->_db->loadResult ();

	}

// use lang table only TODO Look if this not cause errors
	function getProductChilds ($product_id) {

		if (empty($product_id)) {
			return array();
		}
		$db = JFactory::getDBO ();
		$db->setQuery (' SELECT virtuemart_product_id, product_name FROM `#__virtuemart_products_' . VMLANG . '`
			JOIN `#__virtuemart_products` as C using (`virtuemart_product_id`)
			WHERE `product_parent_id` =' . (int)$product_id);
		return $db->loadObjectList ();

	}

	function getProductChildIds ($product_id) {

		if (empty($product_id)) {
			return array();
		}
		$db = JFactory::getDBO ();
		$db->setQuery (' SELECT virtuemart_product_id FROM `#__virtuemart_products` WHERE `product_parent_id` =' . (int)$product_id.' ORDER BY pordering ASC');

		return $db->loadResultArray ();

	}

// use lang table only TODO Look if this not cause errors
	function getProductParent ($product_parent_id) {

		if (empty($product_parent_id)) {
			return array();
		}
		$product_parent_id = (int)$product_parent_id;
		$db = JFactory::getDBO ();
		$db->setQuery (' SELECT * FROM `#__virtuemart_products_' . VMLANG . '` WHERE `virtuemart_product_id` =' . $product_parent_id);
		return $db->loadObject ();
	}


	function sentProductEmailToShoppers () {

		jimport ('joomla.utilities.arrayhelper');
		if (!class_exists ('ShopFunctions')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		}

		$product_id = JRequest::getVar ('virtuemart_product_id', '');
		vmdebug ('sentProductEmailToShoppers product id', $product_id);
		$vars = array();
		$vars['subject'] = JRequest::getVar ('subject');
		$vars['mailbody'] = JRequest::getVar ('mailbody');

		$order_states = JRequest::getVar ('statut', array(), '', 'ARRAY');
		$productShoppers = $this->getProductShoppersByStatus ($product_id, $order_states);
		vmdebug ('productShoppers ', $productShoppers);

		$productModel = VmModel::getModel ('product');
		$product = $productModel->getProduct ($product_id);

		$vendorModel = VmModel::getModel ('vendor');
		$vendor = $vendorModel->getVendor ($product->virtuemart_vendor_id);
		$vendorModel->addImages ($vendor);
		$vars['vendor'] = $vendor;
		$vars['vendorEmail'] = $vendorModel->getVendorEmail ($product->virtuemart_vendor_id);
		$vars['vendorAddress'] = shopFunctions::renderVendorAddress ($product->virtuemart_vendor_id);

		$orderModel = VmModel::getModel ('orders');
		foreach ($productShoppers as $productShopper) {
			$vars['user'] = $productShopper['name'];
			if (shopFunctionsF::renderMail ('productdetails', $productShopper['email'], $vars, 'productdetails', TRUE)) {
				$string = 'COM_VIRTUEMART_MAIL_SEND_SUCCESSFULLY';
			}
			else {
				$string = 'COM_VIRTUEMART_MAIL_NOT_SEND_SUCCESSFULLY';
			}
			/* Update the order history  for each order */
			foreach ($productShopper['order_info'] as $order_info) {
				$orderModel->_updateOrderHist ($order_info['order_id'], $order_info['order_status'], 1, $vars['subject'] . ' ' . $vars['mailbody']);
			}
			// todo: when there is an error while sending emails
			//vmInfo (JText::sprintf ($string, $productShopper['email']));
		}

	}


	public function getProductShoppersByStatus ($product_id, $states) {

		if (empty($states)) {
			return FALSE;
		}
		$orderstatusModel = VmModel::getModel ('orderstatus');
		$orderStates = $orderstatusModel->getOrderStatusNames ();

		foreach ($states as &$status) {
			if (!array_key_exists ($status, $orderStates)) {
				unset($status);
			}
		}
		if (empty($states)) {
			return FALSE;
		}

		$q = 'SELECT ou.* , oi.product_quantity , o.order_number, o.order_status, oi.`order_status` AS order_item_status ,
		o.virtuemart_order_id FROM `#__virtuemart_order_userinfos` as ou
			JOIN `#__virtuemart_order_items` AS oi USING (`virtuemart_order_id`)
			JOIN `#__virtuemart_orders` AS o ON o.`virtuemart_order_id` =  oi.`virtuemart_order_id`
			WHERE ou.`address_type`="BT" AND oi.`virtuemart_product_id`=' . (int)$product_id;
		if (count ($orderStates) !== count ($states)) {
			$q .= ' AND oi.`order_status` IN ( "' . implode ('","', $states) . '") ';
		}
		$q .= '  ORDER BY ou.`email` ASC';
		$this->_db->setQuery ($q);
		$productShoppers = $this->_db->loadAssocList ();

		$shoppers = array();
		foreach ($productShoppers as $productShopper) {
			$key = $productShopper['email'];
			if (!array_key_exists ($key, $shoppers)) {
				$shoppers[$key]['phone'] = !empty($productShopper['phone_1']) ? $productShopper['phone_1'] : (!empty($productShopper['phone_2']) ? $productShopper['phone_2'] : '-');
				$shoppers[$key]['name'] = $productShopper['first_name'] . ' ' . $productShopper['last_name'];
				$shoppers[$key]['email'] = $productShopper['email'];
				$shoppers[$key]['mail_to'] = 'mailto:' . $productShopper['email'];
				$shoppers[$key]['nb_orders'] = 0;
			}
			$i = $shoppers[$key]['nb_orders'];
			$shoppers[$key]['order_info'][$i]['order_number'] = $productShopper['order_number'];
			$shoppers[$key]['order_info'][$i]['order_id'] = $productShopper['virtuemart_order_id'];
			$shoppers[$key]['order_info'][$i]['order_status'] = $productShopper['order_status'];
			$shoppers[$key]['order_info'][$i]['order_item_status_name'] = $orderStates[$productShopper['order_item_status']]['order_status_name'];
			$shoppers[$key]['order_info'][$i]['quantity'] = $productShopper['product_quantity'];
			$shoppers[$key]['nb_orders']++;
		}
		return $shoppers;
	}
}
// No closing tag