<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author RolandD, Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings.php 6350 2012-08-14 17:18:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!class_exists ('VmModel')){
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

/**
 * Model for VirtueMart Products
 *
 * @package VirtueMart
 * @author RolandD
 */
class VirtueMartModelRatings extends VmModel {

	var $_productBought = 0;

	/**
	 * constructs a VmModel
	 * setMainTable defines the maintable of the model
	 * @author Max Milbers
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTable('ratings');


		$layout = JRequest::getString('layout','default');
		$task = JRequest::getCmd('task','default');
		if($layout == 'list_reviews' or $task == 'listreviews'){
			vmdebug('in review list');
			$myarray = array('pr.created_on','virtuemart_rating_review_id','vote');
			$this->removevalidOrderingFieldName('created_on');
			$this->removevalidOrderingFieldName('product_name');
			$this->removevalidOrderingFieldName('virtuemart_rating_id');
			$this->removevalidOrderingFieldName('rating');
			$this->_selectedOrdering = 'pr.created_on';
		} else {
			$myarray = array('created_on','product_name','virtuemart_rating_id');
			$this->removevalidOrderingFieldName('pr.created_on');
			$this->removevalidOrderingFieldName('virtuemart_rating_review_id');
			$this->removevalidOrderingFieldName('vote');
			$this->_selectedOrdering = 'created_on';
		}
		$this->addvalidOrderingFieldName($myarray);

	}

    /**
     * Select the products to list on the product list page
     */
    public function getRatings() {

     	$tables = ' FROM `#__virtuemart_ratings` AS `r` JOIN `#__virtuemart_products_'.VMLANG.'` AS `p`
     			USING (`virtuemart_product_id`) ';
     	$whereString = '';
     	$this->_data = $this->exeSortSearchListQuery(0,' r.*,p.`product_name` ',$tables,$whereString,'',$this->_getOrdering());
// 	    $this->_data = $this->_getList($q, $this->getState('limitstart'), $this->getState('limit'));

		// set total for pagination
// 		$this->_total = $this->_getListCount($q) ;
// 		if(empty($this->_data)) $this->_data = array();
// 		if(!isset($this->_total)) $this->_total = 0;

     	return $this->_data;
    }


    /**
    * Load a single rating
    * @author RolandD
    */
    public function getRating($cids) {

	    if (empty($cids)) {
		    return;
	    }

		/* First copy the product in the product table */
		$ratings_data = $this->getTable('ratings');

		/* Load the rating */
		$joinValue = array('product_name' =>'#__virtuemart_products');

	    if ($cids) {
		    $ratings_data->load ($cids[0], $joinValue, 'virtuemart_product_id');
	    }

		/* Add some variables for a new rating */
		if (JRequest::getWord('task') == 'add') {
			/* Product ID */
			$ratings_data->virtuemart_product_id = JRequest::getInt('virtuemart_product_id',0);

			/* User ID */
			$user = JFactory::getUser();
			$ratings_data->virtuemart_user_id = $user->id;
		}

		return $ratings_data;
    }

	/**
	 * @author Max Milbers
	 * @param $virtuemart_product_id
	 * @return null
	 */
	function getReviews($virtuemart_product_id){

	    if (empty($virtuemart_product_id)) {
		    return NULL;
	    }

	    $select = '`u`.*,`pr`.*,`p`.`product_name`,`rv`.`vote`, `u`.`name` AS customer, `pr`.`published`';
	    $tables = ' FROM `#__virtuemart_rating_reviews` AS `pr`
		LEFT JOIN `#__users` AS `u`	ON `pr`.`created_by` = `u`.`id`
		LEFT JOIN `#__virtuemart_products_'.VMLANG.'` AS `p` ON `p`.`virtuemart_product_id` = `pr`.`virtuemart_product_id`
		LEFT JOIN `#__virtuemart_rating_votes` AS `rv` on `rv`.`virtuemart_product_id`=`pr`.`virtuemart_product_id` and `rv`.`created_by`=`u`.`id`';
	    $whereString = ' WHERE  `p`.`virtuemart_product_id` = "'.$virtuemart_product_id.'"';

	    $result = $this->exeSortSearchListQuery(0,$select,$tables,$whereString,'',$this->_getOrdering());

     	return $result;
    }

	/**
	 * @author Max Milbers
	 * @param $cids
	 * @return mixed@
	 */
	function getReview($cids){

       	$q = 'SELECT `u`.*,`pr`.*,`p`.`product_name`,`rv`.`vote`,CONCAT_WS(" ",`u`.`title`,u.`last_name`,`u`.`first_name`) as customer FROM `#__virtuemart_rating_reviews` AS `pr`
		LEFT JOIN `#__virtuemart_userinfos` AS `u`
     	ON `pr`.`created_by` = `u`.`virtuemart_user_id`
		LEFT JOIN `#__virtuemart_products_'.VMLANG.'` AS `p`
     	ON `p`.`virtuemart_product_id` = `pr`.`virtuemart_product_id`
		LEFT JOIN `#__virtuemart_rating_votes` as `rv` on `rv`.`virtuemart_product_id`=`pr`.`virtuemart_product_id` and `rv`.`created_by`=`pr`.`created_by`
      WHERE virtuemart_rating_review_id="'.(int)$cids[0].'" ' ;
		$this->_db->setQuery($q);
		vmdebug('getReview',$this->_db->getQuery());
		return $this->_db->loadObject();
    }


    /**
     * gets a rating by a product id
     *
     * @author Max Milbers
     * @param int $product_id
     */

    function getRatingByProduct($product_id){
    	$q = 'SELECT * FROM `#__virtuemart_ratings` WHERE `virtuemart_product_id` = "'.(int)$product_id.'" ';
		$this->_db->setQuery($q);
		return $this->_db->loadObject();

    }

    /**
     * gets a review by a product id
     *
     * @author Max Milbers
     * @param int $product_id
     */

    function getReviewByProduct($product_id,$userId=0){
   		if(empty($userId)){
			$user = JFactory::getUser();
			$userId = $user->id;
    	}
		$q = 'SELECT * FROM `#__virtuemart_rating_reviews` WHERE `virtuemart_product_id` = "'.(int)$product_id.'" AND `created_by` = "'.(int)$userId.'" ';
		$this->_db->setQuery($q);
		return $this->_db->loadObject();
    }

    /**
     * gets a reviews by a product id
     *
     * @author Max Milbers
     * @param int $product_id
     */

	function getReviewsByProduct($product_id){
   		if(empty($userId)){
			$user = JFactory::getUser();
			$userId = $user->id;
    	}
		$q = 'SELECT * FROM `#__virtuemart_rating_reviews` WHERE `virtuemart_product_id` = "'.(int)$product_id.'" ';
		$this->_db->setQuery($q);
		return $this->_db->loadObjectList();
    }

    /**
     * gets a vote by a product id and userId
     *
     * @author Max Milbers
     * @param int $product_id
     */

    function getVoteByProduct($product_id,$userId=0){

    	if(empty($userId)){
			$user = JFactory::getUser();
			$userId = $user->id;
    	}
		$q = 'SELECT * FROM `#__virtuemart_rating_votes` WHERE `virtuemart_product_id` = "'.(int)$product_id.'" AND `created_by` = "'.(int)$userId.'" ';
		$this->_db->setQuery($q);
		return $this->_db->loadObject();

    }

    /**
    * Save a rating
    * @author  Max Milbers
    */
    public function saveRating($data) {

		//Check user_rating
		$maxrating = VmConfig::get('vm_maximum_rating_scale',5);

// 		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
// 		if(!Permissions::getInstance()->check('admin')){
		$app = JFactory::getApplication();
		if( $app->isSite() ){
			$user = JFactory::getUser();
			$userId = $user->id;
		} else {
			$userId = $data['created_by'];
		}

		if ( !empty($data['virtuemart_product_id']) && !empty($userId)){

			//sanitize input
			$data['virtuemart_product_id'] = (int)$data['virtuemart_product_id'];
			//normalize the rating
			if ($data['vote'] < 0) {
				$data['vote'] = 0;
			}
			if ($data['vote'] > ($maxrating + 1)) {
				$data['vote'] = $maxrating;
			}

			$data['lastip'] = $_SERVER['REMOTE_ADDR'];

			$data['vote'] = (int) $data['vote'];

			$rating = $this->getRatingByProduct($data['virtuemart_product_id']);
			vmdebug('$rating',$rating);
			$vote = $this->getVoteByProduct($data['virtuemart_product_id'],$userId);
			vmdebug('$vote',$vote);

			$data['virtuemart_rating_vote_id'] = empty($vote->virtuemart_rating_vote_id)? 0: $vote->virtuemart_rating_vote_id;

			if(isset($data['vote'])){
				$votesTable = $this->getTable('rating_votes');
		      $votesTable->bindChecknStore($data,TRUE);
		    	$errors = $votesTable->getErrors();
				foreach($errors as $error){
					vmError(get_class( $this ).'::Error store votes '.$error);
				}
			}

			if(!empty($rating->rates) && empty($vote) ){
				$data['rates'] = $rating->rates + $data['vote'];
				$data['ratingcount'] = $rating->ratingcount+1;
			}
			else {
				if (!empty($rating->rates) && !empty($vote->vote)) {
					$data['rates'] = $rating->rates - $vote->vote + $data['vote'];
					$data['ratingcount'] = $rating->ratingcount;
				}
				else {
					$data['rates'] = $data['vote'];
					$data['ratingcount'] = 1;
				}
			}

			if(empty($data['rates']) || empty($data['ratingcount']) ){
				$data['rating'] = 0;
			} else {
				$data['rating'] = $data['rates']/$data['ratingcount'];
			}

			$data['virtuemart_rating_id'] = empty($rating->virtuemart_rating_id)? 0: $rating->virtuemart_rating_id;
			vmdebug('saveRating $data',$data);
			$rating = $this->getTable('ratings');
			$rating->bindChecknStore($data,TRUE);
			$errors = $rating->getErrors();
			foreach($errors as $error){
				vmError(get_class( $this ).'::Error store rating '.$error);
			}

			if(!empty($data['comment'])){
				$data['comment'] = substr($data['comment'], 0, VmConfig::get('vm_reviews_maximum_comment_length', 2000)) ;

				// no HTML TAGS but permit all alphabet
				$value =	preg_replace('@<[\/\!]*?[^<>]*?>@si','',$data['comment']);//remove all html tags
				$value =	(string)preg_replace('#on[a-z](.+?)\)#si','',$value);//replace start of script onclick() onload()...
				$value = trim(str_replace('"', ' ', $value),"'") ;
				$data['comment'] =	(string)preg_replace('#^\'#si','',$value);//replace ' at start
				$data['comment'] = nl2br($data['comment']);  // keep returns
				//set to defaut value not used (prevent hack)
				$data['review_ok'] = 0;
				$data['review_rating'] = 0;
				$data['review_editable'] = 0;
				// Check if ratings are auto-published (set to 0 prevent injected by user)
				//
				$app = JFactory::getApplication();
				if( $app->isSite() ){
					if (!class_exists ('Permissions')) {
						require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
					}
					if(!Permissions::getInstance()->check('admin')){
						if (VmConfig::get ('reviews_autopublish', 1)) {
							$data['published'] = 1;
						}
					}

				}

				$review = $this->getReviewByProduct($data['virtuemart_product_id'],$userId);

				if(!empty($review->review_rates)){
					$data['review_rates'] = $review->review_rates + $data['vote'];
				} else {
					$data['review_rates'] = $data['vote'];
				}

				if(!empty($review->review_ratingcount)){
					$data['review_ratingcount'] = $review->review_ratingcount+1;
				} else {
					$data['review_ratingcount'] = 1;
				}

				$data['review_rating'] = $data['review_rates']/$data['review_ratingcount'];

				$data['virtuemart_rating_review_id'] = empty($review->virtuemart_rating_review_id)? 0: $review->virtuemart_rating_review_id;

				$reviewTable = $this->getTable('rating_reviews');
		      $reviewTable->bindChecknStore($data,TRUE);
				$errors = $reviewTable->getErrors();
				foreach($errors as $error){
					vmError(get_class( $this ).'::Error store review '.$error);
				}
			}
			return $data['virtuemart_rating_review_id'];
		} else{
			vmError('Cant save rating/review/vote without vote/product_id');
			return FALSE;
		}

    }

    /**
    * removes a product and related table entries
    *
    * @author Max Milberes
    */
    public function remove($ids) {

    	$rating = $this->getTable($this->_maintablename);
    	$review = $this->getTable('rating_reviews');
    	$votes = $this->getTable('rating_votes');

    	$ok = TRUE;
    	foreach($ids as $id) {

    		$rating->load($id);
    		$prod_id = $rating->virtuemart_product_id;

    		if (!$rating->delete($id)) {
    			vmError(get_class( $this ).'::Error deleting ratings '.$rating->getError());
    			$ok = FALSE;
    		}

    		if (!$review->delete($prod_id,'virtuemart_product_id')) {
    			vmError(get_class( $this ).'::Error deleting review '.$review->getError());
    			$ok = FALSE;
    		}

    		if (!$votes->delete($prod_id,'virtuemart_product_id')) {
    			vmError(get_class( $this ).'::Error deleting votes '.$votes->getError());
    			$ok = FALSE;
    		}
    	}

    	return $ok;

    }



    /**
	* Returns the number of reviews assigned to a product
	*
	* @author RolandD
	* @param int $pid Product ID
	* @return int
	*/
	public function countReviewsForProduct($pid) {
		$db = JFactory::getDBO();
		$q = "SELECT COUNT(*) AS total
			FROM #__virtuemart_rating_reviews
			WHERE virtuemart_product_id=".(int)$pid;
		$db->setQuery($q);
		$reviews = $db->loadResult();
		return $reviews;
	}

	public function showReview($product_id){

		return $this->show($product_id, VmConfig::get('showReviewFor','all'));
	}

	public function showRating(){

		return $this->show(0, VmConfig::get('showRatingFor','all'));
	}

	public function allowReview($product_id){
		return $this->show($product_id, VmConfig::get('reviewMode','registered'));
	}

	public function allowRating($product_id){
		return $this->show($product_id, VmConfig::get('reviewMode','registered'));
	}

	/**
	 * Decides if the rating/review should be shown on the FE
	 * @author Max Milbers
	 */
	private function show($product_id, $show){

		//dont show
		if($show == 'none'){
			return FALSE;
		}
		//show all
		else {
			if ($show == 'all') {
				return TRUE;
			}
			//show only registered
			else {
				if ($show == 'registered') {
					$user = JFactory::getUser ();
					return !empty($user->id);
				}
				//show only registered && who bought the product
				else {
					if ($show == 'bought') {
						if (!empty($this->_productBought)) {
							return TRUE;
						}

						$user = JFactory::getUser ();
						if (empty($product_id)) {
							return FALSE;
						}

						$db = JFactory::getDBO ();
						$q = 'SELECT COUNT(*) as total FROM `#__virtuemart_orders` AS o LEFT JOIN `#__virtuemart_order_items` AS oi ';
						$q .= 'ON `o`.`virtuemart_order_id` = `oi`.`virtuemart_order_id` ';
						$q .= 'WHERE o.virtuemart_user_id = "' . $user->id . '" AND oi.virtuemart_product_id = "' . $product_id . '" ';

						$db->setQuery ($q);
						$count = $db->loadResult ();
						if ($count) {
							$this->_productBought = TRUE;
							return TRUE;
						}
						else {
							return FALSE;
						}
					}
				}
			}
		}
	}
}
// pure php no closing tag
