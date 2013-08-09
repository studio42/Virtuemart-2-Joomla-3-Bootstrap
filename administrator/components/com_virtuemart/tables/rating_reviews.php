<?php
/**
*
* Product reviews table
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
* @version $Id: ratings.php 3267 2011-05-16 22:51:49Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Product review table class
 * The class is is used to manage the reviews in the shop.
 *
 * @package		VirtueMart
 * @author Max Milbers
 */
class TableRating_reviews extends VmTable {

	/** @var int Primary key */
	var $virtuemart_rating_review_id	= 0;
	/** @var int Product ID */
	var $virtuemart_product_id			= null;

	/** @var string The user comment */
	var $comment         				= null;
	/** @var int The number of stars awared */
	var $review_ok       				= null;

	/** The rating of shoppers for the review*/
	var $review_rates         			= null;
	var $review_ratingcount      		= null;
	var $review_rating      			= null;
	var $review_editable		   = 1;
	var $lastip      		= null;

	/** @var int State of the review */
	var $published         		= 0;


	/**
	* @author Max Milbers
	* @param JDataBase $db
	*/
	function __construct(&$db) {
		parent::__construct('#__virtuemart_rating_reviews', 'virtuemart_rating_review_id', $db);
		$this->setPrimaryKey('virtuemart_rating_review_id');
		$this->setObligatoryKeys('comment');

		$this->setLoggable();
	}


}
// pure php no closing tag
