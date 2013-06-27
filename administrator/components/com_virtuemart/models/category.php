<?php
/**
*
* Category Model
*
* @package	VirtueMart
* @subpackage Category
* @author jseros, RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: category.php 6396 2012-09-05 17:35:36Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');

/**
 * Model for product categories
 * @author jseros
 */
class VirtueMartModelCategory extends VmModel {

	private $_category_tree;
	public $_cleanCache = true ;

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('categories');

		$this->addvalidOrderingFieldName(array('category_name','category_description','c.ordering','cx.category_shared','c.published'));

		$toCheck = VmConfig::get('browse_cat_orderby_field','category_name');
		if(!in_array($toCheck, $this->_validOrderingFieldName)){
			$toCheck = 'category_name';
		}
		$this->_selectedOrdering = $toCheck;
		$this->_selectedOrderingDir = 'ASC';

		$this->setToggleName('shared');

	}


    /**
     * Retrieve the detail record for the current $id if the data has not already been loaded.
     *
     * @author RickG, jseros, RolandD, Max Milbers
     */
	public function getCategory($virtuemart_category_id=0,$childs=TRUE){

		if(!empty($virtuemart_category_id)) $this->setId((int)$virtuemart_category_id);

  		if (empty($this->_data)) {
   			$this->_data = $this->getTable('categories');
   			$this->_data->load((int)$this->_id);

   			$xrefTable = $this->getTable('category_medias');
   			$this->_data->virtuemart_media_id = $xrefTable->load((int)$this->_id);

   			if($xrefTable->getError()) vmError($xrefTable->getError());

   			if(empty($this->_data->category_template)){
   				$this->_data->category_template = VmConfig::get('categorytemplate');
   			}

   			if(empty($this->_data->category_layout)){
   				$this->_data->category_layout = VmConfig::get('categorylayout');
   			}

   			if($childs){
   				$this->_data->haschildren = $this->hasChildren($this->_id);

   				/* Get children if they exist */
   				if ($this->_data->haschildren) $this->_data->children = $this->getCategories(true,$this->_id);
   				else $this->_data->children = null;

   				/* Get the product count */
   				$this->_data->productcount = $this->countProducts($this->_id);

   				/* Get parent for breatcrumb */
   				$this->_data->parents = $this->getParentsList($this->_id);

   			}

   			if($errs = $this->getErrors()){
   				$app = JFactory::getApplication();
   				foreach($errs as $err){
   					$app->enqueueMessage($err);
   				}
   			}
  		}


  		return $this->_data;

	}

    /**
	 * Get the list of child categories for a given category
	 *
	 * @param int $virtuemart_category_id Category id to check for child categories
	 * @return object List of objects containing the child categories
	 */
	static public function getChildCategoryList($vendorId, $virtuemart_category_id) {

		$key = (int)$vendorId.'_'.(int)$virtuemart_category_id ;

		static $_childCateogryList = array ();
      if (! array_key_exists ($key,$_childCateogryList)){

			$query = 'SELECT L.* FROM `#__virtuemart_categories_'.VMLANG.'` as L
						JOIN `#__virtuemart_categories` as C using (`virtuemart_category_id`)';
			$query .= ' LEFT JOIN `#__virtuemart_category_categories` as CC on C.`virtuemart_category_id` = CC.`category_child_id`';
			$query .= 'WHERE CC.`category_parent_id` = ' . (int)$virtuemart_category_id . ' ';
			//$query .= 'AND C.`virtuemart_category_id` = CC.`category_child_id` ';
			$query .= 'AND C.`virtuemart_vendor_id` = ' . (int)$vendorId . ' ';
			$query .= 'AND C.`published` = 1 ';
			$query .= ' ORDER BY C.`ordering`, L.`category_name` ASC';

			$db = JFactory::getDBO();
			$db->setQuery( $query);
			$childList = $db->loadObjectList();
// 			$childList = $this->_getList( $query );

			if(!empty($childList)){
				if(!class_exists('TableCategory_medias'))require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'category_medias.php');
				foreach($childList as $child){
					$xrefTable = new TableCategory_medias($db);
// 					$xrefTable = $this->getTable('category_medias');
					$child->virtuemart_media_id = $xrefTable->load($child->virtuemart_category_id);
				}
			}

			$_childCateogryList[$key]=$childList ;
		}
		return $_childCateogryList[$key];
	}

// 	public sortArraysPerXref(){

// 		$q = 'SELECT * FROM '
// 	}

	public function getCategoryTree($parentId=0, $level = 0, $onlyPublished = true,$keyword = ''){

		$sortedCats = array();

		$limits = $this->setPaginationLimits();
		$limitStart = $limits[0];
		$limit = $limits[1];

// 		vmRam('What take the cats?');
		$this->_noLimit = true;
		if($keyword!=''){
			$sortedCats = self::getCategories($onlyPublished, false, false, $keyword);
		} else {

			$this->rekurseCats($parentId,$level,$onlyPublished,$keyword,$sortedCats);
		}

		$this->_noLimit = false;
		$this->_total = count($sortedCats);
// 		vmRam('What take the cats?');

// 		vmdebug('getCategoryTree $limitStart',$limitStart,$limit);
		$this->_limitStart = $limitStart;
		$this->_limit = $limit;

		$this->getPagination();

		if(empty($limit)){
			return $sortedCats;
		} else {
			$sortedCats = array_slice($sortedCats, $limitStart,$limit);
			return $sortedCats;
		}

	}

	public function rekurseCats($virtuemart_category_id,$level,$onlyPublished,$keyword,&$sortedCats){
		$level++;

		if($this->hasChildren($virtuemart_category_id)){

			$childCats = self::getCategories($onlyPublished, $virtuemart_category_id, false, $keyword);

			if(!empty($childCats)){
				foreach ($childCats as $key => $category) {
					$category->level = $level;
					$sortedCats[] = $category;
					$this->rekurseCats($category->virtuemart_category_id,$level,$onlyPublished,$keyword,$sortedCats);
				}
			}
		}

	}


	public function getCategories($onlyPublished = true, $parentId = false, $childId = false, $keyword = "") {

		$vendorId = 1;

		$select = ' c.`virtuemart_category_id`, l.`category_description`, l.`category_name`, c.`ordering`, c.`published`, cx.`category_child_id`, cx.`category_parent_id`, c.`shared` ';

		$joinedTables = ' FROM `#__virtuemart_categories_'.VMLANG.'` l
				  JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)
				  LEFT JOIN `#__virtuemart_category_categories` AS cx
				  ON l.`virtuemart_category_id` = cx.`category_child_id` ';

		$where = array();

		if( $onlyPublished ) {
			$where[] = " c.`published` = 1 ";
		}
		if( $parentId !== false ){
			$where[] = ' cx.`category_parent_id` = '. (int)$parentId;
		}

		if( $childId !== false ){
			$where[] = ' cx.`category_child_id` = '. (int)$childId;
		}

		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		if( !Permissions::getInstance()->check('admin') ){
			$where[] = ' (c.`virtuemart_vendor_id` = "'. (int)$vendorId. '" OR c.`shared` = "1") ';
		}

		if( !empty( $keyword ) ) {
			$keyword = '"%' . $this->_db->getEscaped( $keyword, true ) . '%"' ;
			//$keyword = $this->_db->Quote($keyword, false);
			$where[] = ' ( l.`category_name` LIKE '.$keyword.'
							   OR l.`category_description` LIKE '.$keyword.') ';
		}

		$whereString = '';
		if (count($where) > 0){
			$whereString = ' WHERE '.implode(' AND ', $where) ;
		} else {
			$whereString = 'WHERE 1 ';
		}

		$ordering = $this->_getOrdering();

		$this->_category_tree = $this->exeSortSearchListQuery(0,$select,$joinedTables,$whereString,'',$ordering );

		return $this->_category_tree;

	}



	/**
	 * Sorts an array with categories so the order of the categories is the same as in a tree.
	 *
	 * @author jseros
	 *
	 * @param array $this->_category_tree
	 * @return associative array ordering categories
	 * @deprecated
	 */
	public function sortCategoryTree($categoryArr){

		/** FIRST STEP
	    * Order the Category Array and build a Tree of it
	    **/
		$idList = array();
		$rowList = array();
		$depthList = array();

		$children = array();
		$parentIds = array();
		$parentIdsHash = array();
		$parentId = 0;

		for( $i = 0, $nrows = count($categoryArr); $i < $nrows; $i++ ) {
			$parentIds[$i] = $categoryArr[$i]->category_parent_id;

			if($categoryArr[$i]->category_parent_id == 0){
				array_push($idList, $categoryArr[$i]->category_child_id);
				array_push($rowList, $i);
				array_push($depthList, 0);
			}

			$parentId = $parentIds[$i];

			if( isset($parentIdsHash[$parentId] )){
				$parentIdsHash[$parentId][$categoryArr[$i]->category_child_id] = $i;
			}
			else{
				$parentIdsHash[$parentId] = array($categoryArr[$i]->category_child_id => $i);
			}

		}

		$loopCount = 0;
		$watch = array(); // Hash to store children

		while( count($idList) < $nrows ){
			if( $loopCount > $nrows ) break;

			$idTemp = array();
			$rowTemp = array();
			$depthTemp = array();

			for($i = 0, $cIdlist = count($idList); $i < $cIdlist ; $i++) {
				$id = $idList[$i];
				$row = $rowList[$i];
				$depth = $depthList[$i];

				array_push($idTemp, $id);
				array_push($rowTemp, $row);
				array_push($depthTemp, $depth);

				$children = @$parentIdsHash[$id];

				if( !empty($children) ){
					foreach($children as $key => $value) {

						if( !isset($watch[$id][$key]) ){
							$watch[$id][$key] = 1;
							array_push($idTemp, $key);
							array_push($rowTemp, $value);
							array_push($depthTemp, $depth + 1);
						}
					}
				}
			}
			$idList = $idTemp;
			$rowList = $rowTemp;
			$depthList = $depthTemp;
			$loopCount++;
		}

		return array('id_list' => $idList,
					 'row_list' => $rowList,
					 'depth_list' => $depthList,
					 'categories' => $categoryArr
		);
	}


	/**
	* count the products in a category
	*
	* @author RolandD, Max Milbers
	* @return array list of categories product is in
	*/
	public function countProducts($cat_id=0) {

		if(!empty($this->_db))$this->_db = JFactory::getDBO();
		$vendorId = 1;
		if ($cat_id > 0) {
			$q = 'SELECT count(#__virtuemart_products.virtuemart_product_id) AS total
			FROM `#__virtuemart_products`, `#__virtuemart_product_categories`
			WHERE `#__virtuemart_products`.`virtuemart_vendor_id` = "'.(int)$vendorId.'"
			AND `#__virtuemart_product_categories`.`virtuemart_category_id` = '.(int)$cat_id.'
			AND `#__virtuemart_products`.`virtuemart_product_id` = `#__virtuemart_product_categories`.`virtuemart_product_id`
			AND `#__virtuemart_products`.`published` = "1" ';
			$this->_db->setQuery($q);
			$count = $this->_db->loadResult();
		} else $count=0 ;

		return $count;
	}


    /**
	 * Order any category
	 *
     * @author jseros
     * @param  int $id category id
     * @param  int $movement movement number
	 * @return bool
	 */
	public function orderCategory($id, $movement){
		//retrieving the category table object
		//and loading data
		$row = $this->getTable('categories');
		$row->load($id);

		$query = 'SELECT `category_parent_id` FROM `#__virtuemart_category_categories` WHERE `category_child_id` = '. (int)$row->virtuemart_category_id ;
		$this->_db->setQuery($query);
		$parent = $this->_db->loadObject();

		if (!$row->move( $movement, $parent->category_parent_id)) {
			vmError($row->getError());
			return false;
		}

		return true;
	}


	/**
	 * Order category group
	 *
     * @author jseros
     * @param  array $cats categories to order
	 * @return bool
	 */
	public function setOrder($cats, $order){
		$total		= count( $cats );
		$groupings	= array();
		$row = $this->getTable('categories');

		$query = 'SELECT `category_parent_id` FROM `#__virtuemart_categories` c
				  LEFT JOIN `#__virtuemart_category_categories` cx
				  ON c.`virtuemart_category_id` = cx.`category_child_id`
			      WHERE c.`virtuemart_category_id` = %s';

		// update ordering values
		for( $i=0; $i < $total; $i++ ) {

			$row->load( $cats[$i] );
			$this->_db->setQuery( sprintf($query,  (int)$cats[$i] ), 0 ,1 );
			$parent = $this->_db->loadObject();

			$groupings[] = $parent->category_parent_id;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->toggle('ordering',$row->ordering)) {
					vmError($row->getError());
					return false;
				}
			}
		}

		// execute reorder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder($group);
		}

		return true;
	}

    /**
     * Retrieve the detail record for the parent category of $categoryd
     *
     * @author jseros
     *
     * @param int $categoryId Child category id
     * @return JTable parent category data
     */
	public function getParentCategory( $categoryId = 0 ){
		$data = $this->getRelationInfo( $categoryId );
		$parentId = isset($data->category_parent_id) ? $data->category_parent_id : 0;

     	$parent = $this->getTable('categories');
  		$parent->load((int) $parentId);

  		return $parent;
	}


    /**
     * Retrieve category child-parent relation record
     *
     * @author jseros
     *
     * @param int $virtuemart_category_id
     * @return object Record of parent relation
     */
    public function getRelationInfo( $virtuemart_category_id = 0 ){
    	$virtuemart_category_id = (int) $virtuemart_category_id;

    	$query = 'SELECT `category_parent_id`, `ordering`
    			  FROM `#__virtuemart_category_categories`
    			  WHERE `category_child_id` = '. $this->_db->Quote($virtuemart_category_id);
    	$this->_db->setQuery($query);

    	return $this->_db->loadObject();
    }


    /**
	 * Bind the post data to the category table and save it
     *
     * @author jseros, RolandD, Max Milbers
     * @return int category id stored
	 */
    public function store(&$data) {

    	JRequest::checkToken() or jexit( 'Invalid Token, in store category');

		$table = $this->getTable('categories');

/*		vmdebug('categorytemplate to null',VmConfig::get('categorytemplate'),$data['category_template']);
 * VmConfig::get('categorytemplate') = default
 * $data['category_template'] = 0
 */
		if ( !array_key_exists ('category_template' , $data ) ){
			$data['category_template'] = $data['category_layout'] = $data['category_product_layout'] = 0 ;
		}
		if(VmConfig::get('categorytemplate') == $data['category_template'] ){
			$data['category_template'] = 0;
		}

		if(VmConfig::get('categorylayout') == $data['category_layout']){
			$data['category_layout'] = 0;
		}

		if(VmConfig::get('productlayout') == $data['category_product_layout']){
			$data['category_product_layout'] = 0;
		}

// 		vmdebug('category store ',$data);
		$table->bindChecknStore($data);
    	$errors = $table->getErrors();
		foreach($errors as $error){
			vmError($error);
		}

		if(!empty($data['virtuemart_category_id'])){
			$xdata['category_child_id'] = (int)$data['virtuemart_category_id'];
			$xdata['category_parent_id'] = empty($data['category_parent_id'])? 0:(int)$data['category_parent_id'];
			$xdata['ordering'] = empty($data['ordering'])? 0: (int)$data['ordering'];

    		$table = $this->getTable('category_categories');

			$table->bindChecknStore($xdata);
	    	$errors = $table->getErrors();
			foreach($errors as $error){
				vmError($error);
			}
		}

		// Process the images
		$mediaModel = VmModel::getModel('Media');
		$file_id = $mediaModel->storeMedia($data,'category');
      $errors = $mediaModel->getErrors();
		foreach($errors as $error){
			vmError($error);
		}
		if ($this->_cleanCache === true) {
			$cache = JFactory::getCache();
			$cache->clean('_virtuemart');
			$this->_cleanCache = false;
		}
		//jexit();
		return $data['virtuemart_category_id'] ;
	}

	/**
     * Delete all categories selected
     *
     * @author jseros
     * @param  array $cids categories to remove
     * @return boolean if the item remove was successful
     */
    public function remove($cids) {

    	JRequest::checkToken() or jexit( 'Invalid Token, in remove category');

		$table = $this->getTable('categories');

		foreach($cids as $cid) {
		    if( $this->clearProducts($cid) ) {
				if (!$table->delete($cid)) {
				    vmError($table->getError());
				    return false;
				}
// TODO MULTI LANGUE REMOVE
				//deleting relations
				$query = "DELETE FROM `#__virtuemart_product_categories` WHERE `virtuemart_category_id` = ". (int)$cid;
		    	$this->_db->setQuery($query);

		    	if(!$this->_db->query()){
		    		vmError( $this->_db->getErrorMsg() );
		    	}

		    	//updating parent relations
				$query = "UPDATE `#__virtuemart_product_categories` SET `virtuemart_category_id` = 0 WHERE `virtuemart_category_id` = ". (int)$cid;
		    	$this->_db->setQuery($query);

		    	if(!$this->_db->query()){
		    		vmError( $this->_db->getErrorMsg() );
		    	}
		    }
		    else {
				vmError('Could not clear category products');
				return false;
		    }
		}
		return true;
    }


	/**
     * Delete all relations between categories and products
     *
     * @author jseros
     *
     * @param  int $cid categories to remove
     * @return boolean if the item remove was successful
     */
    public function clearProducts($cid) {

    	$query = "UPDATE `#__virtuemart_product_categories` SET `virtuemart_category_id` = 0 WHERE `virtuemart_category_id` =" . (int)$cid;
		$this->_db->setQuery($query);

		if( !$this->_db->query() ){
			return false;
		}

		return true;
    }


	/**
	 * Stuff of categorydetails
	 */

	/* array container for category tree ID*/
	var $container = array();


	/**
	* Checks for children of the category $virtuemart_category_id
	*
	* @author RolandD
	* @param int $virtuemart_category_id the category ID to check
	* @return boolean true when the category has childs, false when not
	*/
	public function hasChildren($virtuemart_category_id) {
// 		vmSetStartTime('hasChildren');
		$db = JFactory::getDBO();
		$q = "SELECT `category_child_id`
			FROM `#__virtuemart_category_categories`
			WHERE `category_parent_id` = ".(int)$virtuemart_category_id;
		$db->setQuery($q);
		$db->query();
		if ($db->getAffectedRows() > 0){
// 			vmTime('hasChildren YES','hasChildren');
			return true;
		} else {
// 			vmTime('hasChildren NO','hasChildren');
			return false;
		}

	}

	/**
	 * Creates a bulleted of the childen of this category if they exist
	 *
	 * @author RolandD
	 * @todo Add vendor ID
	 * @param int $virtuemart_category_id the category ID to create the list of
	 * @return array containing the child categories
	 */
/*	public function getChildrenList($virtuemart_category_id,$limit=false) {
		$db = JFactory::getDBO();
		$childs = array();

		$q = "SELECT `#__virtuemart_categories`.*, `category_child_id`
			FROM `#__virtuemart_categories`, `#__virtuemart_category_categories`
			WHERE `#__virtuemart_category_categories`.`category_parent_id` = ".(int)$virtuemart_category_id."
			AND `#__virtuemart_categories`.`virtuemart_category_id`=`#__virtuemart_category_categories`.`category_child_id`
			AND `#__virtuemart_categories`.`virtuemart_vendor_id` = 1
			AND `#__virtuemart_categories`.`published` = 1
			ORDER BY `#__virtuemart_categories`.`ordering`, `#__virtuemart_categories`.`category_name` ASC";
		if ($limit) $q .=' limit 0,'.$limit;
		$db->setQuery($q);
		$childs = $db->loadObjectList();
		// Get the products in the category
		if(!empty($childs)){
			foreach ($childs as $ckey => $child) {
				$childs[$ckey]->number_of_products = $this->countProducts($child->category_child_id);
			}
		}


		return $childs;
	}
*/

	/**
	 * Creates a bulleted of the childen of this category if they exist
	 *
	 * @author RolandD
	 * @todo Add vendor ID
	 * @param int $virtuemart_category_id the category ID to create the list of
	 * @return array containing the child categories
	 */
	public function getParentsList($virtuemart_category_id) {

		$db = JFactory::getDBO();
		$menu = JFactory::getApplication()->getMenu();
		$parents = array();
		if (empty($query['Itemid'])) {
			$menuItem = $menu->getActive();
		} else {
			$menuItem = $menu->getItem($query['Itemid']);
		}
		$menuCatid = (empty($menuItem->query['virtuemart_category_id'])) ? 0 : $menuItem->query['virtuemart_category_id'];
		if ($menuCatid == $virtuemart_category_id) return ;
		$parents_id = array_reverse($this->getCategoryRecurse($virtuemart_category_id,$menuCatid));
		foreach ($parents_id as $id ) {
			$q = 'SELECT `category_name`,`virtuemart_category_id`
				FROM  `#__virtuemart_categories_'.VMLANG.'`
				WHERE  `virtuemart_category_id`='.(int)$id;

			$db->setQuery($q);

			$parents[] = $db->loadObject();
		}
		return $parents;
	}

	function getCategoryRecurse($virtuemart_category_id,$catMenuId,$first=true ) {
		static $idsArr = array();
		if($first) {
			$idsArr = array();
		}

		$db = JFactory::getDBO();
		$q  = "SELECT `category_child_id` AS `child`, `category_parent_id` AS `parent`
			FROM  `#__virtuemart_category_categories` AS `xref`
			WHERE `xref`.`category_child_id`= ".(int)$virtuemart_category_id;
		$db->setQuery($q);
		if (!$ids = $db->loadObject()) {
			return $idsArr;
		}
		if ($ids->child) $idsArr[] = $ids->child;
		if($ids->child != 0 and $catMenuId != $virtuemart_category_id and $catMenuId != $ids->parent) {
			$this->getCategoryRecurse($ids->parent,$catMenuId,false);
		}
		return $idsArr;
	}

	/*
	* Returns an array of the categories recursively for a given category
	* @author Kohl Patrick
	* @param int $id
	* @param int $maxLevel
	* @Object $this->container
	* @deprecated
	*/
	function treeCat($id=0,$maxLevel =1000) {
		static $level = 0;
		static $num = -1 ;
		$db = JFactory::getDBO();
		$q = 'SELECT `category_child_id`,`category_name` FROM `#__virtuemart_categories_'.VMLANG.'`
		LEFT JOIN `#__virtuemart_category_categories` on `#__virtuemart_categories`.`virtuemart_category_id`=`#__virtuemart_category_categories`.`category_child_id`
		WHERE `category_parent_id`='.(int)$id;
		$db->setQuery($q);
		$num ++;
		// if it is a leaf (no data underneath it) then return
		$childs = $db->loadObjectList();
		if ($level==$maxLevel) return;
		if ($childs) {
			$level++;
			foreach ($childs as $child) {
				$this->container[$num]->id = $child->category_child_id;
				$this->container[$num]->name = $child->category_name;
				$this->container[$num]->level = $level;
				self::treeCat($child->category_child_id,$maxLevel );
			}
			$level--;
		}
	}
	/**
	 * @author Kohl Patrick
	 * @param  $maxlevel the number of level
	 * @param  $id the root category id
 	 * @Object $this->container
	 * @ return categories id, name and level in container
	 * if you set Maxlevel to 0, then you see nothing
	 * max level =1 for simple category,2 for category and child cat ....
	 * don't set it for all (1000 levels)
	 * @deprecated
	 */
	function GetTreeCat($id=0,$maxLevel = 1000) {
		self::treeCat($id ,$maxLevel) ;
		return $this->container ;
	}


	/**
	* This function is repsonsible for returning an array containing category information
	* @param boolean Show only published products?
	* @param string the keyword to filter categories
	* @deprecated
	*/
	function getCategoryTreeArray( $only_published=true, $keyword = "" ) {

		$db = JFactory::getDBO();
		if( empty( $this->_category_tree)) {

			// Get only published categories
			$query  = "SELECT `virtuemart_category_id`, `category_description`, `category_name`,`category_child_id`, `category_parent_id`,`#__virtuemart_categories`.`ordering`, `published` as category_publish
						FROM `#__virtuemart_category_categories`, `#__virtuemart_categories_".VMLANG."` as L
						JOIN `#__virtuemart_categories`  using (`virtuemart_category_id`)
						WHERE ";
			if( $only_published ) {
				$query .= "`#__virtuemart_categories`.`published`=1 AND ";
			}
			$query .= " L.`virtuemart_category_id`=`#__virtuemart_category_categories`.`category_child_id` ";

			if( !empty( $keyword ) ) {
				$keyword = '"%' . $this->_db->getEscaped( $keyword, true ) . '%"' ;
				//$keyword = $this->_db->Quote($keyword, false);

				$query .= 'AND ( `category_name` LIKE '.$keyword.'
						   OR `category_description` LIKE '.$keyword.') ';
			}
/*			if( !empty( $keyword )) {

				$query .= "AND ( `category_name` LIKE '%$keyword%' ";
				$query .= "OR `category_description` LIKE '%$keyword%' ";
				$query .= ") ";
			}*/
			$query .= " ORDER BY `#__virtuemart_categories`.`ordering` ASC, L.`category_name` ASC";

			// initialise the query in the $database connector
			$db->setQuery($query);

			// Transfer the Result into a searchable Array
			$dbCategories = $db->loadAssocList();

		//if (!$ids = $db->loadObject())
			foreach( $dbCategories as $Cat ) {
				$this->_category_tree[$Cat['category_child_id']] = $Cat;
			}
		}
	}

	/**
	 * Sorts an array with categories so the order of the categories is the same as in a tree, just as a flat list.
	 * The Tree Depth is
	 *
	 * @deprecated
	 * @param array $categoryArr
	 */
	function sortCategoryTreeArray() {
		// Copy the Array into an Array with auto_incrementing Indexes
		$key = array_keys($this->_category_tree); // Array of category table primary keys

		$nrows = $size = sizeOf($key); // Category count

		/** FIRST STEP
	    * Order the Category Array and build a Tree of it
	    **/

		$id_list = array();
		$row_list = array();
		$depth_list = array();

		$children = array();
		$parent_ids = array();
		$parent_ids_hash = array();

		//Build an array of category references
		$category_tmp = Array();
		for ($i=0; $i<$size; $i++)
		{
			$category_tmp[$i] = $this->_category_tree[$key[$i]];
			$parent_ids[$i] = $category_tmp[$i]['category_parent_id'];
			if($category_tmp[$i]["category_parent_id"] == 0)
			{
				array_push($id_list,$category_tmp[$i]["category_child_id"]);
				array_push($row_list,$i);
				array_push($depth_list,0);
			}

			$parent_id = $parent_ids[$i];

			if (isset($parent_ids_hash[$parent_id]))
			{
				$parent_ids_hash[$parent_id][$i] = $parent_id;

			}
			else
			{
				$parent_ids_hash[$parent_id] = array($i => $parent_id);
			}

		}

		$loop_count = 0;
		$watch = array(); // Hash to store children
		while(count($id_list) < $nrows) {
			if( $loop_count > $nrows )
			break;
			$id_temp = array();
			$row_temp = array();
			$depth_temp = array();
			for($i = 0 ; $i < count($id_list) ; $i++) {
				$id = $id_list[$i];
				$row = $row_list[$i];
				$depth = $depth_list[$i];
				array_push($id_temp,$id);
				array_push($row_temp,$row);
				array_push($depth_temp,$depth);

				$children = @$parent_ids_hash[$id];

				if (!empty($children))
				{
					foreach($children as $key => $value) {
						if( !isset($watch[$id][$category_tmp[$key]["category_child_id"]])) {
							$watch[$id][$category_tmp[$key]["category_child_id"]] = 1;
							array_push($id_temp,$category_tmp[$key]["category_child_id"]);
							array_push($row_temp,$key);
							array_push($depth_temp,$depth + 1);
						}
					}
				}
			}
			$id_list = $id_temp;
			$row_list = $row_temp;
			$depth_list = $depth_temp;
			$loop_count++;
		}
		return array('id_list' => $id_list,
								'row_list' => $row_list,
								'depth_list' => $depth_list,
								'category_tmp' => $category_tmp);
	}

}